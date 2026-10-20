<?php
require_once __DIR__ . '/config/helpers.php';
require_admin();
require_once __DIR__ . '/config/db.php';
$csrfToken = csrf_token();
$activePage = 'inquiries';

$queryError = false;
try {
    $inquiries = $db_connect->query(
        'SELECT id, name, email, message, created_at FROM inquiries ORDER BY created_at DESC, id DESC'
    );
} catch (mysqli_sql_exception $exception) {
    error_log('Inquiry list query failed: ' . $exception->getMessage());
    $inquiries = null;
    $queryError = true;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pesan Masuk | EDUSCOPE</title>
    <link rel="stylesheet" href="<?= e(app_url('style.css')); ?>">
</head>
<body>
    <?php include __DIR__ . '/partials/site-nav.php'; ?>
    <main class="admin-main">
        <div class="container">
            <header class="page-intro">
                <div>
                    <p class="eyebrow">Pertanyaan pengunjung</p>
                    <h1>Pesan masuk</h1>
                    <p>Pesan yang dikirim lewat formulir di halaman utama akan muncul di sini.</p>
                </div>
            </header>
            <section class="panel" aria-label="Daftar pesan">
                <?php if ($queryError): ?>
                    <div class="empty-state" role="alert"><strong>Pesan belum bisa dibuka.</strong>Coba muat ulang halaman beberapa saat lagi.</div>
                <?php elseif ($inquiries->num_rows === 0): ?>
                    <div class="empty-state"><strong>Belum ada pesan.</strong>Pesan dari pengunjung akan tampil di halaman ini.</div>
                <?php else: ?>
                    <div class="message-list">
                        <?php while ($inquiry = $inquiries->fetch_assoc()): ?>
                            <article class="message-card">
                                <div class="message-card__head">
                                    <div>
                                        <h2><?= e($inquiry['name']); ?></h2>
                                        <a href="mailto:<?= e($inquiry['email']); ?>"><?= e($inquiry['email']); ?></a>
                                    </div>
                                    <time datetime="<?= e(date(DATE_ATOM, strtotime($inquiry['created_at']))); ?>"><?= e(date('d M Y, H:i', strtotime($inquiry['created_at']))); ?></time>
                                </div>
                                <p><?= nl2br(e($inquiry['message'])); ?></p>
                            </article>
                        <?php endwhile; ?>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </main>
</body>
</html>
