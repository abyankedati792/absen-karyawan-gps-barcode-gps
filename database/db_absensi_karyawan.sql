-- =====================================================================
--  DATABASE : db_absensi_karyawan
--  APLIKASI : Website Absensi Karyawan QR Code + GPS (Laravel)
--  ENGINE   : MariaDB / MySQL (XAMPP)
--  Import   : phpMyAdmin  ->  Import  ->  pilih file ini
--  atau CLI : E:\xampp\mysql\bin\mysql.exe -u root < db_absensi_karyawan.sql
-- =====================================================================

CREATE DATABASE IF NOT EXISTS `db_absensi_karyawan`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `db_absensi_karyawan`;

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- =====================================================================
-- 1. KARYAWAN (login auth: email + password)
-- =====================================================================
DROP TABLE IF EXISTS `karyawan`;
CREATE TABLE `karyawan` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nip`           VARCHAR(30)  NOT NULL COMMENT 'Nomor Induk Pegawai',
  `nama`          VARCHAR(100) NOT NULL,
  `email`         VARCHAR(100) NOT NULL,
  `no_hp`         VARCHAR(20)  DEFAULT NULL,
  `password`      VARCHAR(255) NOT NULL,
  `foto`          VARCHAR(255) DEFAULT NULL,
  `jabatan`       VARCHAR(100) DEFAULT NULL,
  `departemen`    VARCHAR(100) DEFAULT NULL,
  `alamat`        TEXT         DEFAULT NULL,
  `role`          ENUM('admin','karyawan') NOT NULL DEFAULT 'karyawan',
  `status`        ENUM('aktif','nonaktif')  NOT NULL DEFAULT 'aktif',
  `remember_token` VARCHAR(100) DEFAULT NULL,
  `created_at`    DATETIME    DEFAULT NULL,
  `updated_at`    DATETIME    DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `karyawan_nip_unique` (`nip`),
  UNIQUE KEY `karyawan_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- 2. LOKASI KANTOR (titik GPS + radius absensi)
