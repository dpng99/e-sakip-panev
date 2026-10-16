-- =============================================================================
-- PROSAKIP / PANEV KEJAKSAAN - DATABASE SEEDERS KHUSUS DATA TAHUN 2026
-- Tanggal Dibuat: 2026-10-16
-- Dialek: MySQL 8.x / MariaDB
-- Filter: Khusus data dengan tahun = '2026' atau dokumen_sakips tahun berjalan
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
-- Data untuk tabel `sasaran_program` (Total: 34 baris)
-- -------------------------------------------------------------
INSERT INTO `sasaran_program` (`id`, `kode_sp`, `nama_sp`, `id_satker`, `is_crosscutting`, `tahun`, `created_at`, `updated_at`) VALUES
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
  (50, 'SP 7', 'Meningkatnya penyelamatan dan pemulihan keuangan/kekayaan negara melalui jalur perdata', '417023', 0, '2026', '2026-10-02 13:54:52', '2026-10-02 13:54:52'),
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
-- Data untuk tabel `indikator_kinerja_program` (Total: 63 baris)
-- -------------------------------------------------------------
INSERT INTO `indikator_kinerja_program` (`id`, `sasaran_program_id`, `kode_ikp`, `nama_ikp`, `id_satker`, `sifat_node`, `tahun`, `created_at`, `updated_at`) VALUES
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
  (100, 55, 'IKP 9.1', 'Persentase penyelesaian penanganan perkara koneksitas pada tahap penyelidikan', '677111', 'RATIO_PERCENTAGE', '2026', '2026-10-02 13:54:59', '2026-10-02 13:54:59'),
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
  (113, 62, 'IKP 2.1', 'Opini BPK atas Laporan Keuangan Kejaksaan RI (WTP)', '419346', 'DIRECT_INPUT', '2026', '2026-10-02 13:54:59', '2026-10-02 13:54:59')
ON DUPLICATE KEY UPDATE `sasaran_program_id` = VALUES(`sasaran_program_id`), `kode_ikp` = VALUES(`kode_ikp`), `nama_ikp` = VALUES(`nama_ikp`), `id_satker` = VALUES(`id_satker`), `sifat_node` = VALUES(`sifat_node`), `tahun` = VALUES(`tahun`), `created_at` = VALUES(`created_at`), `updated_at` = VALUES(`updated_at`);
INSERT INTO `indikator_kinerja_program` (`id`, `sasaran_program_id`, `kode_ikp`, `nama_ikp`, `id_satker`, `sifat_node`, `tahun`, `created_at`, `updated_at`) VALUES
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

