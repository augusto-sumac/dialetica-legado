-- SQL dump generated using DBML (dbml-lang.org)
-- Database: MySQL
-- Generated at: 2022-03-30T01:17:37.373Z

SET @OLD_CHARACTER_SET_CLIENT = @@CHARACTER_SET_CLIENT;
SET @OLD_CHARACTER_SET_RESULTS = @@CHARACTER_SET_RESULTS;
SET @OLD_COLLATION_CONNECTION = @@COLLATION_CONNECTION;
SET NAMES utf8mb4;
SET @OLD_TIME_ZONE = @@TIME_ZONE;
SET TIME_ZONE = '+00:00';
SET @OLD_UNIQUE_CHECKS = @@UNIQUE_CHECKS, UNIQUE_CHECKS = 0;
SET @OLD_FOREIGN_KEY_CHECKS = @@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS = 0;
SET @OLD_SQL_MODE = @@SQL_MODE, SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';
SET @OLD_SQL_NOTES = @@SQL_NOTES, SQL_NOTES = 0;

-- Drop Tables
DROP TABLES IF EXISTS `users`;
DROP TABLES IF EXISTS `users_addresses`;
DROP TABLES IF EXISTS `articles`;
DROP TABLES IF EXISTS `articles_types`;
DROP TABLES IF EXISTS `articles_areas`;
DROP TABLES IF EXISTS `articles_collections`;
DROP TABLES IF EXISTS `articles_subareas`;
DROP TABLES IF EXISTS `articles_specialties`;
DROP TABLES IF EXISTS `articles_coauthors`;
DROP TABLES IF EXISTS `articles_integrations_services`;
DROP TABLES IF EXISTS `settings`;
DROP TABLES IF EXISTS `jobs`;

