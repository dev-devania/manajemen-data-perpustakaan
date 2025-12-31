CREATE DATABASE IF NOT EXISTS `perpustakaan_mvp`
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE `perpustakaan_mvp`;

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `role` ENUM('admin','petugas') NOT NULL DEFAULT 'petugas',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
