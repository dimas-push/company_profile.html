<?php
require_once __DIR__ . '/config/helpers.php';
require_once __DIR__ . '/config/db.php';

$csrfToken = csrf_token();
$courses = array();
$programQueryError = false;

try {
    $courseResult = $db_connect->query(
        'SELECT id, name, description, target, duration, `schedule`, price, image FROM courses WHERE is_active = 1 ORDER BY created_at DESC, id DESC'
    );
    while ($course = $courseResult->fetch_assoc()) {
        $courses[] = $course;
    }
    $courseResult->free();
} catch (mysqli_sql_exception $exception) {
    error_log('Public course list query failed: ' . $exception->getMessage());
    $programQueryError = true;
}

$contactStatus = isset($_GET['contact']) && in_array($_GET['contact'], array('sent', 'error'), true)
    ? $_GET['contact']
    : '';

$featuredCourses = array(
    array(
        'name' => 'Bahasa Inggris',
        'category' => 'Bahasa',
        'mark' => 'EN',
        'style' => 'english',
        'description' => 'Kelas untuk peserta mulai usia pra-TK hingga umum. Kegiatan belajar memakai modul dan banyak latihan praktik.',
        'target' => 'Pra-TK hingga umum',
    ),
    array(
        'name' => 'Bimbel Matematika & IPA',
        'category' => 'Bimbingan sekolah',
        'mark' => 'M + IPA',
        'style' => 'school',
        'description' => 'Pendampingan materi Matematika dan IPA, termasuk konsultasi PR serta materi sekolah yang belum dipahami.',
        'target' => 'Siswa sekolah',
    ),
    array(
        'name' => 'Bahasa Jepang',
        'category' => 'Bahasa',
        'mark' => '日本語',
        'style' => 'japanese',
        'description' => 'Belajar dari Katakana hingga materi JLPT N4. Metode yang dicantumkan EDUSCOPE memakai 70% praktik dan 30% teori.',
        'target' => 'Pemula hingga JLPT N4',
    ),
    array(
        'name' => 'Calistung + Fun English',
        'category' => 'Kelas anak',
        'mark' => 'A B 1 2',
        'style' => 'calistung',
        'description' => 'Kelas membaca, menulis, dan berhitung untuk anak mulai pra-TK. Peserta Calistung juga mendapat pembelajaran Bahasa Inggris gratis.',
        'target' => 'Mulai pra-TK',
    ),
    array(
        'name' => 'Jarimatika',
        'category' => 'Berhitung',
        'mark' => '1 2 3',
        'style' => 'jarimatika',
        'description' => 'Latihan berhitung dengan jari untuk memahami penjumlahan, pengurangan, perkalian, dan pembagian.',
        'target' => 'Anak-anak',
    ),
    array(
        'name' => 'Persiapan TOEFL',
        'category' => 'Persiapan tes',
        'mark' => 'TOEFL',
        'style' => 'toefl',
        'description' => 'Persiapan untuk TOEFL iBT maupun PBT, misalnya untuk kebutuhan kerja, beasiswa, atau studi lanjut.',
        'target' => 'Calon peserta TOEFL',
    ),
    array(
        'name' => 'Corporate English Training',
        'category' => 'Untuk perusahaan',
        'mark' => 'ENG / CO',
        'style' => 'corporate',
        'description' => 'Pelatihan Bahasa Inggris untuk perusahaan. Tanyakan materi dan jadwal langsung kepada pengelola.',
        'target' => 'Perusahaan',
    ),
);

$showFeaturedCourses = $programQueryError || count($courses) === 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Cari bimbingan belajar atau kursus di EDUSCOPE. Lihat pilihan kelas dan tanyakan jadwal serta biayanya.">
    <title>EDUSCOPE | Bimbel dan Kursus</title>
    <link rel="stylesheet" href="<?= e(app_url('style.css')); ?>">
