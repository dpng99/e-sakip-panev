-- =============================================================================
-- PROSAKIP KEJAKSAAN RI - IMPORT BUKTI DUKUNG & KRITERIA LKE 2026
-- Tanggal Dibuat : 2026-10-18 17:54:45
-- Kompatibilitas : MySQL 5.7+ / 8.x / MariaDB (phpMyAdmin & CLI safe)
-- Sifat Script   : 100% IDEMPOTEN (Dapat dijalankan berulang tanpa duplikasi)
-- Deskripsi      : Mengatasi error '1054 Unknown column tabel_sumber' pada testing server,
--                  membuat struktur tabel jika belum ada, memperbarui Master Bukti Dukung (48 dokumen),
--                  Kriteria LKE 2026 (77 kriteria), serta sinkronisasi lke_gabungan.
-- =============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- =============================================================================
-- BAGIAN 1: DDL SKEMA TABEL (PEMERIKSAAN & PENAMBAHAN STRUKTUR KOLOM)
-- =============================================================================

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

-- 1.3 Pastikan kolom 'bobot' dan 'tahun' ada pada lke_komponen
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_komponen' AND COLUMN_NAME = 'bobot';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_komponen` ADD COLUMN `bobot` decimal(5,2) NOT NULL DEFAULT 0.00 AFTER `nama`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_komponen' AND COLUMN_NAME = 'tahun';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_komponen` ADD COLUMN `tahun` smallint unsigned NULL AFTER `bobot`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 1.4 Pastikan kolom 'bobot' dan 'tahun' ada pada lke_subkomponen
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_subkomponen' AND COLUMN_NAME = 'bobot';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_subkomponen` ADD COLUMN `bobot` decimal(5,2) NOT NULL DEFAULT 0.00 AFTER `nama`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_subkomponen' AND COLUMN_NAME = 'tahun';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_subkomponen` ADD COLUMN `tahun` smallint unsigned NULL AFTER `bobot`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 1.5 Pastikan kolom penunjang pada lke_kriteria ada
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
-- SELESAI: IMPORT DATA LKE TAHUN 2026 BERHASIL
-- =============================================================================