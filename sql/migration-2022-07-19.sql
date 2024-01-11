-- 
-- Alter table articles_collections
-- 
alter table `articles_collections`
add column `accept_publication_rules` json null default null
after `book_url`;
-- 
-- Alter table articles add columns collection_token
-- 
alter table `articles`
add column `collection_token` varchar(20) null default null
after `collection_id`;
-- 
-- Creata Specialty N/A
-- 
SET FOREIGN_KEY_CHECKS = 0;
insert into articles_specialties (
        id,
        area_id,
        subarea_id,
        name,
        status,
        created_at,
        updated_at
    )
values(
        1,
        0,
        0,
        'Não Se Aplica',
        1,
        '2021-01-01 00:00:00',
        '2021-01-01 00:00:00'
    );
SET FOREIGN_KEY_CHECKS = 1;