-- -------------------------------------------------------------
-- Data untuk tabel `target_ikp` (Total: 63 baris)
-- -------------------------------------------------------------
INSERT INTO `target_ikp` (`id`, `ikp_id`, `sp_id`, `id_satker`, `tahun`, `target_tahun`, `target_tw1`, `target_tw2`, `target_tw3`, `target_tw4`, `created_at`, `updated_at`) VALUES
  (64, 64, 35, '691270', '2026', '90.00', '90.00', '90.00', '90.00', '90.00', '2026-10-02 13:55:08', '2026-10-02 13:55:08'),
  (65, 65, 35, '691270', '2026', '90.00', '90.00', '90.00', '90.00', '90.00', '2026-10-02 13:55:08', '2026-10-02 13:55:08'),
  (66, 66, 35, '691270', '2026', '90.00', '90.00', '90.00', '90.00', '90.00', '2026-10-02 13:55:08', '2026-10-02 13:55:08'),
  (67, 67, 36, '691270', '2026', '4.00', '4.00', '4.00', '4.00', '4.00', '2026-10-02 13:55:08', '2026-10-02 13:55:08'),
  (68, 68, 36, '691270', '2026', '4.00', '4.00', '4.00', '4.00', '4.00', '2026-10-02 13:55:08', '2026-10-02 13:55:08'),
  (69, 69, 36, '691270', '2026', '4.00', '4.00', '4.00', '4.00', '4.00', '2026-10-02 13:55:08', '2026-10-02 13:55:08'),
  (70, 70, 37, '419344', '2026', '77.00', '77.00', '77.00', '77.00', '77.00', '2026-10-02 13:55:08', '2026-10-02 13:55:08'),
  (71, 71, 37, '419344', '2026', '84.00', '84.00', '84.00', '84.00', '84.00', '2026-10-02 13:55:08', '2026-10-02 13:55:08'),
  (72, 72, 37, '419344', '2026', '84.00', '84.00', '84.00', '84.00', '84.00', '2026-10-02 13:55:08', '2026-10-02 13:55:08'),
  (73, 73, 37, '419344', '2026', '20.00', '20.00', '20.00', '20.00', '20.00', '2026-10-02 13:55:08', '2026-10-02 13:55:08'),
  (74, 74, 37, '419344', '2026', '85.00', '85.00', '85.00', '85.00', '85.00', '2026-10-02 13:55:08', '2026-10-02 13:55:08'),
  (75, 75, 37, '419344', '2026', '72.00', '72.00', '72.00', '72.00', '72.00', '2026-10-02 13:55:08', '2026-10-02 13:55:08'),
  (76, 76, 38, '419344', '2026', '27.00', '27.00', '27.00', '27.00', '27.00', '2026-10-02 13:55:08', '2026-10-02 13:55:08'),
  (77, 77, 38, '419344', '2026', '35.00', '35.00', '35.00', '35.00', '35.00', '2026-10-02 13:55:08', '2026-10-02 13:55:08'),
  (78, 78, 39, '419344', '2026', '85.00', '85.00', '85.00', '85.00', '85.00', '2026-10-02 13:55:08', '2026-10-02 13:55:08'),
  (79, 79, 40, '419344', '2026', '3.60', '3.60', '3.60', '3.60', '3.60', '2026-10-02 13:55:08', '2026-10-02 13:55:08'),
  (80, 80, 41, '419344', '2026', '80.00', '80.00', '80.00', '80.00', '80.00', '2026-10-02 13:55:08', '2026-10-02 13:55:08'),
  (81, 81, 42, '418326', '2026', '80.00', '80.00', '80.00', '80.00', '80.00', '2026-10-02 13:55:08', '2026-10-02 13:55:08'),
  (82, 82, 42, '418326', '2026', '80.00', '80.00', '80.00', '80.00', '80.00', '2026-10-02 13:55:08', '2026-10-02 13:55:08'),
  (83, 83, 42, '418326', '2026', '88.00', '88.00', '88.00', '88.00', '88.00', '2026-10-02 13:55:08', '2026-10-02 13:55:08'),
  (84, 84, 43, '418326', '2026', '61.00', '61.00', '61.00', '61.00', '61.00', '2026-10-02 13:55:08', '2026-10-02 13:55:08'),
  (85, 85, 43, '418326', '2026', '0.00', '0.00', '0.00', '0.00', '0.00', '2026-10-02 13:55:08', '2026-10-02 13:55:08'),
  (86, 86, 44, '418326', '2026', '85.00', '85.00', '85.00', '85.00', '85.00', '2026-10-02 13:55:08', '2026-10-02 13:55:08'),
  (87, 87, 45, '418326', '2026', '3.60', '3.60', '3.60', '3.60', '3.60', '2026-10-02 13:55:08', '2026-10-02 13:55:08'),
  (88, 88, 46, '418326', '2026', '80.00', '80.00', '80.00', '80.00', '80.00', '2026-10-02 13:55:08', '2026-10-02 13:55:08'),
  (89, 89, 47, '418326', '2026', '460.00', '460.00', '460.00', '460.00', '460.00', '2026-10-02 13:55:08', '2026-10-02 13:55:08'),
  (90, 90, 48, '417023', '2026', '100.00', '100.00', '100.00', '100.00', '100.00', '2026-10-02 13:55:08', '2026-10-02 13:55:08'),
  (91, 91, 49, '417023', '2026', '3.60', '3.60', '3.60', '3.60', '3.60', '2026-10-02 13:55:08', '2026-10-02 13:55:08'),
  (92, 92, 50, '417023', '2026', '80.00', '80.00', '80.00', '80.00', '80.00', '2026-10-02 13:55:08', '2026-10-02 13:55:08'),
  (93, 93, 50, '417023', '2026', '80.00', '80.00', '80.00', '80.00', '80.00', '2026-10-02 13:55:08', '2026-10-02 13:55:08'),
  (94, 94, 51, '417023', '2026', '80.00', '80.00', '80.00', '80.00', '80.00', '2026-10-02 13:55:08', '2026-10-02 13:55:08'),
  (95, 95, 51, '417023', '2026', '80.00', '80.00', '80.00', '80.00', '80.00', '2026-10-02 13:55:08', '2026-10-02 13:55:08'),
  (96, 96, 51, '417023', '2026', '80.00', '80.00', '80.00', '80.00', '80.00', '2026-10-02 13:55:08', '2026-10-02 13:55:08'),
  (97, 97, 52, '417023', '2026', '3.60', '3.60', '3.60', '3.60', '3.60', '2026-10-02 13:55:08', '2026-10-02 13:55:08'),
  (98, 98, 53, '417023', '2026', '3.60', '3.60', '3.60', '3.60', '3.60', '2026-10-02 13:55:08', '2026-10-02 13:55:08'),
  (99, 99, 54, '417023', '2026', '80.00', '80.00', '80.00', '80.00', '80.00', '2026-10-02 13:55:08', '2026-10-02 13:55:08'),
  (100, 100, 55, '677111', '2026', '80.00', '80.00', '80.00', '80.00', '80.00', '2026-10-02 13:55:08', '2026-10-02 13:55:08'),
  (101, 101, 55, '677111', '2026', '80.00', '80.00', '80.00', '80.00', '80.00', '2026-10-02 13:55:08', '2026-10-02 13:55:08'),
  (102, 102, 55, '677111', '2026', '80.00', '80.00', '80.00', '80.00', '80.00', '2026-10-02 13:55:08', '2026-10-02 13:55:08'),
  (103, 103, 55, '677111', '2026', '80.00', '80.00', '80.00', '80.00', '80.00', '2026-10-02 13:55:08', '2026-10-02 13:55:08'),
  (104, 104, 55, '677111', '2026', '80.00', '80.00', '80.00', '80.00', '80.00', '2026-10-02 13:55:08', '2026-10-02 13:55:08'),
  (105, 105, 56, '677111', '2026', '80.00', '80.00', '80.00', '80.00', '80.00', '2026-10-02 13:55:08', '2026-10-02 13:55:08'),
  (106, 106, 56, '677111', '2026', '80.00', '80.00', '80.00', '80.00', '80.00', '2026-10-02 13:55:08', '2026-10-02 13:55:08'),
  (107, 107, 56, '677111', '2026', '80.00', '80.00', '80.00', '80.00', '80.00', '2026-10-02 13:55:08', '2026-10-02 13:55:08'),
  (108, 108, 57, '677111', '2026', '85.00', '85.00', '85.00', '85.00', '85.00', '2026-10-02 13:55:08', '2026-10-02 13:55:08'),
  (109, 109, 58, '677111', '2026', '3.60', '3.60', '3.60', '3.60', '3.60', '2026-10-02 13:55:08', '2026-10-02 13:55:08'),
  (110, 110, 59, '677111', '2026', '100.00', '100.00', '100.00', '100.00', '100.00', '2026-10-02 13:55:08', '2026-10-02 13:55:08'),
  (111, 111, 60, '666405', '2026', '3.60', '3.60', '3.60', '3.60', '3.60', '2026-10-02 13:55:08', '2026-10-02 13:55:08'),
  (112, 112, 61, '666405', '2026', '3.60', '3.60', '3.60', '3.60', '3.60', '2026-10-02 13:55:08', '2026-10-02 13:55:08'),
  (113, 113, 62, '419346', '2026', '100.00', '100.00', '100.00', '100.00', '100.00', '2026-10-02 13:55:08', '2026-10-02 13:55:08')
