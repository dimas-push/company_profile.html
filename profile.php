<?php
require_once __DIR__ . '/config/helpers.php';
require_login();
$userName = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Pengguna';
$isAdmin = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
$csrfToken = csrf_token();
$activePage = 'profile';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Akun | EDUSCOPE</title>
    <link rel="stylesheet" href="<?= e(app_url('style.css')); ?>">
</head>
<body>
    <?php include __DIR__ . '/partials/site-nav.php'; ?>
    <main class="admin-main">
        <div class="container">
            <header class="page-intro">
                <div>
                    <p class="eyebrow">Akun Anda</p>
                    <h1>Profil</h1>
                    <p>Pilih halaman yang ingin Anda buka.</p>
                </div>
            </header>
            <section class="panel profile-card" aria-labelledby="profile-name">
                <div class="profile-avatar" aria-hidden="true"><?= e(strtoupper(substr($userName, 0, 1))); ?></div>
                <div>
                    <p class="eyebrow">Hai,</p>
                    <h2 id="profile-name"><?= e($userName); ?></h2>
                    <?php if ($isAdmin): ?>
                        <p>Anda masuk sebagai pengelola EDUSCOPE.</p>
                        <div class="profile-actions">
                            <a class="button" href="<?= e(app_url('show.php')); ?>">Kelola kelas</a>
                            <a class="button button--quiet" href="<?= e(app_url('inquiries.php')); ?>">Lihat pesan</a>
                        </div>
                    <?php else: ?>
                        <p>Anda sudah masuk. Lihat kelas bimbel dan kursus yang tersedia.</p>
                        <div class="profile-actions">
                            <a class="button" href="<?= e(app_url('index.php#program')); ?>">Lihat kelas</a>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </main>
</body>
</html>
