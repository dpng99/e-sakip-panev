-- =============================================================================
-- PROSAKIP / PANEV KEJAKSAAN - IDEMPOTENT SCHEMA & COLUMN SYNCHRONIZATION
-- Dibuat Berdasarkan Skema Rujukan: simemek3.sql
-- Waktu Dibuat: 2026-10-18 18:19:48
-- Sifat Script: 100% IDEMPOTEN (Aman dijalankan berulang kali)
-- Aturan     : Menambah tabel/kolom yang belum ada; jika sudah ada, dilewati.
-- =============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- -----------------------------------------------------------------------------
-- TABEL: `dokumen_sakips`
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `dokumen_sakips` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kategori` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Misal: pedoman, template_2026, arsip_2025',
  `judul` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `urutan` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Kolom `id` pada `dokumen_sakips`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'dokumen_sakips' AND COLUMN_NAME = 'id';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `dokumen_sakips` ADD COLUMN `id` bigint unsigned NOT NULL AUTO_INCREMENT FIRST', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `kategori` pada `dokumen_sakips`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'dokumen_sakips' AND COLUMN_NAME = 'kategori';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `dokumen_sakips` ADD COLUMN `kategori` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT ''Misal: pedoman, template_2026, arsip_2025'' AFTER `id`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `judul` pada `dokumen_sakips`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'dokumen_sakips' AND COLUMN_NAME = 'judul';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `dokumen_sakips` ADD COLUMN `judul` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL AFTER `kategori`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `deskripsi` pada `dokumen_sakips`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'dokumen_sakips' AND COLUMN_NAME = 'deskripsi';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `dokumen_sakips` ADD COLUMN `deskripsi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci AFTER `judul`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `url` pada `dokumen_sakips`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'dokumen_sakips' AND COLUMN_NAME = 'url';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `dokumen_sakips` ADD COLUMN `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL AFTER `deskripsi`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `icon` pada `dokumen_sakips`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'dokumen_sakips' AND COLUMN_NAME = 'icon';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `dokumen_sakips` ADD COLUMN `icon` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `url`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `urutan` pada `dokumen_sakips`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'dokumen_sakips' AND COLUMN_NAME = 'urutan';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `dokumen_sakips` ADD COLUMN `urutan` int NOT NULL DEFAULT ''0'' AFTER `icon`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `is_active` pada `dokumen_sakips`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'dokumen_sakips' AND COLUMN_NAME = 'is_active';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `dokumen_sakips` ADD COLUMN `is_active` tinyint(1) NOT NULL DEFAULT ''1'' AFTER `urutan`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `created_at` pada `dokumen_sakips`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'dokumen_sakips' AND COLUMN_NAME = 'created_at';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `dokumen_sakips` ADD COLUMN `created_at` timestamp NULL DEFAULT NULL AFTER `is_active`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `updated_at` pada `dokumen_sakips`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'dokumen_sakips' AND COLUMN_NAME = 'updated_at';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `dokumen_sakips` ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL AFTER `created_at`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- -----------------------------------------------------------------------------
-- TABEL: `ikss_calculation_runs`
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `ikss_calculation_runs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `year` smallint unsigned NOT NULL,
  `quarter` tinyint unsigned NOT NULL,
  `satker_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `scope` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'satker',
  `trigger` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'manual',
  `status` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'running',
  `parameters_count` int unsigned NOT NULL DEFAULT '0',
  `values_count` int unsigned NOT NULL DEFAULT '0',
  `started_at` timestamp NULL DEFAULT NULL,
  `finished_at` timestamp NULL DEFAULT NULL,
  `error_message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `stats` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ikss_calculation_run_period_idx` (`year`,`quarter`,`status`),
  KEY `ikss_calculation_run_satker_idx` (`satker_id`,`year`,`quarter`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Kolom `id` pada `ikss_calculation_runs`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_calculation_runs' AND COLUMN_NAME = 'id';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_calculation_runs` ADD COLUMN `id` bigint unsigned NOT NULL AUTO_INCREMENT FIRST', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `year` pada `ikss_calculation_runs`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_calculation_runs' AND COLUMN_NAME = 'year';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_calculation_runs` ADD COLUMN `year` smallint unsigned NOT NULL AFTER `id`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `quarter` pada `ikss_calculation_runs`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_calculation_runs' AND COLUMN_NAME = 'quarter';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_calculation_runs` ADD COLUMN `quarter` tinyint unsigned NOT NULL AFTER `year`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `satker_id` pada `ikss_calculation_runs`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_calculation_runs' AND COLUMN_NAME = 'satker_id';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_calculation_runs` ADD COLUMN `satker_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `quarter`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `scope` pada `ikss_calculation_runs`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_calculation_runs' AND COLUMN_NAME = 'scope';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_calculation_runs` ADD COLUMN `scope` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ''satker'' AFTER `satker_id`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `trigger` pada `ikss_calculation_runs`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_calculation_runs' AND COLUMN_NAME = 'trigger';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_calculation_runs` ADD COLUMN `trigger` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ''manual'' AFTER `scope`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `status` pada `ikss_calculation_runs`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_calculation_runs' AND COLUMN_NAME = 'status';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_calculation_runs` ADD COLUMN `status` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ''running'' AFTER `trigger`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `parameters_count` pada `ikss_calculation_runs`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_calculation_runs' AND COLUMN_NAME = 'parameters_count';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_calculation_runs` ADD COLUMN `parameters_count` int unsigned NOT NULL DEFAULT ''0'' AFTER `status`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `values_count` pada `ikss_calculation_runs`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_calculation_runs' AND COLUMN_NAME = 'values_count';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_calculation_runs` ADD COLUMN `values_count` int unsigned NOT NULL DEFAULT ''0'' AFTER `parameters_count`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `started_at` pada `ikss_calculation_runs`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_calculation_runs' AND COLUMN_NAME = 'started_at';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_calculation_runs` ADD COLUMN `started_at` timestamp NULL DEFAULT NULL AFTER `values_count`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `finished_at` pada `ikss_calculation_runs`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_calculation_runs' AND COLUMN_NAME = 'finished_at';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_calculation_runs` ADD COLUMN `finished_at` timestamp NULL DEFAULT NULL AFTER `started_at`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `error_message` pada `ikss_calculation_runs`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_calculation_runs' AND COLUMN_NAME = 'error_message';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_calculation_runs` ADD COLUMN `error_message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci AFTER `finished_at`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `stats` pada `ikss_calculation_runs`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_calculation_runs' AND COLUMN_NAME = 'stats';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_calculation_runs` ADD COLUMN `stats` json DEFAULT NULL AFTER `error_message`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `created_at` pada `ikss_calculation_runs`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_calculation_runs' AND COLUMN_NAME = 'created_at';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_calculation_runs` ADD COLUMN `created_at` timestamp NULL DEFAULT NULL AFTER `stats`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `updated_at` pada `ikss_calculation_runs`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_calculation_runs' AND COLUMN_NAME = 'updated_at';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_calculation_runs` ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL AFTER `created_at`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- -----------------------------------------------------------------------------
-- TABEL: `ikss_parameter_dependencies`
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `ikss_parameter_dependencies` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `parameter_id` bigint unsigned NOT NULL,
  `source_parameter_id` bigint unsigned NOT NULL,
  `role` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'component',
  `weight` decimal(16,6) DEFAULT NULL,
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ikss_parameter_dependency_unique` (`parameter_id`,`source_parameter_id`,`role`),
  KEY `ikss_parameter_dependency_source_idx` (`source_parameter_id`,`parameter_id`),
  CONSTRAINT `ikss_parameter_dependencies_parameter_id_foreign` FOREIGN KEY (`parameter_id`) REFERENCES `ikss_parameters` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ikss_parameter_dependencies_source_parameter_id_foreign` FOREIGN KEY (`source_parameter_id`) REFERENCES `ikss_parameters` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=569 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Kolom `id` pada `ikss_parameter_dependencies`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameter_dependencies' AND COLUMN_NAME = 'id';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameter_dependencies` ADD COLUMN `id` bigint unsigned NOT NULL AUTO_INCREMENT FIRST', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `parameter_id` pada `ikss_parameter_dependencies`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameter_dependencies' AND COLUMN_NAME = 'parameter_id';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameter_dependencies` ADD COLUMN `parameter_id` bigint unsigned NOT NULL AFTER `id`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `source_parameter_id` pada `ikss_parameter_dependencies`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameter_dependencies' AND COLUMN_NAME = 'source_parameter_id';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameter_dependencies` ADD COLUMN `source_parameter_id` bigint unsigned NOT NULL AFTER `parameter_id`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `role` pada `ikss_parameter_dependencies`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameter_dependencies' AND COLUMN_NAME = 'role';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameter_dependencies` ADD COLUMN `role` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ''component'' AFTER `source_parameter_id`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `weight` pada `ikss_parameter_dependencies`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameter_dependencies' AND COLUMN_NAME = 'weight';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameter_dependencies` ADD COLUMN `weight` decimal(16,6) DEFAULT NULL AFTER `role`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `sort_order` pada `ikss_parameter_dependencies`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameter_dependencies' AND COLUMN_NAME = 'sort_order';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameter_dependencies` ADD COLUMN `sort_order` int unsigned NOT NULL DEFAULT ''0'' AFTER `weight`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `created_at` pada `ikss_parameter_dependencies`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameter_dependencies' AND COLUMN_NAME = 'created_at';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameter_dependencies` ADD COLUMN `created_at` timestamp NULL DEFAULT NULL AFTER `sort_order`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `updated_at` pada `ikss_parameter_dependencies`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameter_dependencies' AND COLUMN_NAME = 'updated_at';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameter_dependencies` ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL AFTER `created_at`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- -----------------------------------------------------------------------------
-- TABEL: `ikss_parameter_groups`
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `ikss_parameter_groups` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ikss_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_id` bigint unsigned DEFAULT NULL,
  `code` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `section_code` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `group_type` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'table',
  `settings` json DEFAULT NULL,
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ikss_parameter_groups_code_unique` (`code`),
  KEY `ikss_parameter_groups_parent_id_foreign` (`parent_id`),
  KEY `ikss_parameter_group_catalog_idx` (`ikss_id`,`is_active`,`sort_order`),
  KEY `ikss_parameter_group_section_idx` (`section_code`,`is_active`),
  CONSTRAINT `ikss_parameter_groups_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `ikss_parameter_groups` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Kolom `id` pada `ikss_parameter_groups`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameter_groups' AND COLUMN_NAME = 'id';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameter_groups` ADD COLUMN `id` bigint unsigned NOT NULL AUTO_INCREMENT FIRST', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `ikss_id` pada `ikss_parameter_groups`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameter_groups' AND COLUMN_NAME = 'ikss_id';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameter_groups` ADD COLUMN `ikss_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `id`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `parent_id` pada `ikss_parameter_groups`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameter_groups' AND COLUMN_NAME = 'parent_id';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameter_groups` ADD COLUMN `parent_id` bigint unsigned DEFAULT NULL AFTER `ikss_id`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `code` pada `ikss_parameter_groups`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameter_groups' AND COLUMN_NAME = 'code';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameter_groups` ADD COLUMN `code` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL AFTER `parent_id`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `name` pada `ikss_parameter_groups`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameter_groups' AND COLUMN_NAME = 'name';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameter_groups` ADD COLUMN `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL AFTER `code`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `description` pada `ikss_parameter_groups`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameter_groups' AND COLUMN_NAME = 'description';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameter_groups` ADD COLUMN `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci AFTER `name`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `section_code` pada `ikss_parameter_groups`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameter_groups' AND COLUMN_NAME = 'section_code';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameter_groups` ADD COLUMN `section_code` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `description`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `group_type` pada `ikss_parameter_groups`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameter_groups' AND COLUMN_NAME = 'group_type';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameter_groups` ADD COLUMN `group_type` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ''table'' AFTER `section_code`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `settings` pada `ikss_parameter_groups`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameter_groups' AND COLUMN_NAME = 'settings';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameter_groups` ADD COLUMN `settings` json DEFAULT NULL AFTER `group_type`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `sort_order` pada `ikss_parameter_groups`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameter_groups' AND COLUMN_NAME = 'sort_order';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameter_groups` ADD COLUMN `sort_order` int unsigned NOT NULL DEFAULT ''0'' AFTER `settings`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `is_active` pada `ikss_parameter_groups`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameter_groups' AND COLUMN_NAME = 'is_active';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameter_groups` ADD COLUMN `is_active` tinyint(1) NOT NULL DEFAULT ''1'' AFTER `sort_order`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `created_at` pada `ikss_parameter_groups`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameter_groups' AND COLUMN_NAME = 'created_at';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameter_groups` ADD COLUMN `created_at` timestamp NULL DEFAULT NULL AFTER `is_active`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `updated_at` pada `ikss_parameter_groups`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameter_groups' AND COLUMN_NAME = 'updated_at';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameter_groups` ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL AFTER `created_at`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- -----------------------------------------------------------------------------
