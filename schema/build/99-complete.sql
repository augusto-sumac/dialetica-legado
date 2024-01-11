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


SET @OLD_CHARACTER_SET_CLIENT = @@CHARACTER_SET_CLIENT;
SET @OLD_CHARACTER_SET_RESULTS = @@CHARACTER_SET_RESULTS;
SET @OLD_COLLATION_CONNECTION = @@COLLATION_CONNECTION;
SET NAMES utf8mb4;
SET @OLD_TIME_ZONE = @@TIME_ZONE;
SET TIME_ZONE = '+00:00';
SET @OLD_UNIQUE_CHECKS = @@UNIQUE_CHECKS,
    UNIQUE_CHECKS = 0;
SET @OLD_FOREIGN_KEY_CHECKS = @@FOREIGN_KEY_CHECKS,
    FOREIGN_KEY_CHECKS = 0;
SET @OLD_SQL_MODE = @@SQL_MODE,
    SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';
SET @OLD_SQL_NOTES = @@SQL_NOTES,
    SQL_NOTES = 0;
-- 
-- 
TRUNCATE TABLE `phixies_app_dialetica`.`users`;
-- 
-- Sync users by authors
-- 
INSERT INTO `phixies_app_dialetica`.`users` (
        `id`,
        `type`,
        `name`,
        `email`,
        `document`,
        `phone`,
        `role`,
        `password`,
        `accept_eula`,
        `status`,
        `created_at`,
        `updated_at`
    )
select (`id_autor` + 1000) as 'id',
    'author' as 'type',
    `nome` as 'name',
    `email`,
    `cpf` as 'document',
    `fone` as 'phone',
    `titulacao` as 'role',
    `senha` as 'password',
    `flag_mkt` as 'accept_eula',
    `status` as 'status',
    `datahora_criacao` as 'created_at',
    `datahora_alteracao` as 'updated_at'
from `phixies_dialetica_db_aws`.`autor`;
-- 
-- Sync users
-- 
INSERT INTO `phixies_app_dialetica`.`users` (
        `id`,
        `type`,
        `name`,
        `email`,
        `password`,
        `status`
    )
select (`id` + 1) as 'id',
    'user' as 'type',
    `nome` as 'name',
    `email`,
    `senha` as 'password',
    `status` as 'status'
from `phixies_dialetica_db_aws`.`users`
group by `email`;
-- 
-- 
-- Sync users_addresses
-- 
TRUNCATE TABLE `phixies_app_dialetica`.`users_addresses`;
-- 
INSERT INTO `phixies_app_dialetica`.`users_addresses` (
        `user_id`,
        `zip_code`,
        `street`,
        `number`,
        `district`,
        `complement`,
        `city_ibge_id`,
        `city`,
        `state`
    )
SELECT (`id_autor` + 1000) as 'user_id',
    `cep` as 'zip_code',
    `rua` AS 'street',
    `numero` as 'number',
    `bairro` as 'district',
    `complemento` as 'complement',
    NULL AS 'city_ibge_id',
    `cidade` as 'city',
    `uf` as 'state'
FROM `phixies_dialetica_db_aws`.`obras`
WHERE `cep` IS NOT NULL
GROUP BY `id_autor`,
    `cep`;
-- 
-- 
TRUNCATE TABLE `phixies_app_dialetica`.`articles_types`;
-- 
-- Sync articles_types from obras_tipo
--
INSERT INTO `phixies_app_dialetica`.`articles_types` (
        `id`,
        `name`,
        `price`,
        `price_rule`,
        `status`,
        `created_at`,
        `updated_at`
    )
select `o`.`id` as 'id',
    `o`.`tipo` as 'name',
    `o`.`preco` as 'price',
    'fixed' as 'price_rule',
    1 as 'status',
    `o`.`datahora_criacao` as 'created_at',
    `o`.`datahora_alteracao` as 'updated_at'
from `phixies_dialetica_db_aws`.`obras_tipo` as `o`;
-- 
-- 
TRUNCATE TABLE `phixies_app_dialetica`.`articles_areas`;
-- 
-- Sync articles_areas from obras_areas_conhecimento
--
INSERT INTO `phixies_app_dialetica`.`articles_areas` (
        `id`,
        `name`,
        `status`,
        `created_at`,
        `updated_at`
    )
select `o`.`id` as 'id',
    `o`.`area` as 'name',
    1 as 'status',
    `o`.`datahora_criacao` as 'created_at',
    `o`.`datahora_alteracao` as 'updated_at'
