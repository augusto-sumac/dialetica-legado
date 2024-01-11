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