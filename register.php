<?php
require_once __DIR__ . '/config/helpers.php';
$csrfToken = csrf_token();
$registrationFlash = consume_flash_message('registration');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Buat Akun | EDUSCOPE</title>
    <link rel="stylesheet" href="<?= e(app_url('style.css')); ?>">
    <link rel="stylesheet" href="<?= e(app_url('home.css')); ?>">
    <script src="<?= e(app_url('auth.js')); ?>" defer></script>
</head>
<body class="home-page">
    <main class="auth-page auth-page--register auth-page--eduscope">
        <aside class="auth-aside auth-aside--animated auth-aside--register">
            <a class="home-brand" href="<?= e(app_url('index.php')); ?>">
                <span class="home-brand__word">EDU<span>SCOPE</span></span>
                <small>LEARNING CENTRE - SUKABUMI</small>
            </a>
            <div class="auth-aside__copy">
                <p class="eyebrow">Pendaftaran akun</p>
                <h1>Mulai belajar bersama EDUSCOPE</h1>
                <p>Buat akun untuk melanjutkan proses pendaftaran.</p>
            </div>
            <?php require __DIR__ . '/partials/auth-garden.php'; ?>
            <div class="auth-aside__foot">Bimbel untuk pelajaran sekolah, kursus untuk keterampilan baru.</div>
        </aside>
        <section class="auth-main" aria-labelledby="register-title">
            <div class="auth-card">
                <a class="auth-back" href="<?= e(app_url('index.php')); ?>"><span aria-hidden="true">&larr;</span> Kembali ke beranda</a>
                <p class="home-kicker"><span></span> Satu langkah lagi</p>
                <h2 id="register-title">Buat akun baru</h2>
                <p>Lengkapi data berikut untuk mulai.</p>
                <?php if ($registrationFlash): ?>
                    <div class="flash flash--<?= e($registrationFlash['type']); ?>" role="alert"><?= e($registrationFlash['message']); ?></div>
                <?php endif; ?>
                <form action="<?= e(app_url('backend/register.php')); ?>" method="post">
                    <input type="hidden" name="csrf_token" value="<?= e($csrfToken); ?>">
                    <div class="field">
                        <label for="name">Nama lengkap</label>
                        <input type="text" id="name" name="name" placeholder="Nama lengkap" autocomplete="name" maxlength="255" required>
                    </div>
                    <div class="field">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="nama@email.com" autocomplete="email" maxlength="254" required>
                    </div>
                    <div class="field">
                        <label for="password">Kata sandi</label>
                        <div class="password-control">
                            <input type="password" id="password" name="password" placeholder="Minimal 12 karakter" autocomplete="new-password" minlength="12" required>
                            <button class="password-toggle" type="button" aria-label="Tampilkan kata sandi" aria-controls="password" aria-pressed="false" title="Tampilkan kata sandi" data-password-toggle>
                                <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false" data-eye-show><path d="M2.5 12s3.4-6 9.5-6 9.5 6 9.5 6-3.4 6-9.5 6-9.5-6-9.5-6Z"/><circle cx="12" cy="12" r="2.7"/></svg>
                                <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false" data-eye-hide hidden><path d="M3 3l18 18M10.6 6.2A10.6 10.6 0 0 1 12 6c6.1 0 9.5 6 9.5 6a16 16 0 0 1-3 3.7M6.2 6.3C3.8 8 2.5 12 2.5 12s3.4 6 9.5 6c1.3 0 2.5-.3 3.5-.8"/><path d="M9.9 9.9a3 3 0 0 0 4.2 4.2"/></svg>
                            </button>
                        </div>
                        <p class="field-hint">Panjang kata sandi minimal 12 karakter.</p>
                    </div>
                    <div class="field">
                        <label for="confirm">Ulangi kata sandi</label>
                        <div class="password-control">
                            <input type="password" id="confirm" name="confirm" placeholder="Ketik ulang kata sandi" autocomplete="new-password" minlength="12" required>
                            <button class="password-toggle" type="button" aria-label="Tampilkan kata sandi" aria-controls="confirm" aria-pressed="false" title="Tampilkan kata sandi" data-password-toggle>
                                <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false" data-eye-show><path d="M2.5 12s3.4-6 9.5-6 9.5 6 9.5 6-3.4 6-9.5 6-9.5-6-9.5-6Z"/><circle cx="12" cy="12" r="2.7"/></svg>
                                <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false" data-eye-hide hidden><path d="M3 3l18 18M10.6 6.2A10.6 10.6 0 0 1 12 6c6.1 0 9.5 6 9.5 6a16 16 0 0 1-3 3.7M6.2 6.3C3.8 8 2.5 12 2.5 12s3.4 6 9.5 6c1.3 0 2.5-.3 3.5-.8"/><path d="M9.9 9.9a3 3 0 0 0 4.2 4.2"/></svg>
                            </button>
                        </div>
                    </div>
                    <button class="button home-button" type="submit" name="submit">Daftar <span class="home-button__arrow" aria-hidden="true">&rarr;</span></button>
                </form>
                <p class="auth-footer">Sudah punya akun? <a href="<?= e(app_url('login.php')); ?>">Masuk di sini</a></p>
            </div>
        </section>
    </main>
</body>
</html>