from `phixies_dialetica_db_aws`.`obras_areas_conhecimento` as `o`;
-- 
-- 
TRUNCATE TABLE `phixies_app_dialetica`.`articles_collections`;
-- 
-- Sync articles_collections from obras_areas_conhecimento
--
INSERT INTO `phixies_app_dialetica`.`articles_collections` (
        `id`,
        `area_id`,
        `name`,
        `status`,
        `created_at`,
        `updated_at`
    )
select `o`.`id` as 'id',
    `o`.`area` as 'area_id',
    `o`.`coletania` as 'name',
    1 as 'status',
    `o`.`datahora_criacao` as 'created_at',
    `o`.`datahora_alteracao` as 'updated_at'
from `phixies_dialetica_db_aws`.`coletania` as `o`;
-- 
-- 
TRUNCATE TABLE `phixies_app_dialetica`.`articles_subareas`;
-- 
-- Sync articles_subareas from obras_subareas_conhecimento
--
INSERT INTO `phixies_app_dialetica`.`articles_subareas` (
        `id`,
        `area_id`,
        `name`,
        `status`,
        `created_at`,
        `updated_at`
    )
select `o`.`id` as 'id',
    `o`.`area` as 'area_id',
    `o`.`subarea` as 'name',
    1 as 'status',
    `o`.`datahora_criacao` as 'created_at',
    `o`.`datahora_alteracao` as 'updated_at'
from `phixies_dialetica_db_aws`.`obras_subareas_conhecimento` as `o`
where `o`.`area` >= 13;
-- 
-- 
TRUNCATE TABLE `phixies_app_dialetica`.`articles_specialties`;
-- 
-- Sync articles_specialties from obras_especialidade
--
INSERT INTO `phixies_app_dialetica`.`articles_specialties` (
        `id`,
        `area_id`,
        `subarea_id`,
        `name`,
        `status`,
        `created_at`,
        `updated_at`
    )
select `o`.`id` as 'id',
    `o`.`area` as 'area_id',
    `o`.`subarea` as 'subarea_id',
    `o`.`especialidade` as 'name',
    1 as 'status',
    `o`.`datahora_criacao` as 'created_at',
    `o`.`datahora_alteracao` as 'updated_at'
from `phixies_dialetica_db_aws`.`obras_especialidade` as `o`
where `o`.`area` >= 13
    and `o`.`id` not in(223, 371, 551, 629, 952, 975, 1038);
-- 
-- 
TRUNCATE TABLE `phixies_app_dialetica`.`articles`;
-- 
-- Sync articles from obras
-- 
INSERT INTO `phixies_app_dialetica`.`articles` (
        `id`,
        `type_id`,
        `area_id`,
        `collection_id`,
        `subarea_id`,
        `specialty_id`,
        `author_id`,
        `title`,
        `resume`,
        `tags`,
        `amount`,
        `accept_contract`,
        `accept_publication_rules`,
        `attachment`,
        `stage`,
        `status`,
        `created_at`,
        `updated_at`
    )
select `o`.`id_obra` as 'id',
    1 as 'type_id',
    (
        case
            when `o`.`area` < 1 then null
            else `o`.`area`
        end
    ) as 'area_id',
    (
        case
            when `o`.`coletania` < 1 then null
            else `o`.`coletania`
        end
    ) as 'collection_id',
    (
        case
            when `o`.`sub_area` < 1 then null
            else `o`.`sub_area`
        end
    ) as 'subarea_id',
    (
        case
            when `o`.`especialidade` < 1 then null
            else (
                case
                    when `o`.`especialidade` = 223 then 175
                    when `o`.`especialidade` = 371 then 366
                    when `o`.`especialidade` = 551 then 550
                    when `o`.`especialidade` = 629 then 628
                    when `o`.`especialidade` = 952 then 951
                    when `o`.`especialidade` = 975 then 959
                    when `o`.`especialidade` = 1038 then 1037
                    else `o`.`especialidade`
                end
            )
        end
    ) as 'specialty_id',
    (
        case
            when `o`.`id_autor` = 0 then 0
            else (`o`.`id_autor` + 1000)
        end
    ) as 'author_id',
    `o`.`titulo` as 'title',
    `o`.`resumo` as 'resume',
    `o`.`tags` as 'tags',
    `o`.`valor` as 'amount',
    `o`.`flag_acordo` as 'accept_contract',
    `o`.`flag_formatacao` as 'accept_publication_rules',
    concat(
        '{"name":"',
        regexp_replace(
            replace(`o`.`arquivo`, 'up/', ''),
            '([a-z0-9_]+)\.([a-z0-9]+)',
            '\\1'
        ),
        '","path":"',
        regexp_replace(
            replace(`o`.`arquivo`, 'up/', ''),
            '([a-z0-9_]+)\.([a-z0-9]+)',
            '\\0'
        ),
        '"}'
    ) as 'attachment',
    'complete' as 'stage',
    `o`.`status` as 'status',
    `o`.`datahora_criacao` as 'created_at',
    `o`.`datahora_alteracao` as 'updated_at'
