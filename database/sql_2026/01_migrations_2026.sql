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

SET FOREIGN_KEY_CHECKS = 1;

-- =============================================================================
-- SELESAI: MIGRATIONS TAHUN 2026
-- =============================================================================