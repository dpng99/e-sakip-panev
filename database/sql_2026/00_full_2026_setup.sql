-- =============================================================================
-- PROSAKIP / PANEV KEJAKSAAN - FULL SETUP DATABASE TAHUN 2026
-- (MIGRATIONS DDL + SEEDERS DML)
-- Tanggal Dibuat: 2026-10-16
-- =============================================================================

-- =============================================================================
-- PROSAKIP / PANEV KEJAKSAAN - DATABASE MIGRATIONS TAHUN 2026
-- Tanggal Dibuat: 2026-10-16
-- Dialek: MySQL 8.x / MariaDB
-- =============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- -----------------------------------------------------------------------------
-- 1. TABEL: lke_satker_bukti
-- Asal Migrasi: 2026_09_08_110000_create_lke_satker_tables.php
-- Keterangan  : Tabel bukti dukung evaluasi LKE per satker
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `lke_satker_bukti` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_satker` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `komponen_id` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sub_komponen_id` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kriteria_id` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_bukti` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `buktidukung_id` int DEFAULT NULL,
  `link_bukti_dukung` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tgl_pengisian` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tahun` smallint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lke_satker_bukti_id_satker_index` (`id_satker`),
  KEY `lke_satker_bukti_komponen_id_index` (`komponen_id`),
  KEY `lke_satker_bukti_sub_komponen_id_index` (`sub_komponen_id`),
  KEY `lke_satker_bukti_kriteria_id_index` (`kriteria_id`),
  KEY `lke_satker_bukti_buktidukung_id_index` (`buktidukung_id`),
  KEY `lke_satker_bukti_tahun_index` (`tahun`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 2. TABEL: lke_penilaian_satker
-- Asal Migrasi: 2026_09_08_110000_create_lke_satker_tables.php
-- Update      : 2026_10_07_140000_add_pm_columns_to_lke_penilaian_satker_table.php
-- Keterangan  : Tabel evaluasi penilaian mandiri & evaluator LKE Satker
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `lke_penilaian_satker` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_satker` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tahun` smallint unsigned NOT NULL,
  `komponen_id` bigint unsigned DEFAULT NULL,
  `subkomponen_id` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kriteria_id` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pm_parameter_id` bigint unsigned DEFAULT NULL,
  `pm_nilai` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pm_skor` double DEFAULT NULL,
  `pm_catatan` text COLLATE utf8mb4_unicode_ci,
  `pm_evaluator_id` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parameter_id` bigint unsigned DEFAULT NULL,
  `nilai` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `skor` double NOT NULL DEFAULT '0',
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `evaluator_id` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `satker_tahun_kriteria_unique` (`id_satker`,`tahun`,`kriteria_id`),
  KEY `lke_penilaian_satker_id_satker_index` (`id_satker`),
  KEY `lke_penilaian_satker_tahun_index` (`tahun`),
  KEY `lke_penilaian_satker_komponen_id_index` (`komponen_id`),
  KEY `lke_penilaian_satker_subkomponen_id_index` (`subkomponen_id`),
  KEY `lke_penilaian_satker_kriteria_id_index` (`kriteria_id`),
  KEY `lke_penilaian_satker_parameter_id_index` (`parameter_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 3. TABEL: dokumen_sakips
-- Asal Migrasi: 2026_09_10_102703_create_dokumen_sakips_table.php
-- Keterangan  : Repositori dokumen pedoman & template SAKIP
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `dokumen_sakips` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kategori` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'pedoman, template_2026, arsip_2025',
  `judul` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `urutan` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 4. TABEL: sasaran_program
-- Asal Migrasi: 2026_09_29_100001_create_sasaran_program_table.php
-- Keterangan  : Master Sasaran Program (SP) Cascading Bidang Kejaksaan
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `sasaran_program` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode_sp` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Misal: SP 1, SP 3, SP 13',
  `nama_sp` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nama Sasaran Program',
  `id_satker` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'FK ke satker pengampu utama',
  `is_crosscutting` tinyint NOT NULL DEFAULT '0' COMMENT '1 jika SP dimiliki >1 bidang',
  `tahun` varchar(4) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_sp` (`kode_sp`,`id_satker`,`tahun`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 5. TABEL: indikator_kinerja_program
-- Asal Migrasi: 2026_09_29_100002_create_indikator_kinerja_program_table.php
-- Keterangan  : Master Indikator Kinerja Program (IKP)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `indikator_kinerja_program` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sasaran_program_id` bigint unsigned NOT NULL,
  `kode_ikp` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Misal: IKP 13.1, IKP 3.2',
  `nama_ikp` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nama Indikator Kinerja Program',
  `id_satker` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'FK ke satker pengampu',
  `sifat_node` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'AVERAGE' COMMENT 'AVERAGE, RATIO_PERCENTAGE, INDEX_SCORE, DIRECT_INPUT',
  `tahun` varchar(4) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_ikp` (`kode_ikp`,`id_satker`,`tahun`),
  KEY `indikator_kinerja_program_sasaran_program_id_foreign` (`sasaran_program_id`),
  CONSTRAINT `indikator_kinerja_program_sasaran_program_id_foreign` FOREIGN KEY (`sasaran_program_id`) REFERENCES `sasaran_program` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 6. TABEL: target_ikp
-- Asal Migrasi: 2026_09_29_100003_create_target_ikp_table.php
-- Keterangan  : Target Tahunan dan Triwulanan IKP
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `target_ikp` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ikp_id` bigint unsigned DEFAULT NULL,
  `sp_id` bigint unsigned DEFAULT NULL,
  `id_satker` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tahun` varchar(4) COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_tahun` decimal(10,2) DEFAULT NULL COMMENT 'Target tahunan (%/skor)',
  `target_tw1` decimal(10,2) DEFAULT NULL,
  `target_tw2` decimal(10,2) DEFAULT NULL,
  `target_tw3` decimal(10,2) DEFAULT NULL,
  `target_tw4` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_target_ikp` (`ikp_id`,`id_satker`,`tahun`),
  KEY `idx_target_sp` (`sp_id`,`id_satker`,`tahun`),
  CONSTRAINT `target_ikp_ikp_id_foreign` FOREIGN KEY (`ikp_id`) REFERENCES `indikator_kinerja_program` (`id`) ON DELETE CASCADE,
  CONSTRAINT `target_ikp_sp_id_foreign` FOREIGN KEY (`sp_id`) REFERENCES `sasaran_program` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 7. TABEL: pelaporan_ikp
-- Asal Migrasi: 2026_09_29_100004_create_pelaporan_ikp_table.php
-- Update      : 2026_09_30_100001_add_realisasi_to_pelaporan_ikp_table.php
-- Keterangan  : Realisasi dan Pelaporan Capaian IKP per Triwulan
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `pelaporan_ikp` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ikp_id` bigint unsigned NOT NULL,
  `id_satker` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Satker bidang pengampu',
  `tahun` varchar(4) COLLATE utf8mb4_unicode_ci NOT NULL,
  `triwulan` tinyint NOT NULL COMMENT '1=TW1, 2=TW2, 3=TW3, 4=TW4',
  `realisasi` decimal(20,2) DEFAULT NULL COMMENT 'Nilai realisasi langsung: % atau skor indeks',
  `pembilang` decimal(20,2) DEFAULT NULL COMMENT 'Opsional: angka pembilang untuk audit trail',
  `penyebut` decimal(20,2) DEFAULT NULL COMMENT 'Opsional: angka penyebut untuk audit trail',
  `faktor` text COLLATE utf8mb4_unicode_ci COMMENT 'Faktor-faktor yang mempengaruhi capaian kinerja',
  `langkah_optimalisasi` text COLLATE utf8mb4_unicode_ci COMMENT 'Upaya optimalisasi kinerja',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_pelaporan_ikp` (`ikp_id`,`id_satker`,`tahun`,`triwulan`),
  CONSTRAINT `pelaporan_ikp_ikp_id_foreign` FOREIGN KEY (`ikp_id`) REFERENCES `indikator_kinerja_program` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 8. TABEL: lke_subkomponen_koefisien
-- Asal Migrasi: 2026_10_07_170000_create_lke_subkomponen_koefisien_table.php
-- Keterangan  : Penilaian koefisien 12 subkomponen (Evaluasi & Penilaian Mandiri)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `lke_subkomponen_koefisien` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_satker` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tahun` int NOT NULL,
  `subkomponen_kode` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_koefisien` int NOT NULL DEFAULT '90',
  `uraian` text COLLATE utf8mb4_unicode_ci,
  `bukti_dukung` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `link_bukti_dukung` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nilai` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `koefisien` int NOT NULL DEFAULT '80',
  `evaluator_id` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pm_target_koefisien` int NOT NULL DEFAULT '90',
  `pm_uraian` text COLLATE utf8mb4_unicode_ci,
  `pm_bukti_dukung` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pm_link_bukti_dukung` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pm_nilai` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pm_koefisien` int NOT NULL DEFAULT '80',
  `pm_evaluator_id` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `satker_tahun_subkomp_unique` (`id_satker`,`tahun`,`subkomponen_kode`),
  KEY `lke_subkomponen_koefisien_id_satker_index` (`id_satker`),
  KEY `lke_subkomponen_koefisien_tahun_index` (`tahun`),
  KEY `lke_subkomponen_koefisien_subkomponen_kode_index` (`subkomponen_kode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 9. TABEL: lke_parameter
