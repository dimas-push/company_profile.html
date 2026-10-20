<?php
require_once __DIR__ . '/config/helpers.php';
$csrfToken = csrf_token();
$authFlash = consume_flash_message('auth');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk | EDUSCOPE</title>
    <link rel="stylesheet" href="<?= e(app_url('style.css')); ?>">
</head>
<body>
    <main class="auth-page auth-page--login">
        <aside class="auth-aside auth-aside--login">
            <a class="brand" href="<?= e(app_url('index.php')); ?>">
                <span class="brand__mark" aria-hidden="true">E</span>
                <span>EDUSCOPE<small>Bimbel dan kursus</small></span>
            </a>
            <div class="auth-aside__copy">
                <p class="eyebrow">Halaman pengelola</p>
                <h1>Masuk ke akun EDUSCOPE</h1>
                <p>Pengelola dapat mengubah daftar kelas dan membaca pesan yang masuk.</p>
            </div>
            <?php require __DIR__ . '/partials/auth-garden.php'; ?>
            <div class="auth-aside__foot">Bimbel untuk pelajaran sekolah, kursus untuk keterampilan baru.</div>
        </aside>
        <section class="auth-main" aria-labelledby="login-title">
            <div class="auth-card">
                <a class="auth-back" href="<?= e(app_url('index.php')); ?>">← Kembali ke beranda</a>
                <h2 id="login-title">Masuk ke akun</h2>
                <p>Masukkan email dan kata sandi akun Anda.</p>
                <?php if ($authFlash): ?>
                    <div class="flash flash--<?= e($authFlash['type']); ?>" role="alert"><?= e($authFlash['message']); ?></div>
                <?php endif; ?>
                <form action="<?= e(app_url('backend/login.php')); ?>" method="post">
                    <input type="hidden" name="csrf_token" value="<?= e($csrfToken); ?>">
                    <div class="field">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="nama@email.com" autocomplete="email" maxlength="254" required>
                    </div>
                    <div class="field">
                        <label for="password">Kata sandi</label>
                        <input type="password" id="password" name="password" placeholder="Masukkan kata sandi" autocomplete="current-password" required>
                    </div>
                    <button class="button" type="submit" name="submit">Masuk</button>
                </form>
                <p class="auth-footer">Belum punya akun? <a href="<?= e(app_url('register.php')); ?>">Daftar di sini</a></p>
            </div>
        </section>
    </main>
</body>
</html>
