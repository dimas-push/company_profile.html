<?php

require_once __DIR__ . '/config/helpers.php';
require_admin();
require_once __DIR__ . '/config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect_to(app_url('inquiries.php'));
}

if (!verify_csrf_token(isset($_POST['csrf_token']) ? $_POST['csrf_token'] : null)) {
    redirect_with_flash('inquiries.php', 'inquiries', 'Halaman sudah lama terbuka. Muat ulang sebelum menghapus pesan.');
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$page = filter_input(INPUT_POST, 'page', FILTER_VALIDATE_INT);
$page = $page && $page > 0 ? $page : 1;
if (!$id || $id < 1) {
    redirect_with_flash('inquiries.php?page=' . $page, 'inquiries', 'Pesan yang dipilih tidak valid.');
}

try {
    $statement = $db_connect->prepare('DELETE FROM inquiries WHERE id = ?');
    $statement->bind_param('i', $id);
    $statement->execute();
    $deletedRows = $statement->affected_rows;
    $statement->close();

    if ($deletedRows !== 1) {
        redirect_with_flash('inquiries.php?page=' . $page, 'inquiries', 'Pesan tersebut sudah tidak tersedia.', 'warning');
    }
} catch (Throwable $exception) {
    error_log('Inquiry deletion failed: ' . $exception->getMessage());
    redirect_with_flash('inquiries.php?page=' . $page, 'inquiries', 'Pesan belum berhasil dihapus. Coba lagi nanti.');
}

redirect_with_flash('inquiries.php?page=' . $page, 'inquiries', 'Pesan berhasil dihapus.', 'success');