-- TABEL: `ikss_parameter_value_items`
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `ikss_parameter_value_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `parameter_value_id` bigint unsigned NOT NULL,
  `item_key` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_label` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value_decimal` decimal(24,6) DEFAULT NULL,
  `value_text` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ikss_parameter_value_item_unique` (`parameter_value_id`,`item_key`),
  KEY `ikss_parameter_value_item_key_idx` (`item_key`,`parameter_value_id`),
  CONSTRAINT `ikss_parameter_value_items_parameter_value_id_foreign` FOREIGN KEY (`parameter_value_id`) REFERENCES `ikss_parameter_values` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Kolom `id` pada `ikss_parameter_value_items`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameter_value_items' AND COLUMN_NAME = 'id';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameter_value_items` ADD COLUMN `id` bigint unsigned NOT NULL AUTO_INCREMENT FIRST', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `parameter_value_id` pada `ikss_parameter_value_items`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameter_value_items' AND COLUMN_NAME = 'parameter_value_id';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameter_value_items` ADD COLUMN `parameter_value_id` bigint unsigned NOT NULL AFTER `id`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `item_key` pada `ikss_parameter_value_items`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameter_value_items' AND COLUMN_NAME = 'item_key';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameter_value_items` ADD COLUMN `item_key` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL AFTER `parameter_value_id`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `item_label` pada `ikss_parameter_value_items`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameter_value_items' AND COLUMN_NAME = 'item_label';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameter_value_items` ADD COLUMN `item_label` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL AFTER `item_key`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `value_decimal` pada `ikss_parameter_value_items`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameter_value_items' AND COLUMN_NAME = 'value_decimal';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameter_value_items` ADD COLUMN `value_decimal` decimal(24,6) DEFAULT NULL AFTER `item_label`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `value_text` pada `ikss_parameter_value_items`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameter_value_items' AND COLUMN_NAME = 'value_text';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameter_value_items` ADD COLUMN `value_text` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci AFTER `value_decimal`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `sort_order` pada `ikss_parameter_value_items`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameter_value_items' AND COLUMN_NAME = 'sort_order';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameter_value_items` ADD COLUMN `sort_order` int unsigned NOT NULL DEFAULT ''0'' AFTER `value_text`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `metadata` pada `ikss_parameter_value_items`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameter_value_items' AND COLUMN_NAME = 'metadata';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameter_value_items` ADD COLUMN `metadata` json DEFAULT NULL AFTER `sort_order`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `created_at` pada `ikss_parameter_value_items`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameter_value_items' AND COLUMN_NAME = 'created_at';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameter_value_items` ADD COLUMN `created_at` timestamp NULL DEFAULT NULL AFTER `metadata`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `updated_at` pada `ikss_parameter_value_items`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameter_value_items' AND COLUMN_NAME = 'updated_at';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameter_value_items` ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL AFTER `created_at`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- -----------------------------------------------------------------------------