from `phixies_dialetica_db_aws`.`obras` as `o`;
-- 
-- 
TRUNCATE TABLE `phixies_app_dialetica`.`articles_integrations_services`;
-- 
-- Sync articles payments from obras
--
INSERT INTO `phixies_app_dialetica`.`articles_integrations_services` (
        `user_id`,
        `type`,
        `operation`,
        `source`,
        `source_id`,
        `service`,
        `service_id`,
        `service_status`,
        `service_request_payload`,
        `service_response_payload`,
        `started_at`,
        `finished_at`,
        `created_at`,
        `updated_at`
    )
select (`o`.`id_autor` + 1000) as 'user_id',
    'payment' as 'type',
    'create' as 'operation',
    'articles' as 'source',
    `o`.id_obra as 'source_id',
    'Cielo' as 'service',
    `o`.`paymentID` as 'service_id',
    `o`.`status_pagamento` as 'service_status',
    null as 'service_request_payload',
    concat(
        '{"Payment":{"Tid": "',
        `o`.`TID`,
        '","Status":',
        substring(`o`.`resultado_pagamento`, 1, 1),
        ',"ReturnCode":"',
        substring(`o`.`resultado_pagamento`, 3, 2),
        '","PaymentId":"',
        `o`.`paymentID`,
        '"}}'
    ) as 'service_response_payload',
    coalesce(`o`.`datahora_pagamento`, `o`.`datahora_criacao`) as 'started_at',
    coalesce(`o`.`datahora_pagamento`, `o`.`datahora_criacao`) as 'finished_at',
    `o`.`datahora_criacao` as 'created_at',
    `o`.`datahora_alteracao` as 'updated_at'
from `phixies_dialetica_db_aws`.`obras` as `o`
where (
        `o`.`paymentID` is not null
        and `o`.`paymentID` != ''
    )
    or (
        `o`.`TID` is not null
        and `o`.`TID` != ''
    );
-- 
-- 
TRUNCATE TABLE `phixies_app_dialetica`.`articles_coauthors`;
-- 
-- 
-- Sync phixies_app_dialetica.articles_coauthors from obras_coautor
-- 
INSERT INTO `phixies_app_dialetica`.`articles_coauthors` (
        `article_id`,
        `name`,
        `email`,
        `role`,
        `created_at`,
        `updated_at`
    )
select `o`.`id_obra` as 'article_id',
    trim(`o`.`coautor_nome`) as 'name',
    trim(`o`.`coautor_email`) as 'email',
    trim(`o`.`coautor_titulo`) as 'role',
    `o`.`datahora_criacao` as 'created_at',
    `o`.`datahora_alteracao` as 'updated_at'
from `phixies_dialetica_db_aws`.`obras_coautor` as `o`
where length(trim(`o`.`coautor_nome`)) > 0;
-- 
-- 
-- Update articles.author_address_id from users_addresses join obras
-- 
update `phixies_app_dialetica`.`articles`
set `author_address_id` = (
        select `s`.`id`
        from `phixies_app_dialetica`.`users_addresses` as `s`
            inner join `phixies_dialetica_db_aws`.`obras` as `o` on (
                `s`.`user_id` = (`o`.`id_autor` + 1000)
                and `s`.`zip_code` = `o`.`cep`
            )
        where `o`.`id_obra` = `phixies_app_dialetica`.`articles`.`id`
    );
-- 
-- 
-- Update articles.payment_id from articles_integrations_services
-- 
update `phixies_app_dialetica`.`articles`
set `payment_id` = (
        select `s`.`id`
        from `phixies_app_dialetica`.`articles_integrations_services` as `s`
        where `s`.`type` = 'payment'
            and `s`.`source` = 'articles'
            and `s`.`source_id` = `phixies_app_dialetica`.`articles`.`id`
    );
