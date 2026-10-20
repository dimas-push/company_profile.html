<?php

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
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

    if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
        http_response_code(403);
        exit('Halaman ini hanya bisa dibuka oleh pengelola. <a href="' . e(app_url('profile.php')) . '">Kembali ke akun</a>');
    }
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
    $isSecureRequest = !empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off';
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

    if ($file['error'] !== UPLOAD_ERR_OK || $file['size'] > 5 * 1024 * 1024) {
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
