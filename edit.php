<?php
require_once __DIR__ . '/config/helpers.php';
require_admin();
require_once __DIR__ . '/config/db.php';
$csrfToken = csrf_token();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    redirect_to('show.php');
}

$statement = $db_connect->prepare(
    'SELECT id, name, description, target, duration, `schedule`, price, image FROM courses WHERE id = ? LIMIT 1'
);
$statement->bind_param('i', $id);
$statement->execute();
$statement->bind_result($courseId, $courseName, $courseDescription, $courseTarget, $courseDuration, $courseSchedule, $coursePrice, $courseImage);
$courseFound = $statement->fetch();
$row = $courseFound
    ? array(
        'id' => $courseId,
        'name' => $courseName,
        'description' => $courseDescription,
        'target' => $courseTarget,
        'duration' => $courseDuration,
        'schedule' => $courseSchedule,
        'price' => $coursePrice,
        'image' => $courseImage,
    )
    : null;
$statement->close();

if (!$row) {
    redirect_to('show.php');
}

$formError = '';
if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    !verify_csrf_token(isset($_POST['csrf_token']) ? $_POST['csrf_token'] : null)
) {
    $formError = 'Halaman ini sudah lama terbuka. Muat ulang sebelum menyimpan perubahan.';
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim(isset($_POST['name']) ? $_POST['name'] : '');
    $description = trim(isset($_POST['description']) ? $_POST['description'] : '');
    $target = trim(isset($_POST['target']) ? $_POST['target'] : '');
    $duration = trim(isset($_POST['duration']) ? $_POST['duration'] : '');
    $schedule = trim(isset($_POST['schedule']) ? $_POST['schedule'] : '');
    $price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_INT);

    if (
        $name === '' || strlen($name) > 255 || $description === '' || strlen($description) > 5000 ||
        strlen($target) > 160 || strlen($duration) > 120 || strlen($schedule) > 160 ||
        $price === false || $price === null || $price < 0
    ) {
        $formError = 'Periksa kembali nama, keterangan, dan biaya kelas.';
        $row['name'] = $name;
        $row['description'] = $description;
        $row['target'] = $target;
        $row['duration'] = $duration;
        $row['schedule'] = $schedule;
        $row['price'] = isset($_POST['price']) ? $_POST['price'] : $row['price'];
    } else {
        $uploadError = '';
        $newImagePath = store_image_upload(isset($_FILES['image']) ? $_FILES['image'] : null, $uploadError, false);

        if ($newImagePath === false) {
            $formError = $uploadError;
            $row['name'] = $name;
            $row['description'] = $description;
            $row['target'] = $target;
            $row['duration'] = $duration;
            $row['schedule'] = $schedule;
            $row['price'] = $price;
        } else {
            $image = $newImagePath === null ? $row['image'] : $newImagePath;
            try {
                $statement = $db_connect->prepare(
                    'UPDATE courses SET name = ?, description = ?, target = ?, duration = ?, `schedule` = ?, price = ?, image = ? WHERE id = ?'
                );
                $statement->bind_param('sssssisi', $name, $description, $target, $duration, $schedule, $price, $image, $id);
                $statement->execute();
                $statement->close();
                redirect_with_flash('show.php', 'catalog', 'Perubahan kelas sudah disimpan.', 'success');
            } catch (Throwable $exception) {
                if ($newImagePath !== null) {
                    $newUpload = dirname(__DIR__) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $newImagePath);
                    if (is_file($newUpload)) {
                        unlink($newUpload);
                    }
                }
                error_log('Course update failed: ' . $exception->getMessage());
                $formError = 'Perubahan belum tersimpan. Coba lagi.';
                $row['name'] = $name;
                $row['description'] = $description;
                $row['target'] = $target;
                $row['duration'] = $duration;
                $row['schedule'] = $schedule;
                $row['price'] = $price;
            }
            $row['image'] = $image;
        }
    }
}
$activePage = 'courses';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ubah Kelas | EDUSCOPE</title>
    <link rel="stylesheet" href="<?= e(app_url('style.css')); ?>">
</head>
<body>
    <?php include __DIR__ . '/partials/site-nav.php'; ?>
    <main class="admin-main">
        <div class="container">
            <header class="page-intro">
                <div>
                    <p class="eyebrow">Katalog kelas</p>
                <h1>Ubah kelas</h1>
                    <p>Perbarui keterangan kelas bimbel atau kursus.</p>
                </div>
                <a class="button button--quiet" href="<?= e(app_url('show.php')); ?>">← Kembali ke daftar</a>
            </header>
            <div class="form-layout">
                <section class="form-card">
                    <h2><?= e($row['name']); ?></h2>
                    <p class="form-intro">Ubah bagian yang perlu, lalu simpan perubahan.</p>
                    <?php if ($formError !== ''): ?>
                        <div class="flash flash--danger" role="alert"><?= e($formError); ?></div>
                    <?php endif; ?>
                    <form method="post" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?= e($csrfToken); ?>">
                        <div class="field">
                            <label for="name">Nama kelas</label>
                            <input type="text" id="name" name="name" value="<?= e($row['name']); ?>" maxlength="255" required>
                        </div>
                        <div class="field">
                            <label for="description">Keterangan kelas</label>
                            <textarea id="description" name="description" rows="5" maxlength="5000" required><?= e($row['description']); ?></textarea>
                        </div>
                        <div class="field-grid">
                            <div class="field">
                                <label for="target">Untuk peserta <span class="optional">(opsional)</span></label>
                                <input type="text" id="target" name="target" value="<?= e($row['target']); ?>" maxlength="160">
                            </div>
                            <div class="field">
                                <label for="duration">Lama belajar <span class="optional">(opsional)</span></label>
                                <input type="text" id="duration" name="duration" value="<?= e($row['duration']); ?>" maxlength="120">
                            </div>
                            <div class="field field--full">
                                <label for="schedule">Waktu belajar <span class="optional">(opsional)</span></label>
                                <input type="text" id="schedule" name="schedule" value="<?= e($row['schedule']); ?>" maxlength="160">
                            </div>
                            <div class="field field--full">
                                <label for="price">Biaya kelas (Rp)</label>
                                <input type="number" id="price" name="price" value="<?= e($row['price']); ?>" min="0" step="1" required>
                                <p class="field-hint">Isi 0 jika biayanya perlu ditanyakan terlebih dahulu.</p>
                            </div>
                        </div>
                        <div class="field">
                            <label for="image">Ganti foto <span class="optional">(opsional)</span></label>
                            <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/gif,image/webp">
                            <p class="field-hint">Pilih gambar JPG, PNG, GIF, atau WebP (maksimal 5 MB).</p>
                            <div class="current-image">
                                <?php if (trim($row['image']) !== ''): ?>
                                    <img src="<?= safe_href($row['image']); ?>" alt="Foto kelas saat ini">
                                    <span>Foto saat ini: <?= e(basename($row['image'])); ?></span>
                                <?php else: ?>
                                    <span>Belum ada foto untuk kelas ini.</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button class="button" type="submit" name="update">Simpan perubahan</button>
                            <a class="button button--quiet" href="<?= e(app_url('show.php')); ?>">Batal</a>
                        </div>
                    </form>
                </section>
            </div>
        </div>
    </main>
</body>
</html>
