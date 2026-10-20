CREATE DATABASE IF NOT EXISTS `pemweb-db`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
USE `pemweb-db`;

CREATE TABLE IF NOT EXISTS `users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` enum('admin', 'user') NOT NULL DEFAULT 'user',
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `courses` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `target` varchar(160) NOT NULL DEFAULT '',
  `duration` varchar(120) NOT NULL DEFAULT '',
  `schedule` varchar(160) NOT NULL DEFAULT '',
  `price` int unsigned NOT NULL DEFAULT 0,
  `image` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `courses_active_created` (`is_active`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `courses` (`name`, `description`, `target`, `duration`, `schedule`, `price`, `image`)
SELECT 'Bahasa Inggris', 'Kelas untuk peserta mulai usia pra-TK hingga umum. Kegiatan belajar memakai modul dan banyak latihan praktik.', 'Pra-TK hingga umum', '', '', 0, ''
WHERE NOT EXISTS (SELECT 1 FROM `courses` WHERE `name` = 'Bahasa Inggris');

INSERT INTO `courses` (`name`, `description`, `target`, `duration`, `schedule`, `price`, `image`)
SELECT 'Bimbel Matematika & IPA', 'Pendampingan materi Matematika dan IPA, termasuk konsultasi PR serta materi sekolah yang belum dipahami.', 'Siswa sekolah', '', '', 0, ''
WHERE NOT EXISTS (SELECT 1 FROM `courses` WHERE `name` = 'Bimbel Matematika & IPA');

INSERT INTO `courses` (`name`, `description`, `target`, `duration`, `schedule`, `price`, `image`)
SELECT 'Bahasa Jepang', 'Belajar dari Katakana hingga materi JLPT N4. Metode yang dicantumkan EDUSCOPE memakai 70% praktik dan 30% teori.', 'Pemula hingga JLPT N4', '', '', 0, ''
WHERE NOT EXISTS (SELECT 1 FROM `courses` WHERE `name` = 'Bahasa Jepang');

INSERT INTO `courses` (`name`, `description`, `target`, `duration`, `schedule`, `price`, `image`)
SELECT 'Calistung + Fun English', 'Kelas membaca, menulis, dan berhitung untuk anak mulai pra-TK. Peserta Calistung juga mendapat pembelajaran Bahasa Inggris gratis.', 'Anak mulai pra-TK', '', '', 0, ''
WHERE NOT EXISTS (SELECT 1 FROM `courses` WHERE `name` = 'Calistung + Fun English');

INSERT INTO `courses` (`name`, `description`, `target`, `duration`, `schedule`, `price`, `image`)
SELECT 'Jarimatika', 'Latihan berhitung dengan jari untuk memahami penjumlahan, pengurangan, perkalian, dan pembagian.', 'Anak-anak', '', '', 0, ''
WHERE NOT EXISTS (SELECT 1 FROM `courses` WHERE `name` = 'Jarimatika');

INSERT INTO `courses` (`name`, `description`, `target`, `duration`, `schedule`, `price`, `image`)
SELECT 'Persiapan TOEFL', 'Persiapan untuk TOEFL iBT maupun PBT, misalnya untuk kebutuhan kerja, beasiswa, atau studi lanjut.', 'Calon peserta TOEFL iBT/PBT', '', '', 0, ''
WHERE NOT EXISTS (SELECT 1 FROM `courses` WHERE `name` = 'Persiapan TOEFL');

INSERT INTO `courses` (`name`, `description`, `target`, `duration`, `schedule`, `price`, `image`)
SELECT 'Corporate English Training', 'Pelatihan Bahasa Inggris untuk perusahaan. Tanyakan materi dan jadwal langsung kepada pengelola.', 'Perusahaan', '', '', 0, ''
WHERE NOT EXISTS (SELECT 1 FROM `courses` WHERE `name` = 'Corporate English Training');

CREATE TABLE IF NOT EXISTS `inquiries` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(120) NOT NULL,
  `email` varchar(254) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `inquiries_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
