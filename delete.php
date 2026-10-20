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

$statement = $db_connect->prepare('DELETE FROM courses WHERE id = ?');
$statement->bind_param('i', $id);
$statement->execute();
$statement->close();

redirect_with_flash('show.php', 'catalog', 'Kelas berhasil dihapus.', 'success');
