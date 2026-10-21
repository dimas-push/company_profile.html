<?php

require_once __DIR__ . '/../config/helpers.php';
require_admin();
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect_to(app_url('create.php'));
}

if (!verify_csrf_token(isset($_POST['csrf_token']) ? $_POST['csrf_token'] : null)) {
    redirect_with_flash('create.php', 'program_form', 'Halaman ini sudah lama terbuka. Muat ulang sebelum menyimpan kelas.');
}

$name = trim(post_string('name'));
$description = trim(post_string('description'));
$target = trim(post_string('target'));
$duration = trim(post_string('duration'));
$schedule = trim(post_string('schedule'));
$price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_INT);
$image = isset($_FILES['image']) ? $_FILES['image'] : null;

if (
    $name === '' || strlen($name) > 255 || $description === '' || strlen($description) > 5000 ||
    strlen($target) > 160 || strlen($duration) > 120 || strlen($schedule) > 160 ||
    $price === false || $price === null || $price < 0
) {
    redirect_with_flash('create.php', 'program_form', 'Periksa nama kelas, keterangannya, dan biayanya.');
}

$uploadError = '';
$relativeImagePath = store_image_upload($image, $uploadError);
if ($relativeImagePath === false) {
    redirect_with_flash('create.php', 'program_form', $uploadError);
}
$uploadPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativeImagePath);

try {
    $statement = $db_connect->prepare(
        'INSERT INTO courses (name, description, target, duration, `schedule`, price, image) VALUES (?, ?, ?, ?, ?, ?, ?)'
    );
    $statement->bind_param('sssssis', $name, $description, $target, $duration, $schedule, $price, $relativeImagePath);
    $statement->execute();
    $statement->close();
} catch (Throwable $exception) {
    if (is_file($uploadPath)) {
        unlink($uploadPath);
    }

    error_log('Course creation failed: ' . $exception->getMessage());
    redirect_with_flash('create.php', 'program_form', 'Kelas belum tersimpan. Coba lagi.');
}

redirect_with_flash('show.php', 'catalog', 'Kelas berhasil ditambahkan.', 'success');
