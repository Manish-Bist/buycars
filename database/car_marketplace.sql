-- =========================================================
--  BuyCars — Car Buy & Sell Marketplace
--  Database Schema
--  Import this file in phpMyAdmin / MySQL before running the site
-- =========================================================

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

CREATE DATABASE IF NOT EXISTS `car_marketplace` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `car_marketplace`;

-- -------------------------------------------------
-- Table: users  (both normal users and admins)
-- -------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `avatar` VARCHAR(255) DEFAULT NULL,
  `role` ENUM('user','admin') NOT NULL DEFAULT 'user',
  `status` ENUM('active','blocked') NOT NULL DEFAULT 'active',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- -------------------------------------------------
-- Table: cars  (listings created by users, approved by admin)
-- -------------------------------------------------
CREATE TABLE IF NOT EXISTS `cars` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `seller_id` INT UNSIGNED NOT NULL,
  `title` VARCHAR(150) NOT NULL,
  `brand` VARCHAR(60) NOT NULL,
  `model` VARCHAR(60) NOT NULL,
  `year` SMALLINT UNSIGNED NOT NULL,
  `price` DECIMAL(12,2) NOT NULL,
  `mileage` INT UNSIGNED DEFAULT 0,
  `fuel_type` ENUM('petrol','diesel','electric','hybrid','cng') DEFAULT 'petrol',
  `transmission` ENUM('automatic','manual') DEFAULT 'automatic',
  `condition_type` ENUM('new','used') DEFAULT 'used',
  `color` VARCHAR(40) DEFAULT NULL,
  `location` VARCHAR(120) DEFAULT NULL,
  `description` TEXT,
  `status` ENUM('pending','approved','rejected','sold') NOT NULL DEFAULT 'pending',
  `views` INT UNSIGNED NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`seller_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  INDEX idx_status (`status`),
  INDEX idx_brand (`brand`),
  INDEX idx_price (`price`)
) ENGINE=InnoDB;

-- -------------------------------------------------
-- Table: car_images  (multiple photos per listing)
-- -------------------------------------------------
CREATE TABLE IF NOT EXISTS `car_images` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `car_id` INT UNSIGNED NOT NULL,
  `image_path` VARCHAR(255) NOT NULL,
  `is_primary` TINYINT(1) NOT NULL DEFAULT 0,
  FOREIGN KEY (`car_id`) REFERENCES `cars`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -------------------------------------------------
-- Table: wishlist  (users saving cars they like)
-- -------------------------------------------------
CREATE TABLE IF NOT EXISTS `wishlist` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NOT NULL,
  `car_id` INT UNSIGNED NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_user_car (`user_id`, `car_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`car_id`) REFERENCES `cars`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -------------------------------------------------
-- Table: inquiries  (buyer messages a seller about a car)
-- -------------------------------------------------
CREATE TABLE IF NOT EXISTS `inquiries` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `car_id` INT UNSIGNED NOT NULL,
  `buyer_id` INT UNSIGNED NOT NULL,
  `seller_id` INT UNSIGNED NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `message` TEXT NOT NULL,
  `status` ENUM('new','read','replied') NOT NULL DEFAULT 'new',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`car_id`) REFERENCES `cars`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`buyer_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`seller_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -------------------------------------------------
-- Table: contact_messages  (general "Contact Us" form)
-- -------------------------------------------------
CREATE TABLE IF NOT EXISTS `contact_messages` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `subject` VARCHAR(150) DEFAULT NULL,
  `message` TEXT NOT NULL,
  `is_read` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- -------------------------------------------------
-- Table: newsletter_subscribers
-- -------------------------------------------------
CREATE TABLE IF NOT EXISTS `newsletter_subscribers` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- -------------------------------------------------
-- Table: reviews  (client testimonials shown on homepage)
-- -------------------------------------------------
CREATE TABLE IF NOT EXISTS `reviews` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED DEFAULT NULL,
  `name` VARCHAR(100) NOT NULL,
  `image` VARCHAR(255) DEFAULT NULL,
  `rating` TINYINT UNSIGNED NOT NULL DEFAULT 5,
  `message` TEXT NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

SET FOREIGN_KEY_CHECKS = 1;

-- =========================================================
--  SEED DATA
-- =========================================================

-- Default admin login -> email: admin@buycars.com | password: Admin@123
-- A couple of demo users     -> sagar@example.com / nisha@example.com | password: User@123
-- IMPORTANT: the password column below is just a placeholder text, NOT a real hash.
-- After importing this file, open  database/seed_passwords.php  ONCE in your browser
-- (e.g. http://localhost/car_marketplace/database/seed_passwords.php) — it will
-- generate correct password_hash() values for these 3 accounts automatically.
INSERT INTO `users` (`name`,`email`,`password`,`phone`,`role`,`status`) VALUES
('Site Admin','admin@buycars.com','CHANGE_ME_RUN_SEED_SCRIPT','9800000000','admin','active'),
('Sagar Shrestha','sagar@example.com','CHANGE_ME_RUN_SEED_SCRIPT','9811111111','user','active'),
('Nisha Karki','nisha@example.com','CHANGE_ME_RUN_SEED_SCRIPT','9822222222','user','active');

-- Demo car listings (already approved, using bundled sample images)
INSERT INTO `cars`
(`seller_id`,`title`,`brand`,`model`,`year`,`price`,`mileage`,`fuel_type`,`transmission`,`condition_type`,`color`,`location`,`description`,`status`,`views`)
VALUES
(2,'2021 Toyota Corolla Altis','Toyota','Corolla Altis',2021,62000.00,15000,'petrol','automatic','used','White','Kathmandu','Well maintained single owner car, full service history, new tyres.','approved',120),
(2,'2020 Honda Civic RS Turbo','Honda','Civic',2020,58000.00,22000,'petrol','automatic','used','Red','Pokhara','Sporty and fuel efficient, accident free, alloy wheels.','approved',95),
(3,'2022 Hyundai Creta SX','Hyundai','Creta',2022,45000.00,8000,'diesel','automatic','used','Grey','Lalitpur','Like new condition, top variant with sunroof.','approved',80),
(3,'2019 Ford Mustang GT','Ford','Mustang',2019,72000.00,30000,'petrol','manual','used','Yellow','Kathmandu','Powerful V8 engine, well cared for, a true head turner.','approved',150),
(2,'2023 Tesla Model 3','Tesla','Model 3',2023,89000.00,4000,'electric','automatic','new','Black','Kathmandu','Brand new electric sedan with autopilot and long range battery.','approved',210),
(3,'2018 Suzuki Swift VXi','Suzuki','Swift',2018,21000.00,45000,'petrol','manual','used','Blue','Biratnagar','Economical hatchback perfect for city driving.','approved',60);

INSERT INTO `car_images` (`car_id`,`image_path`,`is_primary`) VALUES
(1,'assets/image/vehicle-1.png',1),
(2,'assets/image/vehicle-2.png',1),
(3,'assets/image/vehicle-3.png',1),
(4,'assets/image/vehicle-4.png',1),
(5,'assets/image/vehicle-5.png',1),
(6,'assets/image/vehicle-6.png',1);

INSERT INTO `reviews` (`name`,`image`,`rating`,`message`) VALUES
('John Deo','assets/image/pic-1.png',5,'Excellent platform, found my dream car within a week and the process was smooth from start to finish.'),
('Maria Gomez','assets/image/pic-2.png',4,'Very easy to list my old car for sale, got genuine buyer inquiries within days.'),
('Ramesh Thapa','assets/image/pic-3.png',5,'Great support from the admin team, verified listings gave me a lot of confidence while buying.');
