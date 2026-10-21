<?php

require_once __DIR__ . '/config/helpers.php';
require_admin();
require_once __DIR__ . '/config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect_to('show.php');
}

if (!verify_csrf_token(isset($_POST['csrf_token']) ? $_POST['csrf_token'] : null)) {
    redirect_with_flash('show.php', 'catalog', 'Halaman sudah lama terbuka. Muat ulang sebelum menghapus kelas.');
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    redirect_to('show.php');
}

$image = '';

try {
    $lookup = $db_connect->prepare('SELECT image FROM courses WHERE id = ? LIMIT 1');
    $lookup->bind_param('i', $id);
    $lookup->execute();
    $lookup->bind_result($image);
    $courseFound = $lookup->fetch();
    $lookup->close();

    if (!$courseFound) {
        redirect_with_flash('show.php', 'catalog', 'Kelas tersebut sudah tidak tersedia.', 'warning');
    }

    $statement = $db_connect->prepare('DELETE FROM courses WHERE id = ?');
    $statement->bind_param('i', $id);
    $statement->execute();
    $deletedRows = $statement->affected_rows;
    $statement->close();

    if ($deletedRows !== 1) {
        redirect_with_flash('show.php', 'catalog', 'Kelas belum berhasil dihapus. Muat ulang daftar, lalu coba lagi.');
    }

    delete_course_image($image);
} catch (Throwable $exception) {
    error_log('Course deletion failed: ' . $exception->getMessage());
    redirect_with_flash('show.php', 'catalog', 'Kelas belum berhasil dihapus. Coba lagi nanti.');
}

redirect_with_flash('show.php', 'catalog', 'Kelas berhasil dihapus.', 'success');
