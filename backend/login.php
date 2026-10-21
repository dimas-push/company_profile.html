<?php

require_once __DIR__ . '/../config/helpers.php';
start_app_session();
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect_to(app_url('login.php'));
}

if (!verify_csrf_token(isset($_POST['csrf_token']) ? $_POST['csrf_token'] : null)) {
    redirect_with_flash('login.php', 'auth', 'Halaman login sudah lama terbuka. Muat ulang, lalu coba lagi.');
}

if (!consume_rate_limit('login', 20, 900)) {
    redirect_with_flash('login.php', 'auth', 'Terlalu banyak percobaan masuk. Tunggu sebentar, lalu coba lagi.');
}

$email = trim(post_string('email'));
$password = post_string('password');

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
    redirect_with_flash('login.php', 'auth', 'Email atau kata sandi tidak cocok.');
}

try {
    $statement = $db_connect->prepare(
        'SELECT id, name, role, password FROM users WHERE email = ? LIMIT 1'
    );
    $statement->bind_param('s', $email);
    $statement->execute();
    $statement->bind_result($userId, $userName, $userRole, $passwordHash);
    $userFound = $statement->fetch();
    $statement->close();
} catch (mysqli_sql_exception $exception) {
    error_log('Login query failed: ' . $exception->getMessage());
    redirect_with_flash('login.php', 'auth', 'Login belum bisa diproses. Coba lagi nanti.');
}

if (!$userFound || !password_verify($password, $passwordHash)) {
    redirect_with_flash('login.php', 'auth', 'Email atau kata sandi tidak cocok.');
}

session_regenerate_id(true);
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
$_SESSION['user_id'] = $userId;
$_SESSION['user_name'] = $userName;
$_SESSION['user_role'] = $userRole;

redirect_to(app_url('profile.php'));