-- 
-- 
-- Update articles.status
-- 
update `phixies_app_dialetica`.`articles`
set `status` = concat(`status`, 0)
where `status` > 0;
-- 
-- 
SET TIME_ZONE = @OLD_TIME_ZONE;
SET SQL_MODE = @OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS = @OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS = @OLD_UNIQUE_CHECKS;
SET CHARACTER_SET_CLIENT = @OLD_CHARACTER_SET_CLIENT;
SET CHARACTER_SET_RESULTS = @OLD_CHARACTER_SET_RESULTS;
SET COLLATION_CONNECTION = @OLD_COLLATION_CONNECTION;
SET SQL_NOTES = @OLD_SQL_NOTES;


insert into `phixies_app_dialetica`.`users` (
        `id`,
        `type`,
        `email`,
        `name`,
        `password`,
        `created_at`,
        `updated_at`
    )
values(
        1,
        'user',
        'marcioantunes.ma@gmail.com',
        'Marcio Antunes',
        '9fc8e76df9e63b2dd37479c3f456f9e8',
        '2022-03-01 14:00:00',
        '2022-03-01 14:00:00'
    ),
    (
        1000,
        'author',
        'marcioantunes.ma@gmail.com',
        'Marcio Antunes',
        '9fc8e76df9e63b2dd37479c3f456f9e8',
        '2022-03-01 14:00:00',
        '2022-03-01 14:00:00'
    );
-- 
-- 
-- Sync created invoices
-- 
-- 
insert into `phixies_app_dialetica`.`articles_integrations_services` (
        `user_id`,
        `type`,
        `operation`,
        `source`,
        `source_id`,
        `service`,
        `service_id`,
        `service_status`,
        `service_request_payload`,
        `service_response_payload`,
        `started_at`,
        `finished_at`
    )