</head>
<body>
    <header class="site-header">
        <nav class="site-nav container" aria-label="Navigasi utama">
            <a class="brand" href="#home" aria-label="EDUSCOPE beranda">
                <span class="brand__mark" aria-hidden="true">E</span>
                <span>EDUSCOPE<small>Bimbel dan kursus</small></span>
            </a>
            <div class="site-nav__links">
                <a href="#tentang">Tentang</a>
                <a href="#program">Kelas</a>
                <a href="#kontak">Kontak</a>
                <a class="button button--small" href="<?= e(app_url('login.php')); ?>">Masuk</a>
            </div>
        </nav>
    </header>

    <main>
        <section class="hero" id="home">
            <div class="hero__inner container">
                <div>
                    <p class="eyebrow">Bimbingan belajar dan kursus</p>
                    <h1>Cari kelas yang cocok untuk Anda.</h1>
                    <p class="hero__copy">Butuh bantuan pelajaran sekolah atau ingin belajar keterampilan baru? Lihat kelas yang tersedia, lalu tanyakan jadwal dan biayanya kepada kami.</p>
                    <div class="hero__actions">
                        <a class="button" href="#program">Lihat kelas <span aria-hidden="true">↗</span></a>
                        <a class="button button--quiet" href="#kontak">Tanya dulu</a>
                    </div>
                    <div class="hero__note"><span class="hero__note-mark" aria-hidden="true">✓</span> Masih ragu memilih? Tanyakan dulu kepada kami.</div>
                </div>
                <div class="hero-art" aria-label="Pilihan bimbingan belajar dan kursus EDUSCOPE">
                    <div class="hero-art__sheet">
                        <div class="hero-art__label">Pilihan kelas EDUSCOPE</div>
                        <div class="hero-art__title">Pelajaran sekolah atau keterampilan baru.</div>
                        <div class="hero-art__footer"><span>Tanya jadwal dan biaya</span><span class="hero-art__spark" aria-hidden="true">✳</span></div>
                    </div>
                    <div class="floating-note"><span aria-hidden="true">↗</span> Tanyakan dulu sebelum memilih</div>
                </div>
            </div>
        </section>

        <section class="section about-section" id="tentang">
            <div class="container">
                <div class="about-intro">
                    <div class="about-intro__copy">
                        <p class="eyebrow">Kenalan dengan EDUSCOPE</p>
                        <h2>Belajar hal baru, selangkah demi selangkah.</h2>
                        <p>EDUSCOPE Learning Centre berdiri di Sukabumi pada 2014, awalnya dengan nama Bonita Institute. Kini, programnya mencakup bimbingan mata pelajaran dan berbagai kursus, dari Calistung hingga persiapan TOEFL.</p>
                        <a class="about-source" href="https://eduscopelc.my.canva.site/" target="_blank" rel="noopener noreferrer">Kenali EDUSCOPE lebih jauh <span aria-hidden="true">&nearr;</span></a>
                    </div>
                    <aside class="about-highlight" aria-label="Sekilas tentang EDUSCOPE">
                        <span class="about-highlight__spark" aria-hidden="true">&#10022;</span>
                        <p class="about-highlight__label">Berawal di Sukabumi</p>
                        <strong>Sejak 2014</strong>
                        <p class="about-highlight__copy">Ragam kelas untuk anak, pelajar, dan peserta dewasa.</p>
                        <div class="about-highlight__tags" aria-label="Jenis pembelajaran">
                            <span>Bimbel</span>
                            <span>Kursus</span>
                            <span>Berbagai usia</span>
                        </div>
                    </aside>
                </div>
                <div class="about-programs-intro">
                    <div>
                        <p class="eyebrow">Pilihan pembelajaran</p>
                        <h3>Ada banyak cara untuk berkembang.</h3>
                    </div>
                    <p>Pilih bidang yang sesuai dengan kebutuhan dan tujuan belajar.</p>
                </div>
                <div class="about-grid">
                    <article class="feature-card">
                        <span class="feature-card__mark" aria-hidden="true">Aa</span>
                        <h3>Bahasa Inggris &amp; Jepang</h3>
                        <p>Belajar Bahasa Inggris untuk berbagai usia, atau Bahasa Jepang dari Katakana hingga persiapan JLPT N4.</p>
                    </article>
                    <article class="feature-card">
                        <span class="feature-card__mark" aria-hidden="true">123</span>
                        <h3>Bimbel &amp; Calistung</h3>
                        <p>Pendampingan Matematika dan IPA serta kelas membaca, menulis, dan berhitung untuk anak mulai pra-TK.</p>
                    </article>
                    <article class="feature-card">
                        <span class="feature-card__mark" aria-hidden="true">Go!</span>
                        <h3>Program lainnya</h3>
                        <p>Pilih Jarimatika, persiapan TOEFL, atau pelatihan Bahasa Inggris untuk kebutuhan perusahaan.</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="section program-section" id="program">
            <div class="container">
                <div class="section-heading">
                    <p class="eyebrow">Kelas yang tersedia</p>
                    <h2>Lihat pilihan bimbel dan kursus.</h2>
                    <p>Rincian kelas di bawah dirangkum dari profil EDUSCOPE. Tanyakan jadwal dan biaya lewat formulir.</p>
                </div>
                <?php if ($showFeaturedCourses): ?>
                    <div class="flash flash--info program-notice" role="status">Menampilkan informasi kelas EDUSCOPE. Jadwal dan biaya belum dicantumkan; silakan tanyakan melalui formulir.</div>
                    <div class="program-grid">
                        <?php foreach ($featuredCourses as $course): ?>
                            <article class="program-card">
                                <div class="program-card__art program-card__art--<?= e($course['style']); ?>" aria-hidden="true">
                                    <span class="program-card__art-mark"><?= e($course['mark']); ?></span>
                                    <span class="program-card__art-label"><?= e($course['category']); ?></span>
                                </div>
                                <div class="program-card__body">
                                    <h3><?= e($course['name']); ?></h3>
                                    <p class="program-card__description"><?= e($course['description']); ?></p>
                                    <div class="program-card__meta"><span><b>Untuk</b> <?= e($course['target']); ?></span></div>
                                    <p class="program-card__price"><a href="#kontak">Tanyakan jadwal dan biaya <span aria-hidden="true">&rarr;</span></a></p>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="program-grid">
                        <?php foreach ($courses as $course): ?>
                            <article class="program-card">
                                <?php
                                $courseArtwork = array('style' => 'english', 'mark' => 'EDU', 'category' => 'Kelas');
                                foreach ($featuredCourses as $featuredCourse) {
                                    if ($featuredCourse['name'] === $course['name']) {
                                        $courseArtwork = $featuredCourse;
                                        break;
                                    }
                                }
                                ?>
                                <?php if (trim($course['image']) !== ''): ?>
                                    <img class="program-card__image" src="<?= safe_href($course['image']); ?>" alt="Foto kelas <?= e($course['name']); ?>" loading="lazy">
                                <?php else: ?>
                                    <div class="program-card__art program-card__art--<?= e($courseArtwork['style']); ?>" aria-hidden="true">
                                        <span class="program-card__art-mark"><?= e($courseArtwork['mark']); ?></span>
                                        <span class="program-card__art-label"><?= e($courseArtwork['category']); ?></span>
                                    </div>
                                <?php endif; ?>
                                <div class="program-card__body">
                                    <h3><?= e($course['name']); ?></h3>
                                    <p class="program-card__description"><?= nl2br(e($course['description'])); ?></p>
                                    <div class="program-card__meta">
                                        <?php if ($course['target'] !== ''): ?><span><b>Sasaran</b> <?= e($course['target']); ?></span><?php endif; ?>
                                        <?php if ($course['duration'] !== ''): ?><span><b>Durasi</b> <?= e($course['duration']); ?></span><?php endif; ?>
                                        <?php if ($course['schedule'] !== ''): ?><span><b>Jadwal</b> <?= e($course['schedule']); ?></span><?php endif; ?>
                                    </div>
                                    <p class="program-card__price">
                                        <?php if ((int) $course['price'] === 0): ?><a href="#kontak">Tanyakan jadwal dan biaya <span aria-hidden="true">&rarr;</span></a><?php else: ?><span class="program-card__amount">Rp <?= number_format($course['price'], 0, ',', '.'); ?></span><?php endif; ?>
                                    </p>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <section class="section contact-section" id="kontak">
            <div class="container contact-grid">
                <div class="contact-copy">
                    <span class="contact-copy__mark" aria-hidden="true">?</span>
                    <p class="eyebrow">Ada yang ingin ditanyakan?</p>
                    <h2>Kami bantu cari kelas yang pas.</h2>
                    <p>Tanyakan bimbel atau kursus yang sesuai dengan kebutuhan belajar Anda.</p>
                    <ul class="contact-topics" aria-label="Topik yang bisa ditanyakan">
                        <li><span aria-hidden="true">&#10003;</span> Pilihan kelas</li>
                        <li><span aria-hidden="true">&#10003;</span> Jadwal belajar</li>
                        <li><span aria-hidden="true">&#10003;</span> Durasi dan biaya</li>
                    </ul>
                    <div class="contact-note">
                        <strong>Belum tahu harus mulai dari mana?</strong>
                        <span>Ceritakan pelajaran atau keterampilan yang ingin Anda pelajari. Pesan akan masuk ke pengelola EDUSCOPE.</span>
                    </div>
                </div>
                <div class="form-card contact-form-card">
                    <div class="contact-form-card__head">
                        <p class="eyebrow">Hubungi EDUSCOPE</p>
                        <h3>Ceritakan yang Anda cari</h3>
                        <p>Isi formulir singkat ini dan tulis pertanyaan Anda.</p>
                    </div>
                    <?php if ($contactStatus === 'sent'): ?>
                        <div class="flash flash--success" role="status">Pesan Anda sudah masuk. Terima kasih sudah menghubungi EDUSCOPE.</div>
                    <?php elseif ($contactStatus === 'error'): ?>
                        <div class="flash flash--danger" role="alert">Pesan belum terkirim. Coba lagi beberapa saat lagi.</div>
                    <?php endif; ?>
                    <form class="contact-form" action="<?= e(app_url('backend/contact.php')); ?>" method="post">
                        <input type="hidden" name="csrf_token" value="<?= e($csrfToken); ?>">
                        <div class="field-grid contact-form__identity">
                            <div class="field">
                                <label for="contact-name">Nama Anda</label>
                                <input id="contact-name" type="text" name="name" autocomplete="name" maxlength="120" placeholder="Nama lengkap" required>
                            </div>
                            <div class="field">
                                <label for="contact-email">Email</label>
                                <input id="contact-email" type="email" name="email" autocomplete="email" maxlength="254" placeholder="nama@email.com" required>
                            </div>
                        </div>
                        <div class="field contact-form__message">
                            <label for="contact-message">Apa yang ingin ditanyakan?</label>
                            <textarea id="contact-message" name="message" rows="4" maxlength="5000" placeholder="Contoh: Saya ingin tahu jadwal kelas Bahasa Inggris..." required></textarea>
                        </div>
                        <div class="honeypot" aria-hidden="true">
                            <label for="contact-website">Jangan isi kolom ini</label>
                            <input id="contact-website" type="text" name="website" tabindex="-1" autocomplete="off">
                        </div>
                        <div class="contact-form__submit">
                            <button class="button" type="submit">Kirim pertanyaan <span aria-hidden="true">&rarr;</span></button>
                            <p class="form-text">Gunakan email aktif agar pengelola bisa menghubungi Anda.</p>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <footer class="site-footer">
        <div class="site-footer__top container">
            <div class="site-footer__brand">
                <a class="brand" href="#home" aria-label="EDUSCOPE beranda">
                    <span class="brand__mark" aria-hidden="true">E</span>
                    <span>EDUSCOPE<small>Bimbel dan kursus</small></span>
                </a>
                <p>Teman belajar untuk memahami pelajaran dan mencoba keterampilan baru.</p>
            </div>
            <nav class="site-footer__nav" aria-label="Navigasi footer">
                <p class="site-footer__label">Jelajahi</p>
                <a href="#tentang">Tentang EDUSCOPE</a>
                <a href="#program">Pilihan kelas</a>
                <a href="#kontak">Hubungi kami</a>
            </nav>
            <div class="site-footer__action">
                <span class="site-footer__badge" aria-hidden="true">&#10022;</span>
                <p class="site-footer__label">Perlu bantuan memilih?</p>
                <a class="site-footer__cta" href="#kontak">Tanyakan ke tim kami <span aria-hidden="true">&rarr;</span></a>
            </div>
        </div>
        <div class="site-footer__bottom container">
            <p>&copy; <?= date('Y'); ?> EDUSCOPE. Belajar dengan langkah yang terasa pas.</p>
            <a href="<?= e(app_url('login.php')); ?>">Akses pengelola <span aria-hidden="true">&nearr;</span></a>
        </div>
    </footer>
    <script>
        (() => {
            const navigation = performance.getEntriesByType('navigation')[0];
            if (!navigation || navigation.type !== 'reload') return;

            history.scrollRestoration = 'manual';
            history.replaceState(history.state, '', window.location.pathname + window.location.search);

            window.addEventListener('pageshow', () => {
                const root = document.documentElement;
                const previousScrollBehavior = root.style.scrollBehavior;
                root.style.scrollBehavior = 'auto';
                window.scrollTo(0, 0);
                requestAnimationFrame(() => {
                    root.style.scrollBehavior = previousScrollBehavior;
                    history.scrollRestoration = 'auto';
                });
            }, { once: true });
        })();
    </script>
</body>
</html>
