<?php
require_once __DIR__ . '/config/helpers.php';
require_login();
$userName = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Pengguna';
$isAdmin = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
$csrfToken = csrf_token();
$activePage = 'profile';
$showHomeBrand = true;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Akun | EDUSCOPE</title>
    <link rel="stylesheet" href="<?= e(app_url('style.css')); ?>">
    <link rel="stylesheet" href="<?= e(app_url('home.css')); ?>">
</head>
<body class="home-page profile-page">
    <a class="home-skip-link" href="#profile-content">Lewati ke menu akun</a>
    <?php include __DIR__ . '/partials/site-nav.php'; ?>
    <main class="profile-main" id="profile-content" tabindex="-1">
        <div class="container">
            <header class="profile-heading">
                <p class="home-kicker"><span></span> RUANG AKUN EDUSCOPE</p>
                <h1>Halo, <?= e($userName); ?>!</h1>
                <p>Kelola akun atau pilih aktivitas EDUSCOPE yang ingin dibuka.</p>
            </header>
            <section class="profile-summary" aria-labelledby="profile-name">
                <div class="profile-summary__identity">
                    <div class="profile-avatar" aria-hidden="true"><?= e(strtoupper(substr($userName, 0, 1))); ?></div>
                    <div class="profile-summary__copy">
                        <span class="profile-role <?= $isAdmin ? 'profile-role--admin' : ''; ?>"><?= $isAdmin ? 'Pengelola' : 'Peserta'; ?></span>
                        <h2 id="profile-name"><?= e($userName); ?></h2>
                        <p><?= $isAdmin ? 'Anda masuk sebagai pengelola EDUSCOPE.' : 'Akunmu siap untuk menjelajahi kelas EDUSCOPE.'; ?></p>
                    </div>
                </div>
                <div class="profile-status"><span aria-hidden="true"></span> Akun aktif</div>
            </section>

            <section class="profile-menu" aria-labelledby="profile-menu-title">
                <header class="profile-menu__heading">
                    <div>
                        <p class="home-kicker"><span></span> LANJUTKAN DARI SINI</p>
                        <h2 id="profile-menu-title">Menu akun</h2>
                    </div>
                    <p>Pilih yang ingin kamu buka.</p>
                </header>
                <div class="profile-actions">
                    <?php if ($isAdmin): ?>
                        <a class="profile-action profile-action--violet" href="<?= e(app_url('show.php')); ?>">
                            <span class="profile-action__icon" aria-hidden="true">01</span>
                            <span class="profile-action__copy"><strong>Kelola kelas</strong><small>Perbarui informasi kelas EDUSCOPE.</small></span>
                            <span class="profile-action__arrow" aria-hidden="true">&rarr;</span>
                        </a>
                        <a class="profile-action profile-action--pink" href="<?= e(app_url('inquiries.php')); ?>">
                            <span class="profile-action__icon" aria-hidden="true">02</span>
                            <span class="profile-action__copy"><strong>Lihat pesan</strong><small>Baca pertanyaan dari calon peserta.</small></span>
                            <span class="profile-action__arrow" aria-hidden="true">&rarr;</span>
                        </a>
                    <?php else: ?>
                        <a class="profile-action profile-action--green" href="<?= e(app_url('index.php#program')); ?>">
                            <span class="profile-action__icon" aria-hidden="true">01</span>
                            <span class="profile-action__copy"><strong>Jelajahi kelas</strong><small>Lihat pilihan bimbel dan kursus yang tersedia.</small></span>
                            <span class="profile-action__arrow" aria-hidden="true">&rarr;</span>
                        </a>
                        <a class="profile-action profile-action--yellow" href="<?= e(app_url('index.php#kontak')); ?>">
                            <span class="profile-action__icon" aria-hidden="true">02</span>
                            <span class="profile-action__copy"><strong>Tanya tim EDUSCOPE</strong><small>Kirim pertanyaan sebelum memilih kelas.</small></span>
                            <span class="profile-action__arrow" aria-hidden="true">&rarr;</span>
                        </a>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </main>
</body>
</html>