-- TABEL: `ikss_parameter_values`
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `ikss_parameter_values` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `parameter_id` bigint unsigned NOT NULL,
  `satker_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `year` smallint unsigned NOT NULL,
  `quarter` tinyint unsigned NOT NULL,
  `month` tinyint unsigned NOT NULL DEFAULT '0',
  `value_decimal` decimal(24,6) DEFAULT NULL,
  `value_text` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `source_type` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'manual',
  `status` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `source_count` int unsigned NOT NULL DEFAULT '1',
  `completeness` decimal(5,2) NOT NULL DEFAULT '100.00',
  `metadata` json DEFAULT NULL,
  `entered_by` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `verified_by` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `calculated_at` timestamp NULL DEFAULT NULL,
  `lock_version` int unsigned NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ikss_parameter_value_period_unique` (`parameter_id`,`satker_id`,`year`,`quarter`,`month`),
  KEY `ikss_parameter_value_satker_period_idx` (`satker_id`,`year`,`quarter`),
  KEY `ikss_parameter_value_parameter_period_idx` (`parameter_id`,`year`,`quarter`),
  KEY `ikss_parameter_value_source_period_idx` (`source_type`,`year`,`quarter`),
  CONSTRAINT `ikss_parameter_values_parameter_id_foreign` FOREIGN KEY (`parameter_id`) REFERENCES `ikss_parameters` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Kolom `id` pada `ikss_parameter_values`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameter_values' AND COLUMN_NAME = 'id';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameter_values` ADD COLUMN `id` bigint unsigned NOT NULL AUTO_INCREMENT FIRST', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `parameter_id` pada `ikss_parameter_values`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameter_values' AND COLUMN_NAME = 'parameter_id';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameter_values` ADD COLUMN `parameter_id` bigint unsigned NOT NULL AFTER `id`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `satker_id` pada `ikss_parameter_values`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameter_values' AND COLUMN_NAME = 'satker_id';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameter_values` ADD COLUMN `satker_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL AFTER `parameter_id`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `year` pada `ikss_parameter_values`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameter_values' AND COLUMN_NAME = 'year';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameter_values` ADD COLUMN `year` smallint unsigned NOT NULL AFTER `satker_id`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `quarter` pada `ikss_parameter_values`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameter_values' AND COLUMN_NAME = 'quarter';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameter_values` ADD COLUMN `quarter` tinyint unsigned NOT NULL AFTER `year`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `month` pada `ikss_parameter_values`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameter_values' AND COLUMN_NAME = 'month';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameter_values` ADD COLUMN `month` tinyint unsigned NOT NULL DEFAULT ''0'' AFTER `quarter`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `value_decimal` pada `ikss_parameter_values`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameter_values' AND COLUMN_NAME = 'value_decimal';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameter_values` ADD COLUMN `value_decimal` decimal(24,6) DEFAULT NULL AFTER `month`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `value_text` pada `ikss_parameter_values`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameter_values' AND COLUMN_NAME = 'value_text';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameter_values` ADD COLUMN `value_text` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci AFTER `value_decimal`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `source_type` pada `ikss_parameter_values`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameter_values' AND COLUMN_NAME = 'source_type';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameter_values` ADD COLUMN `source_type` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ''manual'' AFTER `value_text`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `status` pada `ikss_parameter_values`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameter_values' AND COLUMN_NAME = 'status';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameter_values` ADD COLUMN `status` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ''draft'' AFTER `source_type`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `source_count` pada `ikss_parameter_values`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameter_values' AND COLUMN_NAME = 'source_count';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameter_values` ADD COLUMN `source_count` int unsigned NOT NULL DEFAULT ''1'' AFTER `status`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `completeness` pada `ikss_parameter_values`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameter_values' AND COLUMN_NAME = 'completeness';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameter_values` ADD COLUMN `completeness` decimal(5,2) NOT NULL DEFAULT ''100.00'' AFTER `source_count`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `metadata` pada `ikss_parameter_values`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameter_values' AND COLUMN_NAME = 'metadata';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameter_values` ADD COLUMN `metadata` json DEFAULT NULL AFTER `completeness`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `entered_by` pada `ikss_parameter_values`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameter_values' AND COLUMN_NAME = 'entered_by';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameter_values` ADD COLUMN `entered_by` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `metadata`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `verified_by` pada `ikss_parameter_values`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameter_values' AND COLUMN_NAME = 'verified_by';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameter_values` ADD COLUMN `verified_by` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `entered_by`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `verified_at` pada `ikss_parameter_values`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameter_values' AND COLUMN_NAME = 'verified_at';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameter_values` ADD COLUMN `verified_at` timestamp NULL DEFAULT NULL AFTER `verified_by`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `calculated_at` pada `ikss_parameter_values`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameter_values' AND COLUMN_NAME = 'calculated_at';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameter_values` ADD COLUMN `calculated_at` timestamp NULL DEFAULT NULL AFTER `verified_at`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `lock_version` pada `ikss_parameter_values`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameter_values' AND COLUMN_NAME = 'lock_version';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameter_values` ADD COLUMN `lock_version` int unsigned NOT NULL DEFAULT ''1'' AFTER `calculated_at`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `created_at` pada `ikss_parameter_values`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameter_values' AND COLUMN_NAME = 'created_at';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameter_values` ADD COLUMN `created_at` timestamp NULL DEFAULT NULL AFTER `lock_version`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `updated_at` pada `ikss_parameter_values`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameter_values' AND COLUMN_NAME = 'updated_at';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameter_values` ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL AFTER `created_at`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- -----------------------------------------------------------------------------
-- TABEL: `ikss_parameters`
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `ikss_parameters` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ikss_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_id` bigint unsigned DEFAULT NULL,
  `group_id` bigint unsigned DEFAULT NULL,
  `code` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `parameter_role` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'input',
  `input_mode` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'scalar',
  `source_type` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'manual',
  `source_reference` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `legacy_indicator_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `value_type` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'number',
  `unit` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `period_type` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'quarterly',
  `calculation_method` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'input',
  `aggregation_method` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'sum',
  `aggregation_scope` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'children',
  `entry_levels` json DEFAULT NULL,
  `aggregate_to_levels` json DEFAULT NULL,
  `formula_config` json DEFAULT NULL,
  `decimal_places` tinyint unsigned NOT NULL DEFAULT '2',
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `is_result` tinyint(1) NOT NULL DEFAULT '0',
  `is_required` tinyint(1) NOT NULL DEFAULT '0',
  `include_in_report` tinyint(1) NOT NULL DEFAULT '1',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `valid_from_year` smallint unsigned DEFAULT NULL,
  `valid_until_year` smallint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ikss_parameters_ikss_id_code_unique` (`ikss_id`,`code`),
  KEY `ikss_parameters_parent_id_foreign` (`parent_id`),
  KEY `ikss_parameter_catalog_idx` (`ikss_id`,`is_active`,`sort_order`),
  KEY `ikss_parameter_legacy_idx` (`legacy_indicator_id`,`is_active`),
  KEY `ikss_parameter_group_order_idx` (`group_id`,`is_active`,`sort_order`),
  KEY `ikss_parameter_source_idx` (`source_type`,`source_reference`),
  CONSTRAINT `ikss_parameters_group_id_foreign` FOREIGN KEY (`group_id`) REFERENCES `ikss_parameter_groups` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ikss_parameters_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `ikss_parameters` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=505 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Kolom `id` pada `ikss_parameters`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameters' AND COLUMN_NAME = 'id';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameters` ADD COLUMN `id` bigint unsigned NOT NULL AUTO_INCREMENT FIRST', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `ikss_id` pada `ikss_parameters`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameters' AND COLUMN_NAME = 'ikss_id';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameters` ADD COLUMN `ikss_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL AFTER `id`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `parent_id` pada `ikss_parameters`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameters' AND COLUMN_NAME = 'parent_id';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameters` ADD COLUMN `parent_id` bigint unsigned DEFAULT NULL AFTER `ikss_id`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `group_id` pada `ikss_parameters`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameters' AND COLUMN_NAME = 'group_id';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameters` ADD COLUMN `group_id` bigint unsigned DEFAULT NULL AFTER `parent_id`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `code` pada `ikss_parameters`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameters' AND COLUMN_NAME = 'code';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameters` ADD COLUMN `code` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL AFTER `group_id`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `name` pada `ikss_parameters`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameters' AND COLUMN_NAME = 'name';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameters` ADD COLUMN `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL AFTER `code`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `description` pada `ikss_parameters`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameters' AND COLUMN_NAME = 'description';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameters` ADD COLUMN `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci AFTER `name`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `parameter_role` pada `ikss_parameters`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameters' AND COLUMN_NAME = 'parameter_role';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameters` ADD COLUMN `parameter_role` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ''input'' AFTER `description`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `input_mode` pada `ikss_parameters`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameters' AND COLUMN_NAME = 'input_mode';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameters` ADD COLUMN `input_mode` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ''scalar'' AFTER `parameter_role`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `source_type` pada `ikss_parameters`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameters' AND COLUMN_NAME = 'source_type';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameters` ADD COLUMN `source_type` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ''manual'' AFTER `input_mode`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `source_reference` pada `ikss_parameters`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameters' AND COLUMN_NAME = 'source_reference';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameters` ADD COLUMN `source_reference` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `source_type`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `legacy_indicator_id` pada `ikss_parameters`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameters' AND COLUMN_NAME = 'legacy_indicator_id';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameters` ADD COLUMN `legacy_indicator_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `source_reference`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `value_type` pada `ikss_parameters`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameters' AND COLUMN_NAME = 'value_type';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameters` ADD COLUMN `value_type` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ''number'' AFTER `legacy_indicator_id`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `unit` pada `ikss_parameters`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameters' AND COLUMN_NAME = 'unit';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameters` ADD COLUMN `unit` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `value_type`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `period_type` pada `ikss_parameters`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameters' AND COLUMN_NAME = 'period_type';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameters` ADD COLUMN `period_type` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ''quarterly'' AFTER `unit`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `calculation_method` pada `ikss_parameters`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameters' AND COLUMN_NAME = 'calculation_method';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameters` ADD COLUMN `calculation_method` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ''input'' AFTER `period_type`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `aggregation_method` pada `ikss_parameters`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameters' AND COLUMN_NAME = 'aggregation_method';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameters` ADD COLUMN `aggregation_method` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ''sum'' AFTER `calculation_method`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `aggregation_scope` pada `ikss_parameters`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameters' AND COLUMN_NAME = 'aggregation_scope';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameters` ADD COLUMN `aggregation_scope` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ''children'' AFTER `aggregation_method`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `entry_levels` pada `ikss_parameters`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameters' AND COLUMN_NAME = 'entry_levels';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameters` ADD COLUMN `entry_levels` json DEFAULT NULL AFTER `aggregation_scope`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `aggregate_to_levels` pada `ikss_parameters`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameters' AND COLUMN_NAME = 'aggregate_to_levels';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameters` ADD COLUMN `aggregate_to_levels` json DEFAULT NULL AFTER `entry_levels`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `formula_config` pada `ikss_parameters`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameters' AND COLUMN_NAME = 'formula_config';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameters` ADD COLUMN `formula_config` json DEFAULT NULL AFTER `aggregate_to_levels`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `decimal_places` pada `ikss_parameters`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameters' AND COLUMN_NAME = 'decimal_places';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameters` ADD COLUMN `decimal_places` tinyint unsigned NOT NULL DEFAULT ''2'' AFTER `formula_config`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `sort_order` pada `ikss_parameters`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameters' AND COLUMN_NAME = 'sort_order';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameters` ADD COLUMN `sort_order` int unsigned NOT NULL DEFAULT ''0'' AFTER `decimal_places`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `is_result` pada `ikss_parameters`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameters' AND COLUMN_NAME = 'is_result';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameters` ADD COLUMN `is_result` tinyint(1) NOT NULL DEFAULT ''0'' AFTER `sort_order`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `is_required` pada `ikss_parameters`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameters' AND COLUMN_NAME = 'is_required';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameters` ADD COLUMN `is_required` tinyint(1) NOT NULL DEFAULT ''0'' AFTER `is_result`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `include_in_report` pada `ikss_parameters`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameters' AND COLUMN_NAME = 'include_in_report';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameters` ADD COLUMN `include_in_report` tinyint(1) NOT NULL DEFAULT ''1'' AFTER `is_required`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `is_active` pada `ikss_parameters`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameters' AND COLUMN_NAME = 'is_active';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameters` ADD COLUMN `is_active` tinyint(1) NOT NULL DEFAULT ''1'' AFTER `include_in_report`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `valid_from_year` pada `ikss_parameters`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameters' AND COLUMN_NAME = 'valid_from_year';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameters` ADD COLUMN `valid_from_year` smallint unsigned DEFAULT NULL AFTER `is_active`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `valid_until_year` pada `ikss_parameters`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameters' AND COLUMN_NAME = 'valid_until_year';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameters` ADD COLUMN `valid_until_year` smallint unsigned DEFAULT NULL AFTER `valid_from_year`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `created_at` pada `ikss_parameters`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameters' AND COLUMN_NAME = 'created_at';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameters` ADD COLUMN `created_at` timestamp NULL DEFAULT NULL AFTER `valid_until_year`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `updated_at` pada `ikss_parameters`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_parameters' AND COLUMN_NAME = 'updated_at';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_parameters` ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL AFTER `created_at`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- -----------------------------------------------------------------------------
-- TABEL: `ikss_results`
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `ikss_results` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ikss_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `satker_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `year` smallint unsigned NOT NULL,
  `quarter` tinyint unsigned NOT NULL,
  `target` decimal(24,6) DEFAULT NULL,
  `capaian` decimal(24,6) DEFAULT NULL,
  `achievement` decimal(12,4) DEFAULT NULL,
  `source_count` int unsigned NOT NULL DEFAULT '0',
  `completeness` decimal(5,2) NOT NULL DEFAULT '0.00',
  `status` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'calculated',
  `details` json DEFAULT NULL,
  `calculated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ikss_result_period_unique` (`ikss_id`,`satker_id`,`year`,`quarter`),
  KEY `ikss_result_satker_period_idx` (`satker_id`,`year`,`quarter`),
  KEY `ikss_result_ikss_period_idx` (`ikss_id`,`year`,`quarter`),
  KEY `ikss_result_ranking_idx` (`year`,`quarter`,`achievement`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Kolom `id` pada `ikss_results`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_results' AND COLUMN_NAME = 'id';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_results` ADD COLUMN `id` bigint unsigned NOT NULL AUTO_INCREMENT FIRST', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `ikss_id` pada `ikss_results`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_results' AND COLUMN_NAME = 'ikss_id';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_results` ADD COLUMN `ikss_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL AFTER `id`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `satker_id` pada `ikss_results`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_results' AND COLUMN_NAME = 'satker_id';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_results` ADD COLUMN `satker_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL AFTER `ikss_id`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `year` pada `ikss_results`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_results' AND COLUMN_NAME = 'year';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_results` ADD COLUMN `year` smallint unsigned NOT NULL AFTER `satker_id`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `quarter` pada `ikss_results`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_results' AND COLUMN_NAME = 'quarter';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_results` ADD COLUMN `quarter` tinyint unsigned NOT NULL AFTER `year`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `target` pada `ikss_results`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_results' AND COLUMN_NAME = 'target';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_results` ADD COLUMN `target` decimal(24,6) DEFAULT NULL AFTER `quarter`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `capaian` pada `ikss_results`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_results' AND COLUMN_NAME = 'capaian';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_results` ADD COLUMN `capaian` decimal(24,6) DEFAULT NULL AFTER `target`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `achievement` pada `ikss_results`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_results' AND COLUMN_NAME = 'achievement';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_results` ADD COLUMN `achievement` decimal(12,4) DEFAULT NULL AFTER `capaian`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `source_count` pada `ikss_results`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_results' AND COLUMN_NAME = 'source_count';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_results` ADD COLUMN `source_count` int unsigned NOT NULL DEFAULT ''0'' AFTER `achievement`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `completeness` pada `ikss_results`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_results' AND COLUMN_NAME = 'completeness';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_results` ADD COLUMN `completeness` decimal(5,2) NOT NULL DEFAULT ''0.00'' AFTER `source_count`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `status` pada `ikss_results`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_results' AND COLUMN_NAME = 'status';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_results` ADD COLUMN `status` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ''calculated'' AFTER `completeness`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `details` pada `ikss_results`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_results' AND COLUMN_NAME = 'details';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_results` ADD COLUMN `details` json DEFAULT NULL AFTER `status`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `calculated_at` pada `ikss_results`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_results' AND COLUMN_NAME = 'calculated_at';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_results` ADD COLUMN `calculated_at` timestamp NULL DEFAULT NULL AFTER `details`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `created_at` pada `ikss_results`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_results' AND COLUMN_NAME = 'created_at';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_results` ADD COLUMN `created_at` timestamp NULL DEFAULT NULL AFTER `calculated_at`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `updated_at` pada `ikss_results`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ikss_results' AND COLUMN_NAME = 'updated_at';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `ikss_results` ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL AFTER `created_at`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- -----------------------------------------------------------------------------