values (
        '1',
        'invoice',
        'create',
        'articles',
        '17',
        'PlugNotas',
        '6230da3365749f4b4431cf6f',
        'CANCELADO',
        '[{"idIntegracao":"OBRP0000000017"}]',
        '{"documents":[{"idIntegracao":"OBRP0000000017","prestador":"32431939000105","id":"6230da3365749f4b4431cf6f"}],"message":"Nota(as) em processamento","protocol":""}',
        '2022-03-15 00:00:00',
        '2022-03-15 00:00:00'
    ),
    (
        '1',
        'invoice',
        'create',
        'articles',
        '76',
        'PlugNotas',
        '623113220d257b2e6882cb00',
        'CANCELADO',
        '[{"idIntegracao":"OBRP0000000076"}]',
        '{"documents":[{"idIntegracao":"OBRP0000000076","prestador":"32431939000105","id":"623113220d257b2e6882cb00"}],"message":"Nota(as) em processamento","protocol":""}',
        '2022-03-15 00:00:00',
        '2022-03-15 00:00:00'
    ),
    (
        '1',
        'invoice',
        'create',
        'articles',
        '309',
        'PlugNotas',
        '6232374464e6b2f0c9be0c49',
        'CONCLUIDO',
        '[{"idIntegracao":"OBRP0000000309"}]',
        '{"documents":[{"idIntegracao":"OBRP0000000309","prestador":"32431939000105","id":"6232374464e6b2f0c9be0c49"}],"message":"Nota(as) em processamento","protocol":""}',
        '2022-03-16 00:00:00',
        '2022-03-16 00:00:00'
    ),
    (
        '1',
        'invoice',
        'create',
        'articles',
        '355',
        'PlugNotas',
        '62323747a9fae7e436762c0a',
        'CONCLUIDO',
        '[{"idIntegracao":"OBRP0000000355"}]',
        '{"documents":[{"idIntegracao":"OBRP0000000355","prestador":"32431939000105","id":"62323747a9fae7e436762c0a"}],"message":"Nota(as) em processamento","protocol":""}',
        '2022-03-16 00:00:00',
        '2022-03-16 00:00:00'
    ),
    (
        '1',
        'invoice',
        'create',
        'articles',
        '529',
        'PlugNotas',
        '623b17afea12153850dc34a4',
        'CONCLUIDO',
        '[{"idIntegracao":"OBRP0000000529"}]',
        '{"documents":[{"idIntegracao":"OBRP0000000529","prestador":"32431939000105","id":"623b17afea12153850dc34a4"}],"message":"Nota(as) em processamento","protocol":""}',
        '2022-03-23 00:00:00',
        '2022-03-23 00:00:00'
    ),
    (
        '1',
        'invoice',
        'create',
        'articles',
        '530',
        'PlugNotas',
        '623b190c4a578085e21147ed',
        'CONCLUIDO',
        '[{"idIntegracao":"OBRP0000000530"}]',
        '{"documents":[{"idIntegracao":"OBRP0000000530","prestador":"32431939000105","id":"623b190c4a578085e21147ed"}],"message":"Nota(as) em processamento","protocol":""}',
        '2022-03-23 00:00:00',
        '2022-03-23 00:00:00'
    ),
    (
        '1',
        'invoice',
        'create',
        'articles',
        '20000000017',
        'PlugNotas',
        '623b23bbfd5d983ea80569cf',
        'REJEITADO',
        '[{"idIntegracao":"OBRP20000000017"}]',
        '{"documents":[{"idIntegracao":"OBRP20000000017","prestador":"32431939000105","id":"623b23bbfd5d983ea80569cf"}],"message":"Nota(as) em processamento","protocol":""}',
        '2022-03-23 00:00:00',
        '2022-03-23 00:00:00'
    ),
    (
        '1',
        'invoice',
        'create',
        'articles',
        '20000000076',
        'PlugNotas',
        '623b23beff243ff66aea4b17',
        'CONCLUIDO',
        '[{"idIntegracao":"OBRP20000000076"}]',
        '{"documents":[{"idIntegracao":"OBRP20000000076","prestador":"32431939000105","id":"623b23beff243ff66aea4b17"}],"message":"Nota(as) em processamento","protocol":""}',
        '2022-03-23 00:00:00',
        '2022-03-23 00:00:00'
    ),
    (
        '1',
        'invoice',
        'create',
        'articles',
        '17',
        'PlugNotas',
        '623b38cc8bcf4fe348fddcba',
        'CONCLUIDO',
        '[{"idIntegracao":"OBRP0000000017-2"}]',
        '{"documents":[{"idIntegracao":"OBRP0000000017-2","prestador":"32431939000105","id":"623b38cc8bcf4fe348fddcba"}],"message":"Nota(as) em processamento","protocol":""}',
        '2022-03-23 00:00:00',
        '2022-03-23 00:00:00'
    ),
    (
        '1',
        'invoice',
        'create',
        'articles',
        '953',
        'PlugNotas',
        '623e796b5692077bb40d041c',
        'CANCELADO',
        '[{"idIntegracao":"OBRP0000000953"}]',
        '{"documents":[{"idIntegracao":"OBRP0000000953","prestador":"32431939000105","id":"623e796b5692077bb40d041c"}],"message":"Nota(as) em processamento","protocol":""}',
        '2022-03-25 00:00:00',
        '2022-03-25 00:00:00'
    ),
    (
        '1',
        'invoice',
        'create',
        'articles',
        '953',
        'PlugNotas',
        '623e7a578e265c0ad99883ba',
        'CONCLUIDO',
        '[{"idIntegracao":"OBRP0000000953-2"}]',
        '{"documents":[{"idIntegracao":"OBRP0000000953-2","prestador":"32431939000105","id":"623e7a578e265c0ad99883ba"}],"message":"Nota(as) em processamento","protocol":""}',
        '2022-03-25 00:00:00',
        '2022-03-25 00:00:00'
    );
-- 
-- 
-- Sync articles.invoice_id from articles_integrations_services
-- 
-- 
update `phixies_app_dialetica`.`articles`
set `invoice_id` = (
        select max(`s`.`id`)
        from `phixies_app_dialetica`.`articles_integrations_services` as `s`
        where `s`.`type` = 'invoice'
            and `s`.`operation` = 'create'
            and `s`.`source_id` = `phixies_app_dialetica`.`articles`.`id`
    ),
    `amount` = (
        select `s`.`price`
        from `phixies_app_dialetica`.`articles_types` as `s`
        where `s`.`id` = `phixies_app_dialetica`.`articles`.`type_id`
    );
-- 
-- 
-- Create article type
-- 
-- 
-- 
-- Create revision type
-- 
INSERT INTO `phixies_app_dialetica`.`articles_types` (
        `id`,
        `name`,
        `price`,
        `price_rule`,
        `minimum_price`,
        `status`,
        `created_at`,
        `updated_at`,
        `deleted_at`
    )
VALUES(
        3,
        'Revisão',
        0.05,
        'words',
        300.00,
        1,
        '2022-03-25 19:50:29',
        '2022-03-25 19:51:56',
        NULL
    );