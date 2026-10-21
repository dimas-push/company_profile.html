<?php

require_once __DIR__ . '/environment.php';

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function post_string($key, $default = '')
{
    return isset($_POST[$key]) && is_string($_POST[$key]) ? $_POST[$key] : $default;
}

function safe_href($value)
{
    $url = trim((string) $value);
    $scheme = parse_url($url, PHP_URL_SCHEME);

    if (
        $url === '' ||
        strpos($url, '//') === 0 ||
        strpos($url, '\\') !== false ||
        preg_match('/[\x00-\x1F\x7F]/', $url)
    ) {
        return '#';
    }

    if ($scheme !== null && !in_array(strtolower($scheme), array('http', 'https'), true)) {
        return '#';
    }

    return e($url);
}

function redirect_to($location)
{
    header('Location: ' . $location);
    exit;
}

function app_url($path = '')
{
    $scriptDirectory = isset($_SERVER['SCRIPT_NAME'])
        ? str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']))
        : '';

    if (substr($scriptDirectory, -8) === '/backend') {
        $scriptDirectory = substr($scriptDirectory, 0, -8);
    }

    $basePath = rtrim($scriptDirectory, '/') . '/';
    return $basePath . ltrim((string) $path, '/');
}

function require_login()
{
    start_app_session();

    if (empty($_SESSION['user_id'])) {
        redirect_to(app_url('login.php'));
    }
}

function require_admin()
{
    require_login();
    global $db_connect;

    require_once __DIR__ . '/db.php';

    $userId = (int) $_SESSION['user_id'];
    try {
        $statement = $db_connect->prepare('SELECT role FROM users WHERE id = ? LIMIT 1');
        $statement->bind_param('i', $userId);
        $statement->execute();
        $statement->bind_result($currentRole);
        $userFound = $statement->fetch();
        $statement->close();
    } catch (Throwable $exception) {
        error_log('Admin authorization check failed: ' . $exception->getMessage());
        http_response_code(503);
        exit('Pemeriksaan akses admin sedang bermasalah. Coba lagi nanti.');
    }

    if (!$userFound) {
        $_SESSION = array();
        session_regenerate_id(true);
        redirect_to(app_url('login.php'));
    }

    $_SESSION['user_role'] = $currentRole;

    if ($currentRole !== 'admin') {
        http_response_code(403);
        exit('Halaman ini hanya bisa dibuka oleh pengelola. <a href="' . e(app_url('profile.php')) . '">Kembali ke akun</a>');
    }
}

function consume_rate_limit($scope, $limit, $windowSeconds)
{
    $scope = preg_replace('/[^a-z0-9_-]/i', '', (string) $scope);
    $remoteAddress = isset($_SERVER['REMOTE_ADDR']) ? (string) $_SERVER['REMOTE_ADDR'] : 'unknown';
    if (filter_var($remoteAddress, FILTER_VALIDATE_IP) === false) {
        $remoteAddress = 'unknown';
    }

    $directory = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'rate-limits';
    if (!is_dir($directory) && !@mkdir($directory, 0700, true) && !is_dir($directory)) {
        error_log('Rate limit storage directory is unavailable.');
        return false;
    }

    $filePath = $directory . DIRECTORY_SEPARATOR . $scope . '-' . hash('sha256', $remoteAddress) . '.json';
    $file = @fopen($filePath, 'c+');
    if ($file === false || !flock($file, LOCK_EX)) {
        if (is_resource($file)) {
            fclose($file);
        }
        error_log('Rate limit storage could not be locked.');
        return false;
    }
    @chmod($filePath, 0600);

    rewind($file);
    $storedAttempts = stream_get_contents($file);
    $attempts = json_decode($storedAttempts, true);
    if (!is_array($attempts)) {
        $attempts = array();
    }

    $now = time();
    $cutoff = $now - (int) $windowSeconds;
    $attempts = array_values(array_filter($attempts, function ($timestamp) use ($cutoff, $now) {
        return is_numeric($timestamp) && (int) $timestamp >= $cutoff && (int) $timestamp <= $now;
    }));

    if (count($attempts) >= (int) $limit) {
        flock($file, LOCK_UN);
        fclose($file);
        return false;
    }

    $attempts[] = $now;
    $encodedAttempts = json_encode($attempts);
    rewind($file);
    $saved = false;
    if ($encodedAttempts !== false && ftruncate($file, 0)) {
        $writtenBytes = fwrite($file, $encodedAttempts);
        $saved = $writtenBytes === strlen($encodedAttempts) && fflush($file);
    }
    flock($file, LOCK_UN);
    fclose($file);

    if (!$saved) {
        error_log('Rate limit data could not be saved.');
        return false;
    }

    cleanup_rate_limit_files($directory, $now);
    return true;
}

function cleanup_rate_limit_files($directory, $now)
{
    $lockPath = $directory . DIRECTORY_SEPARATOR . '.cleanup';
    $lockFile = @fopen($lockPath, 'c+');
    if ($lockFile === false || !flock($lockFile, LOCK_EX | LOCK_NB)) {
        if (is_resource($lockFile)) {
            fclose($lockFile);
        }
        return;
    }

    rewind($lockFile);
    $lastCleanup = (int) trim((string) stream_get_contents($lockFile));
    if ($now - $lastCleanup >= 86400) {
        $rateLimitFiles = glob($directory . DIRECTORY_SEPARATOR . '*.json');
        if (is_array($rateLimitFiles)) {
            foreach ($rateLimitFiles as $filePath) {
                if (is_file($filePath) && filemtime($filePath) < $now - 86400) {
                    @unlink($filePath);
                }
            }
        }

        rewind($lockFile);
        ftruncate($lockFile, 0);
        fwrite($lockFile, (string) $now);
        fflush($lockFile);
    }

    flock($lockFile, LOCK_UN);
    fclose($lockFile);
}