-- TABEL: `indikator_kinerja_program`
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `indikator_kinerja_program` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sasaran_program_id` bigint unsigned NOT NULL,
  `kode_ikp` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Misal: IKP 13.1, IKP 3.2',
  `nama_ikp` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nama Indikator Kinerja Program',
  `id_satker` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'FK ke satker pengampu',
  `sifat_node` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'AVERAGE' COMMENT 'AVERAGE, RATIO_PERCENTAGE, INDEX_SCORE, DIRECT_INPUT',
  `tahun` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_ikp` (`kode_ikp`,`id_satker`,`tahun`),
  KEY `indikator_kinerja_program_sasaran_program_id_foreign` (`sasaran_program_id`),
  CONSTRAINT `indikator_kinerja_program_sasaran_program_id_foreign` FOREIGN KEY (`sasaran_program_id`) REFERENCES `sasaran_program` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=127 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Kolom `id` pada `indikator_kinerja_program`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'indikator_kinerja_program' AND COLUMN_NAME = 'id';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `indikator_kinerja_program` ADD COLUMN `id` bigint unsigned NOT NULL AUTO_INCREMENT FIRST', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `sasaran_program_id` pada `indikator_kinerja_program`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'indikator_kinerja_program' AND COLUMN_NAME = 'sasaran_program_id';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `indikator_kinerja_program` ADD COLUMN `sasaran_program_id` bigint unsigned NOT NULL AFTER `id`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `kode_ikp` pada `indikator_kinerja_program`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'indikator_kinerja_program' AND COLUMN_NAME = 'kode_ikp';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `indikator_kinerja_program` ADD COLUMN `kode_ikp` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT ''Misal: IKP 13.1, IKP 3.2'' AFTER `sasaran_program_id`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `nama_ikp` pada `indikator_kinerja_program`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'indikator_kinerja_program' AND COLUMN_NAME = 'nama_ikp';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `indikator_kinerja_program` ADD COLUMN `nama_ikp` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT ''Nama Indikator Kinerja Program'' AFTER `kode_ikp`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `id_satker` pada `indikator_kinerja_program`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'indikator_kinerja_program' AND COLUMN_NAME = 'id_satker';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `indikator_kinerja_program` ADD COLUMN `id_satker` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT ''FK ke satker pengampu'' AFTER `nama_ikp`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `sifat_node` pada `indikator_kinerja_program`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'indikator_kinerja_program' AND COLUMN_NAME = 'sifat_node';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `indikator_kinerja_program` ADD COLUMN `sifat_node` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ''AVERAGE'' COMMENT ''AVERAGE, RATIO_PERCENTAGE, INDEX_SCORE, DIRECT_INPUT'' AFTER `id_satker`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `tahun` pada `indikator_kinerja_program`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'indikator_kinerja_program' AND COLUMN_NAME = 'tahun';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `indikator_kinerja_program` ADD COLUMN `tahun` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL AFTER `sifat_node`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `created_at` pada `indikator_kinerja_program`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'indikator_kinerja_program' AND COLUMN_NAME = 'created_at';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `indikator_kinerja_program` ADD COLUMN `created_at` timestamp NULL DEFAULT NULL AFTER `tahun`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `updated_at` pada `indikator_kinerja_program`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'indikator_kinerja_program' AND COLUMN_NAME = 'updated_at';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `indikator_kinerja_program` ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL AFTER `created_at`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- -----------------------------------------------------------------------------
