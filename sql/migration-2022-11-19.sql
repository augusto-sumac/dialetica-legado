-- Modifiy default value

ALTER TABLE
    `affiliates_coupons` MODIFY COLUMN `type` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'ALL' NOT NULL;