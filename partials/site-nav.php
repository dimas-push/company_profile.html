<?php
$activePage = isset($activePage) ? $activePage : '';
$isAdmin = isset($isAdmin) ? $isAdmin : (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin');
$csrfToken = isset($csrfToken) ? $csrfToken : csrf_token();
?>
<header class="admin-topbar">
    <div class="container admin-topbar__inner">
        <a class="brand" href="<?= e(app_url('index.php')); ?>" aria-label="EDUSCOPE beranda">
            <span class="brand__mark" aria-hidden="true">E</span>
            <span>EDUSCOPE<small>Bimbel dan kursus</small></span>
        </a>
        <nav class="admin-nav" aria-label="Menu pengelola">
            <?php if ($isAdmin): ?>
                <a class="<?= $activePage === 'courses' ? 'is-active' : ''; ?>" href="<?= e(app_url('show.php')); ?>">Kelas</a>
                <a class="<?= $activePage === 'create' ? 'is-active' : ''; ?>" href="<?= e(app_url('create.php')); ?>">Tambah kelas</a>
                <a class="<?= $activePage === 'inquiries' ? 'is-active' : ''; ?>" href="<?= e(app_url('inquiries.php')); ?>">Pesan masuk</a>
            <?php endif; ?>
            <a class="<?= $activePage === 'profile' ? 'is-active' : ''; ?>" href="<?= e(app_url('profile.php')); ?>">Akun</a>
            <form action="<?= e(app_url('logout.php')); ?>" method="post">
                <input type="hidden" name="csrf_token" value="<?= e($csrfToken); ?>">
                <button class="nav-logout" type="submit">Keluar</button>
            </form>
        </nav>
    </div>
</header>
