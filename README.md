# Bookineo Backend

Bookineo est une application web de **location de livres entre particuliers**.  
Cette partie correspond au **backend**, développé en **Symfony**, avec une base de données **MySQL (phpMyAdmin)**.  

---

## Technologies utilisées

- [Symfony 6+](https://symfony.com/) – Framework PHP
- [Doctrine ORM](https://www.doctrine-project.org/) – Gestion de la base de données
- [MySQL](https://www.mysql.com/) + [phpMyAdmin](https://www.phpmyadmin.net/) – Base de données
- [Composer](https://getcomposer.org/) – Gestion des dépendances

---

## Installation et lancement

1. Cloner le dépôt 
git clone https://github.com/ton-profil/bookineo-back.git
cd bookineo-back

2. Installer les dépendances
composer install

3. Configurer l’environnement (Copier le fichier .env en .env.local et adapter la configuration)
DATABASE_URL="mysql://user:password@127.0.0.1:3306/bookineo"

4. Créer la base de données
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

5. Lancer le serveur Symfony
symfony server:start

---

-- =====================================================================
-- Bookineo – schema MySQL
-- =====================================================================

-- 0) DB
CREATE DATABASE IF NOT EXISTS `ecv-bookineo`
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_unicode_ci;

USE `ecv-bookineo`;

SET NAMES utf8mb4;
SET time_zone = "+00:00";

-- 1) USERS --------------------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `first_name`   VARCHAR(100) NULL,
  `last_name`    VARCHAR(100) NULL,
  `email`        VARCHAR(180) NOT NULL,
  `password`     VARCHAR(255) NOT NULL,            -- hash
  `created_at`   DATETIME NULL,
  `updated_at`   DATETIME NULL,
  `created_by`   VARCHAR(180) NULL,
  `updated_by`   VARCHAR(180) NULL,
  `birth_date`   DATE NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_users_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2) BOOKS --------------------------------------------------------------
DROP TABLE IF EXISTS `books`;
CREATE TABLE `books` (
  `id`                INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`             VARCHAR(255) NULL,
  `author`            VARCHAR(255) NULL,
  `publication_date`  DATE NULL,
  `status`            VARCHAR(255) NOT NULL DEFAULT 'available',  -- 'available' | 'rented'
  `price`             DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `owner`             VARCHAR(255) NOT NULL,        -- email du propriétaire
  `description`       VARCHAR(255) NULL,
  `genre`             VARCHAR(100) NULL,

  `renter_email`      VARCHAR(320) NULL,            -- qui a loué (email)
  `rented_at`         DATETIME NULL,                -- début location
  `returned_at`       DATETIME NULL,                -- date de retour
  `last_return_comment` VARCHAR(255) NULL,          -- commentaire restitution

  PRIMARY KEY (`id`),
  KEY `idx_books_status` (`status`),
  KEY `idx_books_genre` (`genre`),
  KEY `idx_books_price` (`price`),
  KEY `idx_books_owner` (`owner`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3) MESSAGES -----------------------------------------------------------
DROP TABLE IF EXISTS `messages`;
CREATE TABLE `messages` (
  `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `sender_email`   VARCHAR(320) NOT NULL,
  `receiver_email` VARCHAR(320) NOT NULL,
  `content`        TEXT NOT NULL,
  `created_at`     DATETIME NOT NULL,
  `read_at`        DATETIME NULL,
  PRIMARY KEY (`id`),
  KEY `idx_messages_receiver_read` (`receiver_email`,`read_at`),
  KEY `idx_messages_pair_date` (`sender_email`,`receiver_email`,`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4) RENTALS ------------------------------------------------------------
DROP TABLE IF EXISTS `rentals`;
CREATE TABLE `rentals` (
  `id`                 INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `book_id`            INT UNSIGNED NULL,
  `renter_first_name`  VARCHAR(100) NULL,
  `renter_last_name`   VARCHAR(100) NULL,
  `start_date`         DATE NOT NULL,
  `due_date`           DATE NULL,
  `return_date`        DATE NULL,
  `comment`            VARCHAR(255) NULL,
  `renter_email`       VARCHAR(320) NULL,
  PRIMARY KEY (`id`),
  KEY `idx_rentals_book` (`book_id`),
  KEY `idx_rentals_renter_email` (`renter_email`),
  CONSTRAINT `fk_rentals_book`
    FOREIGN KEY (`book_id`) REFERENCES `books` (`id`)
    ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- Fin du schéma
-- =====================================================================