-- =====================================================================
DROP TABLE IF EXISTS `lokasi_kantor`;
CREATE TABLE `lokasi_kantor` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama`          VARCHAR(100) NOT NULL,
  `alamat`        TEXT         DEFAULT NULL,
  `latitude`      DECIMAL(10,7) NOT NULL,
  `longitude`     DECIMAL(10,7) NOT NULL,
  `radius`        INT UNSIGNED NOT NULL DEFAULT 100 COMMENT 'radius absen dalam meter',
  `jam_masuk`     TIME         NOT NULL DEFAULT '08:00:00',
  `jam_pulang`    TIME         NOT NULL DEFAULT '17:00:00',
  `toleransi`     INT UNSIGNED NOT NULL DEFAULT 15  COMMENT 'toleransi terlambat (menit)',
  `is_default`    TINYINT(1)   NOT NULL DEFAULT 0,
  `status`        ENUM('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `created_at`    DATETIME    DEFAULT NULL,
  `updated_at`    DATETIME    DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- 3. ABSENSI HARIAN (1 baris per karyawan per hari)
-- =====================================================================
DROP TABLE IF EXISTS `absensi`;
CREATE TABLE `absensi` (
  `id`                INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `karyawan_id`       INT UNSIGNED NOT NULL,
  `lokasi_kantor_id`  INT UNSIGNED NOT NULL,
  `tanggal`           DATE NOT NULL,
  `jam_masuk`         DATETIME DEFAULT NULL,
  `jam_pulang`        DATETIME DEFAULT NULL,
  `status_masuk`      ENUM('hadir','terlambat','izin','sakit','cuti','alpha') NOT NULL DEFAULT 'hadir',
  `status_pulang`     ENUM('selesai','lembur','belum_pulang') DEFAULT NULL,
  `latitude_masuk`    DECIMAL(10,7) DEFAULT NULL,
  `longitude_masuk`   DECIMAL(10,7) DEFAULT NULL,
  `jarak_masuk`       INT UNSIGNED DEFAULT NULL COMMENT 'jarak ke kantor (meter)',
  `alamat_masuk`      VARCHAR(255) DEFAULT NULL,
  `foto_masuk`        VARCHAR(255) DEFAULT NULL,
  `latitude_pulang`   DECIMAL(10,7) DEFAULT NULL,
  `longitude_pulang`  DECIMAL(10,7) DEFAULT NULL,
  `jarak_pulang`      INT UNSIGNED DEFAULT NULL,
  `alamat_pulang`     VARCHAR(255) DEFAULT NULL,
  `catatan`           VARCHAR(255) DEFAULT NULL,
  `created_at`        DATETIME DEFAULT NULL,
  `updated_at`        DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `absensi_karyawan_tanggal_unique` (`karyawan_id`,`tanggal`),
  KEY `absensi_tanggal_index` (`tanggal`),
  KEY `absensi_kantor_index` (`lokasi_kantor_id`),
  CONSTRAINT `absensi_karyawan_id_foreign` FOREIGN KEY (`karyawan_id`) REFERENCES `karyawan` (`id`) ON DELETE CASCADE,
  CONSTRAINT `absensi_lokasi_kantor_id_foreign` FOREIGN KEY (`lokasi_kantor_id`) REFERENCES `lokasi_kantor` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- 4. TOKEN QR DINAMIS (QR berganti tiap beberapa menit)
-- =====================================================================
DROP TABLE IF EXISTS `qr_token`;
CREATE TABLE `qr_token` (
  `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `lokasi_kantor_id` INT UNSIGNED NOT NULL,
  `tanggal`          DATE NOT NULL,
  `token`            VARCHAR(64) NOT NULL,
  `berlaku_sampai`   DATETIME NOT NULL,
  `status`           ENUM('aktif','arsip') NOT NULL DEFAULT 'aktif',
  `created_at`       DATETIME DEFAULT NULL,
  `updated_at`       DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `qr_token_unique` (`token`),
  KEY `qr_token_kantor_tanggal_index` (`lokasi_kantor_id`,`tanggal`),
  CONSTRAINT `qr_token_kantor_foreign` FOREIGN KEY (`lokasi_kantor_id`) REFERENCES `lokasi_kantor` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- 5. LOG SCAN QR (audit setiap kali QR dipindai)