ON DUPLICATE KEY UPDATE `ikp_id` = VALUES(`ikp_id`), `sp_id` = VALUES(`sp_id`), `id_satker` = VALUES(`id_satker`), `tahun` = VALUES(`tahun`), `target_tahun` = VALUES(`target_tahun`), `target_tw1` = VALUES(`target_tw1`), `target_tw2` = VALUES(`target_tw2`), `target_tw3` = VALUES(`target_tw3`), `target_tw4` = VALUES(`target_tw4`), `created_at` = VALUES(`created_at`), `updated_at` = VALUES(`updated_at`);
INSERT INTO `target_ikp` (`id`, `ikp_id`, `sp_id`, `id_satker`, `tahun`, `target_tahun`, `target_tw1`, `target_tw2`, `target_tw3`, `target_tw4`, `created_at`, `updated_at`) VALUES
  (114, 114, 63, '419346', '2026', '3.20', '3.20', '3.20', '3.20', '3.20', '2026-10-02 13:55:08', '2026-10-02 13:55:08'),
  (115, 115, 64, '419346', '2026', '75.00', '75.00', '75.00', '75.00', '75.00', '2026-10-02 13:55:08', '2026-10-02 13:55:08'),
  (116, 116, 65, '419346', '2026', '80.00', '80.00', '80.00', '80.00', '80.00', '2026-10-02 13:55:08', '2026-10-02 13:55:08'),
  (117, 117, 66, '419346', '2026', '70.00', '70.00', '70.00', '70.00', '70.00', '2026-10-02 13:55:08', '2026-10-02 13:55:08'),
  (118, 118, 67, '419346', '2026', '100.00', '100.00', '100.00', '100.00', '100.00', '2026-10-02 13:55:08', '2026-10-02 13:55:08'),
  (119, 119, 68, '419345', '2026', '80.00', '80.00', '80.00', '80.00', '80.00', '2026-10-02 13:55:08', '2026-10-02 13:55:08'),
  (120, 120, 68, '419345', '2026', '80.00', '80.00', '80.00', '80.00', '80.00', '2026-10-02 13:55:08', '2026-10-02 13:55:08'),
  (121, 121, 68, '419345', '2026', '80.00', '80.00', '80.00', '80.00', '80.00', '2026-10-02 13:55:08', '2026-10-02 13:55:08'),
  (122, 122, 68, '419345', '2026', '80.00', '80.00', '80.00', '80.00', '80.00', '2026-10-02 13:55:08', '2026-10-02 13:55:08'),
  (123, 123, 68, '419345', '2026', '80.00', '80.00', '80.00', '80.00', '80.00', '2026-10-02 13:55:08', '2026-10-02 13:55:08'),
  (124, 124, 68, '419345', '2026', '80.00', '80.00', '80.00', '80.00', '80.00', '2026-10-02 13:55:08', '2026-10-02 13:55:08'),
  (125, 125, 68, '419345', '2026', '80.00', '80.00', '80.00', '80.00', '80.00', '2026-10-02 13:55:08', '2026-10-02 13:55:08'),
  (126, 126, 68, '419345', '2026', '4.00', '4.00', '4.00', '4.00', '4.00', '2026-10-02 13:55:08', '2026-10-02 13:55:08')
