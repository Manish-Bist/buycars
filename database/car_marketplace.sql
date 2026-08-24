-- =========================================================
--  BuyCars — Car Buy & Sell Marketplace
--  Database: car_marketplace
--  Engine:   MySQL 5.7+ / MariaDB 10.3+
--
--  Import via phpMyAdmin or:
--    mysql -u root -p car_marketplace < car_marketplace.sql
-- =========================================================

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET NAMES utf8mb4;

CREATE DATABASE IF NOT EXISTS `car_marketplace`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `car_marketplace`;

-- ---------------------------------------------------------
-- users
-- ---------------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
    `id`         INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `name`       VARCHAR(100)     NOT NULL,
    `email`      VARCHAR(150)     NOT NULL,
    `password`   VARCHAR(255)     NOT NULL,
    `phone`      VARCHAR(20)      DEFAULT NULL,
    `avatar`     VARCHAR(255)     DEFAULT NULL,
    `role`       ENUM('user','admin') NOT NULL DEFAULT 'user',
    `status`     ENUM('active','blocked') NOT NULL DEFAULT 'active',
    `created_at` DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------
-- cars
-- ---------------------------------------------------------
DROP TABLE IF EXISTS `cars`;
CREATE TABLE `cars` (
    `id`             INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `seller_id`      INT UNSIGNED  NOT NULL,
    `title`          VARCHAR(150)  NOT NULL,
    `brand`          VARCHAR(60)   NOT NULL,
    `model`          VARCHAR(60)   NOT NULL,
    `year`           SMALLINT UNSIGNED NOT NULL,
    `price`          DECIMAL(12,2) NOT NULL,
    `mileage`        INT UNSIGNED  NOT NULL DEFAULT 0,
    `fuel_type`      ENUM('petrol','diesel','electric','hybrid','cng') NOT NULL DEFAULT 'petrol',
    `transmission`   ENUM('automatic','manual')   NOT NULL DEFAULT 'automatic',
    `condition_type` ENUM('new','used')            NOT NULL DEFAULT 'used',
    `color`          VARCHAR(40)   DEFAULT NULL,
    `location`       VARCHAR(120)  DEFAULT NULL,
    `description`    TEXT,
    `status`         ENUM('pending','approved','rejected','sold') NOT NULL DEFAULT 'pending',
    `views`          INT UNSIGNED  NOT NULL DEFAULT 0,
    `created_at`     DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`     DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_status` (`status`),
    KEY `idx_brand`  (`brand`),
    KEY `idx_price`  (`price`),
    CONSTRAINT `fk_cars_seller` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------
-- car_images
-- ---------------------------------------------------------
DROP TABLE IF EXISTS `car_images`;
CREATE TABLE `car_images` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `car_id`     INT UNSIGNED NOT NULL,
    `image_path` VARCHAR(255) NOT NULL,
    `is_primary` TINYINT(1)   NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    KEY `idx_car` (`car_id`),
    CONSTRAINT `fk_images_car` FOREIGN KEY (`car_id`) REFERENCES `cars` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------
-- wishlist
-- ---------------------------------------------------------
DROP TABLE IF EXISTS `wishlist`;
CREATE TABLE `wishlist` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`    INT UNSIGNED NOT NULL,
    `car_id`     INT UNSIGNED NOT NULL,
    `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_user_car` (`user_id`,`car_id`),
    CONSTRAINT `fk_wish_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_wish_car`  FOREIGN KEY (`car_id`)  REFERENCES `cars`  (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------
-- inquiries
-- ---------------------------------------------------------
DROP TABLE IF EXISTS `inquiries`;
CREATE TABLE `inquiries` (
    `id`        INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `car_id`    INT UNSIGNED NOT NULL,
    `buyer_id`  INT UNSIGNED NOT NULL,
    `seller_id` INT UNSIGNED NOT NULL,
    `name`      VARCHAR(100) NOT NULL,
    `email`     VARCHAR(150) NOT NULL,
    `phone`     VARCHAR(20)  DEFAULT NULL,
    `message`   TEXT         NOT NULL,
    `status`    ENUM('new','read','replied') NOT NULL DEFAULT 'new',
    `created_at` DATETIME    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_inq_car`    FOREIGN KEY (`car_id`)    REFERENCES `cars`  (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_inq_buyer`  FOREIGN KEY (`buyer_id`)  REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_inq_seller` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------
-- contact_messages
-- ---------------------------------------------------------
DROP TABLE IF EXISTS `contact_messages`;
CREATE TABLE `contact_messages` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`       VARCHAR(100) NOT NULL,
    `email`      VARCHAR(150) NOT NULL,
    `subject`    VARCHAR(150) DEFAULT NULL,
    `message`    TEXT         NOT NULL,
    `is_read`    TINYINT(1)   NOT NULL DEFAULT 0,
    `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------
-- newsletter_subscribers
-- ---------------------------------------------------------
DROP TABLE IF EXISTS `newsletter_subscribers`;
CREATE TABLE `newsletter_subscribers` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `email`      VARCHAR(150) NOT NULL,
    `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------
-- reviews (homepage testimonials)
-- ---------------------------------------------------------
DROP TABLE IF EXISTS `reviews`;
CREATE TABLE `reviews` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`    INT UNSIGNED DEFAULT NULL,
    `name`       VARCHAR(100) NOT NULL,
    `image`      VARCHAR(255) DEFAULT NULL,
    `rating`     TINYINT UNSIGNED NOT NULL DEFAULT 5,
    `message`    TEXT         NOT NULL,
    `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_review_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- =========================================================
--  SEED DATA
--  Passwords are set by running: database/seed_passwords.php
--  Admin  → admin@buycars.com  / Admin@123
--  Users  → sagar@example.com  / User@123
--         → nisha@example.com  / User@123
-- =========================================================

INSERT INTO `users` (`name`, `email`, `password`, `phone`, `role`, `status`) VALUES
('Admin',         'admin@buycars.com',  'RUN_SEED_SCRIPT', '9800000000', 'admin', 'active'),
('Sagar Shrestha','sagar@example.com',  'RUN_SEED_SCRIPT', '9811111111', 'user',  'active'),
('Nisha Karki',   'nisha@example.com',  'RUN_SEED_SCRIPT', '9822222222', 'user',  'active');

INSERT INTO `cars`
    (`seller_id`,`title`,`brand`,`model`,`year`,`price`,`mileage`,`fuel_type`,`transmission`,`condition_type`,`color`,`location`,`description`,`status`,`views`)
VALUES
(2,'2021 Toyota Corolla Altis','Toyota','Corolla Altis',2021,62000.00,15000,'petrol','automatic','used','White','Kathmandu',
    'Well maintained single-owner car. Full service history, new tyres, no accidents.','approved',120),
(2,'2020 Honda Civic RS Turbo','Honda','Civic',2020,58000.00,22000,'petrol','automatic','used','Red','Pokhara',
    'Sporty and fuel efficient. Accident free, alloy wheels, sunroof.','approved',95),
(3,'2022 Hyundai Creta SX','Hyundai','Creta',2022,45000.00,8000,'diesel','automatic','used','Grey','Lalitpur',
    'Like-new condition. Top variant with panoramic sunroof and ADAS features.','approved',80),
(3,'2019 Ford Mustang GT','Ford','Mustang',2019,72000.00,30000,'petrol','manual','used','Yellow','Kathmandu',
    'Powerful 5.0 V8. Well cared for, recent service, custom exhaust.','approved',150),
(2,'2023 Tesla Model 3 Long Range','Tesla','Model 3',2023,89000.00,4000,'electric','automatic','new','Black','Kathmandu',
    'Latest model with autopilot, 600 km range, over-the-air updates.','approved',210),
(3,'2018 Suzuki Swift VXi','Suzuki','Swift',2018,21000.00,45000,'petrol','manual','used','Blue','Biratnagar',
    'Economical city hatchback. Genuine mileage, recently serviced.','approved',60);

INSERT INTO `car_images` (`car_id`, `image_path`, `is_primary`) VALUES
(1, 'assets/image/vehicle-1.png', 1),
(2, 'assets/image/vehicle-2.png', 1),
(3, 'assets/image/vehicle-3.png', 1),
(4, 'assets/image/vehicle-4.png', 1),
(5, 'assets/image/vehicle-5.png', 1),
(6, 'assets/image/vehicle-6.png', 1);

INSERT INTO `reviews` (`name`, `image`, `rating`, `message`) VALUES
('John Deo',      'assets/image/pic-1.png', 5, 'Excellent platform. Found my dream car within a week and the whole process was smooth from start to finish.'),
('Maria Gomez',   'assets/image/pic-2.png', 4, 'Very easy to list my old car for sale. Got genuine buyer inquiries within days.'),
('Ramesh Thapa',  'assets/image/pic-3.png', 5, 'Verified listings gave me a lot of confidence while buying. The admin team was very responsive.'),
('Sarah Connor',  'assets/image/pic-4.png', 5, 'Best car marketplace I have used. Clean interface and trustworthy sellers.'),
('Arun Poudel',   'assets/image/pic-5.png', 4, 'Sold my SUV in under two weeks. The inquiry system is really convenient.'),
('Priya Sharma',  'assets/image/pic-6.png', 5, 'Great experience as a first-time car buyer. Highly recommend BuyCars to everyone.');
