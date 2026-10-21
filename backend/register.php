<?php

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/helpers.php';
start_app_session();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect_to(app_url('register.php'));
}

if (!verify_csrf_token(isset($_POST['csrf_token']) ? $_POST['csrf_token'] : null)) {
    redirect_with_flash('register.php', 'registration', 'Halaman pendaftaran sudah lama terbuka. Muat ulang, lalu coba lagi.');
}

if (!consume_rate_limit('register', 20, 3600)) {
    redirect_with_flash('register.php', 'registration', 'Terlalu banyak pendaftaran dari jaringan ini. Coba lagi nanti.');
}

$name = trim(post_string('name'));
$email = trim(post_string('email'));
$password = post_string('password');
$confirmation = post_string('confirm');

if ($name === '' || strlen($name) > 255 || strlen($email) > 254 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    redirect_with_flash('register.php', 'registration', 'Periksa nama dan alamat email Anda.');
}

if (strlen($password) < 12 || $password !== $confirmation) {
    redirect_with_flash('register.php', 'registration', 'Kata sandi minimal 12 karakter. Pastikan isinya sama di kedua kolom.');
}

$passwordHash = password_hash($password, PASSWORD_DEFAULT);

try {
    $statement = $db_connect->prepare(
        'INSERT INTO users (name, email, password) VALUES (?, ?, ?)'
    );
    $statement->bind_param('sss', $name, $email, $passwordHash);
    $statement->execute();
    $statement->close();
} catch (mysqli_sql_exception $exception) {
    if ($exception->getCode() === 1062) {
        redirect_with_flash('register.php', 'registration', 'Email ini sudah terdaftar. Coba masuk atau gunakan email lain.');
    }

    error_log('User registration failed: ' . $exception->getMessage());
    redirect_with_flash('register.php', 'registration', 'Akun belum berhasil dibuat. Coba lagi nanti.');
}

redirect_with_flash('login.php', 'auth', 'Akun sudah dibuat. Silakan masuk.', 'success');