CREATE TABLE `users` (
  `id` bigint(20) PRIMARY KEY AUTO_INCREMENT,
  `type` varchar(30) NOT NULL DEFAULT "user" COMMENT 'user or author',
  `email` varchar(255) NOT NULL,
  `name` varchar(150) NOT NULL,
  `document` varchar(30) DEFAULT null COMMENT 'Only numbers CPF/CNPJ',
  `phone` varchar(20) DEFAULT null COMMENT 'Only numbers phone',
  `role` longtext DEFAULT null COMMENT 'Doutor, Gerente, etc',
  `roles` json DEFAULT null,
  `password` varchar(255) NOT NULL,
  `recovery_password_token` varchar(20) DEFAULT null,
  `accept_eula` tinyint(1) DEFAULT null COMMENT 'null, 0 - false, 1 - true',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0 - Inactive, 1 - Active',
  `access_count` bigint(20) NOT NULL DEFAULT 0,
  `photo_path` varchar(255) DEFAULT null COMMENT 'CDN photo path',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() on update current_timestamp(),
  `deleted_at` datetime DEFAULT null
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `users_addresses` (
  `id` bigint(20) PRIMARY KEY AUTO_INCREMENT,
  `user_id` bigint(20) DEFAULT null,
  `zip_code` varchar(20) DEFAULT null,
  `street` varchar(150) DEFAULT null,
  `number` varchar(30) DEFAULT null,
  `district` varchar(100) DEFAULT null,
  `complement` varchar(150) DEFAULT null,
  `city_ibge_id` bigint(20) DEFAULT null,
  `city` varchar(150) DEFAULT null,
  `state` varchar(2) DEFAULT null,
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0 - Inactive, 1 - Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() on update current_timestamp(),
  `deleted_at` datetime DEFAULT null
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `articles` (
  `id` bigint(20) PRIMARY KEY AUTO_INCREMENT,
  `type_id` bigint(20),
  `area_id` bigint(20),
  `collection_id` bigint(20),
  `subarea_id` bigint(20),
  `specialty_id` bigint(20),
  `author_id` bigint(20) NOT NULL,
  `author_address_id` bigint(20) DEFAULT null,
  `payment_id` bigint(20) DEFAULT null,
  `invoice_id` bigint(20) DEFAULT null,
  `title` varchar(255) NOT NULL,
  `resume` longtext DEFAULT null,
  `tags` mediumtext DEFAULT null,
  `words_count` int(11) DEFAULT null,
  `amount` decimal(10,2) DEFAULT null,
  `accept_contract` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 - false, 1 - true',
  `accept_publication_rules` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 - false, 1 - true',
  `attachment` varchar(255) DEFAULT null COMMENT 'json attachment data',
  `proof_attachment` varchar(255) DEFAULT null COMMENT 'json attachment data',
  `final_attachment` varchar(255) DEFAULT null COMMENT 'json attachment data',
  `stage` varchar(20) NOT NULL DEFAULT "draft",
  `status` int(2) NOT NULL DEFAULT 0 COMMENT '0 - Draft, ...',
  `store_url` mediumtext DEFAULT null,
  `store_coupon` varchar(50) DEFAULT null,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() on update current_timestamp(),
  `deleted_at` datetime DEFAULT null
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `articles_types` (
  `id` bigint(20) PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0,
  `price_rule` varchar(20) NOT NULL DEFAULT "fixed" COMMENT 'fixed, words',
  `minimum_price` decimal(10,2) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0 - Inactive, 1 - Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() on update current_timestamp(),
  `deleted_at` datetime DEFAULT null
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `articles_areas` (
  `id` bigint(20) PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0 - Inactive, 1 - Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() on update current_timestamp(),
  `deleted_at` datetime DEFAULT null
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `articles_collections` (
  `id` bigint(20) PRIMARY KEY AUTO_INCREMENT,
  `area_id` bigint(20) NOT NULL,
  `name` varchar(150) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0 - Inactive, 1 - Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() on update current_timestamp(),
  `deleted_at` datetime DEFAULT null
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `articles_subareas` (
  `id` bigint(20) PRIMARY KEY AUTO_INCREMENT,
  `area_id` bigint(20) NOT NULL,
  `name` varchar(150) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0 - Inactive, 1 - Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() on update current_timestamp(),
  `deleted_at` datetime DEFAULT null
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `articles_specialties` (
  `id` bigint(20) PRIMARY KEY AUTO_INCREMENT,
  `area_id` bigint(20) NOT NULL,
  `subarea_id` bigint(20) NOT NULL,
  `name` varchar(150) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0 - Inactive, 1 - Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() on update current_timestamp(),
  `deleted_at` datetime DEFAULT null
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `articles_coauthors` (
  `id` bigint(20) PRIMARY KEY AUTO_INCREMENT,
  `article_id` bigint(20) NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` longtext DEFAULT null COMMENT 'Doutor, Gerente, etc',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() on update current_timestamp(),
  `deleted_at` datetime DEFAULT null
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `articles_integrations_services` (
  `id` bigint(20) PRIMARY KEY AUTO_INCREMENT,
  `user_id` bigint(20) DEFAULT null,
  `type` varchar(20) NOT NULL COMMENT 'payment, invoice',
  `operation` varchar(20) NOT NULL COMMENT 'create, cancel',
  `source` varchar(20) NOT NULL DEFAULT "articles" COMMENT 'Source table',
  `source_id` bigint(20) NOT NULL COMMENT 'Source table ID',
  `service` varchar(50) NOT NULL DEFAULT "Cielo, PlugNotas",
  `service_id` varchar(50) DEFAULT null COMMENT 'Service response ID',
  `service_status` varchar(50) DEFAULT null COMMENT 'Service response status',
  `service_request_payload` json DEFAULT null COMMENT 'Service request payload',
  `service_response_payload` json DEFAULT null COMMENT 'Service response payload',
  `started_at` datetime DEFAULT null,
  `finished_at` datetime DEFAULT null,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() on update current_timestamp(),
  `deleted_at` datetime DEFAULT null
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `settings` (
  `key` varchar(100) PRIMARY KEY,
  `value` longtext DEFAULT null
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `jobs` (
  `id` bigint(20) PRIMARY KEY AUTO_INCREMENT,
  `user_id` bigint(20) DEFAULT null,
  `job` varchar(255) NOT NULL,
  `data` json NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 -> pending, 1 -> finished, 2 -> error',
  `error` longtext DEFAULT null,
  `started_at` datetime DEFAULT null,
  `finished_at` datetime DEFAULT null,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() on update current_timestamp(),
  `deleted_at` datetime DEFAULT null
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `users_addresses` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `articles` ADD FOREIGN KEY (`type_id`) REFERENCES `articles_types` (`id`);

ALTER TABLE `articles` ADD FOREIGN KEY (`area_id`) REFERENCES `articles_areas` (`id`);

ALTER TABLE `articles` ADD FOREIGN KEY (`collection_id`) REFERENCES `articles_collections` (`id`);

ALTER TABLE `articles` ADD FOREIGN KEY (`subarea_id`) REFERENCES `articles_subareas` (`id`);

ALTER TABLE `articles` ADD FOREIGN KEY (`specialty_id`) REFERENCES `articles_specialties` (`id`);

ALTER TABLE `articles` ADD FOREIGN KEY (`author_id`) REFERENCES `users` (`id`);

ALTER TABLE `articles` ADD FOREIGN KEY (`author_address_id`) REFERENCES `users_addresses` (`id`);

ALTER TABLE `articles` ADD FOREIGN KEY (`payment_id`) REFERENCES `articles_integrations_services` (`id`);

ALTER TABLE `articles` ADD FOREIGN KEY (`invoice_id`) REFERENCES `articles_integrations_services` (`id`);

ALTER TABLE `articles_collections` ADD FOREIGN KEY (`area_id`) REFERENCES `articles_areas` (`id`);

ALTER TABLE `articles_subareas` ADD FOREIGN KEY (`area_id`) REFERENCES `articles_areas` (`id`);

ALTER TABLE `articles_specialties` ADD FOREIGN KEY (`area_id`) REFERENCES `articles_areas` (`id`);

ALTER TABLE `articles_specialties` ADD FOREIGN KEY (`subarea_id`) REFERENCES `articles_subareas` (`id`);

ALTER TABLE `articles_coauthors` ADD FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`);

ALTER TABLE `articles_integrations_services` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `jobs` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

CREATE UNIQUE INDEX `users_unique_type_email` ON `users` (`type`, `email`);

CREATE UNIQUE INDEX `users_addresses_unique_zip_code` ON `users_addresses` (`user_id`, `zip_code`);

CREATE UNIQUE INDEX `articles_types_unique_name` ON `articles_types` (`name`);

CREATE UNIQUE INDEX `articles_areas_unique_name` ON `articles_areas` (`name`);

CREATE UNIQUE INDEX `articles_collections_unique_area_id_name` ON `articles_collections` (`area_id`, `name`);

CREATE UNIQUE INDEX `articles_subareas_unique_area_id_name` ON `articles_subareas` (`area_id`, `name`);

CREATE UNIQUE INDEX `articles_specialties_unique_area_id_subarea_id_name` ON `articles_specialties` (`area_id`, `subarea_id`, `name`);

CREATE UNIQUE INDEX `settings_unique_key` ON `settings` (`key`);

SET TIME_ZONE = @OLD_TIME_ZONE;
SET SQL_MODE = @OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS = @OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS = @OLD_UNIQUE_CHECKS;
SET CHARACTER_SET_CLIENT = @OLD_CHARACTER_SET_CLIENT;
SET CHARACTER_SET_RESULTS = @OLD_CHARACTER_SET_RESULTS;
SET COLLATION_CONNECTION = @OLD_COLLATION_CONNECTION;
SET SQL_NOTES = @OLD_SQL_NOTES;