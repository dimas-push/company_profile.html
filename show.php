<?php
require_once __DIR__ . '/config/helpers.php';
require_admin();
require_once __DIR__ . '/config/db.php';

$queryError = false;
try {
    $courses = $db_connect->query(
        'SELECT id, name, target, duration, `schedule`, price, image, is_active FROM courses ORDER BY id DESC'
    );
    $courseCount = $courses->num_rows;
} catch (mysqli_sql_exception $exception) {
    error_log('Course list query failed: ' . $exception->getMessage());
    $courses = null;
    $courseCount = 0;
    $queryError = true;
}
$csrfToken = csrf_token();
$catalogFlash = consume_flash_message('catalog');
$activePage = 'courses';
$showHomeBrand = true;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Kelas | EDUSCOPE</title>
    <link rel="stylesheet" href="<?= e(app_url('style.css')); ?>">
    <link rel="stylesheet" href="<?= e(app_url('home.css')); ?>">
</head>
<body class="home-page admin-page">
    <a class="home-skip-link" href="#admin-content">Lewati ke daftar kelas</a>
    <?php include __DIR__ . '/partials/site-nav.php'; ?>
    <main class="admin-main" id="admin-content" tabindex="-1">
        <div class="container">
            <header class="page-intro">
                <div>
                    <p class="eyebrow">Katalog kelas</p>
                    <h1>Daftar kelas</h1>
                    <p>Tambah, ubah, atau hapus kelas yang tampil di halaman utama.</p>
                </div>
                <a class="button" href="<?= e(app_url('create.php')); ?>"><span aria-hidden="true">＋</span> Tambah kelas</a>
            </header>

            <?php if ($catalogFlash): ?>
                <div class="flash flash--<?= e($catalogFlash['type']); ?>" role="status"><?= e($catalogFlash['message']); ?></div>
            <?php endif; ?>

            <section class="panel" aria-label="Daftar kelas">
                <div class="catalog-toolbar">
                    <span class="catalog-count" id="courseCount" aria-live="polite"><?= $queryError ? 'Daftar kelas belum tersedia' : (int) $courseCount . ' kelas'; ?></span>
                    <label class="visually-hidden" for="courseSearch">Cari kelas</label>
                    <input class="search-input" type="search" id="courseSearch" placeholder="Cari nama kelas..." autocomplete="off" aria-label="Cari kelas" <?= $queryError ? 'disabled' : ''; ?>>
                </div>

                <?php if ($queryError): ?>
                    <div class="empty-state" role="alert"><strong>Daftar kelas belum bisa dibuka.</strong>Coba muat ulang halaman beberapa saat lagi.</div>
                <?php elseif ($courseCount === 0): ?>
                    <div class="empty-state"><strong>Belum ada kelas.</strong>Pilih “Tambah kelas” untuk memasukkan kelas pertama.</div>
                <?php else: ?>
                    <div class="course-list">
                        <?php while ($row = $courses->fetch_assoc()): ?>
                            <article class="course-row" data-course-name="<?= e($row['name']); ?>">
                                <?php if (trim($row['image']) !== ''): ?>
                                    <img class="course-row__image" src="<?= safe_href($row['image']); ?>" alt="" loading="lazy" decoding="async">
                                <?php else: ?>
                                    <div class="course-row__image course-row__image--placeholder" aria-hidden="true">EDUSCOPE</div>
                                <?php endif; ?>
                                <div class="course-row__content">
                                    <h2><?= e($row['name']); ?></h2>
                                    <div class="course-row__meta">
                                        <?php if ($row['target'] !== ''): ?><span><?= e($row['target']); ?></span><?php endif; ?>
                                        <?php if ($row['duration'] !== ''): ?><span><?= e($row['duration']); ?></span><?php endif; ?>
                                        <?php if ($row['schedule'] !== ''): ?><span><?= e($row['schedule']); ?></span><?php endif; ?>
                                        <?php if ($row['target'] === '' && $row['duration'] === '' && $row['schedule'] === ''): ?><span>Belum ada keterangan tambahan</span><?php endif; ?>
                                    </div>
                                    <div class="course-row__price"><?= (int) $row['price'] === 0 ? 'Biaya belum dicantumkan' : 'Rp ' . number_format($row['price'], 0, ',', '.'); ?></div>
                                    <span class="course-status <?= (int) $row['is_active'] === 1 ? 'course-status--active' : 'course-status--inactive'; ?>">
                                        <?= (int) $row['is_active'] === 1 ? 'Tampil di beranda' : 'Disembunyikan'; ?>
                                    </span>
                                </div>
                                <div class="course-row__actions">
                                    <a class="button button--quiet button--small" href="<?= e(app_url('edit.php?id=' . (int) $row['id'])); ?>">Edit</a>
                                    <form action="<?= e(app_url('delete.php')); ?>" method="post" onsubmit="return confirm('Hapus kelas ini? Tindakan ini tidak bisa dibatalkan.')">
                                        <input type="hidden" name="id" value="<?= (int) $row['id']; ?>">
                                        <input type="hidden" name="csrf_token" value="<?= e($csrfToken); ?>">
                                        <button class="button button--danger button--small" type="submit" aria-label="Hapus kelas <?= e($row['name']); ?>">Hapus</button>
                                    </form>
                                </div>
                            </article>
                        <?php endwhile; ?>
                        <div class="empty-state" id="noSearchResults" hidden><strong>Kelas tidak ditemukan.</strong>Coba kata pencarian lain.</div>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </main>
    <script src="<?= e(app_url('courses.js')); ?>" defer></script>
</body>
</html>