-- TABEL: `indikator_saspro`
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `indikator_saspro` (
  `kode_indikator` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_sastra` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kode_saspro` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_indikator` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi_indikator_saspro` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `link` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lingkup` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `tahun` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hide` varchar(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `urutan` int unsigned DEFAULT NULL,
  PRIMARY KEY (`kode_indikator`),
  KEY `indikator_saspro_kode_saspro_foreign` (`kode_saspro`),
  KEY `indikator_saspro_kode_sastra_foreign` (`kode_sastra`),
  CONSTRAINT `indikator_saspro_kode_saspro_foreign` FOREIGN KEY (`kode_saspro`) REFERENCES `sakip_saspro_new` (`id_saspro`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `indikator_saspro_kode_sastra_foreign` FOREIGN KEY (`kode_sastra`) REFERENCES `sakip_sastra_new` (`id_sastra`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Kolom `kode_indikator` pada `indikator_saspro`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'indikator_saspro' AND COLUMN_NAME = 'kode_indikator';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `indikator_saspro` ADD COLUMN `kode_indikator` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL FIRST', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `kode_sastra` pada `indikator_saspro`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'indikator_saspro' AND COLUMN_NAME = 'kode_sastra';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `indikator_saspro` ADD COLUMN `kode_sastra` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `kode_indikator`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `kode_saspro` pada `indikator_saspro`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'indikator_saspro' AND COLUMN_NAME = 'kode_saspro';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `indikator_saspro` ADD COLUMN `kode_saspro` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `kode_sastra`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `nama_indikator` pada `indikator_saspro`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'indikator_saspro' AND COLUMN_NAME = 'nama_indikator';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `indikator_saspro` ADD COLUMN `nama_indikator` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL AFTER `kode_saspro`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `deskripsi_indikator_saspro` pada `indikator_saspro`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'indikator_saspro' AND COLUMN_NAME = 'deskripsi_indikator_saspro';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `indikator_saspro` ADD COLUMN `deskripsi_indikator_saspro` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci AFTER `nama_indikator`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `link` pada `indikator_saspro`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'indikator_saspro' AND COLUMN_NAME = 'link';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `indikator_saspro` ADD COLUMN `link` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `deskripsi_indikator_saspro`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `lingkup` pada `indikator_saspro`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'indikator_saspro' AND COLUMN_NAME = 'lingkup';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `indikator_saspro` ADD COLUMN `lingkup` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT ''0'' AFTER `link`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `tahun` pada `indikator_saspro`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'indikator_saspro' AND COLUMN_NAME = 'tahun';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `indikator_saspro` ADD COLUMN `tahun` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `lingkup`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `hide` pada `indikator_saspro`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'indikator_saspro' AND COLUMN_NAME = 'hide';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `indikator_saspro` ADD COLUMN `hide` varchar(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ''0'' AFTER `tahun`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `urutan` pada `indikator_saspro`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'indikator_saspro' AND COLUMN_NAME = 'urutan';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `indikator_saspro` ADD COLUMN `urutan` int unsigned DEFAULT NULL AFTER `hide`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- -----------------------------------------------------------------------------
-- TABEL: `indikator_sastra`
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `indikator_sastra` (
  `kode_indikator` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_sastra` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_indikator` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi_indikator_sastra` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `tahun` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hide` varchar(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `urutan` int unsigned DEFAULT NULL,
  `link` char(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lingkup` char(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`kode_indikator`),
  KEY `indikator_sastra_kode_sastra_foreign` (`kode_sastra`),
  CONSTRAINT `indikator_sastra_kode_sastra_foreign` FOREIGN KEY (`kode_sastra`) REFERENCES `sakip_sastra_new` (`id_sastra`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Kolom `kode_indikator` pada `indikator_sastra`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'indikator_sastra' AND COLUMN_NAME = 'kode_indikator';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `indikator_sastra` ADD COLUMN `kode_indikator` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL FIRST', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `kode_sastra` pada `indikator_sastra`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'indikator_sastra' AND COLUMN_NAME = 'kode_sastra';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `indikator_sastra` ADD COLUMN `kode_sastra` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL AFTER `kode_indikator`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `nama_indikator` pada `indikator_sastra`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'indikator_sastra' AND COLUMN_NAME = 'nama_indikator';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `indikator_sastra` ADD COLUMN `nama_indikator` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL AFTER `kode_sastra`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `deskripsi_indikator_sastra` pada `indikator_sastra`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'indikator_sastra' AND COLUMN_NAME = 'deskripsi_indikator_sastra';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `indikator_sastra` ADD COLUMN `deskripsi_indikator_sastra` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci AFTER `nama_indikator`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `tahun` pada `indikator_sastra`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'indikator_sastra' AND COLUMN_NAME = 'tahun';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `indikator_sastra` ADD COLUMN `tahun` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `deskripsi_indikator_sastra`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `hide` pada `indikator_sastra`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'indikator_sastra' AND COLUMN_NAME = 'hide';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `indikator_sastra` ADD COLUMN `hide` varchar(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ''0'' AFTER `tahun`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `urutan` pada `indikator_sastra`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'indikator_sastra' AND COLUMN_NAME = 'urutan';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `indikator_sastra` ADD COLUMN `urutan` int unsigned DEFAULT NULL AFTER `hide`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `link` pada `indikator_sastra`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'indikator_sastra' AND COLUMN_NAME = 'link';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `indikator_sastra` ADD COLUMN `link` char(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `urutan`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `lingkup` pada `indikator_sastra`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'indikator_sastra' AND COLUMN_NAME = 'lingkup';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `indikator_sastra` ADD COLUMN `lingkup` char(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `link`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- -----------------------------------------------------------------------------
-- TABEL: `komponen`
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `komponen` (
  `id` int NOT NULL,
  `nama_komponen` varchar(255) NOT NULL,
  `bobot` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Kolom `id` pada `komponen`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'komponen' AND COLUMN_NAME = 'id';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `komponen` ADD COLUMN `id` int NOT NULL FIRST', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `nama_komponen` pada `komponen`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'komponen' AND COLUMN_NAME = 'nama_komponen';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `komponen` ADD COLUMN `nama_komponen` varchar(255) NOT NULL AFTER `id`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `bobot` pada `komponen`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'komponen' AND COLUMN_NAME = 'bobot';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `komponen` ADD COLUMN `bobot` int NOT NULL AFTER `nama_komponen`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- -----------------------------------------------------------------------------
-- TABEL: `kriteria`
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `kriteria` (
  `no` bigint NOT NULL AUTO_INCREMENT,
  `kode` varchar(255) DEFAULT NULL,
  `id` varchar(512) NOT NULL,
  `id_sub_komponen` varchar(512) DEFAULT NULL,
  `nama_kriteria` varchar(512) DEFAULT NULL,
  `bukti_tulisan` varchar(512) DEFAULT NULL,
  `keterangan` varchar(512) DEFAULT NULL,
  `bukti_pengisian` varchar(512) DEFAULT NULL,
  `format_nama_file` varchar(512) DEFAULT NULL,
  PRIMARY KEY (`no`)
) ENGINE=InnoDB AUTO_INCREMENT=141 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Kolom `no` pada `kriteria`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'kriteria' AND COLUMN_NAME = 'no';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `kriteria` ADD COLUMN `no` bigint NOT NULL AUTO_INCREMENT FIRST', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `kode` pada `kriteria`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'kriteria' AND COLUMN_NAME = 'kode';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `kriteria` ADD COLUMN `kode` varchar(255) DEFAULT NULL AFTER `no`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `id` pada `kriteria`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'kriteria' AND COLUMN_NAME = 'id';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `kriteria` ADD COLUMN `id` varchar(512) NOT NULL AFTER `kode`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `id_sub_komponen` pada `kriteria`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'kriteria' AND COLUMN_NAME = 'id_sub_komponen';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `kriteria` ADD COLUMN `id_sub_komponen` varchar(512) DEFAULT NULL AFTER `id`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `nama_kriteria` pada `kriteria`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'kriteria' AND COLUMN_NAME = 'nama_kriteria';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `kriteria` ADD COLUMN `nama_kriteria` varchar(512) DEFAULT NULL AFTER `id_sub_komponen`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `bukti_tulisan` pada `kriteria`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'kriteria' AND COLUMN_NAME = 'bukti_tulisan';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `kriteria` ADD COLUMN `bukti_tulisan` varchar(512) DEFAULT NULL AFTER `nama_kriteria`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `keterangan` pada `kriteria`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'kriteria' AND COLUMN_NAME = 'keterangan';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `kriteria` ADD COLUMN `keterangan` varchar(512) DEFAULT NULL AFTER `bukti_tulisan`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `bukti_pengisian` pada `kriteria`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'kriteria' AND COLUMN_NAME = 'bukti_pengisian';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `kriteria` ADD COLUMN `bukti_pengisian` varchar(512) DEFAULT NULL AFTER `keterangan`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `format_nama_file` pada `kriteria`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'kriteria' AND COLUMN_NAME = 'format_nama_file';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `kriteria` ADD COLUMN `format_nama_file` varchar(512) DEFAULT NULL AFTER `bukti_pengisian`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- -----------------------------------------------------------------------------
-- TABEL: `lke_buktidukung`
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `lke_buktidukung` (
  `id` int NOT NULL AUTO_INCREMENT,
  `dokumen` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `format_nama_file` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `ada_di_sistem` tinyint(1) NOT NULL DEFAULT '0',
  `tabel_sumber` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tahun` smallint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lke_buktidukung_tahun_idx` (`tahun`),
  KEY `lke_buktidukung_tabel_sumber_index` (`tabel_sumber`)
) ENGINE=InnoDB AUTO_INCREMENT=270 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Kolom `id` pada `lke_buktidukung`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_buktidukung' AND COLUMN_NAME = 'id';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_buktidukung` ADD COLUMN `id` int NOT NULL AUTO_INCREMENT FIRST', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `dokumen` pada `lke_buktidukung`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_buktidukung' AND COLUMN_NAME = 'dokumen';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_buktidukung` ADD COLUMN `dokumen` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL AFTER `id`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `format_nama_file` pada `lke_buktidukung`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_buktidukung' AND COLUMN_NAME = 'format_nama_file';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_buktidukung` ADD COLUMN `format_nama_file` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL AFTER `dokumen`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `keterangan` pada `lke_buktidukung`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_buktidukung' AND COLUMN_NAME = 'keterangan';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_buktidukung` ADD COLUMN `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci AFTER `format_nama_file`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `ada_di_sistem` pada `lke_buktidukung`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_buktidukung' AND COLUMN_NAME = 'ada_di_sistem';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_buktidukung` ADD COLUMN `ada_di_sistem` tinyint(1) NOT NULL DEFAULT ''0'' AFTER `keterangan`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `tabel_sumber` pada `lke_buktidukung`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_buktidukung' AND COLUMN_NAME = 'tabel_sumber';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_buktidukung` ADD COLUMN `tabel_sumber` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL AFTER `ada_di_sistem`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `tahun` pada `lke_buktidukung`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_buktidukung' AND COLUMN_NAME = 'tahun';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_buktidukung` ADD COLUMN `tahun` smallint unsigned DEFAULT NULL AFTER `tabel_sumber`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- -----------------------------------------------------------------------------
-- TABEL: `lke_gabungan`
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `lke_gabungan` (
  `id` int NOT NULL AUTO_INCREMENT,
  `komponen_id` bigint unsigned DEFAULT NULL,
  `sub_komponen_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kriteria_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `buktidukung_id` int NOT NULL DEFAULT (0),
  `tahun` smallint unsigned NOT NULL DEFAULT '2025',
  PRIMARY KEY (`id`),
  KEY `Index 2` (`komponen_id`),
  KEY `Index 3` (`sub_komponen_id`),
  KEY `Index 4` (`kriteria_id`),
  KEY `Index 5` (`buktidukung_id`),
  KEY `lke_gabungan_tahun_idx` (`tahun`),
  KEY `lke_gabungan_tahun_kriteria_idx` (`tahun`,`kriteria_id`),
  KEY `lke_gabungan_tahun_subkomponen_foreign` (`tahun`,`sub_komponen_id`),
  CONSTRAINT `FK_lke_gabungan_lke_buktidukung` FOREIGN KEY (`buktidukung_id`) REFERENCES `lke_buktidukung` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_lke_gabungan_lke_komponen` FOREIGN KEY (`komponen_id`) REFERENCES `lke_komponen` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lke_gabungan_tahun_kriteria_foreign` FOREIGN KEY (`tahun`, `kriteria_id`) REFERENCES `lke_kriteria` (`tahun`, `kode`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `lke_gabungan_tahun_subkomponen_foreign` FOREIGN KEY (`tahun`, `sub_komponen_id`) REFERENCES `lke_subkomponen` (`tahun`, `kode`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1354 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Kolom `id` pada `lke_gabungan`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_gabungan' AND COLUMN_NAME = 'id';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_gabungan` ADD COLUMN `id` int NOT NULL AUTO_INCREMENT FIRST', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `komponen_id` pada `lke_gabungan`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_gabungan' AND COLUMN_NAME = 'komponen_id';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_gabungan` ADD COLUMN `komponen_id` bigint unsigned DEFAULT NULL AFTER `id`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `sub_komponen_id` pada `lke_gabungan`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_gabungan' AND COLUMN_NAME = 'sub_komponen_id';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_gabungan` ADD COLUMN `sub_komponen_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL AFTER `komponen_id`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `kriteria_id` pada `lke_gabungan`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_gabungan' AND COLUMN_NAME = 'kriteria_id';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_gabungan` ADD COLUMN `kriteria_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL AFTER `sub_komponen_id`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `buktidukung_id` pada `lke_gabungan`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_gabungan' AND COLUMN_NAME = 'buktidukung_id';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_gabungan` ADD COLUMN `buktidukung_id` int NOT NULL DEFAULT (0) AFTER `kriteria_id`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `tahun` pada `lke_gabungan`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_gabungan' AND COLUMN_NAME = 'tahun';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_gabungan` ADD COLUMN `tahun` smallint unsigned NOT NULL DEFAULT ''2025'' AFTER `buktidukung_id`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- -----------------------------------------------------------------------------
-- TABEL: `lke_komponen`
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `lke_komponen` (
  `id` bigint unsigned NOT NULL,
  `no` int NOT NULL,
  `nama` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `bobot` decimal(5,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `tahun` smallint unsigned NOT NULL DEFAULT '2025',
  PRIMARY KEY (`id`),
  KEY `lke_komponen_tahun_idx` (`tahun`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Kolom `id` pada `lke_komponen`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_komponen' AND COLUMN_NAME = 'id';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_komponen` ADD COLUMN `id` bigint unsigned NOT NULL FIRST', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `no` pada `lke_komponen`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_komponen' AND COLUMN_NAME = 'no';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_komponen` ADD COLUMN `no` int NOT NULL AFTER `id`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `nama` pada `lke_komponen`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_komponen' AND COLUMN_NAME = 'nama';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_komponen` ADD COLUMN `nama` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL AFTER `no`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `bobot` pada `lke_komponen`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_komponen' AND COLUMN_NAME = 'bobot';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_komponen` ADD COLUMN `bobot` decimal(5,2) NOT NULL DEFAULT ''0.00'' AFTER `nama`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `created_at` pada `lke_komponen`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_komponen' AND COLUMN_NAME = 'created_at';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_komponen` ADD COLUMN `created_at` timestamp NULL DEFAULT NULL AFTER `bobot`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `updated_at` pada `lke_komponen`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_komponen' AND COLUMN_NAME = 'updated_at';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_komponen` ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL AFTER `created_at`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `tahun` pada `lke_komponen`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_komponen' AND COLUMN_NAME = 'tahun';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_komponen` ADD COLUMN `tahun` smallint unsigned NOT NULL DEFAULT ''2025'' AFTER `updated_at`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- -----------------------------------------------------------------------------
-- TABEL: `lke_kriteria`
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `lke_kriteria` (
  `id` bigint unsigned NOT NULL,
  `subkomponen_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `kode` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `dokumen_bukti` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `langkah_kerja` text COLLATE utf8mb4_unicode_ci,
  `kode_bukti` char(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `eselon_i` tinyint(1) NOT NULL DEFAULT '0',
  `kejati` tinyint(1) NOT NULL DEFAULT '0',
  `kejari` tinyint(1) NOT NULL DEFAULT '0',
  `cabjari` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `tahun` smallint unsigned NOT NULL DEFAULT '2025',
  PRIMARY KEY (`tahun`,`kode`),
  KEY `kriteria_subkomponen_id_foreign` (`subkomponen_id`),
  KEY `lke_kriteria_tahun_idx` (`tahun`),
  KEY `lke_kriteria_tahun_kode_idx` (`tahun`,`kode`),
  KEY `lke_kriteria_tahun_subkomponen_foreign` (`tahun`,`subkomponen_id`),
  CONSTRAINT `lke_kriteria_tahun_subkomponen_foreign` FOREIGN KEY (`tahun`, `subkomponen_id`) REFERENCES `lke_subkomponen` (`tahun`, `kode`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Kolom `id` pada `lke_kriteria`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_kriteria' AND COLUMN_NAME = 'id';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_kriteria` ADD COLUMN `id` bigint unsigned NOT NULL FIRST', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `subkomponen_id` pada `lke_kriteria`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_kriteria' AND COLUMN_NAME = 'subkomponen_id';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_kriteria` ADD COLUMN `subkomponen_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ''0'' AFTER `id`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `kode` pada `lke_kriteria`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_kriteria' AND COLUMN_NAME = 'kode';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_kriteria` ADD COLUMN `kode` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL AFTER `subkomponen_id`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `nama` pada `lke_kriteria`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_kriteria' AND COLUMN_NAME = 'nama';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_kriteria` ADD COLUMN `nama` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL AFTER `kode`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `dokumen_bukti` pada `lke_kriteria`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_kriteria' AND COLUMN_NAME = 'dokumen_bukti';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_kriteria` ADD COLUMN `dokumen_bukti` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci AFTER `nama`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `langkah_kerja` pada `lke_kriteria`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_kriteria' AND COLUMN_NAME = 'langkah_kerja';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_kriteria` ADD COLUMN `langkah_kerja` text COLLATE utf8mb4_unicode_ci AFTER `dokumen_bukti`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `kode_bukti` pada `lke_kriteria`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_kriteria' AND COLUMN_NAME = 'kode_bukti';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_kriteria` ADD COLUMN `kode_bukti` char(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `langkah_kerja`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `eselon_i` pada `lke_kriteria`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_kriteria' AND COLUMN_NAME = 'eselon_i';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_kriteria` ADD COLUMN `eselon_i` tinyint(1) NOT NULL DEFAULT ''0'' AFTER `kode_bukti`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `kejati` pada `lke_kriteria`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_kriteria' AND COLUMN_NAME = 'kejati';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_kriteria` ADD COLUMN `kejati` tinyint(1) NOT NULL DEFAULT ''0'' AFTER `eselon_i`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `kejari` pada `lke_kriteria`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_kriteria' AND COLUMN_NAME = 'kejari';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_kriteria` ADD COLUMN `kejari` tinyint(1) NOT NULL DEFAULT ''0'' AFTER `kejati`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `cabjari` pada `lke_kriteria`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_kriteria' AND COLUMN_NAME = 'cabjari';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_kriteria` ADD COLUMN `cabjari` tinyint(1) NOT NULL DEFAULT ''0'' AFTER `kejari`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `created_at` pada `lke_kriteria`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_kriteria' AND COLUMN_NAME = 'created_at';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_kriteria` ADD COLUMN `created_at` timestamp NULL DEFAULT NULL AFTER `cabjari`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `updated_at` pada `lke_kriteria`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_kriteria' AND COLUMN_NAME = 'updated_at';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_kriteria` ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL AFTER `created_at`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `tahun` pada `lke_kriteria`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_kriteria' AND COLUMN_NAME = 'tahun';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_kriteria` ADD COLUMN `tahun` smallint unsigned NOT NULL DEFAULT ''2025'' AFTER `updated_at`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- -----------------------------------------------------------------------------
-- TABEL: `lke_parameter`
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `lke_parameter` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kriteria_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nilai` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `skor` double(8,2) NOT NULL DEFAULT '0.00',
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `tahun` smallint unsigned NOT NULL DEFAULT '2025',
  PRIMARY KEY (`id`),
  KEY `kriteria_id` (`kriteria_id`),
  KEY `lke_parameter_tahun_idx` (`tahun`),
  KEY `lke_parameter_tahun_kriteria_idx` (`tahun`,`kriteria_id`),
  CONSTRAINT `lke_parameter_tahun_kriteria_foreign` FOREIGN KEY (`tahun`, `kriteria_id`) REFERENCES `lke_kriteria` (`tahun`, `kode`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=309 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Kolom `id` pada `lke_parameter`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_parameter' AND COLUMN_NAME = 'id';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_parameter` ADD COLUMN `id` bigint unsigned NOT NULL AUTO_INCREMENT FIRST', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `kriteria_id` pada `lke_parameter`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_parameter' AND COLUMN_NAME = 'kriteria_id';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_parameter` ADD COLUMN `kriteria_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `id`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `nilai` pada `lke_parameter`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_parameter' AND COLUMN_NAME = 'nilai';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_parameter` ADD COLUMN `nilai` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `kriteria_id`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `skor` pada `lke_parameter`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_parameter' AND COLUMN_NAME = 'skor';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_parameter` ADD COLUMN `skor` double(8,2) NOT NULL DEFAULT ''0.00'' AFTER `nilai`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `keterangan` pada `lke_parameter`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_parameter' AND COLUMN_NAME = 'keterangan';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_parameter` ADD COLUMN `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci AFTER `skor`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `created_at` pada `lke_parameter`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_parameter' AND COLUMN_NAME = 'created_at';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_parameter` ADD COLUMN `created_at` timestamp NULL DEFAULT NULL AFTER `keterangan`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `updated_at` pada `lke_parameter`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_parameter' AND COLUMN_NAME = 'updated_at';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_parameter` ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL AFTER `created_at`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `tahun` pada `lke_parameter`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_parameter' AND COLUMN_NAME = 'tahun';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_parameter` ADD COLUMN `tahun` smallint unsigned NOT NULL DEFAULT ''2025'' AFTER `updated_at`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- -----------------------------------------------------------------------------
-- TABEL: `lke_penilaian_satker`
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `lke_penilaian_satker` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_satker` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tahun` smallint unsigned NOT NULL,
  `komponen_id` bigint unsigned DEFAULT NULL,
  `subkomponen_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kriteria_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `pm_parameter_id` bigint unsigned DEFAULT NULL,
  `pm_nilai` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pm_skor` double DEFAULT NULL,
  `pm_catatan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `pm_evaluator_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parameter_id` bigint unsigned DEFAULT NULL,
  `nilai` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `skor` double NOT NULL DEFAULT '0',
  `catatan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `evaluator_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=655 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Kolom `id` pada `lke_penilaian_satker`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_penilaian_satker' AND COLUMN_NAME = 'id';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_penilaian_satker` ADD COLUMN `id` bigint unsigned NOT NULL AUTO_INCREMENT FIRST', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `id_satker` pada `lke_penilaian_satker`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_penilaian_satker' AND COLUMN_NAME = 'id_satker';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_penilaian_satker` ADD COLUMN `id_satker` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL AFTER `id`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `tahun` pada `lke_penilaian_satker`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_penilaian_satker' AND COLUMN_NAME = 'tahun';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_penilaian_satker` ADD COLUMN `tahun` smallint unsigned NOT NULL AFTER `id_satker`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `komponen_id` pada `lke_penilaian_satker`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_penilaian_satker' AND COLUMN_NAME = 'komponen_id';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_penilaian_satker` ADD COLUMN `komponen_id` bigint unsigned DEFAULT NULL AFTER `tahun`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `subkomponen_id` pada `lke_penilaian_satker`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_penilaian_satker' AND COLUMN_NAME = 'subkomponen_id';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_penilaian_satker` ADD COLUMN `subkomponen_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `komponen_id`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `kriteria_id` pada `lke_penilaian_satker`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_penilaian_satker' AND COLUMN_NAME = 'kriteria_id';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_penilaian_satker` ADD COLUMN `kriteria_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL AFTER `subkomponen_id`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `pm_parameter_id` pada `lke_penilaian_satker`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_penilaian_satker' AND COLUMN_NAME = 'pm_parameter_id';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_penilaian_satker` ADD COLUMN `pm_parameter_id` bigint unsigned DEFAULT NULL AFTER `kriteria_id`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `pm_nilai` pada `lke_penilaian_satker`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_penilaian_satker' AND COLUMN_NAME = 'pm_nilai';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_penilaian_satker` ADD COLUMN `pm_nilai` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `pm_parameter_id`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `pm_skor` pada `lke_penilaian_satker`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_penilaian_satker' AND COLUMN_NAME = 'pm_skor';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_penilaian_satker` ADD COLUMN `pm_skor` double DEFAULT NULL AFTER `pm_nilai`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `pm_catatan` pada `lke_penilaian_satker`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_penilaian_satker' AND COLUMN_NAME = 'pm_catatan';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_penilaian_satker` ADD COLUMN `pm_catatan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci AFTER `pm_skor`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `pm_evaluator_id` pada `lke_penilaian_satker`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_penilaian_satker' AND COLUMN_NAME = 'pm_evaluator_id';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_penilaian_satker` ADD COLUMN `pm_evaluator_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `pm_catatan`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `parameter_id` pada `lke_penilaian_satker`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_penilaian_satker' AND COLUMN_NAME = 'parameter_id';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_penilaian_satker` ADD COLUMN `parameter_id` bigint unsigned DEFAULT NULL AFTER `pm_evaluator_id`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `nilai` pada `lke_penilaian_satker`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_penilaian_satker' AND COLUMN_NAME = 'nilai';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_penilaian_satker` ADD COLUMN `nilai` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `parameter_id`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `skor` pada `lke_penilaian_satker`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_penilaian_satker' AND COLUMN_NAME = 'skor';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_penilaian_satker` ADD COLUMN `skor` double NOT NULL DEFAULT ''0'' AFTER `nilai`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `catatan` pada `lke_penilaian_satker`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_penilaian_satker' AND COLUMN_NAME = 'catatan';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_penilaian_satker` ADD COLUMN `catatan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci AFTER `skor`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `evaluator_id` pada `lke_penilaian_satker`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_penilaian_satker' AND COLUMN_NAME = 'evaluator_id';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_penilaian_satker` ADD COLUMN `evaluator_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `catatan`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `created_at` pada `lke_penilaian_satker`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_penilaian_satker' AND COLUMN_NAME = 'created_at';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_penilaian_satker` ADD COLUMN `created_at` timestamp NULL DEFAULT NULL AFTER `evaluator_id`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `updated_at` pada `lke_penilaian_satker`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_penilaian_satker' AND COLUMN_NAME = 'updated_at';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_penilaian_satker` ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL AFTER `created_at`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- -----------------------------------------------------------------------------
-- TABEL: `lke_satker_bukti`
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `lke_satker_bukti` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_satker` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `komponen_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sub_komponen_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kriteria_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_bukti` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `buktidukung_id` int DEFAULT NULL,
  `link_bukti_dukung` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tgl_pengisian` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=61732 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Kolom `id` pada `lke_satker_bukti`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_satker_bukti' AND COLUMN_NAME = 'id';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_satker_bukti` ADD COLUMN `id` bigint unsigned NOT NULL AUTO_INCREMENT FIRST', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `id_satker` pada `lke_satker_bukti`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_satker_bukti' AND COLUMN_NAME = 'id_satker';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_satker_bukti` ADD COLUMN `id_satker` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL AFTER `id`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `komponen_id` pada `lke_satker_bukti`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_satker_bukti' AND COLUMN_NAME = 'komponen_id';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_satker_bukti` ADD COLUMN `komponen_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `id_satker`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `sub_komponen_id` pada `lke_satker_bukti`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_satker_bukti' AND COLUMN_NAME = 'sub_komponen_id';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_satker_bukti` ADD COLUMN `sub_komponen_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `komponen_id`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `kriteria_id` pada `lke_satker_bukti`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_satker_bukti' AND COLUMN_NAME = 'kriteria_id';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_satker_bukti` ADD COLUMN `kriteria_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL AFTER `sub_komponen_id`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `kode_bukti` pada `lke_satker_bukti`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_satker_bukti' AND COLUMN_NAME = 'kode_bukti';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_satker_bukti` ADD COLUMN `kode_bukti` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `kriteria_id`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `buktidukung_id` pada `lke_satker_bukti`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_satker_bukti' AND COLUMN_NAME = 'buktidukung_id';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_satker_bukti` ADD COLUMN `buktidukung_id` int DEFAULT NULL AFTER `kode_bukti`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `link_bukti_dukung` pada `lke_satker_bukti`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_satker_bukti' AND COLUMN_NAME = 'link_bukti_dukung';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_satker_bukti` ADD COLUMN `link_bukti_dukung` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `buktidukung_id`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `tgl_pengisian` pada `lke_satker_bukti`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_satker_bukti' AND COLUMN_NAME = 'tgl_pengisian';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_satker_bukti` ADD COLUMN `tgl_pengisian` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `link_bukti_dukung`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `tahun` pada `lke_satker_bukti`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_satker_bukti' AND COLUMN_NAME = 'tahun';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_satker_bukti` ADD COLUMN `tahun` smallint unsigned DEFAULT NULL AFTER `tgl_pengisian`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `created_at` pada `lke_satker_bukti`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_satker_bukti' AND COLUMN_NAME = 'created_at';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_satker_bukti` ADD COLUMN `created_at` timestamp NULL DEFAULT NULL AFTER `tahun`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `updated_at` pada `lke_satker_bukti`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_satker_bukti' AND COLUMN_NAME = 'updated_at';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_satker_bukti` ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL AFTER `created_at`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- -----------------------------------------------------------------------------
-- TABEL: `lke_subkomponen`
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `lke_subkomponen` (
  `id` bigint unsigned NOT NULL,
  `komponen_id` bigint unsigned NOT NULL,
  `kode` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `bobot` decimal(5,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `tahun` smallint unsigned NOT NULL DEFAULT '2025',
  PRIMARY KEY (`tahun`,`kode`),
  KEY `subkomponen_komponen_id_foreign` (`komponen_id`),
  KEY `lke_subkomponen_tahun_idx` (`tahun`),
  KEY `lke_subkomponen_tahun_kode_idx` (`tahun`,`kode`),
  CONSTRAINT `FK_lke_subkomponen_lke_komponen` FOREIGN KEY (`komponen_id`) REFERENCES `lke_komponen` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Kolom `id` pada `lke_subkomponen`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_subkomponen' AND COLUMN_NAME = 'id';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_subkomponen` ADD COLUMN `id` bigint unsigned NOT NULL FIRST', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `komponen_id` pada `lke_subkomponen`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_subkomponen' AND COLUMN_NAME = 'komponen_id';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_subkomponen` ADD COLUMN `komponen_id` bigint unsigned NOT NULL AFTER `id`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `kode` pada `lke_subkomponen`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_subkomponen' AND COLUMN_NAME = 'kode';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_subkomponen` ADD COLUMN `kode` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL AFTER `komponen_id`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `nama` pada `lke_subkomponen`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_subkomponen' AND COLUMN_NAME = 'nama';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_subkomponen` ADD COLUMN `nama` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL AFTER `kode`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `bobot` pada `lke_subkomponen`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_subkomponen' AND COLUMN_NAME = 'bobot';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_subkomponen` ADD COLUMN `bobot` decimal(5,2) NOT NULL DEFAULT ''0.00'' AFTER `nama`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `created_at` pada `lke_subkomponen`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_subkomponen' AND COLUMN_NAME = 'created_at';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_subkomponen` ADD COLUMN `created_at` timestamp NULL DEFAULT NULL AFTER `bobot`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `updated_at` pada `lke_subkomponen`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_subkomponen' AND COLUMN_NAME = 'updated_at';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_subkomponen` ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL AFTER `created_at`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `tahun` pada `lke_subkomponen`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_subkomponen' AND COLUMN_NAME = 'tahun';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_subkomponen` ADD COLUMN `tahun` smallint unsigned NOT NULL DEFAULT ''2025'' AFTER `updated_at`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- -----------------------------------------------------------------------------
-- TABEL: `lke_subkomponen_koefisien`
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `lke_subkomponen_koefisien` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_satker` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tahun` int NOT NULL,
  `subkomponen_kode` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_koefisien` int NOT NULL DEFAULT '90',
  `uraian` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `bukti_dukung` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `link_bukti_dukung` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nilai` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `koefisien` int NOT NULL DEFAULT '80',
  `evaluator_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pm_target_koefisien` int NOT NULL DEFAULT '90',
  `pm_uraian` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `pm_bukti_dukung` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pm_link_bukti_dukung` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pm_nilai` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pm_koefisien` int NOT NULL DEFAULT '80',
  `pm_evaluator_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `satker_tahun_subkomp_unique` (`id_satker`,`tahun`,`subkomponen_kode`),
  KEY `lke_subkomponen_koefisien_id_satker_index` (`id_satker`),
  KEY `lke_subkomponen_koefisien_tahun_index` (`tahun`),
  KEY `lke_subkomponen_koefisien_subkomponen_kode_index` (`subkomponen_kode`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Kolom `id` pada `lke_subkomponen_koefisien`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_subkomponen_koefisien' AND COLUMN_NAME = 'id';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_subkomponen_koefisien` ADD COLUMN `id` bigint unsigned NOT NULL AUTO_INCREMENT FIRST', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `id_satker` pada `lke_subkomponen_koefisien`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_subkomponen_koefisien' AND COLUMN_NAME = 'id_satker';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_subkomponen_koefisien` ADD COLUMN `id_satker` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL AFTER `id`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `tahun` pada `lke_subkomponen_koefisien`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_subkomponen_koefisien' AND COLUMN_NAME = 'tahun';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_subkomponen_koefisien` ADD COLUMN `tahun` int NOT NULL AFTER `id_satker`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `subkomponen_kode` pada `lke_subkomponen_koefisien`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_subkomponen_koefisien' AND COLUMN_NAME = 'subkomponen_kode';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_subkomponen_koefisien` ADD COLUMN `subkomponen_kode` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL AFTER `tahun`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `target_koefisien` pada `lke_subkomponen_koefisien`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_subkomponen_koefisien' AND COLUMN_NAME = 'target_koefisien';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_subkomponen_koefisien` ADD COLUMN `target_koefisien` int NOT NULL DEFAULT ''90'' AFTER `subkomponen_kode`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `uraian` pada `lke_subkomponen_koefisien`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_subkomponen_koefisien' AND COLUMN_NAME = 'uraian';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_subkomponen_koefisien` ADD COLUMN `uraian` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci AFTER `target_koefisien`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `bukti_dukung` pada `lke_subkomponen_koefisien`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_subkomponen_koefisien' AND COLUMN_NAME = 'bukti_dukung';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_subkomponen_koefisien` ADD COLUMN `bukti_dukung` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `uraian`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `link_bukti_dukung` pada `lke_subkomponen_koefisien`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_subkomponen_koefisien' AND COLUMN_NAME = 'link_bukti_dukung';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_subkomponen_koefisien` ADD COLUMN `link_bukti_dukung` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `bukti_dukung`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `nilai` pada `lke_subkomponen_koefisien`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_subkomponen_koefisien' AND COLUMN_NAME = 'nilai';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_subkomponen_koefisien` ADD COLUMN `nilai` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `link_bukti_dukung`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `koefisien` pada `lke_subkomponen_koefisien`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_subkomponen_koefisien' AND COLUMN_NAME = 'koefisien';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_subkomponen_koefisien` ADD COLUMN `koefisien` int NOT NULL DEFAULT ''80'' AFTER `nilai`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `evaluator_id` pada `lke_subkomponen_koefisien`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_subkomponen_koefisien' AND COLUMN_NAME = 'evaluator_id';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_subkomponen_koefisien` ADD COLUMN `evaluator_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `koefisien`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `pm_target_koefisien` pada `lke_subkomponen_koefisien`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_subkomponen_koefisien' AND COLUMN_NAME = 'pm_target_koefisien';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_subkomponen_koefisien` ADD COLUMN `pm_target_koefisien` int NOT NULL DEFAULT ''90'' AFTER `evaluator_id`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `pm_uraian` pada `lke_subkomponen_koefisien`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_subkomponen_koefisien' AND COLUMN_NAME = 'pm_uraian';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_subkomponen_koefisien` ADD COLUMN `pm_uraian` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci AFTER `pm_target_koefisien`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `pm_bukti_dukung` pada `lke_subkomponen_koefisien`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_subkomponen_koefisien' AND COLUMN_NAME = 'pm_bukti_dukung';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_subkomponen_koefisien` ADD COLUMN `pm_bukti_dukung` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `pm_uraian`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `pm_link_bukti_dukung` pada `lke_subkomponen_koefisien`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_subkomponen_koefisien' AND COLUMN_NAME = 'pm_link_bukti_dukung';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_subkomponen_koefisien` ADD COLUMN `pm_link_bukti_dukung` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `pm_bukti_dukung`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `pm_nilai` pada `lke_subkomponen_koefisien`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_subkomponen_koefisien' AND COLUMN_NAME = 'pm_nilai';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_subkomponen_koefisien` ADD COLUMN `pm_nilai` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `pm_link_bukti_dukung`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `pm_koefisien` pada `lke_subkomponen_koefisien`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_subkomponen_koefisien' AND COLUMN_NAME = 'pm_koefisien';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_subkomponen_koefisien` ADD COLUMN `pm_koefisien` int NOT NULL DEFAULT ''80'' AFTER `pm_nilai`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `pm_evaluator_id` pada `lke_subkomponen_koefisien`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_subkomponen_koefisien' AND COLUMN_NAME = 'pm_evaluator_id';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_subkomponen_koefisien` ADD COLUMN `pm_evaluator_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `pm_koefisien`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `created_at` pada `lke_subkomponen_koefisien`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_subkomponen_koefisien' AND COLUMN_NAME = 'created_at';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_subkomponen_koefisien` ADD COLUMN `created_at` timestamp NULL DEFAULT NULL AFTER `pm_evaluator_id`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `updated_at` pada `lke_subkomponen_koefisien`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lke_subkomponen_koefisien' AND COLUMN_NAME = 'updated_at';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lke_subkomponen_koefisien` ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL AFTER `created_at`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- -----------------------------------------------------------------------------
-- TABEL: `lkjip_template_bindings`
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `lkjip_template_bindings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `template_level` tinyint unsigned NOT NULL,
  `binding_key` varchar(180) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `binding_type` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'scalar',
  `source_type` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `source_key` varchar(180) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `marker` varchar(220) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `formatter` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `options` json DEFAULT NULL,
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lkjip_template_binding_unique` (`template_level`,`binding_key`),
  KEY `lkjip_template_binding_catalog_idx` (`template_level`,`is_active`,`sort_order`)
) ENGINE=InnoDB AUTO_INCREMENT=122 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Kolom `id` pada `lkjip_template_bindings`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lkjip_template_bindings' AND COLUMN_NAME = 'id';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lkjip_template_bindings` ADD COLUMN `id` bigint unsigned NOT NULL AUTO_INCREMENT FIRST', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `template_level` pada `lkjip_template_bindings`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lkjip_template_bindings' AND COLUMN_NAME = 'template_level';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lkjip_template_bindings` ADD COLUMN `template_level` tinyint unsigned NOT NULL AFTER `id`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `binding_key` pada `lkjip_template_bindings`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lkjip_template_bindings' AND COLUMN_NAME = 'binding_key';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lkjip_template_bindings` ADD COLUMN `binding_key` varchar(180) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL AFTER `template_level`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `binding_type` pada `lkjip_template_bindings`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lkjip_template_bindings' AND COLUMN_NAME = 'binding_type';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lkjip_template_bindings` ADD COLUMN `binding_type` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ''scalar'' AFTER `binding_key`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `source_type` pada `lkjip_template_bindings`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lkjip_template_bindings' AND COLUMN_NAME = 'source_type';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lkjip_template_bindings` ADD COLUMN `source_type` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL AFTER `binding_type`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `source_key` pada `lkjip_template_bindings`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lkjip_template_bindings' AND COLUMN_NAME = 'source_key';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lkjip_template_bindings` ADD COLUMN `source_key` varchar(180) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL AFTER `source_type`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `marker` pada `lkjip_template_bindings`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lkjip_template_bindings' AND COLUMN_NAME = 'marker';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lkjip_template_bindings` ADD COLUMN `marker` varchar(220) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL AFTER `source_key`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `formatter` pada `lkjip_template_bindings`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lkjip_template_bindings' AND COLUMN_NAME = 'formatter';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lkjip_template_bindings` ADD COLUMN `formatter` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `marker`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `options` pada `lkjip_template_bindings`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lkjip_template_bindings' AND COLUMN_NAME = 'options';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lkjip_template_bindings` ADD COLUMN `options` json DEFAULT NULL AFTER `formatter`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `sort_order` pada `lkjip_template_bindings`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lkjip_template_bindings' AND COLUMN_NAME = 'sort_order';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lkjip_template_bindings` ADD COLUMN `sort_order` int unsigned NOT NULL DEFAULT ''0'' AFTER `options`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `is_active` pada `lkjip_template_bindings`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lkjip_template_bindings' AND COLUMN_NAME = 'is_active';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lkjip_template_bindings` ADD COLUMN `is_active` tinyint(1) NOT NULL DEFAULT ''1'' AFTER `sort_order`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `created_at` pada `lkjip_template_bindings`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lkjip_template_bindings' AND COLUMN_NAME = 'created_at';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lkjip_template_bindings` ADD COLUMN `created_at` timestamp NULL DEFAULT NULL AFTER `is_active`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `updated_at` pada `lkjip_template_bindings`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lkjip_template_bindings' AND COLUMN_NAME = 'updated_at';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `lkjip_template_bindings` ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL AFTER `created_at`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- -----------------------------------------------------------------------------
-- TABEL: `measurement_thresholds`
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `measurement_thresholds` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `level_code` int DEFAULT NULL,
  `satker_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `indikator_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `indikator_type` enum('kinerja','kepatuhan','kelembagaan') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'kinerja',
  `measurement_unit` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `excellent_min` decimal(10,2) NOT NULL COMMENT 'Minimum untuk excellent status',
  `good_min` decimal(10,2) NOT NULL COMMENT 'Minimum untuk good status',
  `fair_min` decimal(10,2) NOT NULL COMMENT 'Minimum untuk fair status',
  `poor_max` decimal(10,2) NOT NULL COMMENT 'Maximum untuk poor status',
  `status_excellent` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Sempurna',
  `status_good` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Baik',
  `status_fair` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Cukup',
  `status_poor` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Perlu Perhatian',
  `weight` decimal(5,2) NOT NULL DEFAULT '1.00',
  `effective_date` date NOT NULL,
  `deprecated_date` date DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created_by` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Kolom `id` pada `measurement_thresholds`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'measurement_thresholds' AND COLUMN_NAME = 'id';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `measurement_thresholds` ADD COLUMN `id` bigint unsigned NOT NULL AUTO_INCREMENT FIRST', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `level_code` pada `measurement_thresholds`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'measurement_thresholds' AND COLUMN_NAME = 'level_code';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `measurement_thresholds` ADD COLUMN `level_code` int DEFAULT NULL AFTER `id`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `satker_id` pada `measurement_thresholds`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'measurement_thresholds' AND COLUMN_NAME = 'satker_id';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `measurement_thresholds` ADD COLUMN `satker_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `level_code`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `indikator_id` pada `measurement_thresholds`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'measurement_thresholds' AND COLUMN_NAME = 'indikator_id';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `measurement_thresholds` ADD COLUMN `indikator_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL AFTER `satker_id`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `indikator_type` pada `measurement_thresholds`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'measurement_thresholds' AND COLUMN_NAME = 'indikator_type';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `measurement_thresholds` ADD COLUMN `indikator_type` enum(''kinerja'',''kepatuhan'',''kelembagaan'') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ''kinerja'' AFTER `indikator_id`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `measurement_unit` pada `measurement_thresholds`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'measurement_thresholds' AND COLUMN_NAME = 'measurement_unit';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `measurement_thresholds` ADD COLUMN `measurement_unit` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `indikator_type`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `excellent_min` pada `measurement_thresholds`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'measurement_thresholds' AND COLUMN_NAME = 'excellent_min';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `measurement_thresholds` ADD COLUMN `excellent_min` decimal(10,2) NOT NULL COMMENT ''Minimum untuk excellent status'' AFTER `measurement_unit`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `good_min` pada `measurement_thresholds`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'measurement_thresholds' AND COLUMN_NAME = 'good_min';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `measurement_thresholds` ADD COLUMN `good_min` decimal(10,2) NOT NULL COMMENT ''Minimum untuk good status'' AFTER `excellent_min`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `fair_min` pada `measurement_thresholds`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'measurement_thresholds' AND COLUMN_NAME = 'fair_min';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `measurement_thresholds` ADD COLUMN `fair_min` decimal(10,2) NOT NULL COMMENT ''Minimum untuk fair status'' AFTER `good_min`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `poor_max` pada `measurement_thresholds`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'measurement_thresholds' AND COLUMN_NAME = 'poor_max';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `measurement_thresholds` ADD COLUMN `poor_max` decimal(10,2) NOT NULL COMMENT ''Maximum untuk poor status'' AFTER `fair_min`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `status_excellent` pada `measurement_thresholds`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'measurement_thresholds' AND COLUMN_NAME = 'status_excellent';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `measurement_thresholds` ADD COLUMN `status_excellent` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ''Sempurna'' AFTER `poor_max`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `status_good` pada `measurement_thresholds`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'measurement_thresholds' AND COLUMN_NAME = 'status_good';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `measurement_thresholds` ADD COLUMN `status_good` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ''Baik'' AFTER `status_excellent`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `status_fair` pada `measurement_thresholds`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'measurement_thresholds' AND COLUMN_NAME = 'status_fair';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `measurement_thresholds` ADD COLUMN `status_fair` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ''Cukup'' AFTER `status_good`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `status_poor` pada `measurement_thresholds`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'measurement_thresholds' AND COLUMN_NAME = 'status_poor';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `measurement_thresholds` ADD COLUMN `status_poor` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ''Perlu Perhatian'' AFTER `status_fair`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `weight` pada `measurement_thresholds`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'measurement_thresholds' AND COLUMN_NAME = 'weight';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `measurement_thresholds` ADD COLUMN `weight` decimal(5,2) NOT NULL DEFAULT ''1.00'' AFTER `status_poor`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `effective_date` pada `measurement_thresholds`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'measurement_thresholds' AND COLUMN_NAME = 'effective_date';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `measurement_thresholds` ADD COLUMN `effective_date` date NOT NULL AFTER `weight`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `deprecated_date` pada `measurement_thresholds`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'measurement_thresholds' AND COLUMN_NAME = 'deprecated_date';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `measurement_thresholds` ADD COLUMN `deprecated_date` date DEFAULT NULL AFTER `effective_date`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `active` pada `measurement_thresholds`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'measurement_thresholds' AND COLUMN_NAME = 'active';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `measurement_thresholds` ADD COLUMN `active` tinyint(1) NOT NULL DEFAULT ''1'' AFTER `deprecated_date`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `created_by` pada `measurement_thresholds`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'measurement_thresholds' AND COLUMN_NAME = 'created_by';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `measurement_thresholds` ADD COLUMN `created_by` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL AFTER `active`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `created_at` pada `measurement_thresholds`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'measurement_thresholds' AND COLUMN_NAME = 'created_at';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `measurement_thresholds` ADD COLUMN `created_at` timestamp NULL DEFAULT NULL AFTER `created_by`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Kolom `updated_at` pada `measurement_thresholds`
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'measurement_thresholds' AND COLUMN_NAME = 'updated_at';
SET @query = IF(@col_exists = 0, 'ALTER TABLE `measurement_thresholds` ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL AFTER `created_at`', 'SELECT 1');
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- -----------------------------------------------------------------------------
-- VIEWS: `indikator_saspro_view` & `indikator_sastra_view`
-- -----------------------------------------------------------------------------
CREATE OR REPLACE VIEW `indikator_saspro_view` AS select `isp`.`kode_indikator` AS `kode_indikator`,`isp`.`nama_indikator` AS `nama_indikator`,`sp`.`id_saspro` AS `id_saspro`,`sp`.`nama_saspro` AS `nama_saspro`,`s`.`id_sastra` AS `id_sastra`,`s`.`nama_sastra` AS `nama_sastra` from ((`indikator_saspro` `isp` join `sakip_saspro_new` `sp` on((`isp`.`kode_saspro` = `sp`.`id_saspro`))) join `sakip_sastra_new` `s` on((`isp`.`kode_sastra` = `s`.`id_sastra`)));

CREATE OR REPLACE VIEW `indikator_sastra_view` AS select `isv`.`kode_indikator` AS `kode_indikator`,`isv`.`nama_indikator` AS `nama_indikator`,`s`.`id_sastra` AS `id_sastra`,`s`.`nama_sastra` AS `nama_sastra` from (`indikator_sastra` `isv` join `sakip_sastra_new` `s` on((`isv`.`kode_sastra` = `s`.`id_sastra`)));

SET FOREIGN_KEY_CHECKS = 1;
-- =============================================================================
-- SELESAI: SINKRONISASI SKEMA & PENAMBAHAN KOLOM BERHASIL
-- =============================================================================