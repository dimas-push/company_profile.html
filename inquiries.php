<?php
require_once __DIR__ . '/config/helpers.php';
require_admin();
require_once __DIR__ . '/config/db.php';
$csrfToken = csrf_token();
$activePage = 'inquiries';
$showHomeBrand = true;

$itemsPerPage = 20;
$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
$page = $page && $page > 0 ? $page : 1;
$totalInquiries = 0;
$totalPages = 1;
$queryError = false;
try {
    $countResult = $db_connect->query('SELECT COUNT(*) AS total FROM inquiries');
    $totalInquiries = (int) $countResult->fetch_assoc()['total'];
    $totalPages = max(1, (int) ceil($totalInquiries / $itemsPerPage));
    $page = min($page, $totalPages);
    $offset = ($page - 1) * $itemsPerPage;
    $inquiries = $db_connect->query(
        'SELECT id, name, email, message, created_at FROM inquiries ORDER BY created_at DESC, id DESC LIMIT ' . $itemsPerPage . ' OFFSET ' . (int) $offset
    );
} catch (mysqli_sql_exception $exception) {
    error_log('Inquiry list query failed: ' . $exception->getMessage());
    $inquiries = null;
    $queryError = true;
}
$inquiriesFlash = consume_flash_message('inquiries');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pesan Masuk | EDUSCOPE</title>
    <link rel="stylesheet" href="<?= e(app_url('style.css')); ?>">
    <link rel="stylesheet" href="<?= e(app_url('home.css')); ?>">
</head>
<body class="home-page admin-page">
    <a class="home-skip-link" href="#admin-content">Lewati ke daftar pesan</a>
    <?php include __DIR__ . '/partials/site-nav.php'; ?>
    <main class="admin-main" id="admin-content" tabindex="-1">
        <div class="container">
            <header class="page-intro">
                <div>
                    <p class="eyebrow">Pertanyaan pengunjung</p>
                    <h1>Pesan masuk</h1>
                    <p>Pesan yang dikirim lewat formulir utama muncul di sini. Hapus pesan setelah tidak lagi diperlukan.</p>
                </div>
            </header>
            <?php if ($inquiriesFlash): ?>
                <div class="flash flash--<?= e($inquiriesFlash['type']); ?>" role="status"><?= e($inquiriesFlash['message']); ?></div>
            <?php endif; ?>
            <section class="panel" aria-label="Daftar pesan">
                <?php if ($queryError): ?>
                    <div class="empty-state" role="alert"><strong>Pesan belum bisa dibuka.</strong>Coba muat ulang halaman beberapa saat lagi.</div>
                <?php elseif ($totalInquiries === 0): ?>
                    <div class="empty-state"><strong>Belum ada pesan.</strong>Pesan dari pengunjung akan tampil di halaman ini.</div>
                <?php else: ?>
                    <div class="catalog-toolbar">
                        <span class="catalog-count"><?= number_format($totalInquiries, 0, ',', '.'); ?> pesan &middot; halaman <?= (int) $page; ?> dari <?= (int) $totalPages; ?></span>
                    </div>
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
                                <form class="message-card__delete" action="<?= e(app_url('delete-inquiry.php')); ?>" method="post" onsubmit="return confirm('Hapus pesan ini? Data yang dihapus tidak dapat dikembalikan.')">
                                    <input type="hidden" name="id" value="<?= (int) $inquiry['id']; ?>">
                                    <input type="hidden" name="page" value="<?= (int) $page; ?>">
                                    <input type="hidden" name="csrf_token" value="<?= e($csrfToken); ?>">
                                    <button class="button button--danger button--small" type="submit">Hapus pesan</button>
                                </form>
                            </article>
                        <?php endwhile; ?>
                    </div>
                    <?php if ($totalPages > 1): ?>
                        <nav class="pagination" aria-label="Halaman pesan">
                            <?php if ($page > 1): ?><a class="button button--quiet button--small" href="<?= e(app_url('inquiries.php?page=' . ($page - 1))); ?>">Sebelumnya</a><?php endif; ?>
                            <span>Halaman <?= (int) $page; ?> / <?= (int) $totalPages; ?></span>
                            <?php if ($page < $totalPages): ?><a class="button button--quiet button--small" href="<?= e(app_url('inquiries.php?page=' . ($page + 1))); ?>">Berikutnya</a><?php endif; ?>
                        </nav>
                    <?php endif; ?>
                <?php endif; ?>
            </section>
        </div>
    </main>
</body>
</html>