-- =====================================================================
DROP TABLE IF EXISTS `log_scan`;
CREATE TABLE `log_scan` (
  `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `karyawan_id`      INT UNSIGNED DEFAULT NULL,
  `lokasi_kantor_id` INT UNSIGNED DEFAULT NULL,
  `tanggal`          DATE NOT NULL,
  `waktu`            DATETIME NOT NULL,
  `payload`          VARCHAR(255) DEFAULT NULL,
  `latitude`         DECIMAL(10,7) DEFAULT NULL,
  `longitude`        DECIMAL(10,7) DEFAULT NULL,
  `jarak`            INT UNSIGNED DEFAULT NULL,
  `hasil`            ENUM('sukses','gagal') NOT NULL DEFAULT 'sukses',
  `keterangan`       VARCHAR(255) DEFAULT NULL,
  `ip_address`       VARCHAR(45) DEFAULT NULL,
  `device`           VARCHAR(255) DEFAULT NULL,
  `created_at`       DATETIME DEFAULT NULL,
  `updated_at`       DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `log_scan_karyawan_index` (`karyawan_id`),
  KEY `log_scan_tanggal_index` (`tanggal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- 6. IZIN / SAKIT / CUTI
-- =====================================================================
DROP TABLE IF EXISTS `pengajuan_izin`;
CREATE TABLE `pengajuan_izin` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `karyawan_id`   INT UNSIGNED NOT NULL,
  `tanggal_mulai` DATE NOT NULL,
  `tanggal_selesai` DATE NOT NULL,
  `jenis`         ENUM('izin','sakit','cuti') NOT NULL DEFAULT 'izin',
  `alasan`        TEXT NOT NULL,
  `surat`         VARCHAR(255) DEFAULT NULL,
  `status`        ENUM('menunggu','disetujui','ditolak') NOT NULL DEFAULT 'menunggu',
  `catatan_admin` VARCHAR(255) DEFAULT NULL,
  `disetujui_oleh` INT UNSIGNED DEFAULT NULL,
  `created_at`    DATETIME DEFAULT NULL,
  `updated_at`    DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pengajuan_izin_karyawan_index` (`karyawan_id`),
  CONSTRAINT `pengajuan_izin_karyawan_foreign` FOREIGN KEY (`karyawan_id`) REFERENCES `karyawan` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- 7. PENGATURAN APLIKASI (key-value)
-- =====================================================================
DROP TABLE IF EXISTS `pengaturan`;
CREATE TABLE `pengaturan` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `kunci`      VARCHAR(50) NOT NULL,
  `nilai`      TEXT DEFAULT NULL,
  `keterangan` VARCHAR(255) DEFAULT NULL,
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pengaturan_kunci_unique` (`kunci`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- 8. TABEL BAWAAN LARAVEL (session, cache, queue di-set ke `database`)
-- =====================================================================
DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `id`            VARCHAR(255) NOT NULL,
  `user_id`       BIGINT UNSIGNED DEFAULT NULL,
  `ip_address`    VARCHAR(45) DEFAULT NULL,
  `user_agent`    TEXT DEFAULT NULL,
  `payload`       LONGTEXT NOT NULL,
  `last_activity` INT NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache` (
  `key`        VARCHAR(255) NOT NULL,
  `value`      MEDIUMTEXT NOT NULL,
  `expiration` INT NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE `cache_locks` (
  `key`       VARCHAR(255) NOT NULL,
  `owner`     VARCHAR(255) NOT NULL,
  `expiration` INT NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs` (
  `id`           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue`        VARCHAR(255) NOT NULL,
  `payload`      LONGTEXT NOT NULL,
  `attempts`     TINYINT UNSIGNED NOT NULL,
  `reserved_at`  INT UNSIGNED DEFAULT NULL,
  `available_at` INT UNSIGNED NOT NULL,
  `created_at`   INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE `job_batches` (
  `id`            VARCHAR(255) NOT NULL,
  `name`          VARCHAR(255) NOT NULL,
  `total_jobs`    INT NOT NULL,
  `pending_jobs`  INT NOT NULL,
  `failed_jobs`   INT NOT NULL,
  `failed_job_ids` LONGTEXT NOT NULL,
  `options`       MEDIUMTEXT DEFAULT NULL,
  `cancelled_at`  INT DEFAULT NULL,
  `created_at`    INT NOT NULL,
  `finished_at`   INT DEFAULT NULL,
  `failed_at`     INT DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs` (
  `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid`        VARCHAR(255) NOT NULL,
  `connection`  VARCHAR(255) NOT NULL,
  `queue`       VARCHAR(255) NOT NULL,
  `payload`     LONGTEXT NOT NULL,
  `exception`   LONGTEXT NOT NULL,
  `failed_at`   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================================
-- 8b. TABEL MIGRASI LARAVEL
--     Agar `php artisan migrate` pada database hasil import tidak
--     mengulang pembuatan tabel yang sudah ada.
-- =====================================================================
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` VARCHAR(255) NOT NULL,
  `batch` INT NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `migrations` (`migration`, `batch`) VALUES
  ('0001_01_01_000000_create_users_table', 1),
  ('0001_01_01_000001_create_cache_table', 1),
  ('0001_01_01_000002_create_jobs_table', 1),
  ('2026_10_16_000001_create_karyawan_table', 2),
  ('2026_10_16_000002_create_lokasi_kantor_table', 2),
  ('2026_10_16_000003_create_absensi_table', 2),
  ('2026_10_16_000004_create_qr_token_table', 2),
  ('2026_10_16_000005_create_log_scan_table', 2),
  ('2026_10_16_000006_create_pengajuan_izin_table', 2),
  ('2026_10_16_000007_create_pengaturan_table', 2);

-- =====================================================================
-- 9. DATA AWAL
--    Login:
--      admin    / admin123      (role: admin)
--      andi / budi / citra      / karyawan123 (role: karyawan)
-- =====================================================================
INSERT INTO `karyawan`
  (`nip`,`nama`,`email`,`no_hp`,`password`,`jabatan`,`departemen`,`alamat`,`role`,`status`,`created_at`,`updated_at`)
VALUES
  ('ADM-001','Administrator','admin@absensi.test','081234567890',
   '$2y$12$xID7lteiG/BLWzp9wUqeweCGEZPgJFVAI2neXPbEcfNXf7z/tmMPu',
   'HR Manager','Human Resource','Jl. Merdeka No. 1, Tulungrejo','admin','aktif',NOW(),NOW()),
  ('KRY-001','Andi Prasetyo','andi@absensi.test','081234567891',
   '$2y$12$cUgBYTmWyxHKOIX21yk87.oXoSbPEcQxeJzxs0mqEZzTPLBBPogVG',
   'Staff Produksi','Produksi','Jl. Melati No. 12, Tulungrejo','karyawan','aktif',NOW(),NOW()),
  ('KRY-002','Budi Santoso','budi@absensi.test','081234567892',
   '$2y$12$cUgBYTmWyxHKOIX21yk87.oXoSbPEcQxeJzxs0mqEZzTPLBBPogVG',
   'Staff Gudang','Gudang','Jl. Kenanga No. 5, Tulungrejo','karyawan','aktif',NOW(),NOW()),
  ('KRY-003','Citra Dewi','citra@absensi.test','081234567893',
   '$2y$12$cUgBYTmWyxHKOIX21yk87.oXoSbPEcQxeJzxs0mqEZzTPLBBPogVG',
   'Admin Produksi','Produksi','Jl. Mawar No. 8, Tulungrejo','karyawan','aktif',NOW(),NOW());

INSERT INTO `lokasi_kantor`
  (`nama`,`alamat`,`latitude`,`longitude`,`radius`,`jam_masuk`,`jam_pulang`,`toleransi`,`is_default`,`status`,`created_at`,`updated_at`)
VALUES
  ('Kantor Pusat Tulungrejo','Jl. Raya Tulungrejo No. 1, Blitar, Jawa Timur',
   -8.0644000,112.1700000,150,'08:00:00','17:00:00',15,1,'aktif',NOW(),NOW()),
  ('Cabang Kota Blitar','Jl. Kawi No. 20, Blitar, Jawa Timur',
   -8.0973000,112.1574000,200,'08:00:00','17:00:00',15,0,'aktif',NOW(),NOW());

INSERT INTO `pengaturan` (`kunci`,`nilai`,`keterangan`,`created_at`,`updated_at`) VALUES
  ('nama_aplikasi','Absensi Karyawan QR Code + GPS','Nama aplikasi',NOW(),NOW()),
  ('nama_perusahaan','PT. Shelter Indonesia','Nama perusahaan',NOW(),NOW()),
  ('logo','img/logo.svg','Logo perusahaan ( file di folder public/img, bisa diganti lewat menu Pengaturan )',NOW(),NOW()),
  ('radius_default','150','Radius absensi default (meter)',NOW(),NOW()),
  ('menit_qr','5','Masa berlaku QR Code (menit)',NOW(),NOW()),
  ('aktifkan_foto','0','Wajibkan foto saat absen (1=ya, 0=tidak)',NOW(),NOW()),
  ('alamat_kantor','Jl. Raya Tulungrejo No. 1, Blitar, Jawa Timur','Alamat kantor pusat',NOW(),NOW()),
  ('latitude_kantor','-8.0644000','Latitude kantor pusat',NOW(),NOW()),
  ('longitude_kantor','112.1700000','Longitude kantor pusat',NOW(),NOW());
