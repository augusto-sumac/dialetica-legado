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