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

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Temukan bimbel dan kursus EDUSCOPE untuk anak, pelajar, dan peserta umum. Belajar lebih seru dengan program yang sesuai tujuanmu.">
    <meta name="theme-color" content="#5633bf">
    <title>EDUSCOPE | Belajar Ceria, Tumbuh Percaya Diri</title>
    <link rel="preload" as="image" href="<?= e(app_url('img/eduscope-hero.png')); ?>">
    <link rel="stylesheet" href="<?= e(app_url('style.css')); ?>">
    <link rel="stylesheet" href="<?= e(app_url('home.css')); ?>">
</head>
<body class="home-page">
    <a class="home-skip-link" href="#main-content">Lewati ke konten utama</a>

    <div class="home-topbar">
        <div class="home-container home-topbar__inner">
            <p><span aria-hidden="true">✦</span> Belajar hari ini, melangkah lebih percaya diri</p>
            <a href="#kontak"><span aria-hidden="true">✉</span> Yuk, temukan kelas yang pas</a>
        </div>
    </div>

    <header class="home-header">
        <nav class="home-container home-nav" aria-label="Navigasi utama">
            <a class="home-brand" href="#home" aria-label="EDUSCOPE Learning Centre, beranda">
                <span class="home-brand__word">EDU<span>SCOPE</span></span>
                <small>LEARNING CENTRE · SUKABUMI</small>
            </a>

            <button class="home-menu-toggle" type="button" aria-controls="home-nav-links" aria-expanded="false" aria-label="Buka menu">
                <span></span><span></span><span></span>
            </button>

            <div class="home-nav__links" id="home-nav-links">
                <a class="is-active" href="#home">Beranda</a>
                <a href="#program">Program</a>
                <a href="#tentang">Tentang</a>
                <a href="#kenapa">Keunggulan</a>
                <a href="#kontak">Kontak</a>
                <a class="home-nav__login" href="<?= e(app_url('login.php')); ?>">Masuk</a>
                <a class="home-button home-button--nav" href="#kontak">Konsultasi <span aria-hidden="true">→</span></a>
            </div>
        </nav>
    </header>

    <main id="main-content">
        <section class="home-hero" id="home">
            <div class="home-hero__decor home-hero__decor--cloud" aria-hidden="true">☁</div>
            <div class="home-hero__decor home-hero__decor--sun" aria-hidden="true">☼</div>
            <div class="home-hero__decor home-hero__decor--star" aria-hidden="true">✦</div>
            <div class="home-container home-hero__inner">
                <div class="home-hero__copy">
                    <p class="home-kicker"><span aria-hidden="true"></span> Bimbel dan kursus untuk semua langkah</p>
                    <h1>Belajar dengan ceria,<br><span>tumbuh penuh percaya diri.</span></h1>
                    <p class="home-hero__description">Dari pelajaran sekolah sampai keterampilan baru, temukan ruang belajar yang membuat setiap langkah terasa lebih menyenangkan.</p>
                    <div class="home-hero__actions">
                        <a class="home-button" href="#program">Jelajahi program <span class="home-button__arrow" aria-hidden="true">→</span></a>
                        <a class="home-text-link" href="#tentang">Kenali EDUSCOPE <span aria-hidden="true">↗</span></a>
                    </div>
                    <div class="home-hero__trust">
                        <div class="home-trust-item">
                            <span class="home-trust-item__icon home-trust-item__icon--pink" aria-hidden="true">♡</span>
                            <span><strong>Ramah dan suportif</strong><small>Nyaman untuk bertanya</small></span>
                        </div>
                        <div class="home-trust-item">
                            <span class="home-trust-item__icon home-trust-item__icon--green" aria-hidden="true">✎</span>
                            <span><strong>Banyak pilihan kelas</strong><small>Sesuai minat dan tujuan</small></span>
                        </div>
                        <div class="home-trust-item">
                            <span class="home-trust-item__icon home-trust-item__icon--blue" aria-hidden="true">✦</span>
                            <span><strong>Menemani sejak 2014</strong><small>Berawal di Sukabumi</small></span>
                        </div>
                    </div>
                </div>

                <div class="home-hero__visual">
                    <div class="home-hero__image-shape" aria-hidden="true"></div>
                    <img class="home-hero__image" src="<?= e(app_url('img/eduscope-hero.png')); ?>" alt="Ilustrasi suasana belajar dengan anak, pelajar remaja, dan tutor" width="1664" height="1024" fetchpriority="high" decoding="async">
                    <div class="home-hero__badge">
                        <span class="home-hero__badge-icon" aria-hidden="true">✦</span>
                        <span><strong>Ruang untuk berkembang</strong><small>Belajar sesuai langkahmu</small></span>
                    </div>
                    <span class="home-hero__doodle home-hero__doodle--one" aria-hidden="true">✳</span>
                    <span class="home-hero__doodle home-hero__doodle--two" aria-hidden="true">⌁</span>
                </div>
            </div>
            <div class="home-hero__wave" aria-hidden="true"></div>
        </section>

        <section class="home-section home-programs" id="program">
            <div class="home-container">
                <div class="home-section__heading">
                    <p class="home-kicker home-kicker--center"><span aria-hidden="true"></span> Kelas EDUSCOPE <span aria-hidden="true"></span></p>
                    <h2>Program untuk setiap rasa ingin tahu</h2>
                    <p>Lihat pilihan bimbel dan kursus. Jadwal dan biaya bisa ditanyakan langsung kepada tim kami.</p>
                </div>

                <?php if ($programQueryError): ?>
                    <div class="home-program-note" role="alert">Daftar kelas belum bisa dimuat. Silakan coba lagi nanti atau hubungi tim EDUSCOPE.</div>
                <?php elseif (count($courses) === 0): ?>
                    <div class="home-program-note" role="status">Belum ada kelas yang dipublikasikan. Hubungi tim EDUSCOPE untuk menanyakan program belajar.</div>
                <?php else: ?>
                    <div class="home-program-grid">
                        <?php foreach ($courses as $course): ?>
                            <?php
                            $courseArtwork = array('style' => 'school', 'mark' => 'EDU', 'category' => 'Program EDUSCOPE');
                            foreach ($featuredCourses as $featuredCourse) {
                                if ($featuredCourse['name'] === $course['name']) {
                                    $courseArtwork = $featuredCourse;
                                    break;
                                }
                            }
                            ?>
                            <article class="home-course-card home-course-card--<?= e($courseArtwork['style']); ?>">
                                <?php if (trim($course['image']) !== ''): ?>
                                    <img class="home-course-card__photo" src="<?= safe_href($course['image']); ?>" alt="Foto kelas <?= e($course['name']); ?>" loading="lazy" decoding="async">
                                <?php else: ?>
                                    <div class="home-course-card__visual" aria-hidden="true">
                                        <span class="home-course-card__mark"><?= e($courseArtwork['mark']); ?></span>
                                        <span class="home-course-card__category"><?= e($courseArtwork['category']); ?></span>
                                        <span class="home-course-card__spark">✦</span>
                                    </div>
                                <?php endif; ?>
                                <div class="home-course-card__body">
                                    <h3><?= e($course['name']); ?></h3>
                                    <p><?= nl2br(e($course['description'])); ?></p>
                                    <div class="home-course-card__meta">
                                        <?php if ($course['target'] !== ''): ?><span><b>Sasaran</b> <?= e($course['target']); ?></span><?php endif; ?>
                                        <?php if ($course['duration'] !== ''): ?><span><b>Durasi</b> <?= e($course['duration']); ?></span><?php endif; ?>
                                        <?php if ($course['schedule'] !== ''): ?><span><b>Jadwal</b> <?= e($course['schedule']); ?></span><?php endif; ?>
                                    </div>
                                    <div class="home-course-card__price">
                                        <?php if ((int) $course['price'] === 0): ?>
                                            <a class="home-course-card__link" href="#kontak">Tanya jadwal &amp; biaya <span aria-hidden="true">→</span></a>
                                        <?php else: ?>
                                            <span>Rp <?= number_format($course['price'], 0, ',', '.'); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <section class="home-about" id="tentang">
            <div class="home-container home-about__grid">
                <div class="home-about__visual">
                    <div class="home-about__photo-wrap">
                        <img src="<?= e(app_url('img/eduscope-classroom.png')); ?>" alt="Ilustrasi kelas kecil dengan tutor mendampingi anak dan remaja belajar" width="1664" height="1024" loading="lazy" decoding="async">
                    </div>
                    <div class="home-about__year">
                        <span>2014</span>
                        <small>Mulai menemani<br>perjalanan belajar</small>
                    </div>
                    <span class="home-about__sticker" aria-hidden="true">Belajar<br>dengan hati</span>
                    <span class="home-about__doodle home-about__doodle--star" aria-hidden="true">✦</span>
                    <span class="home-about__doodle home-about__doodle--flower" aria-hidden="true">✿</span>
                </div>
                <div class="home-about__copy">
                    <p class="home-kicker">Tentang EDUSCOPE</p>
                    <h2>Mengubah rasa ingin tahu menjadi langkah yang berarti.</h2>
                    <p>EDUSCOPE Learning Centre berdiri di Sukabumi pada 2014, berawal dengan nama Bonita Institute. Kini, pilihan belajarnya mencakup bimbingan mata pelajaran dan beragam kursus.</p>
                    <p>Dari Calistung untuk anak, pelajaran sekolah, bahasa, sampai persiapan TOEFL, setiap peserta bisa mulai dari tujuan belajarnya sendiri.</p>
                    <div class="home-about__tags" aria-label="Bidang pembelajaran">
                        <span>Bimbingan belajar</span>
                        <span>Kursus bahasa</span>
                        <span>Berbagai usia</span>
                    </div>
                    <a class="home-text-link home-about__link" href="https://eduscopelc.my.canva.site/" target="_blank" rel="noopener noreferrer">Kenali EDUSCOPE lebih jauh <span aria-hidden="true">↗</span></a>
                </div>
            </div>
        </section>

        <section class="home-why" id="kenapa">
            <div class="home-container">
                <div class="home-section__heading">
                    <p class="home-kicker home-kicker--center"><span aria-hidden="true"></span> Kenapa EDUSCOPE <span aria-hidden="true"></span></p>
                    <h2>Satu tempat, banyak langkah belajar</h2>
                    <p>Temukan program yang mendukung kebutuhan belajar di setiap tahap.</p>
                </div>
                <div class="home-why__grid">
                    <article class="home-why-card home-why-card--violet">
                        <span class="home-why-card__number">01</span>
                        <span class="home-why-card__icon" aria-hidden="true">Aa</span>
                        <h3>Pilihan belajar beragam</h3>
                        <p>Bimbel sekolah, bahasa, Calistung, Jarimatika, TOEFL, sampai pelatihan untuk perusahaan.</p>
                    </article>
                    <article class="home-why-card home-why-card--green">
                        <span class="home-why-card__number">02</span>
                        <span class="home-why-card__icon" aria-hidden="true">↗</span>
                        <h3>Untuk banyak usia</h3>
                        <p>Program tersedia untuk anak pra-TK, siswa sekolah, peserta umum, hingga kebutuhan perusahaan.</p>
                    </article>
                    <article class="home-why-card home-why-card--yellow">
                        <span class="home-why-card__number">03</span>
                        <span class="home-why-card__icon" aria-hidden="true">✦</span>
                        <h3>Tujuan belajar yang nyata</h3>
                        <p>Perkuat pelajaran, mulai keterampilan bahasa, atau siapkan diri untuk kebutuhan studi dan kerja.</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="home-contact" id="kontak">
            <div class="home-container home-contact__grid">
                <div class="home-contact__copy">
                    <p class="home-kicker">Ada yang ingin ditanyakan?</p>
                    <h2>Yuk, cari kelas yang terasa pas.</h2>
                    <p>Ceritakan pelajaran atau keterampilan yang ingin dipelajari. Tim EDUSCOPE akan menerima pesanmu.</p>
                    <ul class="home-contact__topics">
                        <li><span aria-hidden="true">✓</span> Pilihan kelas</li>
                        <li><span aria-hidden="true">✓</span> Jadwal belajar</li>
                        <li><span aria-hidden="true">✓</span> Durasi dan biaya</li>
                    </ul>
                    <div class="home-contact__note">
                        <span aria-hidden="true">✦</span>
                        <p><strong>Belum yakin mulai dari mana?</strong><br>Ceritakan kebutuhanmu, kami siap membantu mencari informasinya.</p>
                    </div>
                </div>

                <div class="home-contact__card">
                    <div class="home-contact__card-head">
                        <span class="home-contact__card-icon" aria-hidden="true">✉</span>
                        <div>
                            <p class="home-kicker">Hubungi EDUSCOPE</p>
                            <h3>Kirim pertanyaan</h3>
                        </div>
                    </div>
                    <?php if ($contactStatus === 'sent'): ?>
                        <div class="flash flash--success" role="status">Pesanmu sudah masuk. Terima kasih sudah menghubungi EDUSCOPE.</div>
                    <?php elseif ($contactStatus === 'error'): ?>
                        <div class="flash flash--danger" role="alert">Pesan belum terkirim. Coba lagi beberapa saat lagi.</div>
                    <?php endif; ?>
                    <form class="home-contact__form" action="<?= e(app_url('backend/contact.php')); ?>" method="post">
                        <input type="hidden" name="csrf_token" value="<?= e($csrfToken); ?>">
                        <div class="home-contact__row">
                            <div class="home-field">
                                <label for="contact-name">Nama</label>
                                <input id="contact-name" type="text" name="name" autocomplete="name" maxlength="120" placeholder="Nama lengkap" required>
                            </div>
                            <div class="home-field">
                                <label for="contact-email">Email</label>
                                <input id="contact-email" type="email" name="email" autocomplete="email" maxlength="254" placeholder="nama@email.com" required>
                            </div>
                        </div>
                        <div class="home-field">
                            <label for="contact-message">Apa yang ingin ditanyakan?</label>
                            <textarea id="contact-message" name="message" rows="4" maxlength="5000" placeholder="Contoh: Saya ingin tahu jadwal kelas Bahasa Inggris..." required></textarea>
                        </div>
                        <div class="honeypot" aria-hidden="true">
                            <label for="contact-website">Jangan isi kolom ini</label>
                            <input id="contact-website" type="text" name="website" tabindex="-1" autocomplete="off">
                        </div>
                        <div class="home-contact__submit">
                            <button class="home-button" type="submit">Kirim pertanyaan <span aria-hidden="true">→</span></button>
                            <small>Gunakan email aktif agar pengelola dapat menghubungimu.</small>
                        </div>
                        <p class="home-contact__privacy">Nama, email, dan pesan digunakan untuk menanggapi pertanyaanmu. Hindari mengirim data pribadi yang tidak diperlukan.</p>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <footer class="home-footer">
        <div class="home-footer__banner">
            <div class="home-container home-footer__banner-inner">
                <div>
                    <p class="home-footer__eyebrow">Mulai langkah pertamamu</p>
                    <h2>Belajar lebih seru dimulai dari sini.</h2>
                    <p>Tanyakan program yang paling sesuai dengan tujuanmu.</p>
                </div>
                <a class="home-button home-button--sun" href="#kontak">Konsultasi program <span class="home-button__arrow" aria-hidden="true">→</span></a>
                <span class="home-footer__doodle" aria-hidden="true">✧</span>
            </div>
        </div>
        <div class="home-container home-footer__main">
            <div class="home-footer__brand">
                <a class="home-brand home-brand--footer" href="#home">
                    <span class="home-brand__word">EDU<span>SCOPE</span></span>
                    <small>LEARNING CENTRE · SUKABUMI</small>
                </a>
                <p>Teman belajar untuk memahami pelajaran dan mencoba keterampilan baru.</p>
            </div>
            <nav class="home-footer__links" aria-label="Navigasi footer">
                <strong>Jelajahi</strong>
                <a href="#program">Pilihan program</a>
                <a href="#tentang">Tentang EDUSCOPE</a>
                <a href="#kenapa">Kenapa EDUSCOPE</a>
            </nav>
            <nav class="home-footer__links" aria-label="Akun dan bantuan">
                <strong>Butuh bantuan?</strong>
                <a href="#kontak">Kirim pertanyaan</a>
                <a href="<?= e(app_url('login.php')); ?>">Masuk ke akun</a>
            </nav>
        </div>
        <div class="home-container home-footer__bottom">
            <p>&copy; <?= date('Y'); ?> EDUSCOPE Learning Centre. Belajar dengan langkah yang terasa pas.</p>
            <a href="#home">Kembali ke atas ↑</a>
        </div>
    </footer>

    <script>
        (() => {
            const menuButton = document.querySelector('.home-menu-toggle');
            const menuLinks = document.querySelector('#home-nav-links');

            if (menuButton && menuLinks) {
                const closeMenu = () => {
                    menuButton.setAttribute('aria-expanded', 'false');
                    menuButton.setAttribute('aria-label', 'Buka menu');
                    menuLinks.classList.remove('is-open');
                };

                menuButton.addEventListener('click', () => {
                    const isOpen = menuButton.getAttribute('aria-expanded') === 'true';
                    menuButton.setAttribute('aria-expanded', String(!isOpen));
                    menuButton.setAttribute('aria-label', isOpen ? 'Buka menu' : 'Tutup menu');
                    menuLinks.classList.toggle('is-open', !isOpen);
                });

                menuLinks.querySelectorAll('a').forEach((link) => {
                    link.addEventListener('click', closeMenu);
                });

                document.addEventListener('keydown', (event) => {
                    if (event.key === 'Escape') closeMenu();
                });
            }
        })();

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
