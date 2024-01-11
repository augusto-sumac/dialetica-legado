-- 
-- Create table articles_collections_authors
-- 
CREATE TABLE `articles_collections_authors` (
    `collection_id` BIGINT(20) NOT NULL,
    `author_id` BIGINT(20) NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
    `deleted_at` DATETIME DEFAULT NULL,
    KEY `collection_id` (`collection_id`),
    KEY `author_id` (`author_id`),
    PRIMARY KEY (`collection_id`, `author_id`),
    CONSTRAINT `articles_collections_authors_ibfk_1` FOREIGN KEY (`collection_id`) REFERENCES `articles_collections` (`id`),
    CONSTRAINT `articles_collections_authors_ibfk_2` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
-- 
-- Create table affiliates_coupons
-- 
CREATE TABLE `affiliates_coupons` (
    `id` BIGINT(20) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `user_id` BIGINT(20) NOT NULL,
    `token` VARCHAR(40) NOT NULL,
    `type` VARCHAR(20) NOT NULL DEFAULT 'ARTICLES',
    `discount_rule` VARCHAR(20) NOT NULL DEFAULT 'settings' COMMENT 'settings, percent, fixed',
    `discount_value` DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    `affiliate_rule` VARCHAR(20) NOT NULL DEFAULT 'settings' COMMENT 'settings, percent, fixed',
    `affiliate_value` DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    `expires_at` DATETIME NULL DEFAULT '2099-12-31 23:59:59',
    `canceled_at` DATETIME NULL DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
    `deleted_at` DATETIME DEFAULT NULL,
    KEY `user_id` (`user_id`),
    CONSTRAINT `affiliates_coupons_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
-- 
-- Create table affiliates_coupons_entries
-- 
CREATE TABLE `affiliates_coupons_entries` (
    `id` BIGINT(20) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `author_id` BIGINT(20) NOT NULL,
    `article_id` BIGINT(20) NULL DEFAULT NULL,
    `affiliate_coupon_id` BIGINT(20) NOT NULL,
    `type` CHAR(1) NOT NULL DEFAULT 'C' COMMENT 'D -> debit, C -> credit',
    `amount` DECIMAL(10, 2) NOT NULL,
    `status` VARCHAR(3) NOT NULL DEFAULT 'PE' COMMENT 'PE -> Pending, CA -> Canceled, FI -> Finished',
    `available_at` DATE NULL DEFAULT NULL,
    `paid_at` DATETIME NULL DEFAULT NULL,
    `paid_by_user_id` BIGINT(20) NULL DEFAULT NULL,
    `payment_attachment` VARCHAR(255) NULL DEFAULT NULL COMMENT 'json attachment data',
    `canceled_at` DATETIME NULL DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
    `deleted_at` DATETIME DEFAULT NULL,
    KEY `author_id` (`author_id`),
    KEY `article_id` (`article_id`),
    KEY `affiliate_coupon_id` (`affiliate_coupon_id`),
    KEY `paid_by_user_id` (`paid_by_user_id`),
    CONSTRAINT `affiliates_coupons_entries_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`),
    CONSTRAINT `affiliates_coupons_entries_ibfk_2` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`),
    CONSTRAINT `affiliates_coupons_entries_ibfk_3` FOREIGN KEY (`affiliate_coupon_id`) REFERENCES `affiliates_coupons` (`id`),
    CONSTRAINT `affiliates_coupons_entries_ibfk_4` FOREIGN KEY (`paid_by_user_id`) REFERENCES `users` (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
-- 
-- Alter table articles_collections modify/add columns
-- 
ALTER TABLE `articles_collections`
ADD COLUMN `subarea_id` BIGINT(20) DEFAULT NULL
AFTER `area_id`;
-- 
ALTER TABLE `articles_collections`
ADD COLUMN `specialty_id` BIGINT(20) DEFAULT NULL
AFTER `subarea_id`;
-- 
ALTER TABLE `articles_collections`
ADD COLUMN `author_id` BIGINT(20) NULL DEFAULT NULL
AFTER `specialty_id`;
-- 
-- Approval status
ALTER TABLE `articles_collections`
MODIFY COLUMN `status` VARCHAR(3) NOT NULL DEFAULT 'AC';
-- 
ALTER TABLE `articles_collections`
ADD COLUMN `approved_at` DATETIME NULL DEFAULT NULL
AFTER `status`;
-- 
ALTER TABLE `articles_collections`
ADD COLUMN `approved_by` BIGINT(20) NULL DEFAULT NULL
AFTER `approved_at`;
-- 
ALTER TABLE `articles_collections`
ADD COLUMN `description` MEDIUMTEXT NULL DEFAULT NULL
AFTER `name`;
-- 
ALTER TABLE `articles_collections`
ADD CONSTRAINT `articles_collections_subarea_id` FOREIGN KEY (`subarea_id`) REFERENCES `articles_subareas` (`id`),
    ADD CONSTRAINT `articles_collections_specialty_id` FOREIGN KEY (`specialty_id`) REFERENCES `articles_specialties` (`id`),
    ADD CONSTRAINT `articles_collections_collection_id` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`),
    ADD CONSTRAINT `articles_collections_approved_by` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`);
-- 
-- Alter table users modify/add columns
-- 
ALTER TABLE `users`
ADD COLUMN `curriculum` MEDIUMTEXT NULL DEFAULT NULL
AFTER `roles`;
-- 
ALTER TABLE `users`
ADD COLUMN `curriculum_url` VARCHAR(255) NULL DEFAULT NULL
AFTER `curriculum`;
-- {"type":"CC/PIX","bank_code":"0311","agency":"","number":"0123456"}
ALTER TABLE `users`
ADD COLUMN `bank_account` JSON NULL DEFAULT NULL
AFTER `roles`;
-- 
-- Alter table articles_coauthors modify/add columns
-- 
ALTER TABLE `articles_coauthors`
ADD COLUMN `curriculum` MEDIUMTEXT NULL DEFAULT NULL
AFTER `role`;
-- 
ALTER TABLE `articles_coauthors`
ADD COLUMN `curriculum_url` VARCHAR(255) NULL DEFAULT NULL
AFTER `curriculum`;
-- 
-- Alter table articles modify/add columns
-- 
ALTER TABLE `articles`
ADD COLUMN `affiliate_coupon_id` BIGINT(20) NULL DEFAULT NULL
AFTER `author_address_id`;
-- 
ALTER TABLE `articles`
ADD COLUMN `gross_amount` DECIMAL(10, 2) NOT NULL DEFAULT 0.00
AFTER `words_count`;
-- 
ALTER TABLE `articles`
ADD COLUMN `discount_amount` DECIMAL(10, 2) NOT NULL DEFAULT 0.00
AFTER `gross_amount`;
-- 
ALTER TABLE `articles`
ADD CONSTRAINT `articles_affiliate_coupon_id` FOREIGN KEY (`affiliate_coupon_id`) REFERENCES `affiliates_coupons` (`id`);
-- 
-- Update old vales
-- 
UPDATE `articles`
SET `gross_amount` = COALESCE(`amount`, 0.00)
WHERE `gross_amount` < `amount`
    OR `gross_amount` = 0.00;
-- 
-- 
-- Alter table articles_collections modify/add columns
-- 
ALTER TABLE `articles_collections`
ADD COLUMN `volume` INT(4) NULL DEFAULT 1
AFTER `description`;
-- 
ALTER TABLE `articles_collections`
ADD COLUMN `token` VARCHAR(20) NULL DEFAULT NULL
AFTER `volume`;
-- 
-- Alter table jobs modify/add columns
-- 
ALTER TABLE `jobs`
ADD COLUMN `schedule_date` DATETIME NULL DEFAULT NULL
AFTER `job`;
-- 
-- Alter table articles modify/add columns
-- 
alter table articles
add column `review_date` datetime null default null
after `review_comment`;
-- 
-- 
-- Alter table articles_collections modify/add indexes
-- 
SET FOREIGN_KEY_CHECKS = 0;
ALTER TABLE `articles_collections` DROP INDEX `articles_collections_unique_area_id_name`;
SET FOREIGN_KEY_CHECKS = 1;
-- 
CREATE UNIQUE INDEX `articles_collections_unique_idx` USING BTREE ON `articles_collections` (
    `area_id`,
    `subarea_id`,
    `specialty_id`,
    `volume`,
    `name`
);
-- 
-- Update settings
-- 
REPLACE INTO `settings` (`key`, `value`)
VALUES ('minimum_withdrawal_amount', 200),
    ('coupon_discount_percent', 5),
    ('coupon_affiliate_percent', 5),
    ('coupon_affiliate_retention_days', 15),
    ('days_approve_collection', 7),
    ('collection_days_limit', 30),
    ('minimum_articles_in_collection', 4);