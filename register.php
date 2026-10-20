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
</head>
<body>
    <main class="auth-page auth-page--register">
        <aside class="auth-aside auth-aside--animated auth-aside--register">
            <a class="brand" href="<?= e(app_url('index.php')); ?>">
                <span class="brand__mark" aria-hidden="true">E</span>
                <span>EDUSCOPE<small>Bimbel dan kursus</small></span>
            </a>
            <div class="auth-aside__copy">
                <p class="eyebrow">Pendaftaran akun</p>
                <h1>Buat akun EDUSCOPE</h1>
                <p>Masukkan data yang akan Anda gunakan saat masuk nanti.</p>
            </div>
            <?php require __DIR__ . '/partials/auth-garden.php'; ?>
            <div class="auth-aside__foot">Bimbel untuk pelajaran sekolah, kursus untuk keterampilan baru.</div>
        </aside>
        <section class="auth-main" aria-labelledby="register-title">
            <div class="auth-card">
                <a class="auth-back" href="<?= e(app_url('index.php')); ?>">← Kembali ke beranda</a>
                <h2 id="register-title">Buat akun baru</h2>
                <p>Lengkapi data di bawah ini.</p>
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
                        <input type="password" id="password" name="password" placeholder="Minimal 12 karakter" autocomplete="new-password" minlength="12" required>
                        <p class="field-hint">Panjang kata sandi minimal 12 karakter.</p>
                    </div>
                    <div class="field">
                        <label for="confirm">Ulangi kata sandi</label>
                        <input type="password" id="confirm" name="confirm" placeholder="Ketik ulang kata sandi" autocomplete="new-password" minlength="12" required>
                    </div>
                    <button class="button" type="submit" name="submit">Daftar</button>
                </form>
                <p class="auth-footer">Sudah punya akun? <a href="<?= e(app_url('login.php')); ?>">Masuk di sini</a></p>
            </div>
        </section>
    </main>
</body>
</html>