-- Pendukung   : LkeParameterSeeder.php (Oktober 2026)
-- Keterangan  : Master parameter biner penilaian kriteria LKE (Terpenuhi / Tidak Terpenuhi)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `lke_parameter` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kriteria_id` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nilai` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `skor` double(8,2) NOT NULL DEFAULT '0.00',
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `tahun` smallint unsigned NOT NULL DEFAULT '2025',
  PRIMARY KEY (`id`),
  KEY `kriteria_id` (`kriteria_id`),
  KEY `lke_parameter_tahun_idx` (`tahun`),
  KEY `lke_parameter_tahun_kriteria_idx` (`tahun`,`kriteria_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 10. ALTERASI TABEL EKSISTING (Penambahan Kolom Tahun 2026)
-- Asal Migrasi:
--   - 2026_10_07_170000_create_lke_subkomponen_koefisien_table.php (bobot)
--   - 2026_10_14_180000_add_langkah_kerja_to_lke_kriteria_table.php (langkah_kerja)
-- -----------------------------------------------------------------------------
-- Tambah kolom 'bobot' pada lke_komponen jika belum ada
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_komponen' AND COLUMN_NAME = 'bobot';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_komponen` ADD COLUMN `bobot` decimal(5,2) NOT NULL DEFAULT 0.00 AFTER `nama`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Tambah kolom 'bobot' pada lke_subkomponen jika belum ada
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_subkomponen' AND COLUMN_NAME = 'bobot';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_subkomponen` ADD COLUMN `bobot` decimal(5,2) NOT NULL DEFAULT 0.00 AFTER `nama`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Tambah kolom 'langkah_kerja' pada lke_kriteria jika belum ada
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_kriteria' AND COLUMN_NAME = 'langkah_kerja';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_kriteria` ADD COLUMN `langkah_kerja` text COLLATE utf8mb4_unicode_ci NULL AFTER `dokumen_bukti`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Tambah kolom 'tahun' pada lke_parameter jika tabel eksisting belum memilikinya
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_parameter' AND COLUMN_NAME = 'tahun';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_parameter` ADD COLUMN `tahun` smallint unsigned NOT NULL DEFAULT 2025 AFTER `updated_at`, ADD INDEX `lke_parameter_tahun_idx` (`tahun`), ADD INDEX `lke_parameter_tahun_kriteria_idx` (`tahun`, `kriteria_id`)', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- -----------------------------------------------------------------------------
-- 11. TABEL: lke_buktidukung & lke_gabungan
-- -----------------------------------------------------------------------------
-- 1.1 Pastikan tabel lke_buktidukung ada
CREATE TABLE IF NOT EXISTS `lke_buktidukung` (
  `id` int NOT NULL AUTO_INCREMENT,
  `dokumen` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `format_nama_file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `ada_di_sistem` tinyint(1) NOT NULL DEFAULT '0',
  `tabel_sumber` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tahun` smallint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lke_buktidukung_tabel_sumber_index` (`tabel_sumber`),
  KEY `lke_buktidukung_tahun_index` (`tahun`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pastikan kolom 'dokumen' ada pada lke_buktidukung
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_buktidukung' AND COLUMN_NAME = 'dokumen';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_buktidukung` ADD COLUMN `dokumen` text COLLATE utf8mb4_unicode_ci NOT NULL AFTER `id`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Pastikan kolom 'format_nama_file' ada pada lke_buktidukung
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_buktidukung' AND COLUMN_NAME = 'format_nama_file';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_buktidukung` ADD COLUMN `format_nama_file` varchar(255) COLLATE utf8mb4_unicode_ci NULL AFTER `dokumen`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Pastikan kolom 'keterangan' ada pada lke_buktidukung dan mengizinkan NULL
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_buktidukung' AND COLUMN_NAME = 'keterangan';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_buktidukung` ADD COLUMN `keterangan` text COLLATE utf8mb4_unicode_ci NULL AFTER `format_nama_file`', 'ALTER TABLE `lke_buktidukung` MODIFY COLUMN `keterangan` text COLLATE utf8mb4_unicode_ci NULL');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Pastikan kolom 'ada_di_sistem' ada pada lke_buktidukung
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_buktidukung' AND COLUMN_NAME = 'ada_di_sistem';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_buktidukung` ADD COLUMN `ada_di_sistem` tinyint(1) NOT NULL DEFAULT 0 AFTER `keterangan`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Pastikan kolom 'tabel_sumber' ada pada lke_buktidukung (MEMPERBAIKI ERROR 1054 PADA SERVER TESTING)
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_buktidukung' AND COLUMN_NAME = 'tabel_sumber';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_buktidukung` ADD COLUMN `tabel_sumber` varchar(100) COLLATE utf8mb4_unicode_ci NULL AFTER `ada_di_sistem`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Pastikan kolom 'tahun' ada pada lke_buktidukung
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_buktidukung' AND COLUMN_NAME = 'tahun';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_buktidukung` ADD COLUMN `tahun` smallint unsigned NULL AFTER `tabel_sumber`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 1.2 Pastikan tabel lke_gabungan ada
CREATE TABLE IF NOT EXISTS `lke_gabungan` (
  `id` int NOT NULL AUTO_INCREMENT,
  `komponen_id` bigint unsigned DEFAULT NULL,
  `sub_komponen_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kriteria_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `buktidukung_id` int NOT NULL DEFAULT '0',
  `tahun` smallint unsigned NOT NULL DEFAULT '2026',
  PRIMARY KEY (`id`),
  KEY `lke_gabungan_komponen_id_index` (`komponen_id`),
  KEY `lke_gabungan_sub_komponen_id_index` (`sub_komponen_id`),
  KEY `lke_gabungan_kriteria_id_index` (`kriteria_id`),
  KEY `lke_gabungan_buktidukung_id_index` (`buktidukung_id`),
  KEY `lke_gabungan_tahun_index` (`tahun`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pastikan seluruh kolom pada lke_gabungan tersedia (MEMPERBAIKI ERROR 1054 Unknown column 'tahun')
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_gabungan' AND COLUMN_NAME = 'komponen_id';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_gabungan` ADD COLUMN `komponen_id` bigint unsigned NULL AFTER `id`, ADD INDEX `lke_gabungan_komponen_id_index` (`komponen_id`)', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_gabungan' AND COLUMN_NAME = 'sub_komponen_id';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_gabungan` ADD COLUMN `sub_komponen_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL AFTER `komponen_id`, ADD INDEX `lke_gabungan_sub_komponen_id_index` (`sub_komponen_id`)', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_gabungan' AND COLUMN_NAME = 'kriteria_id';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_gabungan` ADD COLUMN `kriteria_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL AFTER `sub_komponen_id`, ADD INDEX `lke_gabungan_kriteria_id_index` (`kriteria_id`)', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_gabungan' AND COLUMN_NAME = 'buktidukung_id';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_gabungan` ADD COLUMN `buktidukung_id` int NOT NULL DEFAULT 0 AFTER `kriteria_id`, ADD INDEX `lke_gabungan_buktidukung_id_index` (`buktidukung_id`)', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_gabungan' AND COLUMN_NAME = 'tahun';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_gabungan` ADD COLUMN `tahun` smallint unsigned NOT NULL DEFAULT 2025 AFTER `buktidukung_id`, ADD INDEX `lke_gabungan_tahun_index` (`tahun`)', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 1.3 Pastikan kolom penunjang pada lke_kriteria ada
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_kriteria' AND COLUMN_NAME = 'dokumen_bukti';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_kriteria` ADD COLUMN `dokumen_bukti` text COLLATE utf8mb4_unicode_ci NULL AFTER `nama`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_kriteria' AND COLUMN_NAME = 'langkah_kerja';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_kriteria` ADD COLUMN `langkah_kerja` text COLLATE utf8mb4_unicode_ci NULL AFTER `dokumen_bukti`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_kriteria' AND COLUMN_NAME = 'kode_bukti';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_kriteria` ADD COLUMN `kode_bukti` char(50) COLLATE utf8mb4_unicode_ci NULL AFTER `langkah_kerja`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_kriteria' AND COLUMN_NAME = 'tahun';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_kriteria` ADD COLUMN `tahun` smallint unsigned NOT NULL DEFAULT 2025 AFTER `updated_at`, ADD INDEX `lke_kriteria_tahun_idx` (`tahun`)', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET FOREIGN_KEY_CHECKS = 1;

-- =============================================================================
-- SELESAI: MIGRATIONS TAHUN 2026
-- =============================================================================

-- =============================================================================
-- PROSAKIP / PANEV KEJAKSAAN - DATABASE SEEDERS DATA TAHUN 2026 (SEMUA DATA)
-- Tanggal Dibuat: 2026-10-16
-- Dialek: MySQL 8.x / MariaDB
-- Mencakup:
--   1. Update Nilai Bobot Komponen & Subkomponen SAKIP (Migrasi 2026_10_07)
--   2. Dokumen SAKIP (Pedoman, Template 2026, Arsip Template)
--   3. Sasaran Program (SP Cascading)
--   4. Indikator Kinerja Program (IKP)
--   5. Target IKP (Target Tahunan & Triwulan)
--   6. LKE Parameter (Parameter Kriteria Nilai Terpenuhi / Tidak Terpenuhi)
-- =============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- -----------------------------------------------------------------------------
-- 1. UPDATE BOBOT STANDAR SAKIP (KOMPONEN & SUBKOMPONEN)
-- -----------------------------------------------------------------------------
UPDATE `lke_komponen` SET `bobot` = 30.00 WHERE `no` = 1;
UPDATE `lke_komponen` SET `bobot` = 30.00 WHERE `no` = 2;
UPDATE `lke_komponen` SET `bobot` = 15.00 WHERE `no` = 3;
UPDATE `lke_komponen` SET `bobot` = 25.00 WHERE `no` = 4;

UPDATE `lke_subkomponen` SET `bobot` = 6.00 WHERE `kode` = '1.A';
UPDATE `lke_subkomponen` SET `bobot` = 9.00 WHERE `kode` = '1.B';
UPDATE `lke_subkomponen` SET `bobot` = 15.00 WHERE `kode` = '1.C';
UPDATE `lke_subkomponen` SET `bobot` = 6.00 WHERE `kode` = '2.A';
UPDATE `lke_subkomponen` SET `bobot` = 9.00 WHERE `kode` = '2.B';
UPDATE `lke_subkomponen` SET `bobot` = 15.00 WHERE `kode` = '2.C';
UPDATE `lke_subkomponen` SET `bobot` = 3.00 WHERE `kode` = '3.A';
UPDATE `lke_subkomponen` SET `bobot` = 4.50 WHERE `kode` = '3.B';
UPDATE `lke_subkomponen` SET `bobot` = 7.50 WHERE `kode` = '3.C';
UPDATE `lke_subkomponen` SET `bobot` = 5.00 WHERE `kode` = '4.A';
UPDATE `lke_subkomponen` SET `bobot` = 7.50 WHERE `kode` = '4.B';
UPDATE `lke_subkomponen` SET `bobot` = 12.50 WHERE `kode` = '4.C';

-- -------------------------------------------------------------
-- Data untuk tabel `dokumen_sakips` (Total: 25 baris)
-- -------------------------------------------------------------
INSERT INTO `dokumen_sakips` (`id`, `kategori`, `judul`, `deskripsi`, `url`, `icon`, `urutan`, `is_active`, `created_at`, `updated_at`) VALUES
  (1, 'pedoman_ketentuan', 'Pedoman JA Nomor 4 Tahun 2025 tentang Evaluasi Akuntabilitas Kinerja Instansi Pemerintah di Lingkungan Kejaksaan Republik Indonesia.pdf', NULL, 'https://drive.google.com/file/d/1Hm8d_Cvk_h9aA8rIYb1XyJO6C1WTwjAs/view', 'bi-file-earmark-pdf-fill text-danger', 1, 1, '2026-10-02 13:56:31', '2026-10-02 13:56:31'),
  (2, 'pedoman_ketentuan', 'PEDOMAN JA NOMOR 4 TAHUN 2024_PENYELENGGARAAN SAKIP', NULL, 'https://drive.google.com/file/d/1mNb9htgVw1ClP_0eHWAwBjYp6ygIG9-m/view?usp=drive_link', 'bi-file-earmark-pdf-fill text-danger', 2, 1, '2026-10-02 13:56:31', '2026-10-02 13:56:31'),
  (3, 'pedoman_ketentuan', 'Peraturan SAKIP', NULL, 'https://drive.google.com/drive/u/0/folders/1lRlkVrXcECSfNdzoGpPpsftYrWHN2ddn', 'bi-folder-fill text-warning', 3, 1, '2026-10-02 13:56:31', '2026-10-02 13:56:31'),
  (4, 'template_tahun_berjalan', 'FORMAT RENCANA AKSI KINERJA DAN LAPORAN MONEV RENAKSI KINERJA ES I TW I 2026', NULL, 'https://drive.google.com/drive/folders/16jJkdH1mW-h4CSKQn2Jgg4suwG86ocvg?usp=drive_link', 'bi-file-earmark-text-fill text-primary', 1, 1, '2026-10-02 13:56:31', '2026-10-02 13:56:31'),
  (5, 'template_tahun_berjalan', 'LKJ TW I Tahun 2026 - KEJATI, KEJARI DAN CABJARI', NULL, 'https://drive.google.com/drive/folders/1e-f1ElSCYFGOPQvvxti3Je0q_vnpCFY8?usp=sharing', 'bi-file-earmark-bar-graph text-info', 2, 1, '2026-10-02 13:56:31', '2026-10-02 13:56:31'),
  (6, 'template_tahun_berjalan', 'Rencana Aksi Kinerja Tahun 2026', NULL, 'https://drive.google.com/drive/folders/1gldPqRO1rTIbeNY8P5eEarnhl1b_PypE?usp=sharing', 'bi-calendar-event text-warning', 3, 1, '2026-10-02 13:56:31', '2026-10-02 13:56:31'),
  (7, 'template_tahun_berjalan', 'Monev Renaksi Kinerja TW I Tahun 2026', NULL, 'https://drive.google.com/drive/folders/1lzttjDxiYNoAKSS0ZNCFbLZIpQ-SuA89?usp=sharing', 'bi-clipboard2-data text-success', 4, 1, '2026-10-02 13:56:31', '2026-10-02 13:56:31'),
  (8, 'template_tahun_berjalan', 'RASTAFF EKA Tahun 2026', NULL, 'https://drive.google.com/drive/folders/1I6UQ5UizAHDwAL8UPcq9Oqo9SHo7rUtX?usp=sharing', 'bi-people-fill text-secondary', 5, 1, '2026-10-02 13:56:31', '2026-10-02 13:56:31'),
  (9, 'arsip_template', 'Template LKjIP Satuan Kerja Tahun 2025', NULL, 'https://drive.google.com/drive/folders/1bAzTx5kaIJvP8jnAA5RZqGGA5hWxbevR?usp=sharing', 'bi-file-earmark-word text-primary', 1, 1, '2026-10-02 13:56:31', '2026-10-02 13:56:31'),
  (10, 'arsip_template', 'Cover LKjIP Satuan Kerja Tahun 2025', NULL, 'https://drive.google.com/drive/folders/1_9_N5Ax5eIhLIUTYG3_4L6pZgqm9uKR_?usp=sharing', 'bi-image text-success', 2, 1, '2026-10-02 13:56:31', '2026-10-02 13:56:31'),
  (11, 'arsip_template', 'Template LKJ Triwulan III Tahun 2025', NULL, 'https://docs.google.com/document/d/14HkvvEJmGyPo0QfhPElCxwVhC5Ri6q2s/edit?usp=sharing&ouid=109556050232324402632&rtpof=true&sd=true', 'bi-file-earmark-text text-info', 3, 1, '2026-10-02 13:56:31', '2026-10-02 13:56:31'),
  (12, 'arsip_template', 'Pohon Kinerja Tahun 2025', NULL, 'https://drive.google.com/drive/folders/1WdulmYbNpOzFCCathQhJ1W_7IVwdFOHi?usp=drive_link', 'bi-diagram-2 text-success', 4, 1, '2026-10-02 13:56:31', '2026-10-02 13:56:31'),
  (13, 'arsip_template', 'Format Rencana Aksi Kinerja Tahun 2025', NULL, 'https://drive.google.com/drive/folders/1Do5DZKQI-mNWfpT9jpMs0Tp8zOx7BD3d?usp=sharing', 'bi-calendar-event text-warning', 5, 1, '2026-10-02 13:56:31', '2026-10-02 13:56:31'),
  (14, 'arsip_template', 'Format Laporan Monev Rencana Aksi Kinerja Tahun 2025', NULL, 'https://drive.google.com/drive/folders/1uHlIHeS44wRVsPBWiY0sOG7Ha5TFerP1?usp=sharing', 'bi-clipboard2-data text-primary', 6, 1, '2026-10-02 13:56:31', '2026-10-02 13:56:31'),
  (15, 'arsip_template', 'IKU / Penetapan Target Kinerja 2025', NULL, 'https://drive.google.com/drive/folders/1KlMJCKb8mmDlvPiYkjkuWNXyrRsQtfMB?usp=sharing', 'bi-bullseye text-danger', 7, 1, '2026-10-02 13:56:31', '2026-10-02 13:56:31'),
  (16, 'arsip_template', 'Rencana Kerja Kejaksaan RI Tahun 2025', NULL, 'https://drive.google.com/file/d/1xxiHanwuk8Cpqn2i9noXKGiCO3iSSKMy/view?usp=sharing', 'bi-file-earmark-pdf text-danger', 8, 1, '2026-10-02 13:56:31', '2026-10-02 13:56:31'),
  (17, 'arsip_template', 'FORMAT RANWAL RENSTRA (SATKER DAERAH) - Google Drive', NULL, 'https://drive.google.com/drive/folders/1_9fQmMKStFHbmFHQ9wQSrJu_1hIk04YL?usp=sharing', 'bi-folder text-warning', 9, 1, '2026-10-02 13:56:31', '2026-10-02 13:56:31'),
  (18, 'arsip_template', 'PK 2025 RANWAL ESELON III KEJATI - Google Drive', NULL, 'https://drive.google.com/drive/folders/1Vvw2zC17nN3Q7KqWiHy4WrxSatIm68_O?usp=sharing', 'bi-folder text-warning', 10, 1, '2026-10-02 13:56:31', '2026-10-02 13:56:31'),
  (19, 'arsip_template', 'PK 2025 RANWAL ESELON IV KEJARI - Google Drive', NULL, 'https://drive.google.com/drive/folders/1fFETnMV0DSH0Bpo8sv1A8HMfoJ8evO9i?usp=sharing', 'bi-folder text-warning', 11, 1, '2026-10-02 13:56:31', '2026-10-02 13:56:31'),
  (20, 'arsip_template', 'PK 2025 RANWAL ESELON V CABJARI - Google Drive', NULL, 'https://drive.google.com/drive/folders/1UuRUanPDHpQu-rcfiwS7lWSVyuvT4lhW?usp=sharing', 'bi-folder text-warning', 12, 1, '2026-10-02 13:56:31', '2026-10-02 13:56:31'),
  (21, 'arsip_template', 'PK 2025 RANWAL ESELON PEJABAT STRUKTURAL LAINNYA - Google Drive', NULL, 'https://drive.google.com/drive/folders/1AlxsuXxMVW9FDCJWhUjrovXsHoMQ3eFP?usp=sharing', 'bi-folder text-warning', 13, 1, '2026-10-02 13:56:31', '2026-10-02 13:56:31'),
  (22, 'arsip_template', 'Template LKJ Triwulan II Tahun 2025', NULL, 'https://docs.google.com/document/d/15oy4mfmFbGb81Bnwz5PPpXs1zN_anokd/edit?usp=sharing&ouid=109556050232324402632&rtpof=true&sd=true', 'bi-file-earmark-text text-info', 14, 1, '2026-10-02 13:56:31', '2026-10-02 13:56:31'),
  (23, 'arsip_template', 'Template LKJ Triwulan I Tahun 2025', NULL, 'https://drive.google.com/drive/u/0/folders/1JAD4l9KZA7d4ANKmexS3rrID3DrPekD1', 'bi-folder text-warning', 15, 1, '2026-10-02 13:56:31', '2026-10-02 13:56:31'),
  (24, 'arsip_template', 'Template RAPAT STAFF EKA Tahun 2025', NULL, 'https://docs.google.com/document/d/12waQaX6lK8NjGzHBCk5fOaetXSzEcwxe/edit?usp=sharing&ouid=109556050232324402632&rtpof=true&sd=true', 'bi-file-earmark-text text-info', 16, 1, '2026-10-02 13:56:31', '2026-10-02 13:56:31'),
  (25, 'arsip_template', 'Template PK Ranwal_Kasatker Tahun 2025', NULL, 'https://drive.google.com/drive/folders/1sDIeMaE1gnVn3pkyI5qBcOaEv_qg8h1f?usp=drive_link', 'bi-folder text-warning', 17, 1, '2026-10-02 13:56:31', '2026-10-02 13:56:31')
ON DUPLICATE KEY UPDATE `kategori` = VALUES(`kategori`), `judul` = VALUES(`judul`), `deskripsi` = VALUES(`deskripsi`), `url` = VALUES(`url`), `icon` = VALUES(`icon`), `urutan` = VALUES(`urutan`), `is_active` = VALUES(`is_active`), `created_at` = VALUES(`created_at`), `updated_at` = VALUES(`updated_at`);

-- -------------------------------------------------------------
-- Data untuk tabel `sasaran_program` (Total: 68 baris)
-- -------------------------------------------------------------
INSERT INTO `sasaran_program` (`id`, `kode_sp`, `nama_sp`, `id_satker`, `is_crosscutting`, `tahun`, `created_at`, `updated_at`) VALUES
  (1, 'SP 13', 'Terselenggaranya pemulihan aset yang terintegrasi', '691270', 0, '2025', '2026-10-02 13:54:52', '2026-10-02 13:54:52'),
  (2, 'SP 14', 'Terwujudnya pengelolaan aset tindak pidana yang transparan, akuntabel, dan modern', '691270', 0, '2025', '2026-10-02 13:54:52', '2026-10-02 13:54:52'),
  (3, 'SP 5', 'Meningkatnya keberhasilan penanganan perkara tindak pidana khusus secara transparan, akuntabel dan profesional', '419344', 0, '2025', '2026-10-02 13:54:52', '2026-10-02 13:54:52'),
  (4, 'SP 6', 'Meningkatnya penyelamatan dan pengembalian kerugian keuangan negara dari penanganan perkara tindak pidana khusus', '419344', 0, '2025', '2026-10-02 13:54:52', '2026-10-02 13:54:52'),
  (5, 'SP 15', 'Meningkatnya kualitas sistem penuntutan yang terintegrasi dan transparan (CMS)', '419344', 1, '2025', '2026-10-02 13:54:52', '2026-10-02 13:54:52'),
  (6, 'SP 17', 'Meningkatnya Kualitas Layanan Publik bidang penegakan hukum', '419344', 1, '2025', '2026-10-02 13:54:52', '2026-10-02 13:54:52'),
  (7, 'SP 18', 'Meningkatnya efektivitas pemanfaatan sarana dan prasarana', '419344', 1, '2025', '2026-10-02 13:54:52', '2026-10-02 13:54:52'),
  (8, 'SP 3', 'Meningkatnya penanganan perkara tindak pidana umum yang berorientasi pada kepastian hukum, keadilan, dan kemanfaatan', '418326', 0, '2025', '2026-10-02 13:54:52', '2026-10-02 13:54:52'),
  (9, 'SP 4', 'Meningkatnya penegakan hukum yang adil, humanis, proporsional, dan efisien melalui pendekatan keadilan restoratif', '418326', 1, '2025', '2026-10-02 13:54:52', '2026-10-02 13:54:52'),
  (10, 'SP 15', 'Meningkatnya kualitas sistem penuntutan yang terintegrasi dan transparan (CMS)', '418326', 1, '2025', '2026-10-02 13:54:52', '2026-10-02 13:54:52'),
  (11, 'SP 17', 'Meningkatnya Kualitas Layanan Publik bidang penegakan hukum', '418326', 1, '2025', '2026-10-02 13:54:52', '2026-10-02 13:54:52'),
  (12, 'SP 18', 'Meningkatnya efektivitas pemanfaatan sarana dan prasarana', '418326', 1, '2025', '2026-10-02 13:54:52', '2026-10-02 13:54:52'),
  (13, 'SP_PIDUM_MGMT', 'Meningkatnya kapasitas dan kapabilitas SDM penanganan perkara tindak pidana umum', '418326', 0, '2025', '2026-10-02 13:54:52', '2026-10-02 13:54:52'),
  (14, 'SP 1', 'Meningkatnya kualitas peran Advocaat Generaal dan Jaksa Pengacara Negara (JPN) dalam penegakan hukum', '417023', 0, '2025', '2026-10-02 13:54:52', '2026-10-02 13:54:52'),
  (15, 'SP 2', 'Meningkatnya efektivitas pelayanan hukum oleh Jaksa Pengacara Negara', '417023', 0, '2025', '2026-10-02 13:54:52', '2026-10-02 13:54:52'),
  (16, 'SP 7', 'Meningkatnya penyelamatan dan pemulihan keuangan/kekayaan negara melalui jalur perdata', '417023', 0, '2025', '2026-10-02 13:54:52', '2026-10-02 13:54:52'),
  (17, 'SP 8', 'Meningkatnya keberhasilan penanganan perkara Perdata dan Tata Usaha Negara', '417023', 0, '2025', '2026-10-02 13:54:52', '2026-10-02 13:54:52'),
  (18, 'SP 16', 'Meningkatnya Kualitas Layanan Publik bidang hukum', '417023', 1, '2025', '2026-10-02 13:54:52', '2026-10-02 13:54:52'),
  (19, 'SP 17', 'Meningkatnya Kualitas Layanan Publik bidang penegakan hukum', '417023', 1, '2025', '2026-10-02 13:54:52', '2026-10-02 13:54:52'),
  (20, 'SP 18', 'Meningkatnya efektivitas pemanfaatan sarana dan prasarana', '417023', 1, '2025', '2026-10-02 13:54:52', '2026-10-02 13:54:52'),
  (21, 'SP 9', 'Meningkatnya efektivitas penanganan perkara koneksitas yang adil dan akuntabel', '677111', 0, '2025', '2026-10-02 13:54:52', '2026-10-02 13:54:52'),
  (22, 'SP 10', 'Meningkatnya koordinasi teknis penuntutan yang dilakukan oleh Oditurat', '677111', 0, '2025', '2026-10-02 13:54:52', '2026-10-02 13:54:52'),
  (23, 'SP 15', 'Meningkatnya kualitas sistem penuntutan yang terintegrasi dan transparan (CMS)', '677111', 1, '2025', '2026-10-02 13:54:52', '2026-10-02 13:54:52'),
  (24, 'SP 16', 'Meningkatnya Kualitas Layanan Publik bidang hukum', '677111', 1, '2025', '2026-10-02 13:54:52', '2026-10-02 13:54:52'),
  (25, 'SP_PIDMIL_MGMT', 'Meningkatnya dukungan teknis operasional penanganan perkara koneksitas', '677111', 0, '2025', '2026-10-02 13:54:52', '2026-10-02 13:54:52'),
  (26, 'SP 9', 'Meningkatnya Kualitas dan Kompetensi SDM Kejaksaan melalui Penyelenggaraan Pendidikan dan Pelatihan', '666405', 0, '2025', '2026-10-02 13:54:52', '2026-10-02 13:54:52'),
  (27, 'SP 10', 'Meningkatnya kualitas layanan internal dukungan manajemen Badan Pendidikan dan Pelatihan', '666405', 0, '2025', '2026-10-02 13:54:52', '2026-10-02 13:54:52'),
  (28, 'SP 2', 'Meningkatnya akuntabilitas pengelolaan keuangan dan kepatuhan perbendaharaan Kejaksaan RI', '419346', 0, '2025', '2026-10-02 13:54:52', '2026-10-02 13:54:52'),
  (29, 'SP 3', 'Meningkatnya efektivitas sistem pengendalian intern pemerintah (SPIP) di lingkungan Kejaksaan RI', '419346', 0, '2025', '2026-10-02 13:54:52', '2026-10-02 13:54:52'),
  (30, 'SP 12', 'Meningkatnya integritas aparatur Kejaksaan Republik Indonesia', '419346', 0, '2025', '2026-10-02 13:54:52', '2026-10-02 13:54:52'),
  (31, 'SP 14', 'Terwujudnya birokrasi Kejaksaan yang bersih dan melayani (Pembangunan ZI)', '419346', 0, '2025', '2026-10-02 13:54:52', '2026-10-02 13:54:52'),
  (32, 'SP_WAS_OPS', 'Meningkatnya kualitas dan ketepatan waktu pelaksanaan pengawasan internal', '419346', 0, '2025', '2026-10-02 13:54:52', '2026-10-02 13:54:52'),
  (33, 'SP_WAS_MGMT', 'Meningkatnya dukungan manajemen dan tata kelola bidang pengawasan', '419346', 0, '2025', '2026-10-02 13:54:52', '2026-10-02 13:54:52'),
  (34, 'SP 11', 'Meningkatnya efektivitas fungsi intelijen penegakan hukum', '419345', 0, '2025', '2026-10-02 13:54:52', '2026-10-02 13:54:52'),
  (35, 'SP 13', 'Terselenggaranya pemulihan aset yang terintegrasi', '691270', 0, '2026', '2026-10-02 13:54:52', '2026-10-02 13:54:52'),
  (36, 'SP 14', 'Terwujudnya pengelolaan aset tindak pidana yang transparan, akuntabel, dan modern', '691270', 0, '2026', '2026-10-02 13:54:52', '2026-10-02 13:54:52'),
  (37, 'SP 5', 'Meningkatnya keberhasilan penanganan perkara tindak pidana khusus secara transparan, akuntabel dan profesional', '419344', 0, '2026', '2026-10-02 13:54:52', '2026-10-02 13:54:52'),
  (38, 'SP 6', 'Meningkatnya penyelamatan dan pengembalian kerugian keuangan negara dari penanganan perkara tindak pidana khusus', '419344', 0, '2026', '2026-10-02 13:54:52', '2026-10-02 13:54:52'),
  (39, 'SP 15', 'Meningkatnya kualitas sistem penuntutan yang terintegrasi dan transparan (CMS)', '419344', 1, '2026', '2026-10-02 13:54:52', '2026-10-02 13:54:52'),
  (40, 'SP 17', 'Meningkatnya Kualitas Layanan Publik bidang penegakan hukum', '419344', 1, '2026', '2026-10-02 13:54:52', '2026-10-02 13:54:52'),
  (41, 'SP 18', 'Meningkatnya efektivitas pemanfaatan sarana dan prasarana', '419344', 1, '2026', '2026-10-02 13:54:52', '2026-10-02 13:54:52'),
  (42, 'SP 3', 'Meningkatnya penanganan perkara tindak pidana umum yang berorientasi pada kepastian hukum, keadilan, dan kemanfaatan', '418326', 0, '2026', '2026-10-02 13:54:52', '2026-10-02 13:54:52'),
  (43, 'SP 4', 'Meningkatnya penegakan hukum yang adil, humanis, proporsional, dan efisien melalui pendekatan keadilan restoratif', '418326', 1, '2026', '2026-10-02 13:54:52', '2026-10-02 13:54:52'),
  (44, 'SP 15', 'Meningkatnya kualitas sistem penuntutan yang terintegrasi dan transparan (CMS)', '418326', 1, '2026', '2026-10-02 13:54:52', '2026-10-02 13:54:52'),
  (45, 'SP 17', 'Meningkatnya Kualitas Layanan Publik bidang penegakan hukum', '418326', 1, '2026', '2026-10-02 13:54:52', '2026-10-02 13:54:52'),
  (46, 'SP 18', 'Meningkatnya efektivitas pemanfaatan sarana dan prasarana', '418326', 1, '2026', '2026-10-02 13:54:52', '2026-10-02 13:54:52'),
  (47, 'SP_PIDUM_MGMT', 'Meningkatnya kapasitas dan kapabilitas SDM penanganan perkara tindak pidana umum', '418326', 0, '2026', '2026-10-02 13:54:52', '2026-10-02 13:54:52'),
  (48, 'SP 1', 'Meningkatnya kualitas peran Advocaat Generaal dan Jaksa Pengacara Negara (JPN) dalam penegakan hukum', '417023', 0, '2026', '2026-10-02 13:54:52', '2026-10-02 13:54:52'),
  (49, 'SP 2', 'Meningkatnya efektivitas pelayanan hukum oleh Jaksa Pengacara Negara', '417023', 0, '2026', '2026-10-02 13:54:52', '2026-10-02 13:54:52'),
  (50, 'SP 7', 'Meningkatnya penyelamatan dan pemulihan keuangan/kekayaan negara melalui jalur perdata', '417023', 0, '2026', '2026-10-02 13:54:52', '2026-10-02 13:54:52')
ON DUPLICATE KEY UPDATE `kode_sp` = VALUES(`kode_sp`), `nama_sp` = VALUES(`nama_sp`), `id_satker` = VALUES(`id_satker`), `is_crosscutting` = VALUES(`is_crosscutting`), `tahun` = VALUES(`tahun`), `created_at` = VALUES(`created_at`), `updated_at` = VALUES(`updated_at`);
INSERT INTO `sasaran_program` (`id`, `kode_sp`, `nama_sp`, `id_satker`, `is_crosscutting`, `tahun`, `created_at`, `updated_at`) VALUES
  (51, 'SP 8', 'Meningkatnya keberhasilan penanganan perkara Perdata dan Tata Usaha Negara', '417023', 0, '2026', '2026-10-02 13:54:52', '2026-10-02 13:54:52'),
  (52, 'SP 16', 'Meningkatnya Kualitas Layanan Publik bidang hukum', '417023', 1, '2026', '2026-10-02 13:54:52', '2026-10-02 13:54:52'),
  (53, 'SP 17', 'Meningkatnya Kualitas Layanan Publik bidang penegakan hukum', '417023', 1, '2026', '2026-10-02 13:54:52', '2026-10-02 13:54:52'),
  (54, 'SP 18', 'Meningkatnya efektivitas pemanfaatan sarana dan prasarana', '417023', 1, '2026', '2026-10-02 13:54:52', '2026-10-02 13:54:52'),
  (55, 'SP 9', 'Meningkatnya efektivitas penanganan perkara koneksitas yang adil dan akuntabel', '677111', 0, '2026', '2026-10-02 13:54:52', '2026-10-02 13:54:52'),
  (56, 'SP 10', 'Meningkatnya koordinasi teknis penuntutan yang dilakukan oleh Oditurat', '677111', 0, '2026', '2026-10-02 13:54:52', '2026-10-02 13:54:52'),
  (57, 'SP 15', 'Meningkatnya kualitas sistem penuntutan yang terintegrasi dan transparan (CMS)', '677111', 1, '2026', '2026-10-02 13:54:52', '2026-10-02 13:54:52'),
  (58, 'SP 16', 'Meningkatnya Kualitas Layanan Publik bidang hukum', '677111', 1, '2026', '2026-10-02 13:54:52', '2026-10-02 13:54:52'),
  (59, 'SP_PIDMIL_MGMT', 'Meningkatnya dukungan teknis operasional penanganan perkara koneksitas', '677111', 0, '2026', '2026-10-02 13:54:52', '2026-10-02 13:54:52'),
  (60, 'SP 9', 'Meningkatnya Kualitas dan Kompetensi SDM Kejaksaan melalui Penyelenggaraan Pendidikan dan Pelatihan', '666405', 0, '2026', '2026-10-02 13:54:52', '2026-10-02 13:54:52'),
  (61, 'SP 10', 'Meningkatnya kualitas layanan internal dukungan manajemen Badan Pendidikan dan Pelatihan', '666405', 0, '2026', '2026-10-02 13:54:52', '2026-10-02 13:54:52'),
  (62, 'SP 2', 'Meningkatnya akuntabilitas pengelolaan keuangan dan kepatuhan perbendaharaan Kejaksaan RI', '419346', 0, '2026', '2026-10-02 13:54:52', '2026-10-02 13:54:52'),
  (63, 'SP 3', 'Meningkatnya efektivitas sistem pengendalian intern pemerintah (SPIP) di lingkungan Kejaksaan RI', '419346', 0, '2026', '2026-10-02 13:54:52', '2026-10-02 13:54:52'),
  (64, 'SP 12', 'Meningkatnya integritas aparatur Kejaksaan Republik Indonesia', '419346', 0, '2026', '2026-10-02 13:54:52', '2026-10-02 13:54:52'),
  (65, 'SP 14', 'Terwujudnya birokrasi Kejaksaan yang bersih dan melayani (Pembangunan ZI)', '419346', 0, '2026', '2026-10-02 13:54:52', '2026-10-02 13:54:52'),
  (66, 'SP_WAS_OPS', 'Meningkatnya kualitas dan ketepatan waktu pelaksanaan pengawasan internal', '419346', 0, '2026', '2026-10-02 13:54:52', '2026-10-02 13:54:52'),
  (67, 'SP_WAS_MGMT', 'Meningkatnya dukungan manajemen dan tata kelola bidang pengawasan', '419346', 0, '2026', '2026-10-02 13:54:52', '2026-10-02 13:54:52'),
  (68, 'SP 11', 'Meningkatnya efektivitas fungsi intelijen penegakan hukum', '419345', 0, '2026', '2026-10-02 13:54:52', '2026-10-02 13:54:52')
ON DUPLICATE KEY UPDATE `kode_sp` = VALUES(`kode_sp`), `nama_sp` = VALUES(`nama_sp`), `id_satker` = VALUES(`id_satker`), `is_crosscutting` = VALUES(`is_crosscutting`), `tahun` = VALUES(`tahun`), `created_at` = VALUES(`created_at`), `updated_at` = VALUES(`updated_at`);

-- -------------------------------------------------------------
-- Data untuk tabel `indikator_kinerja_program` (Total: 126 baris)
-- -------------------------------------------------------------
INSERT INTO `indikator_kinerja_program` (`id`, `sasaran_program_id`, `kode_ikp`, `nama_ikp`, `id_satker`, `sifat_node`, `tahun`, `created_at`, `updated_at`) VALUES
  (1, 1, 'IKP 13.1', 'Tingkat keberhasilan kegiatan penelusuran aset', '691270', 'RATIO_PERCENTAGE', '2025', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (2, 1, 'IKP 13.2', 'Tingkat keberhasilan perampasan aset hasil tindak pidana', '691270', 'RATIO_PERCENTAGE', '2025', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (3, 1, 'IKP 13.3', 'Tingkat keberhasilan pemulihan aset hasil tindak pidana', '691270', 'RATIO_PERCENTAGE', '2025', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (4, 2, 'IKP 14.1', 'Tingkat efektivitas pengelolaan Rupbasan', '691270', 'AVERAGE', '2025', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (5, 2, 'IKP 14.2', 'Tingkat efektivitas penyelesaian penyelamatan aset negara', '691270', 'AVERAGE', '2025', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (6, 2, 'IKP 14.3', 'Tingkat efektivitas pengelolaan data aset negara berbasis teknologi informasi', '691270', 'AVERAGE', '2025', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (7, 3, 'IKP 5.1', 'Tingkat keberhasilan penanganan perkara tindak pidana korupsi dan TPPU', '419344', 'AVERAGE', '2025', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (8, 3, 'IKP 5.2', 'Tingkat keberhasilan penanganan perkara tindak pidana perpajakan dan TPPU', '419344', 'AVERAGE', '2025', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (9, 3, 'IKP 5.3', 'Tingkat keberhasilan penanganan perkara tindak pidana kepabeanan, cukai dan TPPU', '419344', 'AVERAGE', '2025', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (10, 3, 'IKP 5.5', 'Tingkat keberhasilan penanganan perkara tindak pidana khusus lainnya yang merugikan perekonomian negara dan TPPU', '419344', 'AVERAGE', '2025', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (11, 3, 'IKP 5.6', 'Tingkat keberhasilan penanganan perkara pelanggaran HAM yang berat', '419344', 'AVERAGE', '2025', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (12, 3, 'IKP 5.7', 'Tingkat keberhasilan pengendalian pelaksanaan operasi intelijen yustisial dan penindakan', '419344', 'AVERAGE', '2025', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (13, 4, 'IKP 6.1', 'Persentase penyelamatan kerugian keuangan negara dari penanganan perkara tindak pidana korupsi', '419344', 'RATIO_PERCENTAGE', '2025', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (14, 4, 'IKP 6.2', 'Persentase penyelesaian pembayaran pidana denda perkara tindak pidana khusus lainnya', '419344', 'RATIO_PERCENTAGE', '2025', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (15, 5, 'IKP 15.1', 'Persentase pemanfaatan Case Management System (CMS) bidang Pidsus', '419344', 'RATIO_PERCENTAGE', '2025', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (16, 6, 'IKP 17.1', 'Indeks kepuasan masyarakat terhadap layanan publik bidang Pidsus', '419344', 'INDEX_SCORE', '2025', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (17, 7, 'IKP 18.1', 'Persentase pemenuhan dan pemanfaatan sarana prasarana bidang Pidsus', '419344', 'RATIO_PERCENTAGE', '2025', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (18, 8, 'IKP 3.1', 'Tingkat penyelesaian penanganan perkara tindak pidana umum pada tahap prapenuntutan', '418326', 'RATIO_PERCENTAGE', '2025', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (19, 8, 'IKP 3.2', 'Tingkat penyelesaian penanganan perkara tindak pidana umum pada tahap penuntutan', '418326', 'RATIO_PERCENTAGE', '2025', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (20, 8, 'IKP 3.3', 'Tingkat penyelesaian pelaksanaan putusan pengadilan yang telah berkekuatan hukum tetap (eksekusi)', '418326', 'RATIO_PERCENTAGE', '2025', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (21, 9, 'IKP 4.1', 'Persentase penyelesaian perkara tindak pidana umum melalui mekanisme Keadilan Restoratif (Restorative Justice)', '418326', 'RATIO_PERCENTAGE', '2025', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (22, 9, 'IKP 4.2', 'Persentase pelaksanaan pidana kerja sosial dan pidana pengawasan sebagai alternatif pemidanaan penjara (KUHP Baru)', '418326', 'RATIO_PERCENTAGE', '2025', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (23, 10, 'IKP 15.1', 'Persentase pemanfaatan Case Management System (CMS) bidang Pidum', '418326', 'RATIO_PERCENTAGE', '2025', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (24, 11, 'IKP 17.1', 'Indeks kepuasan masyarakat terhadap layanan publik bidang Pidum', '418326', 'INDEX_SCORE', '2025', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (25, 12, 'IKP 18.1', 'Persentase pemenuhan dan pemanfaatan sarana prasarana bidang Pidum', '418326', 'RATIO_PERCENTAGE', '2025', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (26, 13, 'IKP_PIDUM_MGMT.1', 'Jumlah Jaksa yang telah tersertifikasi/mengikuti diklat teknis tindak pidana umum', '418326', 'DIRECT_INPUT', '2025', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (27, 14, 'IKP 1.1', 'Persentase pemberian pertimbangan hukum (legal audit/legal opinion/legal assistance) yang ditindaklanjuti', '417023', 'RATIO_PERCENTAGE', '2025', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (28, 15, 'IKP 2.1', 'Indeks persepsi kualitas pelayanan hukum Datun', '417023', 'INDEX_SCORE', '2025', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (29, 16, 'IKP 7.1', 'Persentase penyelamatan keuangan/kekayaan negara bidang perdata dan tata usaha negara', '417023', 'RATIO_PERCENTAGE', '2025', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (30, 16, 'IKP 7.2', 'Persentase pemulihan keuangan/kekayaan negara bidang perdata dan tata usaha negara', '417023', 'RATIO_PERCENTAGE', '2025', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (31, 17, 'IKP 8.1', 'Persentase keberhasilan penanganan perkara perdata secara litigasi', '417023', 'RATIO_PERCENTAGE', '2025', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (32, 17, 'IKP 8.2', 'Persentase keberhasilan penyelesaian masalah perdata secara non-litigasi/negosiasi/mediasi', '417023', 'RATIO_PERCENTAGE', '2025', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (33, 17, 'IKP 8.3', 'Persentase keberhasilan penanganan perkara tata usaha negara (TUN)', '417023', 'RATIO_PERCENTAGE', '2025', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (34, 18, 'IKP 16.1', 'Indeks kepuasan pelayanan hukum gratis bidang Datun', '417023', 'INDEX_SCORE', '2025', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (35, 19, 'IKP 17.1', 'Indeks kepuasan masyarakat terhadap layanan publik bidang Datun', '417023', 'INDEX_SCORE', '2025', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (36, 20, 'IKP 18.1', 'Persentase pemenuhan dan pemanfaatan sarana prasarana bidang Datun', '417023', 'RATIO_PERCENTAGE', '2025', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (37, 21, 'IKP 9.1', 'Persentase penyelesaian penanganan perkara koneksitas pada tahap penyelidikan', '677111', 'RATIO_PERCENTAGE', '2025', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (38, 21, 'IKP 9.2', 'Persentase penyelesaian penanganan perkara koneksitas pada tahap penyidikan', '677111', 'RATIO_PERCENTAGE', '2025', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (39, 21, 'IKP 9.3', 'Persentase penyelesaian penanganan perkara koneksitas pada tahap penuntutan', '677111', 'RATIO_PERCENTAGE', '2025', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (40, 21, 'IKP 9.4', 'Persentase penyelesaian pelaksanaan putusan pengadilan berkekuatan hukum tetap (eksekusi) perkara koneksitas', '677111', 'RATIO_PERCENTAGE', '2025', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (41, 21, 'IKP 9.5', 'Persentase pengembalian dan pemulihan kerugian keuangan negara akibat tindak pidana koneksitas', '677111', 'RATIO_PERCENTAGE', '2025', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (42, 22, 'IKP 10.1', 'Persentase koordinasi teknis penuntutan pada tahap penindakan perkara koneksitas', '677111', 'RATIO_PERCENTAGE', '2025', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (43, 22, 'IKP 10.2', 'Persentase koordinasi teknis penuntutan pada tahap penuntutan perkara koneksitas', '677111', 'RATIO_PERCENTAGE', '2025', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (44, 22, 'IKP 10.3', 'Persentase koordinasi teknis penuntutan pada tahap eksekusi dan upaya hukum luar biasa', '677111', 'RATIO_PERCENTAGE', '2025', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (45, 23, 'IKP 15.1', 'Persentase pemanfaatan Case Management System (CMS) bidang Pidmil', '677111', 'RATIO_PERCENTAGE', '2025', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (46, 24, 'IKP 16.1', 'Indeks kepuasan relasi kelembagaan TNI dan stakeholder peradilan militer', '677111', 'INDEX_SCORE', '2025', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (47, 25, 'IKP_PIDMIL_MGMT.1', 'Persentase ketepatan waktu penyusunan laporan perkara koneksitas', '677111', 'RATIO_PERCENTAGE', '2025', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (48, 26, 'IKP 9.1', 'Indeks kepuasan pengguna layanan kediklatan Kejaksaan RI', '666405', 'INDEX_SCORE', '2025', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (49, 27, 'IKP 10.1', 'Indeks kepuasan layanan kesekretariatan dan dukungan operasional Badiklat', '666405', 'INDEX_SCORE', '2025', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (50, 28, 'IKP 2.1', 'Opini BPK atas Laporan Keuangan Kejaksaan RI (WTP)', '419346', 'DIRECT_INPUT', '2025', '2026-10-02 13:54:59', '2026-10-02 13:54:59')
ON DUPLICATE KEY UPDATE `sasaran_program_id` = VALUES(`sasaran_program_id`), `kode_ikp` = VALUES(`kode_ikp`), `nama_ikp` = VALUES(`nama_ikp`), `id_satker` = VALUES(`id_satker`), `sifat_node` = VALUES(`sifat_node`), `tahun` = VALUES(`tahun`), `created_at` = VALUES(`created_at`), `updated_at` = VALUES(`updated_at`);
INSERT INTO `indikator_kinerja_program` (`id`, `sasaran_program_id`, `kode_ikp`, `nama_ikp`, `id_satker`, `sifat_node`, `tahun`, `created_at`, `updated_at`) VALUES
  (51, 29, 'IKP 3.1', 'Nilai Maturitas Penyelenggaraan SPIP Terintegrasi', '419346', 'INDEX_SCORE', '2025', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (52, 30, 'IKP 12.1', 'Nilai Indeks Survei Penilaian Integritas (SPI) KPK di lingkungan Kejaksaan RI', '419346', 'INDEX_SCORE', '2025', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (53, 31, 'IKP 14.1', 'Persentase satuan kerja yang memenuhi kriteria Zona Integritas (WBK/WBBM)', '419346', 'RATIO_PERCENTAGE', '2025', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (54, 32, 'IKP_WAS_OPS.1', 'Persentase penyelesaian tindak lanjut hasil pengawasan (TLHP) internal dan eksternal', '419346', 'RATIO_PERCENTAGE', '2025', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (55, 33, 'IKP_WAS_MGMT.1', 'Persentase pemenuhan standar layanan pengawasan internal sesuai SLA', '419346', 'RATIO_PERCENTAGE', '2025', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (56, 34, 'IKP 11.1', 'Persentase Hasil Kegiatan Pelaksanaan Operasi Intelijen di Bidang Ideologi, Politik, Pertahanan, dan Keamanan', '419345', 'RATIO_PERCENTAGE', '2025', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (57, 34, 'IKP 11.2', 'Persentase Hasil Kegiatan Pelaksanaan Operasi Intelijen di Bidang Sosial Budaya dan Kemasyarakatan', '419345', 'RATIO_PERCENTAGE', '2025', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (58, 34, 'IKP 11.3', 'Persentase Hasil Kegiatan Pelaksanaan Operasi Intelijen di Bidang Ekonomi dan Keuangan', '419345', 'RATIO_PERCENTAGE', '2025', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (59, 34, 'IKP 11.4', 'Persentase Pembangunan Strategis Yang Diberikan Pengawalan', '419345', 'RATIO_PERCENTAGE', '2025', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (60, 34, 'IKP 11.5', 'Persentase Hasil Kegiatan Pelaksanaan Operasi Intelijen di Bidang Teknologi Informasi dan Produksi Intelijen', '419345', 'RATIO_PERCENTAGE', '2025', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (61, 34, 'IKP 11.6', 'Persentase Pelaksanaan Major Project Penguatan NSOC SOC dan Pembentukan 121 CSIRT', '419345', 'RATIO_PERCENTAGE', '2025', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (62, 34, 'IKP 11.7', 'Persentase Lembaga atau Pihak Yang Diberi Penyuluhan Dan Penerangan Hukum', '419345', 'RATIO_PERCENTAGE', '2025', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (63, 34, 'IKP 11.8', 'Indeks Kepuasan Pemangku Kepentingan Terhadap Layanan Penyuluhan dan Penerangan Hukum', '419345', 'AVERAGE', '2025', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (64, 35, 'IKP 13.1', 'Tingkat keberhasilan kegiatan penelusuran aset', '691270', 'RATIO_PERCENTAGE', '2026', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (65, 35, 'IKP 13.2', 'Tingkat keberhasilan perampasan aset hasil tindak pidana', '691270', 'RATIO_PERCENTAGE', '2026', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (66, 35, 'IKP 13.3', 'Tingkat keberhasilan pemulihan aset hasil tindak pidana', '691270', 'RATIO_PERCENTAGE', '2026', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (67, 36, 'IKP 14.1', 'Tingkat efektivitas pengelolaan Rupbasan', '691270', 'AVERAGE', '2026', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (68, 36, 'IKP 14.2', 'Tingkat efektivitas penyelesaian penyelamatan aset negara', '691270', 'AVERAGE', '2026', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (69, 36, 'IKP 14.3', 'Tingkat efektivitas pengelolaan data aset negara berbasis teknologi informasi', '691270', 'AVERAGE', '2026', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (70, 37, 'IKP 5.1', 'Tingkat keberhasilan penanganan perkara tindak pidana korupsi dan TPPU', '419344', 'AVERAGE', '2026', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (71, 37, 'IKP 5.2', 'Tingkat keberhasilan penanganan perkara tindak pidana perpajakan dan TPPU', '419344', 'AVERAGE', '2026', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (72, 37, 'IKP 5.3', 'Tingkat keberhasilan penanganan perkara tindak pidana kepabeanan, cukai dan TPPU', '419344', 'AVERAGE', '2026', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (73, 37, 'IKP 5.5', 'Tingkat keberhasilan penanganan perkara tindak pidana khusus lainnya yang merugikan perekonomian negara dan TPPU', '419344', 'AVERAGE', '2026', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (74, 37, 'IKP 5.6', 'Tingkat keberhasilan penanganan perkara pelanggaran HAM yang berat', '419344', 'AVERAGE', '2026', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (75, 37, 'IKP 5.7', 'Tingkat keberhasilan pengendalian pelaksanaan operasi intelijen yustisial dan penindakan', '419344', 'AVERAGE', '2026', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (76, 38, 'IKP 6.1', 'Persentase penyelamatan kerugian keuangan negara dari penanganan perkara tindak pidana korupsi', '419344', 'RATIO_PERCENTAGE', '2026', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (77, 38, 'IKP 6.2', 'Persentase penyelesaian pembayaran pidana denda perkara tindak pidana khusus lainnya', '419344', 'RATIO_PERCENTAGE', '2026', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (78, 39, 'IKP 15.1', 'Persentase pemanfaatan Case Management System (CMS) bidang Pidsus', '419344', 'RATIO_PERCENTAGE', '2026', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (79, 40, 'IKP 17.1', 'Indeks kepuasan masyarakat terhadap layanan publik bidang Pidsus', '419344', 'INDEX_SCORE', '2026', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (80, 41, 'IKP 18.1', 'Persentase pemenuhan dan pemanfaatan sarana prasarana bidang Pidsus', '419344', 'RATIO_PERCENTAGE', '2026', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (81, 42, 'IKP 3.1', 'Tingkat penyelesaian penanganan perkara tindak pidana umum pada tahap prapenuntutan', '418326', 'RATIO_PERCENTAGE', '2026', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (82, 42, 'IKP 3.2', 'Tingkat penyelesaian penanganan perkara tindak pidana umum pada tahap penuntutan', '418326', 'RATIO_PERCENTAGE', '2026', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (83, 42, 'IKP 3.3', 'Tingkat penyelesaian pelaksanaan putusan pengadilan yang telah berkekuatan hukum tetap (eksekusi)', '418326', 'RATIO_PERCENTAGE', '2026', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (84, 43, 'IKP 4.1', 'Persentase penyelesaian perkara tindak pidana umum melalui mekanisme Keadilan Restoratif (Restorative Justice)', '418326', 'RATIO_PERCENTAGE', '2026', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (85, 43, 'IKP 4.2', 'Persentase pelaksanaan pidana kerja sosial dan pidana pengawasan sebagai alternatif pemidanaan penjara (KUHP Baru)', '418326', 'RATIO_PERCENTAGE', '2026', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (86, 44, 'IKP 15.1', 'Persentase pemanfaatan Case Management System (CMS) bidang Pidum', '418326', 'RATIO_PERCENTAGE', '2026', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (87, 45, 'IKP 17.1', 'Indeks kepuasan masyarakat terhadap layanan publik bidang Pidum', '418326', 'INDEX_SCORE', '2026', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (88, 46, 'IKP 18.1', 'Persentase pemenuhan dan pemanfaatan sarana prasarana bidang Pidum', '418326', 'RATIO_PERCENTAGE', '2026', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (89, 47, 'IKP_PIDUM_MGMT.1', 'Jumlah Jaksa yang telah tersertifikasi/mengikuti diklat teknis tindak pidana umum', '418326', 'DIRECT_INPUT', '2026', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (90, 48, 'IKP 1.1', 'Persentase pemberian pertimbangan hukum (legal audit/legal opinion/legal assistance) yang ditindaklanjuti', '417023', 'RATIO_PERCENTAGE', '2026', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (91, 49, 'IKP 2.1', 'Indeks persepsi kualitas pelayanan hukum Datun', '417023', 'INDEX_SCORE', '2026', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (92, 50, 'IKP 7.1', 'Persentase penyelamatan keuangan/kekayaan negara bidang perdata dan tata usaha negara', '417023', 'RATIO_PERCENTAGE', '2026', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (93, 50, 'IKP 7.2', 'Persentase pemulihan keuangan/kekayaan negara bidang perdata dan tata usaha negara', '417023', 'RATIO_PERCENTAGE', '2026', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (94, 51, 'IKP 8.1', 'Persentase keberhasilan penanganan perkara perdata secara litigasi', '417023', 'RATIO_PERCENTAGE', '2026', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (95, 51, 'IKP 8.2', 'Persentase keberhasilan penyelesaian masalah perdata secara non-litigasi/negosiasi/mediasi', '417023', 'RATIO_PERCENTAGE', '2026', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (96, 51, 'IKP 8.3', 'Persentase keberhasilan penanganan perkara tata usaha negara (TUN)', '417023', 'RATIO_PERCENTAGE', '2026', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (97, 52, 'IKP 16.1', 'Indeks kepuasan pelayanan hukum gratis bidang Datun', '417023', 'INDEX_SCORE', '2026', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (98, 53, 'IKP 17.1', 'Indeks kepuasan masyarakat terhadap layanan publik bidang Datun', '417023', 'INDEX_SCORE', '2026', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (99, 54, 'IKP 18.1', 'Persentase pemenuhan dan pemanfaatan sarana prasarana bidang Datun', '417023', 'RATIO_PERCENTAGE', '2026', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (100, 55, 'IKP 9.1', 'Persentase penyelesaian penanganan perkara koneksitas pada tahap penyelidikan', '677111', 'RATIO_PERCENTAGE', '2026', '2026-10-02 13:54:59', '2026-10-02 13:54:59')
ON DUPLICATE KEY UPDATE `sasaran_program_id` = VALUES(`sasaran_program_id`), `kode_ikp` = VALUES(`kode_ikp`), `nama_ikp` = VALUES(`nama_ikp`), `id_satker` = VALUES(`id_satker`), `sifat_node` = VALUES(`sifat_node`), `tahun` = VALUES(`tahun`), `created_at` = VALUES(`created_at`), `updated_at` = VALUES(`updated_at`);
INSERT INTO `indikator_kinerja_program` (`id`, `sasaran_program_id`, `kode_ikp`, `nama_ikp`, `id_satker`, `sifat_node`, `tahun`, `created_at`, `updated_at`) VALUES
  (101, 55, 'IKP 9.2', 'Persentase penyelesaian penanganan perkara koneksitas pada tahap penyidikan', '677111', 'RATIO_PERCENTAGE', '2026', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (102, 55, 'IKP 9.3', 'Persentase penyelesaian penanganan perkara koneksitas pada tahap penuntutan', '677111', 'RATIO_PERCENTAGE', '2026', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (103, 55, 'IKP 9.4', 'Persentase penyelesaian pelaksanaan putusan pengadilan berkekuatan hukum tetap (eksekusi) perkara koneksitas', '677111', 'RATIO_PERCENTAGE', '2026', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (104, 55, 'IKP 9.5', 'Persentase pengembalian dan pemulihan kerugian keuangan negara akibat tindak pidana koneksitas', '677111', 'RATIO_PERCENTAGE', '2026', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (105, 56, 'IKP 10.1', 'Persentase koordinasi teknis penuntutan pada tahap penindakan perkara koneksitas', '677111', 'RATIO_PERCENTAGE', '2026', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (106, 56, 'IKP 10.2', 'Persentase koordinasi teknis penuntutan pada tahap penuntutan perkara koneksitas', '677111', 'RATIO_PERCENTAGE', '2026', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (107, 56, 'IKP 10.3', 'Persentase koordinasi teknis penuntutan pada tahap eksekusi dan upaya hukum luar biasa', '677111', 'RATIO_PERCENTAGE', '2026', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (108, 57, 'IKP 15.1', 'Persentase pemanfaatan Case Management System (CMS) bidang Pidmil', '677111', 'RATIO_PERCENTAGE', '2026', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (109, 58, 'IKP 16.1', 'Indeks kepuasan relasi kelembagaan TNI dan stakeholder peradilan militer', '677111', 'INDEX_SCORE', '2026', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (110, 59, 'IKP_PIDMIL_MGMT.1', 'Persentase ketepatan waktu penyusunan laporan perkara koneksitas', '677111', 'RATIO_PERCENTAGE', '2026', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (111, 60, 'IKP 9.1', 'Indeks kepuasan pengguna layanan kediklatan Kejaksaan RI', '666405', 'INDEX_SCORE', '2026', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (112, 61, 'IKP 10.1', 'Indeks kepuasan layanan kesekretariatan dan dukungan operasional Badiklat', '666405', 'INDEX_SCORE', '2026', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (113, 62, 'IKP 2.1', 'Opini BPK atas Laporan Keuangan Kejaksaan RI (WTP)', '419346', 'DIRECT_INPUT', '2026', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (114, 63, 'IKP 3.1', 'Nilai Maturitas Penyelenggaraan SPIP Terintegrasi', '419346', 'INDEX_SCORE', '2026', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (115, 64, 'IKP 12.1', 'Nilai Indeks Survei Penilaian Integritas (SPI) KPK di lingkungan Kejaksaan RI', '419346', 'INDEX_SCORE', '2026', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (116, 65, 'IKP 14.1', 'Persentase satuan kerja yang memenuhi kriteria Zona Integritas (WBK/WBBM)', '419346', 'RATIO_PERCENTAGE', '2026', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (117, 66, 'IKP_WAS_OPS.1', 'Persentase penyelesaian tindak lanjut hasil pengawasan (TLHP) internal dan eksternal', '419346', 'RATIO_PERCENTAGE', '2026', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (118, 67, 'IKP_WAS_MGMT.1', 'Persentase pemenuhan standar layanan pengawasan internal sesuai SLA', '419346', 'RATIO_PERCENTAGE', '2026', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (119, 68, 'IKP 11.1', 'Persentase Hasil Kegiatan Pelaksanaan Operasi Intelijen di Bidang Ideologi, Politik, Pertahanan, dan Keamanan', '419345', 'RATIO_PERCENTAGE', '2026', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (120, 68, 'IKP 11.2', 'Persentase Hasil Kegiatan Pelaksanaan Operasi Intelijen di Bidang Sosial Budaya dan Kemasyarakatan', '419345', 'RATIO_PERCENTAGE', '2026', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (121, 68, 'IKP 11.3', 'Persentase Hasil Kegiatan Pelaksanaan Operasi Intelijen di Bidang Ekonomi dan Keuangan', '419345', 'RATIO_PERCENTAGE', '2026', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (122, 68, 'IKP 11.4', 'Persentase Pembangunan Strategis Yang Diberikan Pengawalan', '419345', 'RATIO_PERCENTAGE', '2026', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (123, 68, 'IKP 11.5', 'Persentase Hasil Kegiatan Pelaksanaan Operasi Intelijen di Bidang Teknologi Informasi dan Produksi Intelijen', '419345', 'RATIO_PERCENTAGE', '2026', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (124, 68, 'IKP 11.6', 'Persentase Pelaksanaan Major Project Penguatan NSOC SOC dan Pembentukan 121 CSIRT', '419345', 'RATIO_PERCENTAGE', '2026', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (125, 68, 'IKP 11.7', 'Persentase Lembaga atau Pihak Yang Diberi Penyuluhan Dan Penerangan Hukum', '419345', 'RATIO_PERCENTAGE', '2026', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
  (126, 68, 'IKP 11.8', 'Indeks Kepuasan Pemangku Kepentingan Terhadap Layanan Penyuluhan dan Penerangan Hukum', '419345', 'AVERAGE', '2026', '2026-10-02 13:54:59', '2026-10-02 13:54:59')
ON DUPLICATE KEY UPDATE `sasaran_program_id` = VALUES(`sasaran_program_id`), `kode_ikp` = VALUES(`kode_ikp`), `nama_ikp` = VALUES(`nama_ikp`), `id_satker` = VALUES(`id_satker`), `sifat_node` = VALUES(`sifat_node`), `tahun` = VALUES(`tahun`), `created_at` = VALUES(`created_at`), `updated_at` = VALUES(`updated_at`);

-- -----------------------------------------------------------------------------
-- Pastikan kolom 'tahun' ada pada tabel lke_parameter sebelum data diisi
-- -----------------------------------------------------------------------------
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_parameter' AND COLUMN_NAME = 'tahun';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_parameter` ADD COLUMN `tahun` smallint unsigned NOT NULL DEFAULT 2025 AFTER `updated_at`, ADD INDEX `lke_parameter_tahun_idx` (`tahun`), ADD INDEX `lke_parameter_tahun_kriteria_idx` (`tahun`, `kriteria_id`)', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- -------------------------------------------------------------
-- Data untuk tabel `lke_parameter` (Total: 308 baris)
-- -------------------------------------------------------------
INSERT INTO `lke_parameter` (`id`, `kriteria_id`, `nilai`, `skor`, `keterangan`, `created_at`, `updated_at`, `tahun`) VALUES
  (1, '1.A.1', 'Terpenuhi', 1, 'Pedoman 4 Tahun 2024 TELAH disosialisasikan/didistribusikan kepada seluruh pegawai', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (2, '1.A.1', 'Tidak Terpenuhi', 0, 'Pedoman 4 Tahun 2024 BELUM disosialisasikan/didistribusikan kepada seluruh pegawai', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (3, '1.A.2', 'Terpenuhi', 1, 'Apabila terdapat dokumen RENSTRA Satuan Kerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (4, '1.A.2', 'Tidak Terpenuhi', 0, 'Apabila tidak tersedia dokumen RENSTRA Satuan Kerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (5, '1.A.3', 'Terpenuhi', 1, 'Apabila terdapat dokumen RENJA Satuan Kerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (6, '1.A.3', 'Tidak Terpenuhi', 0, 'Apabila tidak tersedia dokumen RENJA Satuan Kerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (7, '1.A.4', 'Terpenuhi', 1, 'Apabila terdapat dokumen rencana aksi kinerja Satuan Kerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (8, '1.A.4', 'Tidak Terpenuhi', 0, 'Apabila tidak tersedia dokumen rencana aksi kinerja Satuan Kerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (9, '1.A.5', 'Terpenuhi', 1, '2 dokumen tersebut tersedia', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (10, '1.A.5', 'Tidak Terpenuhi', 0, 'Apabila tidak tersedia dokumen', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (11, '1.A.6', 'Terpenuhi', 1, 'Apabila terdapat dokumen Perjanjian Kinerja untuk SELURUH Pejabat Struktural', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (12, '1.A.6', 'Tidak Terpenuhi', 0, 'Apabila tidak tersedia dokumen Perjanjian Kinerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (13, '1.B.1', 'Terpenuhi', 1, 'apabila 4 dokumen tersebut telah ditetapkan dengan Keputusan atau ditandatangani oleh Kepala Satuan Kerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (14, '1.B.1', 'Tidak Terpenuhi', 0, 'apabila hanya 3 dokumen tersebut telah ditetapkan dengan Keputusan atau ditandatangani oleh Kepala Satuan Kerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (15, '1.B.2', 'Terpenuhi', 1, 'Jika 3 dokumen perencanaan tersebut telah dipublikasikan tepat waktu', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (16, '1.B.2', 'Tidak Terpenuhi', 0, 'Kriteria belum terpenuhi: Dokumen Perencanaan telah dipublikasikan tepat waktu', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (17, '1.B.3', 'Terpenuhi', 1, 'Jika terpenuhi 4 kriteria', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (18, '1.B.3', 'Tidak Terpenuhi', 0, 'Jika terpenuhi 3 kriteria', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (19, '1.B.4', 'Terpenuhi', 1, 'Jika seluruh Sasaran Strategis, Sasaran Program, dan Sasaran Kegiatan pada Satuan Kerja telah sesuai dengan Cascading Kinerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (20, '1.B.4', 'Tidak Terpenuhi', 0, 'Jika terdapat Sasaran Strategis, Sasaran Program, dan Sasaran Kegiatan pada Satuan Kerja tidak sesuai dengan Cascading Kinerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (21, '1.B.5', 'Terpenuhi', 1, 'Jika seluruh target kinerja yang ditetapkan oleh satuan kerja lebih besar atau sama dengan target kinerja nasional yang ditetapkan dalam Rencana Strategis Kejaksaan RI.', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (22, '1.B.5', 'Tidak Terpenuhi', 0, 'Jika terdapat target kinerja yang ditetapkan oleh satuan kerja lebih kecil dari target kinerja nasional yang ditetapkan dalam Rencana Strategis Kejaksaan RI.', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (23, '1.B.6', 'Terpenuhi', 1, 'Indikator Kinerja Utama (IKU) telah menggambarkan kondisi kinerja utama secara berkesinambungan (sustainable)', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (24, '1.B.6', 'Tidak Terpenuhi', 0, 'Indikator Kinerja Utama (IKU) belum menggambarkan kondisi kinerja utama secara sustainable / terdapat revisi yang tidak sesuai', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (25, '1.B.7', 'Terpenuhi', 1, 'Terdapat Peningkatan Target per Tahun pada Periode Renstra (5 Tahun) yang menantang secara realistis', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (26, '1.B.7', 'Tidak Terpenuhi', 0, 'Terdapat Peningkatan Target per Tahun pada Renstra (5 Tahun) yang menantang, tetapi tidak realistis', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (27, '1.B.8', 'Terpenuhi', 1, 'Seluruh Dokumen RENSTRA, IKU, dan Perjanjian Kinerja Satuan Kerja telah selaras dan menggambarkan hubungan sebab akibat (Cascading)', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (28, '1.B.8', 'Tidak Terpenuhi', 0, 'Terdapat Dokumen RENSTRA, IKU, dan Perjanjian Kinerja Satuan Kerja yang belum selaras dan menggambarkan hubungan sebab akibat (Cascading)', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (29, '1.B.9', 'Terpenuhi', 1, 'Perencanaan Kinerja telah memberikan informasi keterkaitan lintas bidang/tusi (Crosscutting)', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (30, '1.B.9', 'Tidak Terpenuhi', 0, 'Crosscutting belum tergambar pada dokumen perencanaan kinerja satuan kerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (31, '1.B.10', 'Terpenuhi', 1, 'Seluruh pegawai satuan kerja telah merumuskan dan menetapkan perencanaan kinerja (SKP 100%)', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (32, '1.B.10', 'Tidak Terpenuhi', 0, 'Masih terdapat pegawai yang belum menyusun atau menetapkan SKP perencanaan kinerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (33, '1.C.1', 'Terpenuhi', 1, 'Anggaran dan Target pada RENJA telah selaras Dengan DIPA', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (34, '1.C.1', 'Tidak Terpenuhi', 0, 'Anggaran dan Target pada RENJA belum seluruhnya Selaras Dengan DIPA', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (35, '1.C.2', 'Terpenuhi', 1, 'Seluruh Rencana Aksi memiliki korelasi (hubungan sebab akibat) dengan IKU', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (36, '1.C.2', 'Tidak Terpenuhi', 0, 'Terdapat Rencana Aksi yang tidak memiliki korelasi (hubungan sebab akibat) dengan IKU', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (37, '1.C.3', 'Terpenuhi', 1, 'Seluruh indikator yang diperjanjikan telah tercapai sesuai target/melebihi target (data pada Laporan Kinerja Satuan Kerja)', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (38, '1.C.3', 'Tidak Terpenuhi', 0, 'indikator yang diperjanjikan seluruhnya tidak mencapai target yang telah diperjanjikan (data pada Laporan Kinerja Satuan Kerja)', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (39, '1.C.4', 'Terpenuhi', 1, 'Jika rencana aksi kinerja dipantau/dilaporkan pada Rapat Staf EKA Triwulan I dan II Tahun 2025', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (40, '1.C.4', 'Tidak Terpenuhi', 0, 'Jika rencana aksi kinerja dipantau/dilaporkan pada Rapat Staf EKA hanya pada salah satu triwulan', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (41, '1.C.5', 'Terpenuhi', 1, 'Penetapan target kinerja telah disusun sebagai bentuk penyempurnaan perencanaan kinerja yang ditetapkan dari hasil analisis capaian kinerja/ tren capaian kinerja periode-periode sebelumnya, beban kerja dan ketersediaan anggaran pada masing-masing satuan kerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (42, '1.C.5', 'Tidak Terpenuhi', 0, 'Penetapan target kinerja yang disusun belum sepenuhnya mempertimbangkan hasil analisis capaian kinerja/ tren capaian kinerja periode-periode sebelumnya, beban kerja dan ketersediaan anggaran pada masing-masing satuan kerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (43, '1.C.6', 'Terpenuhi', 1, 'Penetapan target kinerja telah disusun sebagai bentuk penyempurnaan perencanaan kinerja yang ditetapkan dari hasil analisis capaian kinerja/ tren capaian kinerja periode-periode sebelumnya, beban kerja dan ketersediaan anggaran pada masing-masing satuan kerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (44, '1.C.6', 'Tidak Terpenuhi', 0, 'Tidak terdapat dokumen Penetapan Target Kinerja Satuan Kerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (45, '1.C.7', 'Terpenuhi', 1, '1. Satuan Kerja menyusun Laporan Kinerja sesuai dengan Perjanjian Kinerja yang telah ditetapkan setiap triwulan. 2. Satuan Kerja melakukan evaluasi terhadap capaian kinerja setiap triwulan salah satunya melalui Rapat Staf Evaluasi Kinerja dan Anggaran.', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (46, '1.C.7', 'Tidak Terpenuhi', 0, '1. Satuan Kerja menyusun Laporan Kinerja sesuai dengan Perjanjian Kinerja yang telah ditetapkan setiap semester. 2. Satuan Kerja melakukan evaluasi terhadap capaian kinerja setiap semester salah satunya melalui Rapat Staf Evaluasi Kinerja dan Anggaran.', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (47, '1.C.8', 'Terpenuhi', 1, 'Hasil observasi dan atau wawancara menunjukkan bahwa pegawai SUDAH memahami kinerja yang telah direncanakan dan memiliki komitmen untuk mencapai sasaran kinerja tersebut', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (48, '1.C.8', 'Tidak Terpenuhi', 0, 'Hasil observasi dan atau wawancara menunjukkan bahwa pegawai TIDAK memahami kinerja yang telah direncanakan dan memiliki komitmen untuk mencapai sasaran kinerja tersebut', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (49, '2.A.1', 'Terpenuhi', 1, 'Pedoman 4 Tahun 2024 TELAH disosialisasikan/didistribusikan kepada seluruh pegawai', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (50, '2.A.1', 'Tidak Terpenuhi', 0, 'Pedoman 4 Tahun 2024 BELUM disosialisasikan/didistribusikan kepada seluruh pegawai', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025)
ON DUPLICATE KEY UPDATE `kriteria_id` = VALUES(`kriteria_id`), `nilai` = VALUES(`nilai`), `skor` = VALUES(`skor`), `keterangan` = VALUES(`keterangan`), `created_at` = VALUES(`created_at`), `updated_at` = VALUES(`updated_at`), `tahun` = VALUES(`tahun`);
INSERT INTO `lke_parameter` (`id`, `kriteria_id`, `nilai`, `skor`, `keterangan`, `created_at`, `updated_at`, `tahun`) VALUES
  (51, '2.A.2', 'Terpenuhi', 1, 'Jika TELAH tersedia dokumen penetapan target kinerja satuan kerja tahun 2025 yang memuat definisi operasional yang jelas', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (52, '2.A.2', 'Tidak Terpenuhi', 0, 'Jika BELUM tersedia dokumen penetapan target kinerja satuan kerja tahun 2025', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (53, '2.A.3', 'Terpenuhi', 1, 'ika TELAH tersedia Memorandum Kepala Satuan Kerja perihal pengumpulan data kinerja yang dapat diandalkan', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (54, '2.A.3', 'Tidak Terpenuhi', 0, 'Jika BELUM tersedia Memorandum Kepala Satuan Kerja perihal pengumpulan data kinerja yang dapat diandalkan', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (55, '2.B.1', 'Terpenuhi', 1, 'Jika Laporan Rapat Staf Triwulan I dan II TELAH memuat pembahasan terkait hasil pengukuran capaian kinerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (56, '2.B.1', 'Tidak Terpenuhi', 0, 'Jika Laporan Rapat Staf Triwulan I dan II TIDAK memuat pembahasan terkait hasil pengukuran capaian kinerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (57, '2.B.2', 'Terpenuhi', 1, 'Data kinerja pada nota dinas penyampaian data kinerja setiap triwulan seluruhnya sesuai dengan Penetapan Target Kinerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (58, '2.B.2', 'Tidak Terpenuhi', 0, 'Data kinerja pada nota dinas penyampaian data kinerja setiap triwulan seluruhnya tidak sesuai dengan Penetapan Target Kinerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (59, '2.B.3', 'Terpenuhi', 1, 'Data kinerja pada nota dinas penyampaian data kinerja setiap triwulan seluruhnya sesuai dengan Penetapan Target Kinerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (60, '2.B.3', 'Tidak Terpenuhi', 0, 'Data kinerja pada nota dinas penyampaian data kinerja setiap triwulan seluruhnya tidak sesuai dengan Penetapan Target Kinerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (61, '2.B.4', 'Terpenuhi', 1, 'Jika pengukuran telah dilakukan dan dituangkan dalam Laporan Kinerja triwulan I dan II Tahun 2025', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (62, '2.B.4', 'Tidak Terpenuhi', 0, 'Jika pengukuran TIDAK dilakukan dan dituangkan dalam Laporan Kinerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (63, '2.B.5', 'Terpenuhi', 1, 'Jika TERDAPAT Nota dinas penyampaian data kinerja dari seluruh bidang untuk menghitung capaian kinerja kepala satuan kerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (64, '2.B.5', 'Tidak Terpenuhi', 0, 'Jika TIDAK terdapat Nota dinas penyampaian data kinerja dari seluruh bidang untuk menghitung capaian kinerja kepala satuan kerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (65, '2.B.6', 'Terpenuhi', 1, 'Satuan Kerja telah melakukan penginputan data kinerja ke dalam aplikasi (SICANA/aplikasi kinerja) secara tertib', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (66, '2.B.6', 'Tidak Terpenuhi', 0, 'Satuan Kerja belum melakukan penginputan data kinerja ke dalam aplikasi kinerja secara tertib', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (67, '2.B.7', 'Terpenuhi', 1, 'Hasil pengukuran pada apllikasi SICANA sama dengan hasil pengukuran yang dituangkan pada laporan kinerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (68, '2.B.7', 'Tidak Terpenuhi', 0, 'Hasil pengukuran pada apllikasi SICANA tidak sama dengan hasil pengukuran yang dituangkan pada laporan kinerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (69, '2.C.1', 'Terpenuhi', 1, 'Satuan kerja telah memberikan apresiasi/ penghargaan DAN pembinaan/punishment secara internal kepada satuan kerja/unit kerja dibawahnya berdasarkan pengukuran kinerja yang telah dilaksanakan', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (70, '2.C.1', 'Tidak Terpenuhi', 0, 'Satuan kerja TIDAK memberikan apresiasi/ penghargaan maupun pembinaan/punishment secara internal kepada satuan kerja/unit kerja dibawahnya berdasarkan pengukuran kinerja yang telah dilaksanakan', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (71, '2.C.2', 'Terpenuhi', 1, 'Jika TERDAPAT sampel dokumen Rekomendasi Kenaikan Pangkat/Promosi/Mutasi yang dilengkapi dengan SKP dan clearence', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (72, '2.C.2', 'Tidak Terpenuhi', 0, 'Jika TIDAK terdapat sampel dokumen Rekomendasi Kenaikan Pangkat/Promosi/Mutasi yang dilengkapi dengan SKP dan clearence', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (73, '2.C.3', 'Terpenuhi', 1, 'Jika Laporan Rapat Staf Triwulan I dan II TELAH memuat uraian target, realisasi, dan strategi optimalisasi capaian kinerja pada periode selanjutnya dalam rangka pemenuhan target kinerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (74, '2.C.3', 'Tidak Terpenuhi', 0, 'Jika Laporan Rapat Staf Triwulan I dan II TIDAK memuat uraian target, realisasi, dan strategi optimalisasi capaian kinerja pada periode selanjutnya dalam rangka pemenuhan target kinerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (75, '2.C.4', 'Terpenuhi', 1, 'Jika Laporan Rapat Staf Triwulan I dan II TELAH memuat penyesuaian pada kegiatan (bisa dalam bentuk perubahan jadwal kegiatan untuk periode selanjutnya) berdasarkan pengukuran kinerja yang telah dilakukan', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (76, '2.C.4', 'Tidak Terpenuhi', 0, 'Jika Laporan Rapat Staf Triwulan I dan II TIDAK memuat penyesuaian pada kegiatan (bisa dalam bentuk perubahan jadwal kegiatan untuk periode selanjutnya) berdasarkan pengukuran kinerja yang telah dilakukan', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (77, '2.C.5', 'Terpenuhi', 1, 'Jika Laporan Rapat Staf Triwulan I dan II TELAH memuat optimalisasi anggaran kegiatan (untuk periode selanjutnya) berdasarkan pengukuran kinerja yang telah dilakukan', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (78, '2.C.5', 'Tidak Terpenuhi', 0, 'Jika Laporan Rapat Staf Triwulan I dan II TIDAK memuat optimalisasi anggaran kegiatan (untuk periode selanjutnya) berdasarkan pengukuran kinerja yang telah dilakukan', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (79, '2.C.6', 'Terpenuhi', 1, 'Rata-rata capaian Indikator Kinerja Sasaran Strategis ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€šÃ‚Â°ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¥ 100% DAN realisasi anggaran ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€šÃ‚Â°ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¥95%', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (80, '2.C.6', 'Tidak Terpenuhi', 0, 'Rata-rata capaian Indikator Kinerja Sasaran Strategis TIDAK mencapai 100%', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (81, '2.C.7', 'Terpenuhi', 1, 'Hasil observasi dan atau wawancara menunjukkan bahwa pegawai SUDAH memahami dan peduli atas hasil pengukuran kinerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (82, '2.C.7', 'Tidak Terpenuhi', 0, 'Hasil observasi dan atau wawancara menunjukkan bahwa pegawai TIDAK memahami dan peduli atas hasil pengukuran kinerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (83, '2.C.8', 'Terpenuhi', 1, 'Hasil observasi dan atau wawancara menunjukkan bahwa pegawai SUDAH memahami dan peduli atas hasil pengukuran kinerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (84, '2.C.8', 'Tidak Terpenuhi', 0, 'Hasil observasi dan atau wawancara menunjukkan bahwa pegawai TIDAK memahami dan peduli atas hasil pengukuran kinerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (85, '3.A.1', 'Terpenuhi', 1, 'Satuan Kerja Telah Menyusun LKJIP Triwulan I dan II Tahun 2025', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (86, '3.A.1', 'Tidak Terpenuhi', 0, 'Satuan Kerja TIDAK Menyusun LKJIP Triwulan I dan II Tahun 2025', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (87, '3.A.2', 'Terpenuhi', 1, 'Jika telah disusun Laporan Kinerja triwulan I dan II Tahun 2025', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (88, '3.A.2', 'Tidak Terpenuhi', 0, 'Jika TIDAK dilakukan penyusunan Laporan Kinerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (89, '3.A.3', 'Terpenuhi', 1, 'Jika Laporan Kinerja triwulan I dan II Tahun 2025 telah ditandatangani oleh Kepala Satuan Kerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (90, '3.A.3', 'Tidak Terpenuhi', 0, 'Jika Laporan Kinerja TIDAK ditandatangani oleh Kepala Satuan Kerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (91, '3.A.4', 'Terpenuhi', 1, 'Apabila laporan kinerja satuan kerja triwulan IV /Laporan kinerja tahunan telah direviu APIP pada Bidang Pengawasan', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (92, '3.A.4', 'Tidak Terpenuhi', 0, 'Apabila laporan kinerja satuan kerja triwulan IV /Laporan kinerja tahunan tidak direviu APIP pada Bidang Pengawasan', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (93, '3.A.5', 'Terpenuhi', 1, 'Apabila Laporan Kinerja telah dipublikasi melalui website masing-masing.', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (94, '3.A.5', 'Tidak Terpenuhi', 0, 'Apabila Laporan Kinerja belum dipublikasi melalui website masing-masing satuan kerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (95, '3.A.6', 'Terpenuhi', 1, 'Bahwa seluruh laporan kinerja telah disampaikan secara tepat waktu (Sesuai dengan Pedoman 4 Tahun 2024 bahwa Laporan Kinerja dilaporkan atau diupload melalui ESR dan SICANA paling lambat 30 Hari setelah triwulan berakhir)', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (96, '3.A.6', 'Tidak Terpenuhi', 0, 'Apabila terdapat laporan kinerja yang dilaporkan/diupload tidak tepat waktu', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (97, '3.B.1', 'Terpenuhi', 1, 'Apabila seluruh laporan kinerja telah disusun sesuai standar yang terdiri dari BAB I - BAB IV sesuai dengan pedoman nomor 4 tahun 2024 yang telah sesuai dengan dokumen Perjanjian Kinerja Kepala Satuan Kerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (98, '3.B.1', 'Tidak Terpenuhi', 0, 'Apabila laporan kinerja telah disusun sesuai standar yang terdiri dari BAB I - BAB IV sesuai dengan pedoman nomor 4 tahun 2024 namun terdapat beberapa indikator yang dilaporkan dalam Laporan Kinerja tidak sesuai dengan dokumen Perjanjian Kinerja Kepala Satuan Kerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (99, '3.B.2', 'Terpenuhi', 1, 'Apabila Laporan Kinerja telah memuat capaian kinerja dari seluruh indikator pada Perjanjian Kinerja (sesuai dengan format yang telah diberikan dari Kejaksaan Agung) indikator dalam PK lengkap', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (100, '3.B.2', 'Tidak Terpenuhi', 0, 'Apabila indikator yang disajikan dalam Laporan Kinerja tidak ada', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025)
ON DUPLICATE KEY UPDATE `kriteria_id` = VALUES(`kriteria_id`), `nilai` = VALUES(`nilai`), `skor` = VALUES(`skor`), `keterangan` = VALUES(`keterangan`), `created_at` = VALUES(`created_at`), `updated_at` = VALUES(`updated_at`), `tahun` = VALUES(`tahun`);
INSERT INTO `lke_parameter` (`id`, `kriteria_id`, `nilai`, `skor`, `keterangan`, `created_at`, `updated_at`, `tahun`) VALUES
  (101, '3.B.3', 'Terpenuhi', 1, 'Apabila terdapat perbandingan realisasi kinerja dengan target kinerja tahunan untuk seluruh indikator kinerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (102, '3.B.3', 'Tidak Terpenuhi', 0, 'Tidak terdapat perbandingan realisasi kinerja dengan target kinerja tahunan', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (103, '3.B.4', 'Terpenuhi', 1, 'Apabila pada laporan triwulan II terdapat perbandingan realisasi kinerja dengan kinerja triwulan sebelumnya (mencakup seluruh indikator)', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (104, '3.B.4', 'Tidak Terpenuhi', 0, 'tidak terdapat perbandingan kinerja dengan triwulan sebelumnya', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (105, '3.B.5', 'Terpenuhi', 1, 'apabila pada laporan tahunan terdapat perbandingan realisasi kinerja dengan kinerja tahun-tahun sebelumnya (mencakup seluruh indikator)', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (106, '3.B.5', 'Tidak Terpenuhi', 0, 'tidak terdapat perbandingan kinerja dengan tahun sebelumnya', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (107, '3.B.6', 'Terpenuhi', 1, 'apabila seluruh indikator pada Laporan Kinerja telah memuat analisa faktor-faktor yang mempengaruhi keberhasilan, dan hambatan yang dihadapi serta upaya nyata yang telah dilakukan', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (108, '3.B.6', 'Tidak Terpenuhi', 0, 'terdapat laporan kinerja namun tidak terinformasikan hambatan atau upaya nyata untuk mencapai target kinerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (109, '3.B.7', 'Terpenuhi', 1, 'apabila dalam laporan kinerja telah diinformasikan tentang efisiensi sumber daya baik sumber daya manusia maupun anggaran dalam mencapai kinerja.', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (110, '3.B.7', 'Tidak Terpenuhi', 0, 'apabila dalam laporan kinerja belum menginformasikan tentang efisiensi sumber daya baik sumber daya manusia maupun anggaran dalam mencapai kinerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (111, '3.B.8', 'Terpenuhi', 1, 'Apabila Laporan Kinerja telah memuat strategi peningkatan capaian kinerja untuk periode selanjutnya pada seluruh indikator', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (112, '3.B.8', 'Tidak Terpenuhi', 0, 'Apabila tidak terdapat informasi upaya perbaikan dan penyempurnaan kinerja untuk periode selanjutnya pada Laporan Kinerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (113, '3.C.1', 'Terpenuhi', 1, 'Jika Laporan Rapat Staf Triwulan I dan II TELAH yang memuat pembahasan laporan kinerja serta dinamika kelompok untuk pencapaian target kinerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (114, '3.C.1', 'Tidak Terpenuhi', 0, 'Jika Laporan Rapat Staf Triwulan I dan II TIDAK yang memuat pembahasan laporan kinerja serta dinamika kelompok untuk pencapaian target kinerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (115, '3.C.2', 'Terpenuhi', 1, 'Hasil observasi dan atau wawancara menunjukkan bahwa pegawai SUDAH memahami pegawai informasi di dalam laporan kinerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (116, '3.C.2', 'Tidak Terpenuhi', 0, 'Hasil observasi dan atau wawancara menunjukkan bahwa pegawai BELUM memahami pegawai informasi di dalam laporan kinerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (117, '3.C.3', 'Terpenuhi', 1, 'Jika Laporan Kinerja Satuan Kerja Triwulan I dan II Tahun 2025 TELAH memuat penyesuaian pada kegiatan (bisa dalam bentuk perubahan jadwal kegiatan untuk periode selanjutnya) berdasarkan informasi dalam laporan kinerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (118, '3.C.3', 'Tidak Terpenuhi', 0, 'Jika Laporan Kinerja Satuan Kerja Triwulan I dan II Tahun 2025 TIDAK memuat penyesuaian pada kegiatan (bisa dalam bentuk perubahan jadwal kegiatan untuk periode selanjutnya) berdasarkan informasi dalam laporan kinerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (119, '3.C.4', 'Terpenuhi', 1, 'Jika Laporan Kinerja Satuan Kerja Triwulan I dan II Tahun 2025 TELAH memuat optimalisasi anggaran kegiatan (untuk periode selanjutnya) berdasarkan informasi dalam laporan kinerja)', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (120, '3.C.4', 'Tidak Terpenuhi', 0, 'Jika Laporan Kinerja Satuan Kerja Triwulan I dan II Tahun 2025 TIDAK memuat optimalisasi anggaran kegiatan (untuk periode selanjutnya) berdasarkan informasi dalam laporan kinerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (121, '3.C.5', 'Terpenuhi', 1, 'Jika Laporan Kinerja Satuan Kerja Triwulan I dan II Tahun 2025 TELAH memuat evaluasi pencapaian keberhasilan kinerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (122, '3.C.5', 'Tidak Terpenuhi', 0, 'Jika Laporan Kinerja Satuan Kerja Triwulan I dan II Tahun 2025 TIDAK memuat evaluasi pencapaian keberhasilan kinerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (123, '3.C.6', 'Terpenuhi', 1, 'Penetapan target kinerja TELAH disusun sebagai bentuk penyesuaian perencanaan kinerja berdasarkan informasi yang terdapat pada laporan kinerja periode sebelumnya', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (124, '3.C.6', 'Tidak Terpenuhi', 0, 'Penetapan target kinerja BELUM disusun sebagai bentuk penyesuaian perencanaan kinerja berdasarkan informasi yang terdapat pada laporan kinerja periode sebelumnya', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (125, '3.C.7', 'Terpenuhi', 1, 'Hasil observasi dan atau wawancara menunjukkan bahwa SUDAH terpenuhi/terjadiperubahan budaya kinerja organisasi yang berpedoman pada laporan kinerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (126, '3.C.7', 'Tidak Terpenuhi', 0, 'Hasil observasi dan atau wawancara menunjukkan bahwa BELUM terpenuhi/terjadi perubahan budaya kinerja organisasi yang berpedoman pada laporan kinerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (127, '4.A.1', 'Terpenuhi', 1, 'Pedoman 4 Tahun 2025 TELAH disosialisasikan/didistribusikan kepada seluruh pegawai', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (128, '4.A.1', 'Tidak Terpenuhi', 0, 'Pedoman 4 Tahun 2025 BELUM disosialisasikan/didistribusikan kepada seluruh pegawai', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (129, '4.A.2', 'Terpenuhi', 1, 'Jika TERDAPAT surat penyampaian PM AKIP INTERNAL berserta LKE kepada Jaksa Agung Muda Bidang Pengawasan', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (130, '4.A.2', 'Tidak Terpenuhi', 0, 'Jika TIDAK terdapat Surat penyampaian PM AKIP INTERNAL berserta LKE kepada Jaksa Agung Muda Bidang Pengawasan', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (131, '4.A.3', 'Terpenuhi', 1, 'Jika TERDAPAT surat penyampaian PM AKIP INTERNAL berserta LKE kepada Jaksa Agung Muda Bidang Pengawasan', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (132, '4.A.3', 'Tidak Terpenuhi', 0, 'Jika TIDAK terdapat Surat penyampaian PM AKIP INTERNAL berserta LKE kepada Jaksa Agung Muda Bidang Pengawasan', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (133, '4.B.1', 'Terpenuhi', 1, 'Jika TERDAPAT surat penyampaian PM AKIP Internal berserta LKE kepada Jaksa Agung Muda Bidang Pengawasan', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (134, '4.B.1', 'Tidak Terpenuhi', 0, 'Jika TIDAK terdapat Surat penyampaian PM AKIP Internal berserta LKE kepada Jaksa Agung Muda Bidang Pengawasan', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (135, '4.B.2', 'Terpenuhi', 1, 'Tersedia notulensi dan dokumentasi atas pelaksanaan sosialisasi/bimbingan teknis/pengarahan terkait SAKIP kepada kepala satuan kerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (136, '4.B.2', 'Tidak Terpenuhi', 0, 'SDM Tim Penilai Mandiri telah mengikuti sosialisasi/bimbingan teknis/pengarahan terkait Sistem Akuntabilitas Kinerja Instansi Pemerintah', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (137, '4.B.3', 'Terpenuhi', 1, 'Jika TERDAPAT surat penyampaian PM AKIP INTERNAL berserta LKE kepada Jaksa Agung Muda Bidang Pengawasan', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (138, '4.B.3', 'Tidak Terpenuhi', 0, 'Jika TIDAK terdapat Surat penyampaian PM AKIP INTERNAL berserta LKE kepada Jaksa Agung Muda Bidang Pengawasan', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (139, '4.B.4', 'Terpenuhi', 1, 'Jika TERDAPAT surat penyampaian PM AKIP INTERNAL berserta LKE kepada Jaksa Agung Muda Bidang Pengawasan', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (140, '4.B.4', 'Tidak Terpenuhi', 0, 'Jika TIDAK terdapat Surat penyampaian PM AKIP INTERNAL berserta LKE kepada Jaksa Agung Muda Bidang Pengawasan', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (141, '4.B.5', 'Terpenuhi', 1, 'Jika TERDAPAT surat penyampaian PM AKIP INTERNAL berserta LKE kepada Jaksa Agung Muda Bidang Pengawasan', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (142, '4.B.5', 'Tidak Terpenuhi', 0, 'Jika TIDAK terdapat Surat penyampaian PM AKIP INTERNAL berserta LKE kepada Jaksa Agung Muda Bidang Pengawasan', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (143, '4.C.1', 'Terpenuhi', 1, 'Seluruh rekomendasi hasil evaluasi AKIP internal tahun sebelumnya telah ditindaklanjuti (100%)', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (144, '4.C.1', 'Tidak Terpenuhi', 0, 'Rekomendasi hasil evaluasi AKIP internal tahun sebelumnya belum seluruhnya ditindaklanjuti', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (145, '4.C.2', 'Terpenuhi', 1, 'Jika tidak ada hasil evaluasi dan rekomendasi berulang pada LHE T-1 dibandingkan LHE T-2', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (146, '4.C.2', 'Tidak Terpenuhi', 0, 'Jika masih TERDAPAT hasil evaluasi dan rekomendasi berulang pada LHE T-1 dibandingkan LHE T-2', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (147, '4.C.3', 'Terpenuhi', 1, 'Nilai LHE meningkat dari tahun sebelumnya', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (148, '4.C.3', 'Tidak Terpenuhi', 0, 'Nilai LHE menurun dari tahun sebelumnya', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (149, '4.C.4', 'Terpenuhi', 1, 'Jika telah dilakukan MONITORING SECARA PERIODIK atas capaian rencana aksi tindak lanjut hasil evaluasi dan rekomendasi AKIP', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (150, '4.C.4', 'Tidak Terpenuhi', 0, 'Jika TIDAK terdapat dokumen Rencana Aksi tindak lanjut pemenuhan evaluasi dan rekomendasi AKIP', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025)
ON DUPLICATE KEY UPDATE `kriteria_id` = VALUES(`kriteria_id`), `nilai` = VALUES(`nilai`), `skor` = VALUES(`skor`), `keterangan` = VALUES(`keterangan`), `created_at` = VALUES(`created_at`), `updated_at` = VALUES(`updated_at`), `tahun` = VALUES(`tahun`);
INSERT INTO `lke_parameter` (`id`, `kriteria_id`, `nilai`, `skor`, `keterangan`, `created_at`, `updated_at`, `tahun`) VALUES
  (151, '4.C.5', 'Terpenuhi', 1, 'Telah terjadi perbaikan dan peningkatan capaian Indikator Kinerja Sasaran Strategis (IKSS)', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (152, '4.C.5', 'Tidak Terpenuhi', 0, 'Belum terjadi perbaikan atau peningkatan capaian Indikator Kinerja Sasaran Strategis (IKSS)', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (153, '4.C.6', 'Terpenuhi', 1, 'Jika hasil dari evaluasi dan rekomendasi pada LHE T-1 TELAH menjadi Rencana Aksi Kinerja tahun berjalan', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (154, '4.C.6', 'Tidak Terpenuhi', 0, 'Jika hasil dari evaluasi dan rekomendasi pada LHE T-1 BELUM menjadi Rencana Aksi Kinerja tahun berjalan', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2025),
  (155, '1.A.1', 'Terpenuhi', 1, 'Pedoman 4 Tahun 2024 TELAH disosialisasikan/didistribusikan kepada seluruh pegawai', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (156, '1.A.1', 'Tidak Terpenuhi', 0, 'Pedoman 4 Tahun 2024 BELUM disosialisasikan/didistribusikan kepada seluruh pegawai', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (157, '1.A.2', 'Terpenuhi', 1, 'Apabila terdapat dokumen RENSTRA Satuan Kerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (158, '1.A.2', 'Tidak Terpenuhi', 0, 'Apabila tidak tersedia dokumen RENSTRA Satuan Kerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (159, '1.A.3', 'Terpenuhi', 1, 'Apabila terdapat dokumen RENJA Satuan Kerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (160, '1.A.3', 'Tidak Terpenuhi', 0, 'Apabila tidak tersedia dokumen RENJA Satuan Kerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (161, '1.A.4', 'Terpenuhi', 1, 'Apabila terdapat dokumen rencana aksi kinerja Satuan Kerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (162, '1.A.4', 'Tidak Terpenuhi', 0, 'Apabila tidak tersedia dokumen rencana aksi kinerja Satuan Kerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (163, '1.A.5', 'Terpenuhi', 1, '2 dokumen tersebut tersedia', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (164, '1.A.5', 'Tidak Terpenuhi', 0, 'Apabila tidak tersedia dokumen', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (165, '1.A.6', 'Terpenuhi', 1, 'Apabila terdapat dokumen Perjanjian Kinerja untuk SELURUH Pejabat Struktural', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (166, '1.A.6', 'Tidak Terpenuhi', 0, 'Apabila tidak tersedia dokumen Perjanjian Kinerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (167, '1.B.1', 'Terpenuhi', 1, 'apabila 4 dokumen tersebut telah ditetapkan dengan Keputusan atau ditandatangani oleh Kepala Satuan Kerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (168, '1.B.1', 'Tidak Terpenuhi', 0, 'apabila hanya 3 dokumen tersebut telah ditetapkan dengan Keputusan atau ditandatangani oleh Kepala Satuan Kerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (169, '1.B.2', 'Terpenuhi', 1, 'Jika 3 dokumen perencanaan tersebut telah dipublikasikan tepat waktu', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (170, '1.B.2', 'Tidak Terpenuhi', 0, 'Kriteria belum terpenuhi: Dokumen Perencanaan telah dipublikasikan tepat waktu', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (171, '1.B.3', 'Terpenuhi', 1, 'Jika terpenuhi 4 kriteria', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (172, '1.B.3', 'Tidak Terpenuhi', 0, 'Jika terpenuhi 3 kriteria', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (173, '1.B.4', 'Terpenuhi', 1, 'Jika seluruh Sasaran Strategis, Sasaran Program, dan Sasaran Kegiatan pada Satuan Kerja telah sesuai dengan Cascading Kinerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (174, '1.B.4', 'Tidak Terpenuhi', 0, 'Jika terdapat Sasaran Strategis, Sasaran Program, dan Sasaran Kegiatan pada Satuan Kerja tidak sesuai dengan Cascading Kinerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (175, '1.B.5', 'Terpenuhi', 1, 'Jika seluruh target kinerja yang ditetapkan oleh satuan kerja lebih besar atau sama dengan target kinerja nasional yang ditetapkan dalam Rencana Strategis Kejaksaan RI.', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (176, '1.B.5', 'Tidak Terpenuhi', 0, 'Jika terdapat target kinerja yang ditetapkan oleh satuan kerja lebih kecil dari target kinerja nasional yang ditetapkan dalam Rencana Strategis Kejaksaan RI.', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (177, '1.B.6', 'Terpenuhi', 1, 'Indikator Kinerja Utama (IKU) telah menggambarkan kondisi kinerja utama secara berkesinambungan (sustainable)', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (178, '1.B.6', 'Tidak Terpenuhi', 0, 'Indikator Kinerja Utama (IKU) belum menggambarkan kondisi kinerja utama secara sustainable / terdapat revisi yang tidak sesuai', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (179, '1.B.7', 'Terpenuhi', 1, 'Terdapat Peningkatan Target per Tahun pada Periode Renstra (5 Tahun) yang menantang secara realistis', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (180, '1.B.7', 'Tidak Terpenuhi', 0, 'Terdapat Peningkatan Target per Tahun pada Renstra (5 Tahun) yang menantang, tetapi tidak realistis', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (181, '1.B.8', 'Terpenuhi', 1, 'Seluruh Dokumen RENSTRA, IKU, dan Perjanjian Kinerja Satuan Kerja telah selaras dan menggambarkan hubungan sebab akibat (Cascading)', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (182, '1.B.8', 'Tidak Terpenuhi', 0, 'Terdapat Dokumen RENSTRA, IKU, dan Perjanjian Kinerja Satuan Kerja yang belum selaras dan menggambarkan hubungan sebab akibat (Cascading)', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (183, '1.B.9', 'Terpenuhi', 1, 'Perencanaan Kinerja telah memberikan informasi keterkaitan lintas bidang/tusi (Crosscutting)', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (184, '1.B.9', 'Tidak Terpenuhi', 0, 'Crosscutting belum tergambar pada dokumen perencanaan kinerja satuan kerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (185, '1.B.10', 'Terpenuhi', 1, 'Seluruh pegawai satuan kerja telah merumuskan dan menetapkan perencanaan kinerja (SKP 100%)', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (186, '1.B.10', 'Tidak Terpenuhi', 0, 'Masih terdapat pegawai yang belum menyusun atau menetapkan SKP perencanaan kinerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (187, '1.C.1', 'Terpenuhi', 1, 'Anggaran dan Target pada RENJA telah selaras Dengan DIPA', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (188, '1.C.1', 'Tidak Terpenuhi', 0, 'Anggaran dan Target pada RENJA belum seluruhnya Selaras Dengan DIPA', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (189, '1.C.2', 'Terpenuhi', 1, 'Seluruh Rencana Aksi memiliki korelasi (hubungan sebab akibat) dengan IKU', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (190, '1.C.2', 'Tidak Terpenuhi', 0, 'Terdapat Rencana Aksi yang tidak memiliki korelasi (hubungan sebab akibat) dengan IKU', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (191, '1.C.3', 'Terpenuhi', 1, 'Seluruh indikator yang diperjanjikan telah tercapai sesuai target/melebihi target (data pada Laporan Kinerja Satuan Kerja)', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (192, '1.C.3', 'Tidak Terpenuhi', 0, 'indikator yang diperjanjikan seluruhnya tidak mencapai target yang telah diperjanjikan (data pada Laporan Kinerja Satuan Kerja)', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (193, '1.C.4', 'Terpenuhi', 1, 'Jika rencana aksi kinerja dipantau/dilaporkan pada Rapat Staf EKA Triwulan I dan II Tahun 2025', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (194, '1.C.4', 'Tidak Terpenuhi', 0, 'Jika rencana aksi kinerja dipantau/dilaporkan pada Rapat Staf EKA hanya pada salah satu triwulan', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (195, '1.C.5', 'Terpenuhi', 1, 'Penetapan target kinerja telah disusun sebagai bentuk penyempurnaan perencanaan kinerja yang ditetapkan dari hasil analisis capaian kinerja/ tren capaian kinerja periode-periode sebelumnya, beban kerja dan ketersediaan anggaran pada masing-masing satuan kerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (196, '1.C.5', 'Tidak Terpenuhi', 0, 'Penetapan target kinerja yang disusun belum sepenuhnya mempertimbangkan hasil analisis capaian kinerja/ tren capaian kinerja periode-periode sebelumnya, beban kerja dan ketersediaan anggaran pada masing-masing satuan kerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (197, '1.C.6', 'Terpenuhi', 1, 'Penetapan target kinerja telah disusun sebagai bentuk penyempurnaan perencanaan kinerja yang ditetapkan dari hasil analisis capaian kinerja/ tren capaian kinerja periode-periode sebelumnya, beban kerja dan ketersediaan anggaran pada masing-masing satuan kerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (198, '1.C.6', 'Tidak Terpenuhi', 0, 'Tidak terdapat dokumen Penetapan Target Kinerja Satuan Kerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (199, '1.C.7', 'Terpenuhi', 1, '1. Satuan Kerja menyusun Laporan Kinerja sesuai dengan Perjanjian Kinerja yang telah ditetapkan setiap triwulan. 2. Satuan Kerja melakukan evaluasi terhadap capaian kinerja setiap triwulan salah satunya melalui Rapat Staf Evaluasi Kinerja dan Anggaran.', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (200, '1.C.7', 'Tidak Terpenuhi', 0, '1. Satuan Kerja menyusun Laporan Kinerja sesuai dengan Perjanjian Kinerja yang telah ditetapkan setiap semester. 2. Satuan Kerja melakukan evaluasi terhadap capaian kinerja setiap semester salah satunya melalui Rapat Staf Evaluasi Kinerja dan Anggaran.', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026)
ON DUPLICATE KEY UPDATE `kriteria_id` = VALUES(`kriteria_id`), `nilai` = VALUES(`nilai`), `skor` = VALUES(`skor`), `keterangan` = VALUES(`keterangan`), `created_at` = VALUES(`created_at`), `updated_at` = VALUES(`updated_at`), `tahun` = VALUES(`tahun`);
INSERT INTO `lke_parameter` (`id`, `kriteria_id`, `nilai`, `skor`, `keterangan`, `created_at`, `updated_at`, `tahun`) VALUES
  (201, '1.C.8', 'Terpenuhi', 1, 'Hasil observasi dan atau wawancara menunjukkan bahwa pegawai SUDAH memahami kinerja yang telah direncanakan dan memiliki komitmen untuk mencapai sasaran kinerja tersebut', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (202, '1.C.8', 'Tidak Terpenuhi', 0, 'Hasil observasi dan atau wawancara menunjukkan bahwa pegawai TIDAK memahami kinerja yang telah direncanakan dan memiliki komitmen untuk mencapai sasaran kinerja tersebut', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (203, '2.A.1', 'Terpenuhi', 1, 'Pedoman 4 Tahun 2024 TELAH disosialisasikan/didistribusikan kepada seluruh pegawai', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (204, '2.A.1', 'Tidak Terpenuhi', 0, 'Pedoman 4 Tahun 2024 BELUM disosialisasikan/didistribusikan kepada seluruh pegawai', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (205, '2.A.2', 'Terpenuhi', 1, 'Jika TELAH tersedia dokumen penetapan target kinerja satuan kerja tahun 2025 yang memuat definisi operasional yang jelas', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (206, '2.A.2', 'Tidak Terpenuhi', 0, 'Jika BELUM tersedia dokumen penetapan target kinerja satuan kerja tahun 2025', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (207, '2.A.3', 'Terpenuhi', 1, 'ika TELAH tersedia Memorandum Kepala Satuan Kerja perihal pengumpulan data kinerja yang dapat diandalkan', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (208, '2.A.3', 'Tidak Terpenuhi', 0, 'Jika BELUM tersedia Memorandum Kepala Satuan Kerja perihal pengumpulan data kinerja yang dapat diandalkan', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (209, '2.B.1', 'Terpenuhi', 1, 'Jika Laporan Rapat Staf Triwulan I dan II TELAH memuat pembahasan terkait hasil pengukuran capaian kinerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (210, '2.B.1', 'Tidak Terpenuhi', 0, 'Jika Laporan Rapat Staf Triwulan I dan II TIDAK memuat pembahasan terkait hasil pengukuran capaian kinerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (211, '2.B.2', 'Terpenuhi', 1, 'Data kinerja pada nota dinas penyampaian data kinerja setiap triwulan seluruhnya sesuai dengan Penetapan Target Kinerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (212, '2.B.2', 'Tidak Terpenuhi', 0, 'Data kinerja pada nota dinas penyampaian data kinerja setiap triwulan seluruhnya tidak sesuai dengan Penetapan Target Kinerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (213, '2.B.3', 'Terpenuhi', 1, 'Data kinerja pada nota dinas penyampaian data kinerja setiap triwulan seluruhnya sesuai dengan Penetapan Target Kinerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (214, '2.B.3', 'Tidak Terpenuhi', 0, 'Data kinerja pada nota dinas penyampaian data kinerja setiap triwulan seluruhnya tidak sesuai dengan Penetapan Target Kinerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (215, '2.B.4', 'Terpenuhi', 1, 'Jika pengukuran telah dilakukan dan dituangkan dalam Laporan Kinerja triwulan I dan II Tahun 2025', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (216, '2.B.4', 'Tidak Terpenuhi', 0, 'Jika pengukuran TIDAK dilakukan dan dituangkan dalam Laporan Kinerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (217, '2.B.5', 'Terpenuhi', 1, 'Jika TERDAPAT Nota dinas penyampaian data kinerja dari seluruh bidang untuk menghitung capaian kinerja kepala satuan kerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (218, '2.B.5', 'Tidak Terpenuhi', 0, 'Jika TIDAK terdapat Nota dinas penyampaian data kinerja dari seluruh bidang untuk menghitung capaian kinerja kepala satuan kerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (219, '2.B.6', 'Terpenuhi', 1, 'Satuan Kerja telah melakukan penginputan data kinerja ke dalam aplikasi (SICANA/aplikasi kinerja) secara tertib', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (220, '2.B.6', 'Tidak Terpenuhi', 0, 'Satuan Kerja belum melakukan penginputan data kinerja ke dalam aplikasi kinerja secara tertib', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (221, '2.B.7', 'Terpenuhi', 1, 'Hasil pengukuran pada apllikasi SICANA sama dengan hasil pengukuran yang dituangkan pada laporan kinerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (222, '2.B.7', 'Tidak Terpenuhi', 0, 'Hasil pengukuran pada apllikasi SICANA tidak sama dengan hasil pengukuran yang dituangkan pada laporan kinerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (223, '2.C.1', 'Terpenuhi', 1, 'Satuan kerja telah memberikan apresiasi/ penghargaan DAN pembinaan/punishment secara internal kepada satuan kerja/unit kerja dibawahnya berdasarkan pengukuran kinerja yang telah dilaksanakan', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (224, '2.C.1', 'Tidak Terpenuhi', 0, 'Satuan kerja TIDAK memberikan apresiasi/ penghargaan maupun pembinaan/punishment secara internal kepada satuan kerja/unit kerja dibawahnya berdasarkan pengukuran kinerja yang telah dilaksanakan', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (225, '2.C.2', 'Terpenuhi', 1, 'Jika TERDAPAT sampel dokumen Rekomendasi Kenaikan Pangkat/Promosi/Mutasi yang dilengkapi dengan SKP dan clearence', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (226, '2.C.2', 'Tidak Terpenuhi', 0, 'Jika TIDAK terdapat sampel dokumen Rekomendasi Kenaikan Pangkat/Promosi/Mutasi yang dilengkapi dengan SKP dan clearence', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (227, '2.C.3', 'Terpenuhi', 1, 'Jika Laporan Rapat Staf Triwulan I dan II TELAH memuat uraian target, realisasi, dan strategi optimalisasi capaian kinerja pada periode selanjutnya dalam rangka pemenuhan target kinerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (228, '2.C.3', 'Tidak Terpenuhi', 0, 'Jika Laporan Rapat Staf Triwulan I dan II TIDAK memuat uraian target, realisasi, dan strategi optimalisasi capaian kinerja pada periode selanjutnya dalam rangka pemenuhan target kinerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (229, '2.C.4', 'Terpenuhi', 1, 'Jika Laporan Rapat Staf Triwulan I dan II TELAH memuat penyesuaian pada kegiatan (bisa dalam bentuk perubahan jadwal kegiatan untuk periode selanjutnya) berdasarkan pengukuran kinerja yang telah dilakukan', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (230, '2.C.4', 'Tidak Terpenuhi', 0, 'Jika Laporan Rapat Staf Triwulan I dan II TIDAK memuat penyesuaian pada kegiatan (bisa dalam bentuk perubahan jadwal kegiatan untuk periode selanjutnya) berdasarkan pengukuran kinerja yang telah dilakukan', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (231, '2.C.5', 'Terpenuhi', 1, 'Jika Laporan Rapat Staf Triwulan I dan II TELAH memuat optimalisasi anggaran kegiatan (untuk periode selanjutnya) berdasarkan pengukuran kinerja yang telah dilakukan', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (232, '2.C.5', 'Tidak Terpenuhi', 0, 'Jika Laporan Rapat Staf Triwulan I dan II TIDAK memuat optimalisasi anggaran kegiatan (untuk periode selanjutnya) berdasarkan pengukuran kinerja yang telah dilakukan', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (233, '2.C.6', 'Terpenuhi', 1, 'Rata-rata capaian Indikator Kinerja Sasaran Strategis ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€šÃ‚Â°ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¥ 100% DAN realisasi anggaran ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€šÃ‚Â°ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¥95%', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (234, '2.C.6', 'Tidak Terpenuhi', 0, 'Rata-rata capaian Indikator Kinerja Sasaran Strategis TIDAK mencapai 100%', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (235, '2.C.7', 'Terpenuhi', 1, 'Hasil observasi dan atau wawancara menunjukkan bahwa pegawai SUDAH memahami dan peduli atas hasil pengukuran kinerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (236, '2.C.7', 'Tidak Terpenuhi', 0, 'Hasil observasi dan atau wawancara menunjukkan bahwa pegawai TIDAK memahami dan peduli atas hasil pengukuran kinerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (237, '2.C.8', 'Terpenuhi', 1, 'Hasil observasi dan atau wawancara menunjukkan bahwa pegawai SUDAH memahami dan peduli atas hasil pengukuran kinerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (238, '2.C.8', 'Tidak Terpenuhi', 0, 'Hasil observasi dan atau wawancara menunjukkan bahwa pegawai TIDAK memahami dan peduli atas hasil pengukuran kinerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (239, '3.A.1', 'Terpenuhi', 1, 'Satuan Kerja Telah Menyusun LKJIP Triwulan I dan II Tahun 2025', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (240, '3.A.1', 'Tidak Terpenuhi', 0, 'Satuan Kerja TIDAK Menyusun LKJIP Triwulan I dan II Tahun 2025', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (241, '3.A.2', 'Terpenuhi', 1, 'Jika telah disusun Laporan Kinerja triwulan I dan II Tahun 2025', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (242, '3.A.2', 'Tidak Terpenuhi', 0, 'Jika TIDAK dilakukan penyusunan Laporan Kinerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (243, '3.A.3', 'Terpenuhi', 1, 'Jika Laporan Kinerja triwulan I dan II Tahun 2025 telah ditandatangani oleh Kepala Satuan Kerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (244, '3.A.3', 'Tidak Terpenuhi', 0, 'Jika Laporan Kinerja TIDAK ditandatangani oleh Kepala Satuan Kerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (245, '3.A.4', 'Terpenuhi', 1, 'Apabila laporan kinerja satuan kerja triwulan IV /Laporan kinerja tahunan telah direviu APIP pada Bidang Pengawasan', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (246, '3.A.4', 'Tidak Terpenuhi', 0, 'Apabila laporan kinerja satuan kerja triwulan IV /Laporan kinerja tahunan tidak direviu APIP pada Bidang Pengawasan', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (247, '3.A.5', 'Terpenuhi', 1, 'Apabila Laporan Kinerja telah dipublikasi melalui website masing-masing.', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (248, '3.A.5', 'Tidak Terpenuhi', 0, 'Apabila Laporan Kinerja belum dipublikasi melalui website masing-masing satuan kerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (249, '3.A.6', 'Terpenuhi', 1, 'Bahwa seluruh laporan kinerja telah disampaikan secara tepat waktu (Sesuai dengan Pedoman 4 Tahun 2024 bahwa Laporan Kinerja dilaporkan atau diupload melalui ESR dan SICANA paling lambat 30 Hari setelah triwulan berakhir)', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (250, '3.A.6', 'Tidak Terpenuhi', 0, 'Apabila terdapat laporan kinerja yang dilaporkan/diupload tidak tepat waktu', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026)
ON DUPLICATE KEY UPDATE `kriteria_id` = VALUES(`kriteria_id`), `nilai` = VALUES(`nilai`), `skor` = VALUES(`skor`), `keterangan` = VALUES(`keterangan`), `created_at` = VALUES(`created_at`), `updated_at` = VALUES(`updated_at`), `tahun` = VALUES(`tahun`);
INSERT INTO `lke_parameter` (`id`, `kriteria_id`, `nilai`, `skor`, `keterangan`, `created_at`, `updated_at`, `tahun`) VALUES
  (251, '3.B.1', 'Terpenuhi', 1, 'Apabila seluruh laporan kinerja telah disusun sesuai standar yang terdiri dari BAB I - BAB IV sesuai dengan pedoman nomor 4 tahun 2024 yang telah sesuai dengan dokumen Perjanjian Kinerja Kepala Satuan Kerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (252, '3.B.1', 'Tidak Terpenuhi', 0, 'Apabila laporan kinerja telah disusun sesuai standar yang terdiri dari BAB I - BAB IV sesuai dengan pedoman nomor 4 tahun 2024 namun terdapat beberapa indikator yang dilaporkan dalam Laporan Kinerja tidak sesuai dengan dokumen Perjanjian Kinerja Kepala Satuan Kerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (253, '3.B.2', 'Terpenuhi', 1, 'Apabila Laporan Kinerja telah memuat capaian kinerja dari seluruh indikator pada Perjanjian Kinerja (sesuai dengan format yang telah diberikan dari Kejaksaan Agung) indikator dalam PK lengkap', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (254, '3.B.2', 'Tidak Terpenuhi', 0, 'Apabila indikator yang disajikan dalam Laporan Kinerja tidak ada', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (255, '3.B.3', 'Terpenuhi', 1, 'Apabila terdapat perbandingan realisasi kinerja dengan target kinerja tahunan untuk seluruh indikator kinerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (256, '3.B.3', 'Tidak Terpenuhi', 0, 'Tidak terdapat perbandingan realisasi kinerja dengan target kinerja tahunan', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (257, '3.B.4', 'Terpenuhi', 1, 'Apabila pada laporan triwulan II terdapat perbandingan realisasi kinerja dengan kinerja triwulan sebelumnya (mencakup seluruh indikator)', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (258, '3.B.4', 'Tidak Terpenuhi', 0, 'tidak terdapat perbandingan kinerja dengan triwulan sebelumnya', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (259, '3.B.5', 'Terpenuhi', 1, 'apabila pada laporan tahunan terdapat perbandingan realisasi kinerja dengan kinerja tahun-tahun sebelumnya (mencakup seluruh indikator)', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (260, '3.B.5', 'Tidak Terpenuhi', 0, 'tidak terdapat perbandingan kinerja dengan tahun sebelumnya', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (261, '3.B.6', 'Terpenuhi', 1, 'apabila seluruh indikator pada Laporan Kinerja telah memuat analisa faktor-faktor yang mempengaruhi keberhasilan, dan hambatan yang dihadapi serta upaya nyata yang telah dilakukan', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (262, '3.B.6', 'Tidak Terpenuhi', 0, 'terdapat laporan kinerja namun tidak terinformasikan hambatan atau upaya nyata untuk mencapai target kinerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (263, '3.B.7', 'Terpenuhi', 1, 'apabila dalam laporan kinerja telah diinformasikan tentang efisiensi sumber daya baik sumber daya manusia maupun anggaran dalam mencapai kinerja.', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (264, '3.B.7', 'Tidak Terpenuhi', 0, 'apabila dalam laporan kinerja belum menginformasikan tentang efisiensi sumber daya baik sumber daya manusia maupun anggaran dalam mencapai kinerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (265, '3.B.8', 'Terpenuhi', 1, 'Apabila Laporan Kinerja telah memuat strategi peningkatan capaian kinerja untuk periode selanjutnya pada seluruh indikator', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (266, '3.B.8', 'Tidak Terpenuhi', 0, 'Apabila tidak terdapat informasi upaya perbaikan dan penyempurnaan kinerja untuk periode selanjutnya pada Laporan Kinerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (267, '3.C.1', 'Terpenuhi', 1, 'Jika Laporan Rapat Staf Triwulan I dan II TELAH yang memuat pembahasan laporan kinerja serta dinamika kelompok untuk pencapaian target kinerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (268, '3.C.1', 'Tidak Terpenuhi', 0, 'Jika Laporan Rapat Staf Triwulan I dan II TIDAK yang memuat pembahasan laporan kinerja serta dinamika kelompok untuk pencapaian target kinerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (269, '3.C.2', 'Terpenuhi', 1, 'Hasil observasi dan atau wawancara menunjukkan bahwa pegawai SUDAH memahami pegawai informasi di dalam laporan kinerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (270, '3.C.2', 'Tidak Terpenuhi', 0, 'Hasil observasi dan atau wawancara menunjukkan bahwa pegawai BELUM memahami pegawai informasi di dalam laporan kinerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (271, '3.C.3', 'Terpenuhi', 1, 'Jika Laporan Kinerja Satuan Kerja Triwulan I dan II Tahun 2025 TELAH memuat penyesuaian pada kegiatan (bisa dalam bentuk perubahan jadwal kegiatan untuk periode selanjutnya) berdasarkan informasi dalam laporan kinerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (272, '3.C.3', 'Tidak Terpenuhi', 0, 'Jika Laporan Kinerja Satuan Kerja Triwulan I dan II Tahun 2025 TIDAK memuat penyesuaian pada kegiatan (bisa dalam bentuk perubahan jadwal kegiatan untuk periode selanjutnya) berdasarkan informasi dalam laporan kinerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (273, '3.C.4', 'Terpenuhi', 1, 'Jika Laporan Kinerja Satuan Kerja Triwulan I dan II Tahun 2025 TELAH memuat optimalisasi anggaran kegiatan (untuk periode selanjutnya) berdasarkan informasi dalam laporan kinerja)', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (274, '3.C.4', 'Tidak Terpenuhi', 0, 'Jika Laporan Kinerja Satuan Kerja Triwulan I dan II Tahun 2025 TIDAK memuat optimalisasi anggaran kegiatan (untuk periode selanjutnya) berdasarkan informasi dalam laporan kinerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (275, '3.C.5', 'Terpenuhi', 1, 'Jika Laporan Kinerja Satuan Kerja Triwulan I dan II Tahun 2025 TELAH memuat evaluasi pencapaian keberhasilan kinerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (276, '3.C.5', 'Tidak Terpenuhi', 0, 'Jika Laporan Kinerja Satuan Kerja Triwulan I dan II Tahun 2025 TIDAK memuat evaluasi pencapaian keberhasilan kinerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (277, '3.C.6', 'Terpenuhi', 1, 'Penetapan target kinerja TELAH disusun sebagai bentuk penyesuaian perencanaan kinerja berdasarkan informasi yang terdapat pada laporan kinerja periode sebelumnya', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (278, '3.C.6', 'Tidak Terpenuhi', 0, 'Penetapan target kinerja BELUM disusun sebagai bentuk penyesuaian perencanaan kinerja berdasarkan informasi yang terdapat pada laporan kinerja periode sebelumnya', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (279, '3.C.7', 'Terpenuhi', 1, 'Hasil observasi dan atau wawancara menunjukkan bahwa SUDAH terpenuhi/terjadiperubahan budaya kinerja organisasi yang berpedoman pada laporan kinerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (280, '3.C.7', 'Tidak Terpenuhi', 0, 'Hasil observasi dan atau wawancara menunjukkan bahwa BELUM terpenuhi/terjadi perubahan budaya kinerja organisasi yang berpedoman pada laporan kinerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (281, '4.A.1', 'Terpenuhi', 1, 'Pedoman 4 Tahun 2025 TELAH disosialisasikan/didistribusikan kepada seluruh pegawai', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (282, '4.A.1', 'Tidak Terpenuhi', 0, 'Pedoman 4 Tahun 2025 BELUM disosialisasikan/didistribusikan kepada seluruh pegawai', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (283, '4.A.2', 'Terpenuhi', 1, 'Jika TERDAPAT surat penyampaian PM AKIP INTERNAL berserta LKE kepada Jaksa Agung Muda Bidang Pengawasan', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (284, '4.A.2', 'Tidak Terpenuhi', 0, 'Jika TIDAK terdapat Surat penyampaian PM AKIP INTERNAL berserta LKE kepada Jaksa Agung Muda Bidang Pengawasan', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (285, '4.A.3', 'Terpenuhi', 1, 'Jika TERDAPAT surat penyampaian PM AKIP INTERNAL berserta LKE kepada Jaksa Agung Muda Bidang Pengawasan', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (286, '4.A.3', 'Tidak Terpenuhi', 0, 'Jika TIDAK terdapat Surat penyampaian PM AKIP INTERNAL berserta LKE kepada Jaksa Agung Muda Bidang Pengawasan', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (287, '4.B.1', 'Terpenuhi', 1, 'Jika TERDAPAT surat penyampaian PM AKIP Internal berserta LKE kepada Jaksa Agung Muda Bidang Pengawasan', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (288, '4.B.1', 'Tidak Terpenuhi', 0, 'Jika TIDAK terdapat Surat penyampaian PM AKIP Internal berserta LKE kepada Jaksa Agung Muda Bidang Pengawasan', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (289, '4.B.2', 'Terpenuhi', 1, 'Tersedia notulensi dan dokumentasi atas pelaksanaan sosialisasi/bimbingan teknis/pengarahan terkait SAKIP kepada kepala satuan kerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (290, '4.B.2', 'Tidak Terpenuhi', 0, 'SDM Tim Penilai Mandiri telah mengikuti sosialisasi/bimbingan teknis/pengarahan terkait Sistem Akuntabilitas Kinerja Instansi Pemerintah', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (291, '4.B.3', 'Terpenuhi', 1, 'Jika TERDAPAT surat penyampaian PM AKIP INTERNAL berserta LKE kepada Jaksa Agung Muda Bidang Pengawasan', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (292, '4.B.3', 'Tidak Terpenuhi', 0, 'Jika TIDAK terdapat Surat penyampaian PM AKIP INTERNAL berserta LKE kepada Jaksa Agung Muda Bidang Pengawasan', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (293, '4.B.4', 'Terpenuhi', 1, 'Jika TERDAPAT surat penyampaian PM AKIP INTERNAL berserta LKE kepada Jaksa Agung Muda Bidang Pengawasan', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (294, '4.B.4', 'Tidak Terpenuhi', 0, 'Jika TIDAK terdapat Surat penyampaian PM AKIP INTERNAL berserta LKE kepada Jaksa Agung Muda Bidang Pengawasan', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (295, '4.B.5', 'Terpenuhi', 1, 'Jika TERDAPAT surat penyampaian PM AKIP INTERNAL berserta LKE kepada Jaksa Agung Muda Bidang Pengawasan', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (296, '4.B.5', 'Tidak Terpenuhi', 0, 'Jika TIDAK terdapat Surat penyampaian PM AKIP INTERNAL berserta LKE kepada Jaksa Agung Muda Bidang Pengawasan', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (297, '4.C.1', 'Terpenuhi', 1, 'Seluruh rekomendasi hasil evaluasi AKIP internal tahun sebelumnya telah ditindaklanjuti (100%)', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (298, '4.C.1', 'Tidak Terpenuhi', 0, 'Rekomendasi hasil evaluasi AKIP internal tahun sebelumnya belum seluruhnya ditindaklanjuti', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (299, '4.C.2', 'Terpenuhi', 1, 'Jika tidak ada hasil evaluasi dan rekomendasi berulang pada LHE T-1 dibandingkan LHE T-2', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (300, '4.C.2', 'Tidak Terpenuhi', 0, 'Jika masih TERDAPAT hasil evaluasi dan rekomendasi berulang pada LHE T-1 dibandingkan LHE T-2', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026)
ON DUPLICATE KEY UPDATE `kriteria_id` = VALUES(`kriteria_id`), `nilai` = VALUES(`nilai`), `skor` = VALUES(`skor`), `keterangan` = VALUES(`keterangan`), `created_at` = VALUES(`created_at`), `updated_at` = VALUES(`updated_at`), `tahun` = VALUES(`tahun`);
INSERT INTO `lke_parameter` (`id`, `kriteria_id`, `nilai`, `skor`, `keterangan`, `created_at`, `updated_at`, `tahun`) VALUES
  (301, '4.C.3', 'Terpenuhi', 1, 'Nilai LHE meningkat dari tahun sebelumnya', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (302, '4.C.3', 'Tidak Terpenuhi', 0, 'Nilai LHE menurun dari tahun sebelumnya', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (303, '4.C.4', 'Terpenuhi', 1, 'Jika telah dilakukan MONITORING SECARA PERIODIK atas capaian rencana aksi tindak lanjut hasil evaluasi dan rekomendasi AKIP', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (304, '4.C.4', 'Tidak Terpenuhi', 0, 'Jika TIDAK terdapat dokumen Rencana Aksi tindak lanjut pemenuhan evaluasi dan rekomendasi AKIP', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (305, '4.C.5', 'Terpenuhi', 1, 'Telah terjadi perbaikan dan peningkatan capaian Indikator Kinerja Sasaran Strategis (IKSS)', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (306, '4.C.5', 'Tidak Terpenuhi', 0, 'Belum terjadi perbaikan atau peningkatan capaian Indikator Kinerja Sasaran Strategis (IKSS)', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (307, '4.C.6', 'Terpenuhi', 1, 'Jika hasil dari evaluasi dan rekomendasi pada LHE T-1 TELAH menjadi Rencana Aksi Kinerja tahun berjalan', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (308, '4.C.6', 'Tidak Terpenuhi', 0, 'Jika hasil dari evaluasi dan rekomendasi pada LHE T-1 BELUM menjadi Rencana Aksi Kinerja tahun berjalan', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026)
ON DUPLICATE KEY UPDATE `kriteria_id` = VALUES(`kriteria_id`), `nilai` = VALUES(`nilai`), `skor` = VALUES(`skor`), `keterangan` = VALUES(`keterangan`), `created_at` = VALUES(`created_at`), `updated_at` = VALUES(`updated_at`), `tahun` = VALUES(`tahun`);

-- =============================================================================
-- BAGIAN 2: MASTER BUKTI DUKUNG (lke_buktidukung - 48 DOKUMEN TAHUN 2026)
-- =============================================================================
REPLACE INTO `lke_buktidukung` (`id`, `dokumen`, `format_nama_file`, `keterangan`, `ada_di_sistem`, `tabel_sumber`, `tahun`) VALUES
  (1, 'RENSTRA Satuan Kerja Tahun 2025-2029', 'renstra_{tahun}.{ext}', '', 1, 'sinori_sakip_renstra', 2026),
  (2, 'Rencana Kerja (RENJA) Satuan Kerja Tahun 2026', 'renja_{tahun}.{ext}', '', 1, 'sinori_sakip_renja', 2026),
  (3, 'Rencana Aksi Kinerja Satuan Kerja Tahun 2026', 'renaksi_{tahun}.{ext}', '', 1, 'sinori_sakip_renaksi', 2026),
  (4, 'Kebutuhan Riil (TOR dan RAB) Satuan Kerja Tahun 2026', 'rkakl_{tahun}.{ext}', '', 1, 'sinori_sakip_rkakl', 2026),
  (5, 'DIPA Satuan Kerja Tahun 2026', 'dipa_{tahun}.{ext}', '', 1, 'sinori_sakip_dipa', 2026),
  (6, 'Perjanjian Kinerja Satuan Kerja Tahun 2026', 'pk_{tahun}.{ext}', '', 1, 'pk', 2026),
  (7, 'Perjanjian Kinerja Satuan Kerja Tahun 2025', 'pk_{tahun}.{ext}', '', 2, 'pk', 2025),
  (8, 'Indikator Kinerja Utama (IKU) / Penetapan Target Kinerja Tahun 2025', 'iku_{tahun}.{ext}', '', 1, 'sinori_sakip_iku', NULL),
  (9, 'Indikator Kinerja Utama (IKU) / Penetapan Target Kinerja Tahun 2025', 'iku_{tahun}.{ext}', '', 1, 'sinori_sakip_iku', NULL),
  (10, 'Laporan Kinerja Satuan Kerja Triwulan I Tahun 2026', 'lkjip_{tahun}_tw1.{ext}', '', 1, 'sinori_sakip_lakip', 2026),
  (11, 'Laporan Kinerja Satuan Kerja Triwulan II Tahun 2026', 'lkjip_{tahun}_tw2.{ext}', '', 1, 'sinori_sakip_lakip', 2026),
  (12, 'Laporan Kinerja Satuan Kerja Triwulan IV Tahun 2025', 'lkjip_{tahun_t1}_tw4.{ext}', '', 1, 'lhe', 2025),
  (13, 'Laporan Rapat Staf EKA Satuan Kerja Triwulan I Tahun 2026', 'rastaff_{tahun}_tw1.{ext}', '', 1, 'sinori_sakip_rastaff', 2026),
  (14, 'Laporan Rapat Staf EKA Satuan Kerja Triwulan II Tahun 2026', 'rastaff_{tahun}_tw2.{ext}', '', 1, 'sinori_sakip_rastaff', 2026),
  (15, 'Laporan Hasil Evaluasi (LHE) AKIP Internal Tahun 2025', 'lhe_{tahun_t1}.{ext}', '', 1, 'lhe', 2026),
  (16, 'Laporan Hasil Evaluasi (LHE) AKIP Internal Tahun 2025', 'lhe_{tahun}.{ext}', '', 1, 'lhe', 2026),
  (17, 'Rencana Aksi Tindak Lanjut LHE 2025', 'tl_lhe_{tahun}.{ext}', '', 1, 'tl_lhe_akip', 2025),
  (18, 'Laporan Monev Rencana Aksi Tahun 2026 TW I', 'monev_{tahun}_tw1.{ext}', '', 1, 'sinori_sakip_renaksieval', 2026),
  (19, 'Laporan Monev Rencana Aksi Tahun 2026 TW II', 'monev_{tahun}_tw2.{ext}', '', 1, 'sinori_sakip_renaksieval', 2026),
  (20, 'Pohon Kinerja Kejaksaan RI', 'pokin_{tahun}.{ext}', '', 1, 'pokin_ranwal', 2026),
  (21, 'Rancangan Awal RENSTRA Kejaksaan RI Tahun 2025-2029', 'renstra_lembaga_{tahun}.{ext}', '', 1, 'sinori_sakip_renstra', 2026),
  (22, 'Laporan Kinerja Satuan Kerja Triwulan IV Tahun 2023', 'lkjip_{tahun_t2}.{ext}', '', 1, 'sinori_sakip_lakip', 2026),
  (23, 'Screenshot statistik kepatuhan pegawai dalam menyusun Perjanjian Kinerja pada aplikasi MySimkari (E-Kinerja)', 'sampel_skp_{tahun}.{ext}', '', 1, 'sample_skp', 2026),
  (24, 'SK Tim Penilai Mandiri Evaluasi AKIP Internal Tahun 2026', 'tim_pm_{tahun}.{ext}', '', 1, 'sk_pm', 2026),
  (25, 'SK Tim Penilai Mandiri (dan Tim Evaluator khusus Kejati) ', 'tim_evaluator_{tahun}.{ext}', '', 1, 'sk_pk', 2026),
  (26, 'Daftar hadir SDM Tim PM dan atau Evaluator atas kegiatan sosialisasi/bimbingan teknis/pengarahan terkait Sistem Akuntabilitas Kinerja Instansi Pemerintah', 'absen_pm_{tahun}.{ext}', '', 1, 'absen_pm', 2026),
  (27, 'Nota dinas kepada kepala satuan kerja perihal peyampaian notulensi atas kegiatan sosialisasi/bimbingan teknis/pengarahan terkait Sistem Akuntabilitas Kinerja Instansi Pemerintah', 'notulensi_bimtek_{tahun}.{ext}', '', 1, 'notulensi_pm', 2026),
  (28, 'Surat atau Nota Dinas Laporan terkait sosialisasi/bimtek Pedoman 4 Tahun 2024 tentang Penyelenggaraan AKIP di Kejaksaan RI yang dilengkapi dengan daftar hadir dan dokumentasi', 'nodis_penyelenggaraan_akip_{tahun}.{ext}', 'upload diluar menu tata kelola', 1, 'nodis_p_akip', 2026),
  (29, 'Surat atau Nota Dinas Sosialisasi/Distribusi Pedoman 4 Tahun 2025 tentang Penyelenggaraan AKIP di Lingkungan Satker', 'nodis_evaluasi_akip_{tahun}.{ext}', '', 1, 'nodis_eval_akip', 2026),
  (30, 'Memorandum Kepala Satuan Kerja perihal pengumpulan data kinerja yang dapat diandalkan sesuai dengan dokumen penetapan target kinerja', 'memo_data_kinerja_{tahun}.{ext}', '', 1, 'memo_datakinerja', 2026),
  (31, 'Nota dinas penyampaian data kinerja dari seluruh bidang', 'nodis_data_kinerja_{tahun}.{ext}', '', 1, 'nodis_datakinerja', 2026),
  (32, 'Laporan/Dokumentasi/Sertifikat/Surat terkait pemberian penghargaan dan atau pembinaan yang telah dilakukan oleh internal Satuan Kerja berdasarkan pada capaian kinerja', 'reward_punishment_{tahun}.{ext}', '', 1, 'reward_punish', 2026),
  (33, 'sampel Surat Usulan/Rekomendasi atas Kenaikan Pangkat/Promosi/Mutasi yang dilengkapi dengan SKP dan clearence', 'sampel_rekom_{tahun}.{ext}', '', 1, 'sampel_rekom', 2026),
  (34, 'Bukti tangkapan layar atau link publikasi DOKUMEN PERENCANAAN', 'ss_perencanaan_{tahun}.{ext}', '', 1, 'ss_perencanaan', 2026),
  (35, 'Bukti tangkapan layar atau link publikasi LAPORAN KINERJA pada website', 'ss_laporan_web_{tahun}.{ext}', '', 1, 'ss_laporweb', 2026),
  (36, 'Bukti tangkapan layar atau link publikasi LAPORAN KINERJA pada ESR dan SICANA', 'ss_laporan_app_{tahun}.{ext}', '', 1, 'ss_laporapp', 2026),
  (37, 'Pengantar Laporan Kinerja Satuan Kerja Triwulan I Tahun 2026', 'tar_lkjip_{tahun}_tw1.{ext}', '', 1, 'tar_lkjip', 2026),
  (38, 'Pengantar Laporan Kinerja Satuan Kerja Triwulan II Tahun 2026', 'tar_lkjip_{tahun}_tw2.{ext}', '', 1, 'tar_lkjip', 2026),
  (39, 'Memorandum Laporan Kinerja Satuan Kerja Triwulan I Tahun 2026', 'memo_lkjip_{tahun}_tw1.{ext}', '', 1, 'memo_lkjip', 2026),
  (40, 'Memorandum Laporan Kinerja Satuan Kerja Triwulan II Tahun 2026', 'memo_lkjip_{tahun}_tw2.{ext}', '', 1, 'memo_lkjip', 2026),
  (41, 'Surat penyampaian PM AKIP internal', 'tar_pm_{tahun}.{ext}', '', 1, 'tar_pm', 2026),
  (42, 'Berita Acara pelaksanaan Rapat Pra Evaluasi Asisten Pembinaan dengan Asisten Pengawasan', 'ba_praevaluasi_{tahun}.{ext}', '', 1, 'ba_praeval', 2026),
  (43, 'Berita Acara pelaksanaan Rapat Reviu Rancangan LHE (Rapat Pleno)', 'ba_pleno_{tahun}.{ext}', '', 1, 'ba_pleno', 2026),
  (44, 'Laporan Hasil Evaluasi (LHE) AKIP Internal Tahun 2023', 'lhe_{tahun_t2}.{ext}', '', 1, 'lhe', 2026),
  (45, 'Screenshot statistik kepatuhan pegawai dalam menyusun Rencana Kinerja pada aplikasi MySimkari (E-Kinerja)', 'sampel_skp_{tahun}.{ext}', '', 1, 'sample_skp', 2026),
  (46, 'Screenshot pengisian capaian kinerja IKSS (Kepala Satker) pada E-SAKIP Kejaksaan melalui menu pengukuran', NULL, '', 0, NULL, 2026),
  (47, 'screenshot monitoring Nilai Kinerja Perencanaan Anggaran (NKPA) pada aplikasi Monev Kemenkeu', NULL, '', 0, NULL, 2026),
  (48, 'Screenshot statistik kepatuhan pegawai dalam menyusun Sasaran Kerja Pegawai pada aplikasi MySimkari (E-Kinerja)', NULL, '', 0, NULL, 2026);

-- =============================================================================
-- BAGIAN 3: UPDATE MASTER KOMPONEN (ID 1, 2, 3, 4) & SUBKOMPONEN SAKIP
-- Komponen ID tetap menggunakan 1, 2, 3, 4 (hanya update data bobot/nama saja)
-- =============================================================================
-- Bersihkan komponen duplikat ID > 4 dan subkomponen ID > 12 jika pernah terbuat
DELETE FROM `lke_komponen` WHERE `id` > 4;
DELETE FROM `lke_subkomponen` WHERE `id` > 12;

-- Update bobot dan data baku komponen ID 1, 2, 3, 4
UPDATE `lke_komponen` SET `nama` = 'Perencanaan Kinerja', `bobot` = 30.00, `updated_at` = NOW() WHERE `id` = 1;
UPDATE `lke_komponen` SET `nama` = 'Pengukuran Kinerja', `bobot` = 30.00, `updated_at` = NOW() WHERE `id` = 2;
UPDATE `lke_komponen` SET `nama` = 'Pelaporan Kinerja', `bobot` = 15.00, `updated_at` = NOW() WHERE `id` = 3;
UPDATE `lke_komponen` SET `nama` = 'Evaluasi Akuntabilitas Kinerja Internal', `bobot` = 25.00, `updated_at` = NOW() WHERE `id` = 4;

-- Update bobot dan komponen_id untuk 12 subkomponen utama (komponen_id merujuk ke 1, 2, 3, 4)
UPDATE `lke_subkomponen` SET `komponen_id` = 1, `bobot` = 6.00, `nama` = 'Tersedianyan Dokumen Perencanaan kinerja', `updated_at` = NOW() WHERE `kode` = '1.A' AND `id` <= 12;
UPDATE `lke_subkomponen` SET `komponen_id` = 1, `bobot` = 9.00, `nama` = 'Dokumen Perencanaan kinerja telah memenuhi standar yang baik, yaitu untuk mencapai hasil dengan ukuran kinerja yang SMART, menggunakan penyelarasan (cascading) disetiap level secara logis, serta memperhatikan kinerja bidang lain (crosscutting)', `updated_at` = NOW() WHERE `kode` = '1.B' AND `id` <= 12;
UPDATE `lke_subkomponen` SET `komponen_id` = 1, `bobot` = 15.00, `nama` = 'Perencanaan kinerja telah dimanfaatkan untuk mewujudkan hasil yang berkesinambungan', `updated_at` = NOW() WHERE `kode` = '1.C' AND `id` <= 12;

UPDATE `lke_subkomponen` SET `komponen_id` = 2, `bobot` = 6.00, `nama` = 'Pengukuran Kinerja telah dilakukan', `updated_at` = NOW() WHERE `kode` = '2.A' AND `id` <= 12;
UPDATE `lke_subkomponen` SET `komponen_id` = 2, `bobot` = 9.00, `nama` = 'Pengukuran Kinerja telah menjadi kebutuhan dalam mewujudkan Kinerja secara Efektif dan Efisien dan telah dilakukan secara berjenjang dan berkelanjutan', `updated_at` = NOW() WHERE `kode` = '2.B' AND `id` <= 12;
UPDATE `lke_subkomponen` SET `komponen_id` = 2, `bobot` = 15.00, `nama` = 'Pengukuran Kinerja telah dijadikan dasar dalam pemberian reward dan punishment, serta penyesuaian strategi dalam mencapai kinerja yang efektif dan efisien', `updated_at` = NOW() WHERE `kode` = '2.C' AND `id` <= 12;

UPDATE `lke_subkomponen` SET `komponen_id` = 3, `bobot` = 3.00, `nama` = 'Terdapat Dokumen Laporan yang menggambarkan Kinerja', `updated_at` = NOW() WHERE `kode` = '3.A' AND `id` <= 12;
UPDATE `lke_subkomponen` SET `komponen_id` = 3, `bobot` = 4.50, `nama` = 'Dokumen Laporan Kinerja telah memenuhi Standar menggambarkan Kualitas atas Pencapaian Kinerja, informasi keberhasilan/kegagalan kinerja serta upaya perbaikan/penyempurnaannya', `updated_at` = NOW() WHERE `kode` = '3.B' AND `id` <= 12;
UPDATE `lke_subkomponen` SET `komponen_id` = 3, `bobot` = 7.50, `nama` = 'Pelaporan Kinerja telah memberikan dampak yang besar dalam penyesuaian strategi/kebijakan dalam mencapai kinerja berikutnya', `updated_at` = NOW() WHERE `kode` = '3.C' AND `id` <= 12;

UPDATE `lke_subkomponen` SET `komponen_id` = 4, `bobot` = 5.00, `nama` = 'Evaluasi Akuntabilitas Kinerja Internal telah dilaksanakan', `updated_at` = NOW() WHERE `kode` = '4.A' AND `id` <= 12;
UPDATE `lke_subkomponen` SET `komponen_id` = 4, `bobot` = 7.50, `nama` = 'Evaluasi Akuntabilitas Kinerja Internal telah dilaksanakan secara berkualitas dengan Sumber Daya yang memadai', `updated_at` = NOW() WHERE `kode` = '4.B' AND `id` <= 12;
UPDATE `lke_subkomponen` SET `komponen_id` = 4, `bobot` = 12.50, `nama` = 'Implementasi SAKIP telah meningkat karena evaluasi Akuntabilitas Kinerja Internal sehingga memberikan kesan yang nyata (dampak) dalam efektifitas dan efisiensi kinerja', `updated_at` = NOW() WHERE `kode` = '4.C' AND `id` <= 12;

-- =============================================================================
-- BAGIAN 4: MASTER KRITERIA EVALUASI LKE 2026 (lke_kriteria - 77 KRITERIA)
-- =============================================================================
INSERT INTO `lke_kriteria` (`id`, `subkomponen_id`, `kode`, `nama`, `dokumen_bukti`, `langkah_kerja`, `kode_bukti`, `eselon_i`, `kejati`, `kejari`, `cabjari`, `tahun`, `created_at`, `updated_at`) VALUES
  (94, '1.A', '1.A.1', 'Terdapat pedoman teknis perencanaan kinerja (penyelenggaraan SAKIP).', 'Surat atau Nota Dinas Laporan terkait sosialisasi/bimtek Pedoman 4 Tahun 2024 tentang Penyelenggaraan AKIP di Kejaksaan RI yang dilengkapi dengan daftar hadir dan dokumentasi', 'Cek apakah terdapat bukti bahwa satuan kerja telah menyelenggarakan dan atau mengikuti sosialisasi/bimtek terkait pedoman 4 Tahun 2024 tentang Penyelenggaraan AKIP di Lingkungan Kejaksaan RI', '28', 1, 1, 1, 1, 2026, NOW(), NOW()),
  (95, '1.A', '1.A.2', 'Terdapat dokumen perencanaan kinerja jangka menengah (Renstra).', 'Dokumen RENSTRA Satuan Kerja Tahun 2025-2029', 'Cek ketersediaan dokumen (sudah jelas)', '1', 1, 1, 1, 1, 2026, NOW(), NOW()),
  (96, '1.A', '1.A.3', 'Terdapat dokumen perencanaan kinerja jangka pendek (Renja).', 'Rencana Kerja (RENJA) Satuan Kerja Tahun 2026', 'Cek ketersediaan dokumen (sudah jelas)', '2', 1, 1, 1, 1, 2026, NOW(), NOW()),
  (97, '1.A', '1.A.4', 'Terdapat dokumen perencanaan aktivitas yang mendukung kinerja', 'Rencana Aksi Kinerja Satuan Kerja Tahun 2026', 'Cek ketersediaan dokumen (sudah jelas)', '3', 1, 1, 1, 1, 2026, NOW(), NOW()),
  (98, '1.A', '1.A.5', 'Terdapat dokumen perencanaan anggaran yang mendukung kinerja', '1. Kebutuhan Riil (TOR dan RAB) Satuan Kerja Tahun Anggaran 2026
2. Dokumen DIPA Satuan Kerja Tahun Anggaran 2026', 'Cek ketersediaan dokumen (sudah jelas)', '4, 5', 1, 1, 1, 1, 2026, NOW(), NOW()),
  (99, '1.A', '1.A.6', 'Setiap satuan kerja merumuskan dan menetapkan Perencanaan Kinerja', 'Dokumen Perjanjian Kinerja Satuan Kerja Tahun 2026', 'Cek ketersediaan dokumen (sudah jelas)', '6', 1, 1, 1, 1, 2026, NOW(), NOW()),
  (100, '1.B', '1.B.1', 'Dokumen Perencanaan Kinerja telah diformalkan', 'Penetapan/Keputusan Kepala Satker terkait:
1. RENSTRA Satuan Kerja
2. RENJA Satuan Kerja
3.Perjanjian Kinerja', 'Cek apakah seluruh dokumen tersebut sudah ditetapkan/ditandatangani oleh Kasatker.', '1, 2, 6', 1, 1, 1, 1, 2026, NOW(), NOW()),
  (101, '1.B', '1.B.2', 'Dokumen Perencanaan telah dipublikasikan tepat waktu', 'Bukti tangkapan layar atau link publikasi pada website satuan kerja yang menampilkan waktu publikasi dari dokumen:
1. RENSTRA Satuan Kerja Tahun 2025-2029
2. RENJA Satuan Kerja Tahun 2026

(Seluruh bukti dukung tersebut dimuat dalam 1 file pdf)', 'Cek apakah publikasi belum melewati batas waktu berikut:
1. Renstra sebelum tanggal 31 Januari 2026
2. RENJA sebelum tanggal 30 Agustus 2026

(Perhatikan apabila ada perbaikan dokumen, maka yang digunakan adalah tanggal dokumen yang pertama)', '34', 1, 1, 1, 1, 2026, NOW(), NOW()),
  (102, '1.B', '1.B.3', 'Dokumen Perencanaan Kinerja telah menggambarkan kebutuhan atas kinerja sebenarnya yang perlu dicapai', 'RENSTRA Satuan Kerja Tahun 2025-2029;', 'Cek apakah sasaran dan indikator pada lampiran RENSTRA Satuan Kerja telah disesuaikan dengan ruang lingkup tugas dan fungsi Satuan Kerja berdasarkan Cascading Kinerja (tidak terdapat sasaran kinerja satker yang tidak sesuai dengan level satkernya)', '1', 1, 1, 1, 1, 2026, NOW(), NOW()),
  (103, '1.B', '1.B.4', 'Kualitas Rumusan Hasil (Tujuan/Sasaran) telah jelas menggambarkan kondisi kinerja yang dicapai', '1. Perjanjian Kinerja Tahun 2026;
2. Laporan Laporan Kinerja Triwulan IV Tahun 2025', 'Cek apakah seluruh target pada Perjanjian Kinerja 2026 telah disusun sesuai tren capaian kinerja periode tahun 2025 (minimal sama dengan capaian target tahun sebelumnya)', '6, 12', 1, 1, 1, 1, 2026, NOW(), NOW()),
  (104, '1.B', '1.B.5', 'Ukuran keberhasilan (indikator kinerja) telah memenuhi kriteria Specific, Measurable, Achievable, Relevant, Time-bound (SMART).', 'Rencana Aksi Kinerja Satuan Kerja Tahun 2026', 'Cek apakah target setiap indikator pada setiap tahun telah didistribusikan secara tepat pada setiap periode triwulan dan jumlahnya telah sesuai dengan target dalam 1 tahun.', '3', 1, 1, 1, 1, 2026, NOW(), NOW()),
  (105, '1.B', '1.B.6', 'Indikator Kinerja Utama (IKU) telah menggambarkan kondisi kinerja utama yang harus dicapai, tertuang secara berkelanjutan (sustainable-tidak sering diganti dalam 1 periode Perencanaan Strategis)', 'Dokumen IKU Kejaksaan RI Tahun 2025-2029 (tingkat Lembaga)', 'Kriteria sudah terpenuhi di tingkat Lembaga.', NULL, 1, 1, 1, 1, 2026, NOW(), NOW()),
  (106, '1.B', '1.B.7', 'Target yang ditetapkan dalam perencanaan kinerja dapat dicapai (achievable), menantang, dan realistis', '1. Perjanjian Kinerja Tahun 2026
2. Laporan Laporan Kinerja Triwulan IV Tahun 2025', 'Cek apakah:
1. Seluruh target pada Tahun 2025 telah tercapai;
2. Target pada Tahun 2026 lebih tinggi dibandingkan capaian target tahun 2025.', '6, 12', 1, 1, 1, 1, 2026, NOW(), NOW()),
  (107, '1.B', '1.B.8', 'Setiap dokumen perencanaan kinerja menggambarkan hubungan yang berkesinambungan, serta selaras antara kondisi/Hasil yang akan dicapai di setiap level jabatan (Cascading)', '1. RENSTRA Satuan Kerja Tahun 2025-2029 
2. Perjanjian Kinerja Tahun 2026', 'Cek apakah terdapat keselarasan sasaran dan indikator antara dokumen Renstra Satker di level IKSS dengan Dokumen PK Kepala Satuan Kerja', '1, 6', 1, 1, 1, 1, 2026, NOW(), NOW()),
  (108, '1.B', '1.B.9', 'Perencanaan Kinerja dapat memberikan informasi tentang hubungan kinerja, strategi, kebijakan, bahkan aktivitas antar bidang/dengan tugas dan fungsi lain yang berkaitan (Crosscutting).', 'Pohon Kinerja Kejaksaan RI Tahun 2025-2029 (tingkat Lembaga)', 'Kriteria sudah terpenuhi di tingkat Lembaga.', NULL, 1, 1, 1, 1, 2026, NOW(), NOW()),
  (109, '1.B', '1.B.10', 'Setiap pegawai merumuskan dan menetapkan perencanaan kinerja', 'Screenshot statistik kepatuhan pegawai dalam menyusun Perjanjian Kinerja pada aplikasi MySimkari (E-Kinerja)', 'Cek apakah statistik kepatuhan pegawai dalam menyusun Perjanjian Kinerja pada aplikasi MySimkari (E-Kinerja) sudah mencapai minimal 75%.', '23', 1, 1, 1, 1, 2026, NOW(), NOW()),
  (110, '1.C', '1.C.1', 'Anggaran yang ditetapkan mengacu pada kinerja yang ingin dicapai', '1. RENJA Satuan Kerja
2. dokumen DIPA Awal satuan kerja', 'Cek apakah anggaran yang telah tersusun dalam DIPA awal selaras dengan dokumen Rencana Kinerja Satuan Kerja', '2, 5', 1, 1, 1, 1, 2026, NOW(), NOW()),
  (111, '1.C', '1.C.2', 'Aktivitas yang dilaksanakan telah mendukung kinerja yang ingin dicapai', '1. Rencana Aksi Kinerja
2. Perjanjian Kinerja Satuan Kerja', 'Cek apakah Rencana Aksi telah memiliki korelasi dan mendukung seluruh sasaran dan indikator yang ditetapkan pada Perjanjian Kinerja', '3, 6', 1, 1, 1, 1, 2026, NOW(), NOW()),
  (112, '1.C', '1.C.3', 'Target yang ditetapkan dalam perencanaan kinerja telah dicapai dengan baik, atau setidaknya masih on the right track', '1. Perjanjian Kinerja Satuan Kerja Tahun 2025
2. Laporan Kinerja Satuan Kerja Tahun 2025', 'Cek apakah seluruh Indikator Kinerja Sasaran Strategis (IKSS) yang diperjanjikan oleh Kasatker telah tercapai sesuai target atau melebihi target..', '7, 12', 1, 1, 1, 1, 2026, NOW(), NOW()),
  (113, '1.C', '1.C.4', 'Rencana aksi kinerja dapat berjalan dinamis karena capaian kinerja selalu dipantau secara berkala', '1. Laporan Monev Rencana Aksi Kinerja Triwulan I Tahun 2026
2. Laporan Monev Rencana Aksi Kinerja Triwulan II Tahun 2026', 'Cek apakah tersedia Laporan Monev Rencana Aksi Kinerja triwulan I dan II berdasarkan Rencana Aksi Kinerja Tahun 2026', '18, 19', 1, 1, 1, 1, 2026, NOW(), NOW()),
  (114, '1.C', '1.C.5', 'Terdapat perbaikan/penyempurnaan dokumen perencanaan kinerja yang ditetapkan dari hasil analisis perbaikan kinerja sebelumnya dalam mewujudkan kondisi/hasil yang lebih baik', 'Dokumen Laporan Rapat Staf Triwulan I dan II Tahun 2026 yang juga melampirkan:
1. Daftar hadir Rapat; dan
2. Dokumentasi Rapat.', 'Cek apakah pada laporan rapat staf telah betul-betul dilaksanakan dengan melihat bukti dukung berupa laporan, daftar hadir, dan dokumentasi rapat staf untuk periode triwulan I dan II Tahun 2026', '13, 14', 1, 1, 1, 1, 2026, NOW(), NOW()),
  (115, '1.C', '1.C.6', 'Terdapat perbaikan/penyempurnaan Dokumen Perencanaan Kinerja dalam mewujudkan kondisi/hasil yang lebih baik.', 'Dokumen Laporan Rapat Staf Triwulan I dan II Tahun 2026 yang juga melampirkan:
1. Daftar hadir Rapat; dan
2. Dokumentasi Rapat.', 'Cek apakah pada pelaksanaan rapat staf telah membahas dan menyusun upaya/strategi optimalisasi capaian kinejra dalam mewujudkan kondisi yang lebih baik', '13, 14', 1, 1, 1, 1, 2026, NOW(), NOW()),
  (116, '1.C', '1.C.7', 'setiap satuan kerja memahami dan peduli, serta berkomitmen dalam mencapai kinerja yang telah direncanakan', 'Dokumen Laporan Rapat Staf Triwulan I dan II Tahun 2026 yang juga melampirkan:
1. Daftar hadir Rapat; dan
2. Dokumentasi Rapat.', 'Cek apakah pelaksanaan rapat staf telah dihadiri oleh seluruh pegawai pada satuan kerja khususnya Kepala Satuan Kerja beserta Pejabat Struktural serta memahami dan berkomitmen pada hasil rapat melalui tanda tangan pada laporan.', '13, 14', 1, 1, 1, 1, 2026, NOW(), NOW()),
  (117, '1.C', '1.C.8', 'Setiap Pegawai memahami dan peduli, serta berkomitmen dalam mencapai kinerja yang telah direncanakan.', 'Screenshot statistik kepatuhan pegawai dalam menyusun Rencana Kinerja pada aplikasi MySimkari (E-Kinerja)', 'Cek apakah statistik kepatuhan pegawai dalam menyusun Rencana Kinerja pada aplikasi MySimkari (E-Kinerja) sudah mencapai minimal 75%.', '45', 1, 1, 1, 1, 2026, NOW(), NOW()),
  (118, '2.A', '2.A.1', 'Terdapat pedoman teknis pengukuran kinerja dan pengumpulan data kinerja', 'Surat atau Nota Dinas Laporan terkait sosialisasi/bimtek Pedoman 4 Tahun 2024 tentang Penyelenggaraan AKIP di Kejaksaan RI yang dilengkapi dengan daftar hadir dan dokumentasi', 'Cek apakah terdapat bukti bahwa satuan kerja telah menyelenggarakan dan atau mengikuti sosialisasi/bimtek terkait pedoman 4 Tahun 2024 tentang Penyelenggaraan AKIP di Lingkungan Kejaksaan RI', '28', 1, 1, 1, 1, 2026, NOW(), NOW()),
  (119, '2.A', '2.A.2', 'Terdapat definisi operasional yang jelas atas kinerja dan cara mengukur indikator kinerja', 'Dokumen IKU Kejaksaan RI Tahun 2025-2029 (tingkat Lembaga)', 'Kriteria sudah terpenuhi di tingkat Lembaga.', NULL, 1, 1, 1, 1, 2026, NOW(), NOW()),
  (120, '2.A', '2.A.3', 'Terdapat mekanisme yang jelas terhadap pengumpulan data kinerja yang dapat diandalkan', 'Memorandum Kepala Satuan Kerja perihal pengumpulan data kinerja yang dapat diandalkan sesuai dengan dokumen penetapan target kinerja', 'Cek apakah tersedia dokumen bukti dukung tersebut (sudah jelas)', '30', 1, 1, 1, 1, 2026, NOW(), NOW()),
  (121, '2.A', '2.B.1', 'Pimpinan selalu terlibat sebagai pengambil keputusan (decision maker) dalam mengukur capaian kinerja', 'Dokumen Laporan Rapat Staf Triwulan I dan II Tahun 2026 yang juga melampirkan:
1. Daftar hadir Rapat; dan
2. Dokumentasi Rapat.', 'Cek apakah pelaksanaan rapat staf telah dihadiri oleh seluruh pegawai pada satuan kerja khususnya Kepala Satuan Kerja beserta Pejabat Struktural serta memahami dan berkomitmen pada hasil rapat melalui tanda tangan pada laporan.', '13, 14', 1, 1, 1, 1, 2026, NOW(), NOW()),
  (122, '2.A', '2.B.2', 'Data kinerja yang dikumpulkan telah relevan untuk mengukur capaian kinerja yang diharapkan dan mendukung capaian kinerja yang diharapkan', 'Nota dinas penyampaian data kinerja dari seluruh bidang pada penyusunya Laporan Kinerja Triwulan IV Tahun 2025', 'Cek apakah seluruh bidang pada satker telah menyampaikan data kinerjanya', '31', 1, 1, 1, 1, 2026, NOW(), NOW()),
  (123, '2.A', '2.B.3', 'Data kinerja yang dikumpulkan telah mendukung capaian kinerja yang diharapkan.', '1. Nota dinas penyampaian data kinerja dari seluruh bidang pada penyusunya Laporan Kinerja Triwulan IV Tahun 2025;
2. Laporan Kinerja Satuan KerjaTriwulan IV Tahun 2025.', 'Cek apakah data kinerja yang disampaikan oleh bidang telah sesuai dengan yang dibutuhkan pada manual IKU dan dituangkan pada Laporan Kinerja', '12, 31', 1, 1, 1, 1, 2026, NOW(), NOW()),
  (124, '2.A', '2.B.4', 'Pengukuran kinerja telah dilakukan secara berkala', 'Laporan Kinerja Triwulan I dan II Tahun 2026;', 'Cek apakah seluruh dokumen bukti dukung tersebut telah tersedia.', '10, 11', 1, 1, 1, 1, 2026, NOW(), NOW()),
  (125, '2.A', '2.B.5', 'Setiap satuan kerja  melakukan pemantauan atas pengukuran capaian kinerja unit dibawahnya secara berjenjang', 'Dokumen Laporan Rapat Staf Triwulan I dan II Tahun 2026 yang juga melampirkan:
1. Daftar hadir Rapat; dan
2. Dokumentasi Rapat.', 'Cek apakah seluruh dokumen bukti dukung tersebut telah tersedia.', '13, 14', 1, 1, 1, 1, 2026, NOW(), NOW()),
  (126, '2.B', '2.B.6', 'Pengumpulan data kinerja telah memanfaatkan Teknologi Informasi', '1. Screenshot pengisian capaian kinerja IKSS (Kepala Satker) pada E-SAKIP Kejaksaan melalui menu pengukuran.
2. Dokumen Laporan Kinerja Tahun 2026 Triwulan I 
3. Dokumen Laporan Kinerja Tahun 2026 Triwulan II', 'Cek apakah data capaian kinerja IKSS telah diinput pada E-SAKIP Kejaksaan RI dan telah sesuai dengan hasil pengukuran kinerja pada Laporan Kinerja Triwulan I dan II Tahun 2026', '13, 14, 46', 1, 1, 1, 1, 2026, NOW(), NOW()),
  (127, '2.B', '2.B.7', 'Pengukuran capaian kinerja telah memanfaatkan Teknologi Informasi', 'Data hasil pengukuran telah tersedia pada aplikasi E-SAKIP Kejaksaan RI', 'Kriteria sudah terpenuhi di tingkat Lembaga.', NULL, 1, 1, 1, 1, 2026, NOW(), NOW()),
  (128, '2.C', '2.C.1', 'Pegukuran kinerja telah menjadi dasar dalam pemberian penghargaan dan pembinaan', 'Laporan/Dokumentasi/Sertifikat/Surat terkait pemberian penghargaan dan atau pembinaan yang telah dilakukan oleh internal Satuan Kerja berdasarkan pada capaian kinerja', 'Cek apakah dokumen bukti dukung tersebut telah tersedia. Penghargaan bisa terhadap kinerja individu ataupun unit kerja (bidang/badan) namun harus berdasarkan hasil capaian kinerja.', '32', 1, 1, 1, 1, 2026, NOW(), NOW()),
  (129, '2.C', '2.C.2', 'Pengukuran kinerja telah menjadi salah satu pertimbangan penempatan baik dalam jabatan struktural maupun fungsional.', 'Sampel Surat Usulan/Rekomendasi atas Kenaikan Pangkat/Promosi/Mutasi yang dilengkapi dengan SKP dan clearence', 'Cek apakah dokumen bukti dukung tersebut telah tersedia.', '33', 1, 1, 1, 1, 2026, NOW(), NOW()),
  (130, '2.C', '2.C.3', 'Pengukuran Kinerja telah mempengaruhi penyesuaian strategi dalam mencapai Kinerja.', 'Dokumen Laporan Rapat Staf Triwulan I dan II Tahun 2026 yang juga melampirkan:
1. Daftar hadir Rapat; dan
2. Dokumentasi Rapat.', 'Cek apakah Laporan Rapat Staf Satuan Kerja telah memuat uraian target, realisasi, dan strategi optimalisasi capaian kinerja pada periode selanjutnya dalam rangka pemenuhan target kinerja', '13, 14', 1, 1, 1, 1, 2026, NOW(), NOW()),
  (131, '2.C', '2.C.4', 'Pengukuran Kinerja telah mempengaruhi penyesuaian kegiatan dalam mencapai kinerja', 'Dokumen Laporan Rapat Staf Triwulan I dan II Tahun 2026 yang juga melampirkan:
1. Daftar hadir Rapat; dan
2. Dokumentasi Rapat.', 'Cek apakah Laporan Rapat Staf Satuan Kerja telah memuat strategi optimalisasi capaian kinerja berupa penyesuaian kegiatan', '13, 14', 1, 1, 1, 1, 2026, NOW(), NOW()),
  (132, '2.C', '2.C.5', 'Pengukuran kinerja telah mempengaruhi penyesuaian anggaran dalam mencapai kinerja', 'Dokumen Laporan Rapat Staf Triwulan I dan II Tahun 2026 yang juga melampirkan:
1. Daftar hadir Rapat; dan
2. Dokumentasi Rapat.', 'Cek apakah Laporan Rapat Staf Satuan Kerja telah memuat strategi optimalisasi capaian kinerja berupa penyesuaian anggaran (optimalisasi/revisi anggaran)', '13, 14', 1, 1, 1, 1, 2026, NOW(), NOW()),
  (133, '2.C', '2.C.6', 'Terdapat efisiensi atas penggunaan anggaran dalam mencapai kinerja', 'screenshot monitoring Nilai Kinerja Perencanaan Anggaran (NKPA) pada aplikasi Monev Kemenkeu', 'Terdapat nilai efisiensi yang terdiri dari komponen Penggunaan SBK, Efisiensi SBK, dan Efisiensi Non-SBK (bisa salah satu dari ketiga komponen tersebut)', '47', 1, 1, 1, 1, 2026, NOW(), NOW()),
  (134, '2.C', '2.C.7', 'Setiap satuan kerja memahami dan peduli atas hasil pengukuran kinerja', 'Dokumen Laporan Rapat Staf Triwulan I dan II Tahun 2026 yang juga melampirkan:
1. Daftar hadir Rapat; dan
2. Dokumentasi Rapat.', 'Cek apakah Laporan Rapat Staf Satuan Kerja telah memuat capaian terhadap taarget kinerja berdasarkan hasil pengukuran kinerja.', '13, 14', 1, 1, 1, 1, 2026, NOW(), NOW()),
  (135, '2.C', '2.C.8', 'Setiap pegawai memahami dan peduli atas hasil pengukuran kinerja', 'Screenshot statistik kepatuhan pegawai dalam menyusun Sasaran Kerja Pegawai pada aplikasi MySimkari (E-Kinerja)', 'Cek apakah statistik kepatuhan pegawai dalam menyusun Sasaran Kerja Pegawai pada aplikasi MySimkari (E-Kinerja) sudah mencapai minimal 75%.', '48', 1, 1, 1, 1, 2026, NOW(), NOW()),
  (136, '3.A', '3.A.1', 'Dokumen laporan kinerja satuan kerja telah disusun', 'Laporan Kinerja Satuan Kerja Tahun 2025 Triwulan IV', 'Cek ketersediaan dokumen (sudah jelas)', '12', 1, 1, 1, 1, 2026, NOW(), NOW()),
  (137, '3.A', '3.A.2', 'Dokumen laporan Kinerja telah disusun secara berkala.', '1. Laporan Kinerja Satuan Kerja Tahun 2025 Triwulan IV 
2. Laporan Kinerja Satuan Kerja Tahun 2026 Triwulan I
3. Laporan Kinerja Satuan Kerja Tahun 2026 Triwulan II', 'Cek ketersediaan dokumen (sudah jelas)', '10, 11, 12', 1, 1, 1, 1, 2026, NOW(), NOW()),
  (138, '3.A', '3.A.3', 'Dokumen laporan kinerja telah diformalkan', 'Laporan Kinerja Satuan Kerja Triwulan I dan II Tahun 2026', 'Cek ketersediaan dokumen (sudah jelas)', '10, 11', 1, 1, 1, 1, 2026, NOW(), NOW()),
  (139, '3.A', '3.A.4', 'Dokumen laporan kinerja direviu', 'Laporan Kinerja Satuan Kerja Tahun 2025 Triwulan IV', 'Cek apakah terdapat pernyataan telah direviu oleh Bidang Pengawasan pada Laporan kinerja Triwulan IV/Tahunan Satker Tahun 2025 telah', '12', 1, 1, 1, 1, 2026, NOW(), NOW()),
  (140, '3.A', '3.A.5', 'Dokumen laporan kinerja telah dipublikasi', 'Tangkapan layar dan atau tautan yang menunjukkan Laporan Kinerja telah diupload pada website masing-masing satuan kerja', 'Cek ketersediaan dokumen (sudah jelas)', '35', 1, 1, 1, 1, 2026, NOW(), NOW()),
  (141, '3.A', '3.A.6', 'Dokumen laporan kinerja telah disampaikan tepat waktu', 'Screenshot bukti waktu upload dokumen laporan kinerja pada aplikasi ESR dan E-SAKIP Kejaksaan RI.', 'Cek apakah laporan kinerja telah diunggah tepat waktu yaitu maksimal 30 hari setelah periode triwulan berakhir', '36', 1, 1, 1, 1, 2026, NOW(), NOW()),
  (142, '3.B', '3.B.1', 'Dokumen Laporan Kinerja disusun secara berkualitas sesuai dengan standar', 'Laporan Kinerja Satuan Kerja Triwulan I dan II Tahun 2026', 'Cek apakah laporan kinerja telah disusun sesuai standar sstematika yang terdiri dari BAB I - BAB IV sesuai dengan template dalam Pedoman Jaksa Agung Nomor 4 tahun 2024', '10, 11', 1, 1, 1, 1, 2026, NOW(), NOW()),
  (143, '3.B', '3.B.2', 'Dokumen Laporan Kinerja telah mengungkap seluruh informasi tentang pencapaian kinerja', 'Laporan Kinerja Satuan Kerja Triwulan I dan II Tahun 2026', 'Cek apakah laporan kinerja  telah memuat capaian kinerja dari seluruh indikator', '10, 11', 1, 1, 1, 1, 2026, NOW(), NOW()),
  (144, '3.B', '3.B.3', 'Dokumen Laporan Kinerja  telah menginfokan perbandingan realisasi kinerja dengan target kinerja', 'Laporan Kinerja Satuan Kerja Triwulan I dan II Tahun 2026', 'Cek apakah terdapat perbandingan realisasi kinerja dengan target kinerja tahunan untuk setiap indikator kinerja', '10, 11', 1, 1, 1, 1, 2026, NOW(), NOW()),
  (145, '3.B', '3.B.4', 'Dokumen Laporan Kinerja  triwulan telah menginfokan perbandingan realisasi kinerja dengan kinerja triwulan sebelumnya', 'Laporan Kinerja Satuan Kerja Triwulan I dan II Tahun 2026', 'Cek apakah terdapat perbandingan realisasi kinerja dengan periode triwulan II Tahun 2026 dengan kinerja triwulan I sebelumnya (mencakup seluruh indikator)', '10, 11', 1, 1, 1, 1, 2026, NOW(), NOW()),
  (146, '3.B', '3.B.5', 'Dokumen Laporan Kinerja telah menginformasikan perbandingan realisasi kinerja dengan realisasi kinerja tahun-tahun sebelumnya', 'Laporan Kinerja Satuan Kerja Triwulan IV/Tahunan Tahun 2025', 'Cek apakah pada laporan tahunan 2025 pada BAB I (Pendahuluan) telah menjelaskan bahwa tahun 2025 adalah tahun pertama dari Renstra periode 2025-2029 serta terdapat informasi terkait capaian kinerja pada Tahun 2024 (minimal capaian evaluasi AKIP Interrnal)', '12', 1, 1, 1, 1, 2026, NOW(), NOW()),
  (147, '3.B', '3.B.6', 'Dokumen Laporan Kinerja telah menginformasikan kualitas atas capaian kinerja beserta upaya nyata dan/atau hambatan', 'Laporan Kinerja Satuan Kerja Triwulan I dan II Tahun 2026', 'Cek apakah pada laporan kinerja telah memuat informasi analisa faktor-faktor yang mempengaruhi keberhasilan, dan hambatan yang dihadapi serta upaya nyata yang telah dilakukan pada seluruh indikator sasaran kinerja', '10, 11', 1, 1, 1, 1, 2026, NOW(), NOW()),
  (148, '3.B', '3.B.7', 'Dokumen laporan Kinerja telah menginfokan efisiensi atas penggunaan sumber daya dalam mencapai Kinerja.', 'Laporan Kinerja Satuan Kerja Triwulan I dan II Tahun 2026', 'Cek apakah pada laporan kinerja telah memuat informasi terkait efisiensi atas peggunaan sumber daya (minimal mencantumkan realisasi anggaran)', '10, 11', 1, 1, 1, 1, 2026, NOW(), NOW()),
  (149, '3.B', '3.B.8', 'Dokumen Laporan Kinerja telah menginfokan upaya perbaikan dan penyempurnaan kinerja ke depan (Rekomendasi perbaikan kinerja )', 'Laporan Kinerja Satuan Kerja Triwulan I dan II Tahun 2025', 'Cek apakah pada laporan kinerja telah memuat upaya optimalisasi berupa strategi peningkatan capaian kinerja untuk periode selanjutnya pada seluruh indikator', '10, 11', 1, 1, 1, 1, 2026, NOW(), NOW()),
  (150, '3.C', '3.C.1', 'Informasi dalam laporan kinerja selalu menjadi perhatian utama pimpinan (bertanggung jawab)', 'Surat / Nota Dinas/ Memorandum penyampaian Laporan Kinerja Satuan Kerja Triwulan IV Tahun 2025 kepada seluruh bidang/direktorat/biro yang ditandatangani oleh Kepala Satuan Kerja', 'Cek apakah dokumem bukti dukung tersebut tersedia (sudah jelas)', '39, 40', 1, 1, 1, 1, 2026, NOW(), NOW()),
  (151, '3.C', '3.C.2', 'penyajian informasi dalam laporan kinerja menjadi kepedulian seluruh pegawai', 'Dilakukan observasi dan atau wawancara melalui wawancara dengan metode random sampling terkait pemahaman pegawai terhadap informasi di dalam laporan kinerja', 'Tim PM dapat langsung memilih jawaban tanpa bukti dukung. Tim Evaluator yang akan melakukan observasi dan wawancara.', NULL, 1, 1, 1, 1, 2026, NOW(), NOW()),
  (152, '3.C', '3.C.3', 'Informasi dalam laporan kinerja berkala telah digunakan dalam penyesuaian aktivitas untuk mencapai kinerja', 'Dilakukan observasi dan atau wawancara melalui wawancara dengan metode random sampling terkait pemanfaat informasi pegawai terhadap informasi di dalam laporan kinerja', 'Tim PM dapat langsung memilih jawaban tanpa bukti dukung. Tim Evaluator yang akan melakukan observasi dan wawancara.', NULL, 1, 1, 1, 1, 2026, NOW(), NOW()),
  (153, '3.C', '3.C.4', 'Informasi dalam laporan Kinerja berkala telah digunakan dalam penyesuaian penggunaan anggaran untuk mencapai Kinerja.', '1. Laporan Monev Rencana Aksi Kinerja Triwulan I Tahun 2026
2. Laporan Monev Rencana Aksi Kinerja Triwulan II Tahun 2026', 'Cek apakah tersedia Laporan Monev Rencana Aksi Kinerja triwulan I dan II berdasarkan Rencana Aksi Kinerja Tahun 2026', '18, 19', 1, 1, 1, 1, 2026, NOW(), NOW()),
  (154, '3.C', '3.C.5', 'Informasi dalam laporan Kinerja telah digunakan dalam evaluasi pencapaian keberhasilan Kinerja.', 'Dokumen Laporan Rapat Staf Triwulan I dan II Tahun 2026', 'Cek apakah pada laporan rapat staf telah memuat evaluasi atas pencapaian kinerja', '13, 14', 1, 1, 1, 1, 2026, NOW(), NOW()),
  (155, '3.C', '3.C.6', 'Informasi dalam laporan Kinerja telah digunakan dalam penyesuaian perencanaan Kinerja pada periode selanjutnya.', '1. Perjanjian Kinerja Tahun 2026
2. Laporan Kinerja Triwulan IV Tahun 2025', 'Cek apakah target yang ditetapkan pada tahun 2026 sudah relevan jika dibandingkan hasil capaian kinerja tahun 2025 (penentuan target didasarkan atas capaian pada periode tahun sebelumnya_', '6, 12', 1, 1, 1, 1, 2026, NOW(), NOW()),
  (156, '3.C', '3.C.7', 'Informasi dalam laporan Kinerja selalu mempengaruhi perubahan budaya Kinerja organisasi', 'Dilakukan observasi dan atau wawancara melalui wawancara dengan metode random sampling terkait pengaruh informasi dalam laporan kinerja terjadap budaya kinerja', 'Tim PM dapat langsung memilih jawaban tanpa bukti dukung. Tim Evaluator yang akan melakukan observasi dan wawancara.', NULL, 1, 1, 1, 1, 2026, NOW(), NOW()),
  (157, '4.A', '4.A.1', 'Terdapat pedoman teknis Evaluasi AKIP', 'Daftar Hadir/Laporan/ Dokumentasi terkait sosialisasi/bimtek Pedoman Jaksa Agung Nomor 4 Tahun 2025 tentang Evaluasi AKIP di Lingkungan Kejaksaan RI', 'Cek apakah terdapat bukti bahwa satuan kerja telah menyelenggarakan dan atau mengikuti sosialisasi/bimtek terkait pedoman 4 Tahun 2025 tentang Evaluasi AKIP di Lingkungan Kejaksaan RI', '29', 1, 1, 1, 1, 2026, NOW(), NOW()),
  (158, '4.A', '4.A.2', 'Evaluasi AKIP internal telah dilaksanakan pada satuan kerja', 'Surat penyampaian hasil PM AKIP internal satuan kerja', 'Cek apakah terdapat dokumen bukti dukung tersebut. (untuk satker Kejati dan Eselon 1 ditujukan kepada Jaksa Agung Muda   Pembinaan c.q. Kepala Biro Perencanaan. Sedangkan untuk satker Kejari dan Cabjari ditujukan kepada Kejaksaan Tinggi c.q. Asisten Bidang Pembinaan', '41', 1, 1, 1, 1, 2026, NOW(), NOW()),
  (159, '4.A', '4.A.3', 'Evaluasi AKIP internal telah dilaksanakan secara berjenjang', 'Pedoman Jaksa Agung Nomor 4 Tahun 2025 dan LHE AKIP Internal', 'Kriteria sudah terpenuhi di tingkat Lembaga.', NULL, 1, 1, 1, 1, 2026, NOW(), NOW()),
  (160, '4.B', '4.B.1', 'Evaluasi AKIP Internal telah dilaksanakan sesuai standar', 'Untuk Kejati:
1. SK Tim Penilai Mandiri (dan Tim Evaluator khusus Kejati)
2. Berita Acara pelaksanaan Rapat Pra Evaluasi Asisten Pembinaan dengan Asisten Pengawasan
3. Berita Acara pelaksanaan Rapat Reviu Rancangan LHE (Rapat Pleno)

Untuk Satker Eselon I, Kejari, dan Cabjari:
1. SK Tim Penilaian Mandiri (PM)
2. Surat penyampaian hasil PM Evaluasi AKIP Internal', 'Cek ketersediaan seluruh dokumen tersebut (sudah jelas)', '24, 42, 43, 41', 1, 1, 1, 1, 2026, NOW(), NOW()),
  (161, '4.B', '4.B.2', 'Evaluasi AKIP internal telah dilaksanakan oleh SDM yang memadai', '1. SK Tim Penilai Mandiri (dan Tim Evaluator khusus Kejati)
2. Daftar Hadir/Laporan/ Dokumentasi terkait sosialisasi/bimtek Pedoman Jaksa Agung Nomor 4 Tahun 2025 tentang Evaluasi AKIP di Lingkungan Kejaksaan RI', 'Cek ketersediaan seluruh dokumen tersebut (sudah jelas)', '24, 29', 1, 1, 1, 1, 2026, NOW(), NOW()),
  (162, '4.B', '4.B.3', 'Evaluasi AKIP internal telah dilaksanakan dengan pendalaman yang memadai', 'LHE seluruh Kejaksaan Negeri dan atau Cabang Kejaksaan Negeri di wilayahnya', 'Tim PM dapat langsung memilih jawaban tanpa bukti dukung. Tim Evaluator yang akan melakukan observasi dan wawancara.', NULL, 1, 1, 1, 1, 2026, NOW(), NOW()),
  (163, '4.B', '4.B.4', 'Evaluasi AKIP Internal telah dilaksanakan pada seluruh satuan kerja', 'LHE AKIP Internal Satuan Kerja pada wilayah hukumnya.', 'Khusus untuk Satker Kejaksaan Tinggi: Cek apakah sudah diterbitkan LHE untuk seluruh satker di wilayah hukumnya.Untuk Satker Kejaksaan Negeri dan Cabang Kejaksaan Negeri kriterira ini terpenuhi jika kriteria 4b1 sudah terpenuhi.', NULL, 1, 1, 1, 1, 2026, NOW(), NOW()),
  (164, '4.B', '4.B.5', 'Evaluasi AKIP telah dilaksanakan menggunakan teknologi informasi.', 'Penggunaan E-SAKIP Kejaksaan dalam pelaksanaan Evaluasi AKIP Internal', 'Kriteria sudah terpenuhi di tingkat Lembaga.', NULL, 1, 1, 1, 1, 2026, NOW(), NOW()),
  (165, '4.C', '4.C.1', 'Seluruh rekomendasi atas evaluasi AKIP internal tahun sebelumnya telah ditindaklanjuti', '1. LHE AKIP yang diterbitkan tahun 2025
2. Tindak Lanjut LHE AKIP yang disusun pada tahun 2025', 'Cek apakah pada dokumen Tindak Lanjut (TL) LHE AKIP Satuan Kerja telah relevan dan menindaklanjuti seluruh catatan dan rekomendasi pada LHE AKIP yang diterima', '15, 17', 1, 1, 1, 1, 2026, NOW(), NOW()),
  (166, '4.C', '4.C.2', 'Telah terjadi peningkatan implementasi SAKIP dengan melaksanakan tindak lanjut atas rerkomendasi hasil AKIP tahun sebelumnya.', '1. Tindak Lanjut LHE AKIP yang disusun pada tahun 2025
2. Dokumen laporan rapat staf satuan kerja tahun 2026 triwulan I
3. Dokumen laporan rapat staf satuan kerja tahun 2026 triwulan II', 'Cek apakah pada dokumen laporan rapat staf terlihat upaya peningkatan implementasi SAKIP berdasarkan tindak lanjut rekomendasi AKIP yang telah ditetapkan sebelumnya', '13, 14, 17', 1, 1, 1, 1, 2026, NOW(), NOW()),
  (167, '4.C', '4.C.3', 'Hasil Evaluasi AKIP telah dimanfaatkan untuk perbaikan dan peningkatan akuntabilitas kinerja.', 'Dilakukan observasi dan atau wawancara melalui wawancara dengan metode random sampling terkait pemanfaat hasil evaluasi AKIP pada perbaikan dan peningkatan akuntabilitas kinerja', 'Tim PM dapat langsung memilih jawaban tanpa bukti dukung. Tim Evaluator yang akan melakukan observasi dan wawancara.', NULL, 1, 1, 1, 1, 2026, NOW(), NOW()),
  (168, '4.C', '4.C.4', 'Hasil dari Evaluasi AKIP telah dimanfaatkan dalam mendukung efektifitas dan efisiensi Kinerja', 'Dilakukan observasi dan atau wawancara melalui wawancara dengan metode random sampling terkait pemanfaat hasil evaluasi AKIP dalam mendukung efektifitas dan efisiensi kinerja', 'Tim PM dapat langsung memilih jawaban tanpa bukti dukung. Tim Evaluator yang akan melakukan observasi dan wawancara.', NULL, 1, 1, 1, 1, 2026, NOW(), NOW()),
  (169, '4.C', '4.C.5', 'Telah terjadi perbaikan dan peningkatan kinerja dengan memanfaatkan hasil evaluasi AKIP', 'Dilakukan observasi dan atau wawancara melalui wawancara dengan metode random sampling terkait pemanfaat hasil evaluasi AKIP pada perbaikan dan peningkatan kinerja', 'Tim PM dapat langsung memilih jawaban tanpa bukti dukung. Tim Evaluator yang akan melakukan observasi dan wawancara.', NULL, 1, 1, 1, 1, 2026, NOW(), NOW()),
  (170, '4.C', '4.C.6', 'Hasil dari Evaluasi AKIP tahun sebelumnya telah dimanfaatkan dalam penyusunan dan perencanaan kinerja dan anggaran periode selanjutnya.', 'Dilakukan observasi dan atau wawancara melalui wawancara dengan metode random sampling terkait pemanfaat hasil evaluasi AKIP pada perbaikan dan peningkatan kinerja', 'Tim PM dapat langsung memilih jawaban tanpa bukti dukung. Tim Evaluator yang akan melakukan observasi dan wawancara.', NULL, 1, 1, 1, 1, 2026, NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `subkomponen_id` = VALUES(`subkomponen_id`),
  `nama`           = VALUES(`nama`),
  `dokumen_bukti`  = VALUES(`dokumen_bukti`),
  `langkah_kerja`  = VALUES(`langkah_kerja`),
  `kode_bukti`     = VALUES(`kode_bukti`),
  `eselon_i`       = VALUES(`eselon_i`),
  `kejati`         = VALUES(`kejati`),
  `kejari`         = VALUES(`kejari`),
  `cabjari`        = VALUES(`cabjari`),
  `updated_at`     = NOW();

-- =============================================================================
-- BAGIAN 5: SINKRONISASI PIVOT RELASI KRITERIA & BUKTI DUKUNG (lke_gabungan)
-- =============================================================================
-- Self-healing check: Pastikan kolom 'tahun' ada pada lke_gabungan sebelum query delete/insert
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_gabungan' AND COLUMN_NAME = 'tahun';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_gabungan` ADD COLUMN `tahun` smallint unsigned NOT NULL DEFAULT 2025 AFTER `buktidukung_id`, ADD INDEX `lke_gabungan_tahun_index` (`tahun`)', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Amankan baris eksisting agar memiliki nilai tahun (default 2025)
UPDATE `lke_gabungan` SET `tahun` = 2025 WHERE `tahun` IS NULL OR `tahun` = 0;

DELETE FROM `lke_gabungan` WHERE `tahun` = 2026;

INSERT INTO `lke_gabungan` (`komponen_id`, `sub_komponen_id`, `kriteria_id`, `buktidukung_id`, `tahun`) VALUES
  (1, '1.A', '1.A.1', 28, 2026),
  (1, '1.A', '1.A.2', 1, 2026),
  (1, '1.A', '1.A.3', 2, 2026),
  (1, '1.A', '1.A.4', 3, 2026),
  (1, '1.A', '1.A.5', 4, 2026),
  (1, '1.A', '1.A.5', 5, 2026),
  (1, '1.A', '1.A.6', 6, 2026),
  (1, '1.B', '1.B.1', 1, 2026),
  (1, '1.B', '1.B.1', 2, 2026),
  (1, '1.B', '1.B.1', 6, 2026),
  (1, '1.B', '1.B.2', 34, 2026),
  (1, '1.B', '1.B.3', 1, 2026),
  (1, '1.B', '1.B.4', 6, 2026),
  (1, '1.B', '1.B.4', 12, 2026),
  (1, '1.B', '1.B.5', 3, 2026),
  (1, '1.B', '1.B.7', 6, 2026),
  (1, '1.B', '1.B.7', 12, 2026),
  (1, '1.B', '1.B.8', 1, 2026),
  (1, '1.B', '1.B.8', 6, 2026),
  (1, '1.B', '1.B.10', 23, 2026),
  (1, '1.C', '1.C.1', 2, 2026),
  (1, '1.C', '1.C.1', 5, 2026),
  (1, '1.C', '1.C.2', 3, 2026),
  (1, '1.C', '1.C.2', 6, 2026),
  (1, '1.C', '1.C.3', 7, 2026),
  (1, '1.C', '1.C.3', 12, 2026),
  (1, '1.C', '1.C.4', 18, 2026),
  (1, '1.C', '1.C.4', 19, 2026),
  (1, '1.C', '1.C.5', 13, 2026),
  (1, '1.C', '1.C.5', 14, 2026),
  (1, '1.C', '1.C.6', 13, 2026),
  (1, '1.C', '1.C.6', 14, 2026),
  (1, '1.C', '1.C.7', 13, 2026),
  (1, '1.C', '1.C.7', 14, 2026),
  (1, '1.C', '1.C.8', 45, 2026),
  (2, '2.A', '2.A.1', 28, 2026),
  (2, '2.A', '2.A.3', 30, 2026),
  (2, '2.A', '2.B.1', 13, 2026),
  (2, '2.A', '2.B.1', 14, 2026),
  (2, '2.A', '2.B.2', 31, 2026),
  (2, '2.A', '2.B.3', 12, 2026),
  (2, '2.A', '2.B.3', 31, 2026),
  (2, '2.A', '2.B.4', 10, 2026),
  (2, '2.A', '2.B.4', 11, 2026),
  (2, '2.A', '2.B.5', 13, 2026),
  (2, '2.A', '2.B.5', 14, 2026),
  (2, '2.B', '2.B.6', 13, 2026),
  (2, '2.B', '2.B.6', 14, 2026),
  (2, '2.B', '2.B.6', 46, 2026),
  (2, '2.C', '2.C.1', 32, 2026),
  (2, '2.C', '2.C.2', 33, 2026),
  (2, '2.C', '2.C.3', 13, 2026),
  (2, '2.C', '2.C.3', 14, 2026),
  (2, '2.C', '2.C.4', 13, 2026),
  (2, '2.C', '2.C.4', 14, 2026),
  (2, '2.C', '2.C.5', 13, 2026),
  (2, '2.C', '2.C.5', 14, 2026),
  (2, '2.C', '2.C.6', 47, 2026),
  (2, '2.C', '2.C.7', 13, 2026),
  (2, '2.C', '2.C.7', 14, 2026),
  (2, '2.C', '2.C.8', 48, 2026),
  (3, '3.A', '3.A.1', 12, 2026),
  (3, '3.A', '3.A.2', 10, 2026),
  (3, '3.A', '3.A.2', 11, 2026),
  (3, '3.A', '3.A.2', 12, 2026),
  (3, '3.A', '3.A.3', 10, 2026),
  (3, '3.A', '3.A.3', 11, 2026),
  (3, '3.A', '3.A.4', 12, 2026),
  (3, '3.A', '3.A.5', 35, 2026),
  (3, '3.A', '3.A.6', 36, 2026),
  (3, '3.B', '3.B.1', 10, 2026),
  (3, '3.B', '3.B.1', 11, 2026),
  (3, '3.B', '3.B.2', 10, 2026),
  (3, '3.B', '3.B.2', 11, 2026),
  (3, '3.B', '3.B.3', 10, 2026),
  (3, '3.B', '3.B.3', 11, 2026),
  (3, '3.B', '3.B.4', 10, 2026),
  (3, '3.B', '3.B.4', 11, 2026),
  (3, '3.B', '3.B.5', 12, 2026),
  (3, '3.B', '3.B.6', 10, 2026),
  (3, '3.B', '3.B.6', 11, 2026),
  (3, '3.B', '3.B.7', 10, 2026),
  (3, '3.B', '3.B.7', 11, 2026),
  (3, '3.B', '3.B.8', 10, 2026),
  (3, '3.B', '3.B.8', 11, 2026),
  (3, '3.C', '3.C.1', 39, 2026),
  (3, '3.C', '3.C.1', 40, 2026),
  (3, '3.C', '3.C.4', 18, 2026),
  (3, '3.C', '3.C.4', 19, 2026),
  (3, '3.C', '3.C.5', 13, 2026),
  (3, '3.C', '3.C.5', 14, 2026),
  (3, '3.C', '3.C.6', 6, 2026),
  (3, '3.C', '3.C.6', 12, 2026),
  (4, '4.A', '4.A.1', 29, 2026),
  (4, '4.A', '4.A.2', 41, 2026),
  (4, '4.B', '4.B.1', 24, 2026),
  (4, '4.B', '4.B.1', 42, 2026),
  (4, '4.B', '4.B.1', 43, 2026),
  (4, '4.B', '4.B.1', 41, 2026),
  (4, '4.B', '4.B.2', 24, 2026),
  (4, '4.B', '4.B.2', 29, 2026),
  (4, '4.C', '4.C.1', 15, 2026),
  (4, '4.C', '4.C.1', 17, 2026),
  (4, '4.C', '4.C.2', 13, 2026),
  (4, '4.C', '4.C.2', 14, 2026),
  (4, '4.C', '4.C.2', 17, 2026);

SET FOREIGN_KEY_CHECKS = 1;

-- =============================================================================
-- =============================================================================
-- SELESAI: SEEDERS TAHUN 2026 (SEMUA DATA)
-- =============================================================================
