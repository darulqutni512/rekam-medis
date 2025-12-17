-- SQL schema for Rekam Medis project
-- Created: 2025-12-15

CREATE DATABASE IF NOT EXISTS `db_rekammedis` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `db_rekammedis`;

-- Table: dokter
CREATE TABLE IF NOT EXISTS `dokter` (
  `id_dokter` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_dokter` VARCHAR(255) NOT NULL,
  `spesialisasi` VARCHAR(150) DEFAULT NULL,
  `no_sip` VARCHAR(100) DEFAULT NULL,
  PRIMARY KEY (`id_dokter`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: pasien
CREATE TABLE IF NOT EXISTS `pasien` (
  `id_pasien` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_pasien` VARCHAR(255) NOT NULL,
  `nik` VARCHAR(50) DEFAULT NULL,
  `tanggal_lahir` DATE DEFAULT NULL,
  `jenis_kelamin` ENUM('Laki-laki','Perempuan') DEFAULT NULL,
  `alamat` TEXT,
  `no_telepon` VARCHAR(50) DEFAULT NULL,
  PRIMARY KEY (`id_pasien`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: rekam_medis (used for medical records)
CREATE TABLE IF NOT EXISTS `rekam_medis` (
  `id_rekam` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_pasien` INT UNSIGNED NOT NULL,
  `id_dokter` INT UNSIGNED DEFAULT NULL,
  `tanggal` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `keluhan` TEXT,
  `diagnosa` TEXT,
  `tindakan` TEXT,
  `keterangan` TEXT,
  PRIMARY KEY (`id_rekam`),
  KEY `idx_id_pasien` (`id_pasien`),
  KEY `idx_id_dokter` (`id_dokter`),
  CONSTRAINT `fk_rekam_pasien` FOREIGN KEY (`id_pasien`) REFERENCES `pasien`(`id_pasien`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_rekam_dokter` FOREIGN KEY (`id_dokter`) REFERENCES `dokter`(`id_dokter`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: obat (medicines)
CREATE TABLE IF NOT EXISTS `obat` (
  `id_obat` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_obat` VARCHAR(255) NOT NULL,
  `stok` INT DEFAULT 0,
  `satuan` VARCHAR(50) DEFAULT 'pcs',
  `keterangan` TEXT,
  PRIMARY KEY (`id_obat`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Pivot table: rekam_obat (relation between rekam_medis and obat)
CREATE TABLE IF NOT EXISTS `rekam_obat` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_rekam` INT UNSIGNED NOT NULL,
  `id_obat` INT UNSIGNED NOT NULL,
  `catatan` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_rekam` (`id_rekam`),
  KEY `idx_obat` (`id_obat`),
  CONSTRAINT `fk_rekamobat_rekam` FOREIGN KEY (`id_rekam`) REFERENCES `rekam_medis`(`id_rekam`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_rekamobat_obat` FOREIGN KEY (`id_obat`) REFERENCES `obat`(`id_obat`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: users (simple auth/staff table)
CREATE TABLE IF NOT EXISTS `users` (
  `id_user` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(100) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `role` VARCHAR(50) DEFAULT 'staff',
  `nama` VARCHAR(255) DEFAULT NULL,
  `email` VARCHAR(150) DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: appointment (scheduling)
CREATE TABLE IF NOT EXISTS `appointment` (
  `id_appointment` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_pasien` INT UNSIGNED NOT NULL,
  `id_dokter` INT UNSIGNED DEFAULT NULL,
  `tanggal` DATETIME NOT NULL,
  `status` ENUM('scheduled','completed','cancelled') DEFAULT 'scheduled',
  `catatan` TEXT,
  PRIMARY KEY (`id_appointment`),
  KEY `idx_appointment_pasien` (`id_pasien`),
  KEY `idx_appointment_dokter` (`id_dokter`),
  CONSTRAINT `fk_appointment_pasien` FOREIGN KEY (`id_pasien`) REFERENCES `pasien`(`id_pasien`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_appointment_dokter` FOREIGN KEY (`id_dokter`) REFERENCES `dokter`(`id_dokter`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- End of schema
