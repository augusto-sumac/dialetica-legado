-- 
-- Alter table articles modidy columns
-- 
ALTER TABLE `articles`
MODIFY COLUMN `resume` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL;
ALTER TABLE `articles`
MODIFY COLUMN `tags` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL;
-- 
-- Alter table articles add columns
--
ALTER TABLE `articles`
ADD COLUMN `proof_attachment` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'json attachment data'
after `attachment`;
ALTER TABLE `articles`
ADD COLUMN `final_attachment` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'json attachment data'
after `proof_attachment`;
-- 
-- Alter table articles_types
-- 
ALTER TABLE `articles_types`
ADD COLUMN `minimum_price` DECIMAL(10, 2) NOT NULL DEFAULT 0
AFTER `price_rule`;
-- 
-- Create revision type
-- 
INSERT INTO articles_types (
        id,
        name,
        price,
        price_rule,
        minimum_price,
        status,
        created_at,
        updated_at,
        deleted_at
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