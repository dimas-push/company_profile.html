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

$name = trim(isset($_POST['name']) ? $_POST['name'] : '');
$email = trim(isset($_POST['email']) ? $_POST['email'] : '');
$password = isset($_POST['password']) ? $_POST['password'] : '';
$confirmation = isset($_POST['confirm']) ? $_POST['confirm'] : '';

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