function delete_course_image($relativePath)
{
    if (!is_string($relativePath) || !preg_match('/^uploads\/[a-f0-9]{32}\.(jpg|png|gif|webp)$/i', $relativePath)) {
        return false;
    }

    $uploadDirectory = realpath(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'uploads');
    $imagePath = realpath(dirname(__DIR__) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath));
    if ($uploadDirectory === false || $imagePath === false || !is_file($imagePath)) {
        return false;
    }

    $uploadPrefix = rtrim($uploadDirectory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    if (stripos($imagePath, $uploadPrefix) !== 0) {
        return false;
    }

    return @unlink($imagePath);
}

function set_flash_message($key, $message, $type = 'danger')
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        start_app_session();
    }

    $_SESSION['flash_messages'][$key] = array(
        'message' => (string) $message,
        'type' => in_array($type, array('success', 'info', 'warning', 'danger'), true) ? $type : 'info',
    );
}

function consume_flash_message($key)
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        start_app_session();
    }

    if (!isset($_SESSION['flash_messages'][$key])) {
        return null;
    }

    $message = $_SESSION['flash_messages'][$key];
    unset($_SESSION['flash_messages'][$key]);
    return $message;
}

function redirect_with_flash($path, $key, $message, $type = 'danger')
{
    set_flash_message($key, $message, $type);
    redirect_to(app_url($path));
}

function start_app_session()
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return true;
    }

    $sessionDirectoryCandidates = array(
        sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'eduscope-sessions',
        dirname(__DIR__) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'sessions',
    );
    $sessionDirectory = null;

    foreach ($sessionDirectoryCandidates as $candidate) {
        if (!is_dir($candidate) && !@mkdir($candidate, 0700, true) && !is_dir($candidate)) {
            continue;
        }

        if (is_writable($candidate)) {
            $sessionDirectory = $candidate;
            break;
        }
    }

    if ($sessionDirectory === null) {
        error_log('The application session directory is not writable.');
        http_response_code(500);
        exit('Sesi belum bisa dimulai. Coba lagi nanti.');
    }

    session_name('EDUSCOPESESSID');
    session_save_path($sessionDirectory);
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    $isSecureRequest = is_production_environment() || (!empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off');
    session_set_cookie_params(array(
        'lifetime' => 0,
        'path' => '/',
        'secure' => $isSecureRequest,
        'httponly' => true,
        'samesite' => 'Lax',
    ));

    if (!session_start()) {
        http_response_code(500);
        exit('Sesi belum bisa dimulai. Coba lagi nanti.');
    }

    return true;
}

function csrf_token()
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        start_app_session();
    }

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token)
{
    return session_status() === PHP_SESSION_ACTIVE &&
        isset($_SESSION['csrf_token']) &&
        is_string($token) &&
        hash_equals($_SESSION['csrf_token'], $token);
}

function store_image_upload($file, &$error, $required = true)
{
    $error = '';

    if ($file === null && !$required) {
        return null;
    }

    if (!is_array($file) || !isset($file['error'], $file['size'], $file['tmp_name'])) {
        $error = 'Pilih foto kelas terlebih dahulu.';
        return false;
    }

    if ($file['error'] === UPLOAD_ERR_NO_FILE && !$required) {
        return null;
    }

    if ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE) {
        $error = 'Foto melewati batas upload server. Coba gambar yang lebih kecil.';
        return false;
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $error = 'Foto belum berhasil diunggah. Coba pilih file lain.';
        return false;
    }

    if ($file['size'] > 5 * 1024 * 1024) {
        $error = 'Ukuran foto tidak boleh lebih dari 5 MB.';
        return false;
    }

    $allowedTypes = array(
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp',
    );
    $fileInfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $fileInfo->file($file['tmp_name']);

    if (!isset($allowedTypes[$mimeType])) {
        $error = 'Gunakan foto berformat JPG, PNG, GIF, atau WebP.';
        return false;
    }

    $imageDimensions = @getimagesize($file['tmp_name']);
    if (
        $imageDimensions === false ||
        $imageDimensions[0] > 6000 ||
        $imageDimensions[1] > 6000 ||
        ($imageDimensions[0] * $imageDimensions[1]) > 20000000
    ) {
        $error = 'Pilih foto berukuran maksimal 6000 × 6000 piksel.';
        return false;
    }

    $uploadDirectory = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'uploads';
    if (!is_dir($uploadDirectory) && !mkdir($uploadDirectory, 0755, true) && !is_dir($uploadDirectory)) {
        $error = 'Folder penyimpanan foto belum tersedia.';
        return false;
    }

    $fileName = bin2hex(random_bytes(16)) . '.' . $allowedTypes[$mimeType];
    $uploadPath = $uploadDirectory . DIRECTORY_SEPARATOR . $fileName;

    if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
        $error = 'Foto belum berhasil diunggah. Coba pilih file lagi.';
        return false;
    }

    return 'uploads/' . $fileName;
}
