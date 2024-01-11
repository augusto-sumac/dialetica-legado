-- 
-- Alter table articles add columns
--
ALTER TABLE `articles`
ADD COLUMN `review_comment` TEXT(1500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL
AFTER `final_attachment`;
ALTER TABLE `articles`
ADD COLUMN `doi` VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL
AFTER `status`;
-- 
-- Alter table articles_collections modify columns
--
ALTER TABLE `articles_collections`
MODIFY COLUMN area_id bigint(20) NULL DEFAULT NULL;
-- 
-- Alter table articles_collections add columns
--
ALTER TABLE `articles_collections`
ADD COLUMN `isbn` VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL
AFTER `name`;
ALTER TABLE `articles_collections`
ADD COLUMN `isbn_e_book` VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL
AFTER `isbn`;
ALTER TABLE `articles_collections`
ADD COLUMN `doi` VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL
AFTER `isbn_e_book`;
ALTER TABLE `articles_collections`
ADD COLUMN `book_url` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL
AFTER `doi`;