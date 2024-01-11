-- 
-- Alter table articles_collections
-- 
alter table `articles_collections`
add column `expires_at` datetime null default null
after `approved_by`;
-- 
-- Alter table affiliates_coupons
-- 
alter table `affiliates_coupons`
add column `max_uses` int null default null
after `affiliate_value`,
    add column `max_uses_user` int null default null
after `max_uses`,
    add column `start_at` datetime null default null
after `max_uses_user`;
-- 
-- Alter table affiliates_coupons
-- 
alter table `affiliates_coupons`
modify column `user_id` bigint(20) null,
    modify column `discount_value` decimal(10, 2) default 0.00 null,
    modify column `affiliate_value` decimal(10, 2) default 0.00 null,
    modify column `expires_at` datetime default null null;
-- 
-- Alter table affiliates_coupons_entries
-- 
alter table `affiliates_coupons_entries`
modify column `author_id` bigint(20) null;
-- 
-- Alter table articles_collections
-- 
alter table articles_collections
add column `cover_image` varchar(255) null default null
after `accept_publication_rules`;
-- 
-- Alter table articles_collections_authors
-- 
alter table articles_collections_authors
add column `accepted_at` datetime null default null
after `author_id`;
-- 
-- Update articles_collections_authors.accepted_at
-- 
update articles_collections_authors
set accepted_at = created_at
where accepted_at is null;
-- 
-- Update articles_collections.expires_at
-- 
update `articles_collections`
set `expires_at` = date_add(`created_at`, interval 30 day)
where `expires_at` is null
    and `author_id` is not null;