ON DUPLICATE KEY UPDATE `ikp_id` = VALUES(`ikp_id`), `sp_id` = VALUES(`sp_id`), `id_satker` = VALUES(`id_satker`), `tahun` = VALUES(`tahun`), `target_tahun` = VALUES(`target_tahun`), `target_tw1` = VALUES(`target_tw1`), `target_tw2` = VALUES(`target_tw2`), `target_tw3` = VALUES(`target_tw3`), `target_tw4` = VALUES(`target_tw4`), `created_at` = VALUES(`created_at`), `updated_at` = VALUES(`updated_at`);

-- -----------------------------------------------------------------------------
-- Pastikan kolom 'tahun' ada pada tabel lke_parameter sebelum data diisi
-- -----------------------------------------------------------------------------
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_parameter' AND COLUMN_NAME = 'tahun';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_parameter` ADD COLUMN `tahun` smallint unsigned NOT NULL DEFAULT 2025 AFTER `updated_at`, ADD INDEX `lke_parameter_tahun_idx` (`tahun`), ADD INDEX `lke_parameter_tahun_kriteria_idx` (`tahun`, `kriteria_id`)', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- -------------------------------------------------------------
-- Data untuk tabel `lke_parameter` (Total: 154 baris)
-- -------------------------------------------------------------
INSERT INTO `lke_parameter` (`id`, `kriteria_id`, `nilai`, `skor`, `keterangan`, `created_at`, `updated_at`, `tahun`) VALUES
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
  (200, '1.C.7', 'Tidak Terpenuhi', 0, '1. Satuan Kerja menyusun Laporan Kinerja sesuai dengan Perjanjian Kinerja yang telah ditetapkan setiap semester. 2. Satuan Kerja melakukan evaluasi terhadap capaian kinerja setiap semester salah satunya melalui Rapat Staf Evaluasi Kinerja dan Anggaran.', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (201, '1.C.8', 'Terpenuhi', 1, 'Hasil observasi dan atau wawancara menunjukkan bahwa pegawai SUDAH memahami kinerja yang telah direncanakan dan memiliki komitmen untuk mencapai sasaran kinerja tersebut', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (202, '1.C.8', 'Tidak Terpenuhi', 0, 'Hasil observasi dan atau wawancara menunjukkan bahwa pegawai TIDAK memahami kinerja yang telah direncanakan dan memiliki komitmen untuk mencapai sasaran kinerja tersebut', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (203, '2.A.1', 'Terpenuhi', 1, 'Pedoman 4 Tahun 2024 TELAH disosialisasikan/didistribusikan kepada seluruh pegawai', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (204, '2.A.1', 'Tidak Terpenuhi', 0, 'Pedoman 4 Tahun 2024 BELUM disosialisasikan/didistribusikan kepada seluruh pegawai', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026)
ON DUPLICATE KEY UPDATE `kriteria_id` = VALUES(`kriteria_id`), `nilai` = VALUES(`nilai`), `skor` = VALUES(`skor`), `keterangan` = VALUES(`keterangan`), `created_at` = VALUES(`created_at`), `updated_at` = VALUES(`updated_at`), `tahun` = VALUES(`tahun`);
INSERT INTO `lke_parameter` (`id`, `kriteria_id`, `nilai`, `skor`, `keterangan`, `created_at`, `updated_at`, `tahun`) VALUES
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
  (250, '3.A.6', 'Tidak Terpenuhi', 0, 'Apabila terdapat laporan kinerja yang dilaporkan/diupload tidak tepat waktu', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (251, '3.B.1', 'Terpenuhi', 1, 'Apabila seluruh laporan kinerja telah disusun sesuai standar yang terdiri dari BAB I - BAB IV sesuai dengan pedoman nomor 4 tahun 2024 yang telah sesuai dengan dokumen Perjanjian Kinerja Kepala Satuan Kerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (252, '3.B.1', 'Tidak Terpenuhi', 0, 'Apabila laporan kinerja telah disusun sesuai standar yang terdiri dari BAB I - BAB IV sesuai dengan pedoman nomor 4 tahun 2024 namun terdapat beberapa indikator yang dilaporkan dalam Laporan Kinerja tidak sesuai dengan dokumen Perjanjian Kinerja Kepala Satuan Kerja', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (253, '3.B.2', 'Terpenuhi', 1, 'Apabila Laporan Kinerja telah memuat capaian kinerja dari seluruh indikator pada Perjanjian Kinerja (sesuai dengan format yang telah diberikan dari Kejaksaan Agung) indikator dalam PK lengkap', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (254, '3.B.2', 'Tidak Terpenuhi', 0, 'Apabila indikator yang disajikan dalam Laporan Kinerja tidak ada', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026)
ON DUPLICATE KEY UPDATE `kriteria_id` = VALUES(`kriteria_id`), `nilai` = VALUES(`nilai`), `skor` = VALUES(`skor`), `keterangan` = VALUES(`keterangan`), `created_at` = VALUES(`created_at`), `updated_at` = VALUES(`updated_at`), `tahun` = VALUES(`tahun`);
INSERT INTO `lke_parameter` (`id`, `kriteria_id`, `nilai`, `skor`, `keterangan`, `created_at`, `updated_at`, `tahun`) VALUES
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
  (300, '4.C.2', 'Tidak Terpenuhi', 0, 'Jika masih TERDAPAT hasil evaluasi dan rekomendasi berulang pada LHE T-1 dibandingkan LHE T-2', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (301, '4.C.3', 'Terpenuhi', 1, 'Nilai LHE meningkat dari tahun sebelumnya', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (302, '4.C.3', 'Tidak Terpenuhi', 0, 'Nilai LHE menurun dari tahun sebelumnya', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (303, '4.C.4', 'Terpenuhi', 1, 'Jika telah dilakukan MONITORING SECARA PERIODIK atas capaian rencana aksi tindak lanjut hasil evaluasi dan rekomendasi AKIP', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (304, '4.C.4', 'Tidak Terpenuhi', 0, 'Jika TIDAK terdapat dokumen Rencana Aksi tindak lanjut pemenuhan evaluasi dan rekomendasi AKIP', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026)
ON DUPLICATE KEY UPDATE `kriteria_id` = VALUES(`kriteria_id`), `nilai` = VALUES(`nilai`), `skor` = VALUES(`skor`), `keterangan` = VALUES(`keterangan`), `created_at` = VALUES(`created_at`), `updated_at` = VALUES(`updated_at`), `tahun` = VALUES(`tahun`);
INSERT INTO `lke_parameter` (`id`, `kriteria_id`, `nilai`, `skor`, `keterangan`, `created_at`, `updated_at`, `tahun`) VALUES
  (305, '4.C.5', 'Terpenuhi', 1, 'Telah terjadi perbaikan dan peningkatan capaian Indikator Kinerja Sasaran Strategis (IKSS)', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (306, '4.C.5', 'Tidak Terpenuhi', 0, 'Belum terjadi perbaikan atau peningkatan capaian Indikator Kinerja Sasaran Strategis (IKSS)', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (307, '4.C.6', 'Terpenuhi', 1, 'Jika hasil dari evaluasi dan rekomendasi pada LHE T-1 TELAH menjadi Rencana Aksi Kinerja tahun berjalan', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026),
  (308, '4.C.6', 'Tidak Terpenuhi', 0, 'Jika hasil dari evaluasi dan rekomendasi pada LHE T-1 BELUM menjadi Rencana Aksi Kinerja tahun berjalan', '2026-10-07 10:11:21', '2026-10-07 10:11:21', 2026)
ON DUPLICATE KEY UPDATE `kriteria_id` = VALUES(`kriteria_id`), `nilai` = VALUES(`nilai`), `skor` = VALUES(`skor`), `keterangan` = VALUES(`keterangan`), `created_at` = VALUES(`created_at`), `updated_at` = VALUES(`updated_at`), `tahun` = VALUES(`tahun`);

SET FOREIGN_KEY_CHECKS = 1;
-- =============================================================================
-- SELESAI: SEEDERS KHUSUS TAHUN 2026
-- =============================================================================
