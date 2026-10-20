<?php
require_once __DIR__ . '/config/helpers.php';
require_admin();
$csrfToken = csrf_token();
$programFormFlash = consume_flash_message('program_form');
$activePage = 'create';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tambah Kelas | EDUSCOPE</title>
    <link rel="stylesheet" href="<?= e(app_url('style.css')); ?>">
</head>
<body>
    <?php include __DIR__ . '/partials/site-nav.php'; ?>
    <main class="admin-main">
        <div class="container">
            <header class="page-intro">
                <div>
                    <p class="eyebrow">Katalog kelas</p>
                    <h1>Tambah kelas</h1>
                    <p>Masukkan kelas bimbel atau kursus yang ingin ditampilkan di halaman utama.</p>
                </div>
                <a class="button button--quiet" href="<?= e(app_url('show.php')); ?>">← Kembali ke daftar</a>
            </header>

            <div class="form-layout">
                <section class="form-card">
                    <h2>Detail kelas</h2>
                    <p class="form-intro">Lengkapi nama, keterangan, jadwal, dan biaya kelas.</p>
                    <?php if ($programFormFlash): ?>
                        <div class="flash flash--<?= e($programFormFlash['type']); ?>" role="alert"><?= e($programFormFlash['message']); ?></div>
                    <?php endif; ?>
                    <form action="<?= e(app_url('backend/create.php')); ?>" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?= e($csrfToken); ?>">
                        <div class="field">
                            <label for="name">Nama kelas</label>
                            <input type="text" id="name" name="name" placeholder="Contoh: Bimbel Matematika SMP" maxlength="255" required>
                        </div>
                        <div class="field">
                            <label for="description">Keterangan kelas</label>
                            <textarea id="description" name="description" rows="5" maxlength="5000" placeholder="Apa yang akan dipelajari di kelas ini?" required></textarea>
                        </div>
                        <div class="field-grid">
                            <div class="field">
                                <label for="target">Untuk peserta <span class="optional">(opsional)</span></label>
                                <input type="text" id="target" name="target" maxlength="160" placeholder="Contoh: SMP kelas 7–9">
                            </div>
                            <div class="field">
                                <label for="duration">Lama belajar <span class="optional">(opsional)</span></label>
                                <input type="text" id="duration" name="duration" maxlength="120" placeholder="Contoh: 8 pertemuan">
                            </div>
                            <div class="field field--full">
                                <label for="schedule">Waktu belajar <span class="optional">(opsional)</span></label>
                                <input type="text" id="schedule" name="schedule" maxlength="160" placeholder="Contoh: Sabtu, 09.00–11.00">
                            </div>
                            <div class="field field--full">
                                <label for="price">Biaya kelas (Rp)</label>
                                <input type="number" id="price" name="price" min="0" step="1" placeholder="0" required>
                                <p class="field-hint">Isi 0 jika biayanya perlu ditanyakan terlebih dahulu.</p>
                            </div>
                        </div>
                        <div class="field">
                            <label for="image">Foto kelas</label>
                            <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/gif,image/webp" required>
                            <p class="field-hint">Pilih gambar JPG, PNG, GIF, atau WebP (maksimal 5 MB).</p>
                        </div>
                        <div class="form-actions">
                            <button class="button" type="submit" name="submit">Simpan kelas</button>
                            <a class="button button--quiet" href="<?= e(app_url('show.php')); ?>">Batal</a>
                        </div>
                    </form>
                </section>
            </div>
        </div>
    </main>
</body>
</html>
