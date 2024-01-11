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
        1,
        'invoice',
        'create',
        'articles',
        17,
        'PlugNotas',
        '6230da3365749f4b4431cf6f',
        'CANCELADO',
        '[{"idIntegracao":"OBRP0000000017","prestador":{"cpfCnpj":"32431939000105","email":"atendimento@editoradialetica.com"},"tomador":{"telefone":{"ddd":"31","numero":"999887849"},"cpfCnpj":"07527949651","razaoSocial":"Vitor Medrado","email":"vitormedrado@gmail.com","endereco":{"codigoPais":"1058","descricaoPais":"Brasil","descricaoCidade":"Belo Horizonte","cep":"30180060","tipoLogradouro":"Rua","logradouro":"Rua Juiz de Fora","codigoCidade":"3106200","estado":"MG","numero":"673","bairro":"Barro Preto"}},"servico":{"codigo":"03158","descricaoLC116":"Datilograf, digitação, estenogrf, expdnte, secret, redação, ed. revis, infr estrut adm e congêneres","discriminacao":"Serviço de publicação de capítulo de livro.","cnae":"03158","codigoTributacao":"3550308","codigoCidadeIncidencia":"3550308","descricaoCidadeIncidencia":"SÃO PAULO","iss":{"aliquota":0,"tipoTributacao":6},"retencao":{"pis":{"aliquota":0},"cofins":{"aliquota":0},"csll":{"aliquota":0}},"valor":{"servico":399}}}]',
        '{"documents":[{"idIntegracao":"OBRP0000000017","prestador":"32431939000105","id":"6230da3365749f4b4431cf6f"}],"message":"Nota(as) em processamento","protocol":"d1a8fb39-5626-490b-a4fc-0f827c423683"}',
        '2022-03-15 15:25:54',
        '2022-03-15 15:25:54'
    ),
    (
        1,
        'invoice',
        'cancel',
        'articles',
        17,
        'PlugNotas',
        '6230da3365749f4b4431cf6f',
        'CONCLUIDO',
        null,
        '{"message":"Cancelamento em processamento","data":{"protocol":"dd54b6d0-303d-42dc-8c87-d4d8381b3392"}}',
        '2022-03-17 00:27:38',
        '2022-03-17 00:27:38'
    ),
    (
        1,
        'invoice',
        'create',
        'articles',
        76,
        'PlugNotas',
        '623113220d257b2e6882cb00',
        'CANCELADO',
        '[{"idIntegracao":"OBRP0000000076","prestador":{"cpfCnpj":"32431939000105","email":"atendimento@editoradialetica.com"},"tomador":{"telefone":{"ddd":"31","numero":"999887849"},"cpfCnpj":"07527949651","razaoSocial":"Vitor Medrado","email":"vitormedrado@gmail.com","endereco":{"codigoPais":"1058","descricaoPais":"Brasil","descricaoCidade":"Belo Horizonte","cep":"30180060","tipoLogradouro":"Rua","logradouro":"Rua Juiz de Fora","codigoCidade":"3106200","estado":"MG","numero":"673","bairro":"Barro Preto"}},"servico":{"codigo":"03158","descricaoLC116":"Datilograf, digitação, estenogrf, expdnte, secret, redação, ed. revis, infr estrut adm e congêneres","discriminacao":"Serviço de publicação de capítulo de livro.","cnae":"03158","codigoTributacao":"3550308","codigoCidadeIncidencia":"3550308","descricaoCidadeIncidencia":"SÃO PAULO","iss":{"aliquota":0,"tipoTributacao":6},"retencao":{"pis":{"aliquota":0},"cofins":{"aliquota":0},"csll":{"aliquota":0}},"valor":{"servico":5}}}]',
        '{"documents":[{"idIntegracao":"OBRP0000000076","prestador":"32431939000105","id":"623113220d257b2e6882cb00"}],"message":"Nota(as) em processamento","protocol":"8f6069da-1b7b-446f-aea2-a151f7fd0006"}',
        '2022-03-15 15:25:54',
        '2022-03-15 15:25:54'
    ),
    (
        1,
        'invoice',
        'cancel',
        'articles',
        76,
        'PlugNotas',
        '623113220d257b2e6882cb00',
        'CONCLUIDO',
        null,
        '{"message":"Cancelamento em processamento","data":{"protocol":"75fff4a5-833e-48d6-8e08-a053b19db446"}}',
        '2022-03-17 00:27:22',
        '2022-03-17 00:27:22'
    ),
    (
        1,
        'invoice',
        'create',
        'articles',
        309,
        'PlugNotas',
        '6232374464e6b2f0c9be0c49',
        'CONCLUIDO',
        '[{"idIntegracao":"OBRP0000000309","prestador":{"cpfCnpj":"32431939000105","email":"atendimento@editoradialetica.com"},"tomador":{"telefone":{"ddd":"31","numero":"983111384"},"cpfCnpj":"09786125665","razaoSocial":"Tiago Aroeira","email":"tiagoaroeira@gmail.com","endereco":{"codigoPais":"1058","descricaoPais":"Brasil","descricaoCidade":"Belo Horizonte","cep":"31035520","tipoLogradouro":"Rua","logradouro":"Rua Godofredo de Araújo","codigoCidade":"3106200","estado":"MG","numero":"49","bairro":"Sagrada Família"}},"servico":{"codigo":"03158","descricaoLC116":"Datilograf, digitação, estenogrf, expdnte, secret, redação, ed. revis, infr estrut adm e congêneres","discriminacao":"Serviço de publicação de capítulo de livro.","cnae":"03158","codigoTributacao":"3550308","codigoCidadeIncidencia":"3550308","descricaoCidadeIncidencia":"SÃO PAULO","iss":{"aliquota":0,"tipoTributacao":6},"retencao":{"pis":{"aliquota":0},"cofins":{"aliquota":0},"csll":{"aliquota":0}},"valor":{"servico":396}}}]',
        '{"documents":[{"idIntegracao":"OBRP0000000309","prestador":"32431939000105","id":"6232374464e6b2f0c9be0c49"}],"message":"Nota(as) em processamento","protocol":"5a7ee5d8-0d4d-4afd-9980-8e74b7e0321e"}',
        '2022-03-15 15:25:54',
        '2022-03-15 15:25:54'
    ),
    (
        1,
        'invoice',
        'create',
        'articles',
        355,
        'PlugNotas',
        '62323747a9fae7e436762c0a',
        'CONCLUIDO',
        '[{"idIntegracao":"OBRP0000000355","prestador":{"cpfCnpj":"32431939000105","email":"atendimento@editoradialetica.com"},"tomador":{"telefone":{"ddd":"31","numero":"983111384"},"cpfCnpj":"09786125665","razaoSocial":"Tiago Aroeira","email":"tiagoaroeira@gmail.com","endereco":{"codigoPais":"1058","descricaoPais":"Brasil","descricaoCidade":"Belo Horizonte","cep":"31035520","tipoLogradouro":"Rua","logradouro":"Rua Godofredo de Araújo","codigoCidade":"3106200","estado":"MG","numero":"49","bairro":"Sagrada Família"}},"servico":{"codigo":"03158","descricaoLC116":"Datilograf, digitação, estenogrf, expdnte, secret, redação, ed. revis, infr estrut adm e congêneres","discriminacao":"Serviço de publicação de capítulo de livro.","cnae":"03158","codigoTributacao":"3550308","codigoCidadeIncidencia":"3550308","descricaoCidadeIncidencia":"SÃO PAULO","iss":{"aliquota":0,"tipoTributacao":6},"retencao":{"pis":{"aliquota":0},"cofins":{"aliquota":0},"csll":{"aliquota":0}},"valor":{"servico":396}}}]',
        '{"documents":[{"idIntegracao":"OBRP0000000355","prestador":"32431939000105","id":"62323747a9fae7e436762c0a"}],"message":"Nota(as) em processamento","protocol":"89d4a472-1228-437e-81ba-ad97b20c018e"}',
        '2022-03-16 16:15:19',
        '2022-03-16 16:15:19'
    );