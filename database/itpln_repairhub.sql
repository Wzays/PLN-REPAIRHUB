-- ============================================================
-- ITPLN-RepairHub — Database Schema & Seed Data
-- Jalankan di phpMyAdmin atau MySQL CLI Laragon
-- ============================================================

CREATE DATABASE IF NOT EXISTS `itpln_repairhub`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `itpln_repairhub`;

-- ------------------------------------------------------------
-- 1. Tabel customers
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `customers` (
  `id`      VARCHAR(50)  PRIMARY KEY,
  `name`    VARCHAR(255) NOT NULL,
  `email`   VARCHAR(255) NOT NULL UNIQUE,
  `phone`   VARCHAR(50),
  `address` TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 2. Tabel technicians
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `technicians` (
  `id`        VARCHAR(50)   PRIMARY KEY,
  `name`      VARCHAR(255)  NOT NULL,
  `specialty` VARCHAR(255),
  `phone`     VARCHAR(50),
  `avatar`    TEXT,
  `status`    VARCHAR(50)   DEFAULT 'available',
  `rating`    DECIMAL(3,1)  DEFAULT 5.0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 3. Tabel services
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `services` (
  `id`          VARCHAR(50)   PRIMARY KEY,
  `name`        VARCHAR(255)  NOT NULL,
  `price`       INT           NOT NULL DEFAULT 0,
  `duration`    VARCHAR(50),
  `description` TEXT,
  `category`    VARCHAR(100)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 4. Tabel bookings
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `bookings` (
  `id`            VARCHAR(50)  PRIMARY KEY,
  `customerId`    VARCHAR(50)  NOT NULL,
  `serviceId`     VARCHAR(50)  NOT NULL,
  `technicianId`  VARCHAR(50),
  `bookingDate`   VARCHAR(20),
  `bookingTime`   VARCHAR(10),
  `address`       TEXT,
  `status`        VARCHAR(50)  DEFAULT 'pending',
  `notes`         TEXT,
  `difficulty`    VARCHAR(255) DEFAULT 'Standar',
  `finalPrice`    INT          DEFAULT 0,
  `createdAt`     VARCHAR(50),
  FOREIGN KEY (`customerId`)   REFERENCES `customers`(`id`)   ON DELETE CASCADE,
  FOREIGN KEY (`serviceId`)    REFERENCES `services`(`id`)    ON DELETE CASCADE,
  FOREIGN KEY (`technicianId`) REFERENCES `technicians`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 5. Tabel device_issues
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `device_issues` (
  `id`               VARCHAR(50)  PRIMARY KEY,
  `bookingId`        VARCHAR(50)  NOT NULL,
  `deviceName`       VARCHAR(255),
  `issueType`        VARCHAR(255),
  `issueDescription` TEXT,
  `photoUrl`         LONGTEXT,
  FOREIGN KEY (`bookingId`) REFERENCES `bookings`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- SEED DATA
-- ============================================================

-- Seed: customers
INSERT IGNORE INTO `customers` (`id`, `name`, `email`, `phone`, `address`) VALUES
('cust_1', 'Reza Reynaldi', 'rezarenaldi122@gmail.com', '+62 812-9988-7766', 'Kampus ITPLN, Duri Kosambi, Jakarta Barat');

-- Seed: technicians
INSERT IGNORE INTO `technicians` (`id`, `name`, `specialty`, `phone`, `avatar`, `status`, `rating`) VALUES
('tech_1', 'Ahmadi Rendy',  'Teknisi Motherboard & Hardware Laptop',    '+62 812-3456-7890', 'https://images.unsplash.com/photo-1622037022824-0a71d7a59ae8?auto=format&fit=crop&q=80&w=300', 'available', 4.9),
('tech_2', 'Budi Santoso',  'Software, Recovery Data, & OS Specialist', '+62 821-9876-5432', 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=300', 'available', 4.8),
('tech_3', 'Hendra Wijaya', 'Instalasi Jaringan & Pembersihan Overheat', '+62 857-4321-8765', 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&q=80&w=300', 'busy',      4.7);

-- Seed: services
INSERT IGNORE INTO `services` (`id`, `name`, `price`, `duration`, `description`, `category`) VALUES
('srv_1', 'Install Ulang Sistem Operasi (Windows/macOS)', 150000,  '1-2 Jam',   'Perbaikan OS korup, lambat, upgrade sistem baru, sudah termasuk driver + software basic.', 'Software'),
('srv_2', 'Ganti Layar LCD Laptop',                       1250000, '2-3 Jam',   'Layar retak, blank, berkedip, atau bergaris. Garansi suku cadang 3 bulan.',               'Hardware'),
('srv_3', 'Penyelamatan & Recovery Data',                  450000,  '1-2 Hari',  'Mengembalikan berkas penting dari SSD/HDD rusak, terhapus, terformat, atau terkena ransomware.', 'Data'),
('srv_4', 'Deep Cleaning & Ganti Thermal Paste CPU',       180000,  '1 Jam',     'Membersihkan debu kipas, melancarkan sirkulasi udara, mengganti thermal paste kualitas Arctic MX-6.', 'Maintenance'),
('srv_5', 'Upgrade Kecepatan (RAM / SSD Fast Boot)',        120000,  '30 Menit',  'Jasa upgrade storage/RAM agar booting responsif. (Belum termasuk harga parts).',          'Hardware');

-- Seed: bookings
INSERT IGNORE INTO `bookings` (`id`, `customerId`, `serviceId`, `technicianId`, `bookingDate`, `bookingTime`, `address`, `status`, `notes`, `difficulty`, `finalPrice`, `createdAt`) VALUES
('book_1', 'cust_1', 'srv_4', 'tech_1', '2026-06-22', '10:00', 'Gedung Rektorat ITPLN Lantai 2, Duri Kosambi, Jakarta Barat', 'confirmed', 'Laptop Asus ROG cepat panas di atas 90 derajat celcius saat main game.', 'Panas Berlebih / Overheat', 180000, '2026-06-18T12:00:00.000Z');

-- Seed: device_issues
INSERT IGNORE INTO `device_issues` (`id`, `bookingId`, `deviceName`, `issueType`, `issueDescription`, `photoUrl`) VALUES
('issue_1', 'book_1', 'Asus ROG Strix G15', 'Hardware Overheat', 'Suhu ekstrem, kipas berisik seolah bekerja 100% konstan.', 'https://images.unsplash.com/photo-1603302576837-37561b2e2302?auto=format&fit=crop&q=80&w=300');
