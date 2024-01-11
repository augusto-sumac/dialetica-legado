<?php

use ConvertApi\ConvertApi;

// ConvertApi::setApiSecret('88hPieLi9GH6D2fG');
ConvertApi::setApiSecret('cmdQAXk2gTbaA13G');

/**
 * Format ID value
 *
 * @param int|string $id
 * @return string
 */
function str_pad_id($id = null, $length = 5)
{
    return str_pad($id, $length, 0, STR_PAD_LEFT);
}

/**
 * Format installment value
 *
 * @param int|string $id
 * @return string
 */
function str_pad_installment($installment = null, $length = 2, $empty = '-')
{
    if (!$installment) {
        return $empty;
    }

    return str_pad($installment, max(strlen($installment), $length), 0, STR_PAD_LEFT);
}

/**
 * Current author user SESSION
 *
 * @return object
 */
function logged_author($key = null)
{
    if ($key) {
        return array_get(objectToArray($_SESSION), 'author.' . $key);
    }

    return (object) array_get($_SESSION, 'author', []);
}

/**
 * Current user SESSION
 *
 * @return object
 */
function logged_user($key = null)
{
    if ($key) {
        return array_get(objectToArray($_SESSION), 'user.' . $key);
    }

    return (object) array_get($_SESSION, 'user', []);
}

function user_can_delete($only_validate = false)
{
    $users = ['marcioantunes.ma@gmail.com', 'vitor.medrado@editoradialetica.com'];

    if (!in_array(logged_user()->email, $users)) {
        if ($only_validate) {
            return false;
        }

        throw new GenericResponseException(response_json(
            [
                'message' => 'Você não tem acesso a esta função'
            ],
            403
        ));
    }

    return true;
}

const TB_ARTICLES = 'articles';
const TB_ARTICLES_AREAS = 'articles_areas';
const TB_ARTICLES_COAUTHORS = 'articles_coauthors';
const TB_ARTICLES_COLLECTIONS = 'articles_collections';
const TB_ARTICLES_INTEGRATIONS_SERVICES = 'articles_integrations_services';
const TB_ARTICLES_PAYMENTS = 'articles_integrations_services';
const TB_ARTICLES_INVOICES = 'articles_integrations_services';
const TB_ARTICLES_SPECIALTIES = 'articles_specialties';
const TB_ARTICLES_SUBAREAS = 'articles_subareas';
const TB_ARTICLES_TYPES = 'articles_types';
const TB_SETTINGS = 'settings';
const TB_USERS = 'users';
const TB_AUTHORS = 'users';
const TB_USERS_ADDRESSES = 'users_addresses';
const TB_AUTHORS_ADDRESSES = 'users_addresses';
const TB_JOBS = 'jobs';
const TB_ARTICLES_COLLECTIONS_AUTHORS = 'articles_collections_authors';
const TB_AFFILIATES_COUPONS = 'affiliates_coupons';
const TB_AFFILIATES_COUPONS_ENTRIES = 'affiliates_coupons_entries';

function articles()
{
    return DB::table(TB_ARTICLES)->where(TB_ARTICLES . '.type_id', 1);
}
function reviews()
{
    return DB::table(TB_ARTICLES)->where(TB_ARTICLES . '.type_id', 3);
}
function articles_areas()
{
    return DB::table(TB_ARTICLES_AREAS);
}
function articles_coauthors()
{
    return DB::table(TB_ARTICLES_COAUTHORS);
}
function articles_collections()
{
    return DB::table(TB_ARTICLES_COLLECTIONS);
}
function articles_integrations_services()
{
    return DB::table(TB_ARTICLES_INTEGRATIONS_SERVICES);
}
function articles_payments()
{
    return DB::table(TB_ARTICLES_PAYMENTS)->where_type('payment');
}
function articles_invoices()
{
    return DB::table(TB_ARTICLES_INVOICES)->where_type('invoice');
}
function articles_specialties()
{
    return DB::table(TB_ARTICLES_SPECIALTIES);
}
function articles_subareas()
{
    return DB::table(TB_ARTICLES_SUBAREAS);
}
function articles_types()
{
    return DB::table(TB_ARTICLES_TYPES);
}
function settings()
{
    return DB::table(TB_SETTINGS);
}
function users()
{
    return DB::table(TB_USERS)->where_type('user');
}
function users_addresses()
{
    return DB::table(TB_USERS_ADDRESSES);
}
function authors()
{
    return DB::table(TB_AUTHORS)->where_type('author');
}
function authors_addresses()
{
    return DB::table(TB_AUTHORS_ADDRESSES);
}
function jobs()
{
    return DB::table(TB_JOBS);
}
function articles_collections_authors()
{
    return DB::table(TB_ARTICLES_COLLECTIONS_AUTHORS);
}
function affiliates_coupons()
{
    return DB::table(TB_AFFILIATES_COUPONS);
}
function affiliates_coupons_entries()
{
    return DB::table(TB_AFFILIATES_COUPONS_ENTRIES);
}

/**
 * Get config item
 *
 * @param string $key
 * @param mixed $default
 * @return mixed
 */
function config($key, $default = null)
{
    return array_get($GLOBALS, "config.${key}", $default);
}

function attr_data_id($id = null)
{
    return 'data-id="' . ($id ? $id : null) . '"';
}

/**
 * Gerador de senhas
 *
 * @param integer $tamanho
 * @param boolean $maiusculas
 * @param boolean $minusculas
 * @param boolean $numeros
 * @param boolean $simbolos
 * @return void
 */
function gerar_senha($tamanho = 10, $maiusculas = true, $minusculas = true, $numeros = true, $simbolos = false)
{
    $senha = '';

    if ($maiusculas) {
        // se $maiusculas for "true", a variável $ma é embaralhada e adicionada para a variável $senha
        $senha = str_shuffle($senha . "ABCDEFGHIJKLMNOPQRSTUVYXWZ");
    }

    if ($minusculas) {
        // se $minusculas for "true", a variável $mi é embaralhada e adicionada para a variável $senha
        $senha = str_shuffle($senha . "abcdefghijklmnopqrstuvyxwz");
    }

    if ($numeros) {
        // se $numeros for "true", a variável $nu é embaralhada e adicionada para a variável $senha
        $senha = str_shuffle($senha . "0123456789");
    }

    if ($simbolos) {
        // se $simbolos for "true", a variável $si é embaralhada e adicionada para a variável $senha
        $senha = str_shuffle($senha . "!@#$%¨&*()_+=");
    }

    // retorna a senha embaralhada com "str_shuffle" com o tamanho definido pela variável $tamanho
    return substr(str_shuffle(str_shuffle(str_shuffle($senha))), 0, $tamanho);
}

function generate_unique_coupon($prefix = 'DIALETICA', $length = 6)
{
    $generate = fn () => $prefix . '-' . gerar_senha($length);

    $token = $generate();

    while (affiliates_coupons()->where_token($token)->first()) {
        $token = $generate();
    }

    return $token;
}

function make_option($value, $label, $selected = null)
{
    $selected = $selected === $value ? ' selected' : '';
    return '<option value="' . $value . '"' . $selected . '>' . $label . '</option>';
}

function make_select_options($options = [], $selected = null)
{
    return implode('', array_map(
        fn ($value, $label) => make_option($value, $label, $selected),
        array_keys($options),
        array_values($options),
    ));
}

/**
 * Retorna um array com os estados brasileiros
 * @return array
 */
function estados_brasil($key = null)
{
    $values = array(
        ''   => 'Selecione...',
        'AC' => 'Acre',
        'AL' => 'Alagoas',
        'AP' => 'Amapá',
        'AM' => 'Amazonas',
        'BA' => 'Bahia',
        'CE' => 'Ceará',
        'DF' => 'Distrito Federal',
        'ES' => 'Espírito Santo',
        'GO' => 'Goiás',
        'MA' => 'Maranhão',
        'MT' => 'Mato Grosso',
        'MS' => 'Mato Grosso do Sul',
        'MG' => 'Minas Gerais',
        'PA' => 'Pará',
        'PB' => 'Paraíba',
        'PR' => 'Paraná',
        'PE' => 'Pernambuco',
        'PI' => 'Piauí',
        'RJ' => 'Rio de Janeiro',
        'RN' => 'Rio Grande do Norte',
        'RS' => 'Rio Grande do Sul',
        'RO' => 'Rondônia',
        'RR' => 'Roraima',
        'SC' => 'Santa Catarina',
        'SP' => 'São Paulo',
        'SE' => 'Sergipe',
        'TO' => 'Tocantins',
    );

    return array_get($values, $key, $values);
}

function select_options_estados_brasil($selected = null)
{
    return make_select_options(estados_brasil(), $selected);
}

function author_role($key = null)
{
    $values = [
        ['value' => 'Doutor', 'label' => 'Doutor'],
        ['value' => 'Doutorando', 'label' => 'Doutorando'],
        ['value' => 'Mestre', 'label' => 'Mestre'],
        ['value' => 'Mestrando', 'label' => 'Mestrando'],
        ['value' => 'Pós-graduado', 'label' => 'Pós-graduado'],
        ['value' => 'Pós-graduando', 'label' => 'Pós-graduando'],
        ['value' => 'Graduado', 'label' => 'Graduado'],
        ['value' => 'Graduando', 'label' => 'Graduando'],
    ];

    if ($key === null) {
        return $values;
    }

    return current(array_filter($values, fn ($i) => $i['value'] == $key));
}

function article_status($key = null, $group = 1, $collection_author_id = null)
{
    $values = [
        [
            'groups' => [1, 2, 3],
            'value' => 0,
            'color' => 'muted',
            'label' => 'Rascunho'
        ],
        [
            'groups' => [1, 2, 3],
            'value' => 9,
            'color' => 'danger',
            'label' => 'Cancelado'
        ],
        [
            'groups' => [1],
            'value' => 10,
            'color' => 'danger',
            'label' => 'Recusado',
            'btn_label' => 'Recusar',
        ],
        [
            'groups' => [1],
            'value' => 11,
            'color' => 'danger',
            'label' => 'Recusado Organizador',
            'btn_label' => 'Rejeitar',
            'next' => 41
        ],
        [
            'groups' => [1],
            'value' => 20,
            'color' => 'primary',
            'label' => 'Obra em análise',
            'prev' => 10,
            'next' => 30
        ],
        [
            'groups' => [1, 2, 3],
            'value' => 30,
            'color' => 'secondary',
            'label' => 'Pagamento Pendente',
            'btn_label' => 'Pagamento',
        ],
        [
            'groups' => [1, 2, 3],
            'value' => 31,
            'color' => 'danger',
            'label' => 'Falha Pagamento'
        ],
        [
            'groups' => [1],
            'value' => 32,
            'color' => 'success',
            'label' => 'Pagamento Aprovado',
            // 'next' => 40
            'next' => $collection_author_id ? 41 : 40
        ],
        [
            'groups' => [3],
            // Somente para revisões
            'value' => 33,
            'color' => 'success',
            'label' => 'Pagamento Aprovado',
            'next' => 60
        ],
        [
            'groups' => [1, 2],
            'value' => 40,
            'color' => 'info',
            'label' => 'Em Produção',
            'btn_label' => 'Produção',
            'next' => 50
        ],
        [
            'groups' => [1],
            'value' => 41,
            'color' => 'info',
            'label' => 'Análise Organizador',
            'prev' => 11,
            'next' => 42
        ],
        [
            'groups' => [1],
            'value' => 42,
            'color' => 'success',
            'label' => 'Aprovado Organizador',
            'prev' => 41,
            'next' => 50
        ],
        [
            'groups' => [1, 2],
            'value' => 50,
            'color' => 'success',
            'label' => 'Publicado',
            'btn_label' => 'Publicar',
        ],
        [
            'groups' => [3],
            'value' => 60,
            'color' => 'info',
            'label' => 'Revisão em andamento',
            'btn_label' => 'Iniciar Revisão',
            'next' => 70
        ],
        [
            'groups' => [3],
            'value' => 70,
            'color' => 'success',
            'label' => 'Revisão em concluída',
            'btn_label' => 'Concluir',
        ]
    ];

    if ($key === null) {
        return array_values(array_filter($values, fn ($i) => in_array($group, $i['groups'])));
    }

    return current(array_filter($values, fn ($i) => $i['value'] == $key));
}

function article_payment_status($key = null)
{
    $values = [
        ['value' => -1, 'color' => 'muted', 'label' => ''],
        ['value' => 0, 'color' => 'danger', 'label' => 'Erro Processamento'],
        ['value' => 1, 'color' => 'info', 'label' => 'Gerado - Ag. Pagamento'],
        ['value' => 2, 'color' => 'success', 'label' => 'Pagamento Aprovado'],
        ['value' => 3, 'color' => 'danger', 'label' => 'Não Autorizado'],
        ['value' => 5, 'color' => 'danger', 'label' => 'Não Autorizado'],
        ['value' => 10, 'color' => 'danger', 'label' => 'Cancelado/Estornado'],
        ['value' => 11, 'color' => 'danger', 'label' => 'Cancelado/Estornado'],
        ['value' => 12, 'color' => 'warning', 'label' => 'Ag. Confirmação'],
        ['value' => 13, 'color' => 'warning', 'label' => 'Cancelado/Estornado'],
        ['value' => 20, 'color' => 'warning', 'label' => 'Recorrência agendada'],
        ['value' => 57, 'color' => 'muted', 'label' => 'Cartão Expirado'],
        ['value' => 78, 'color' => 'muted', 'label' => 'Cartão Bloqueado'],
        ['value' => 99, 'color' => 'muted', 'label' => 'Tempo para pgto excedeu o limite'],
        ['value' => 77, 'color' => 'muted', 'label' => 'Cartão Cancelado'],
        ['value' => 70, 'color' => 'muted', 'label' => 'Problemas com o Cartão de Crédito'],
        ['value' => 'PROCESSANDO', 'color' => 'secondary', 'label' => 'Processando'],
        ['value' => 'CANCELADO', 'color' => 'danger', 'label' => 'Cancelado/Estornado'],
    ];

    if ($key === null) {
        return array_values(array_filter($values, fn ($i) => $i['value'] !== -1));
    }

    return current(array_filter($values, fn ($i) => $i['value'] == $key));
}

function article_invoice_status($key = null)
{
    $values = [
        [
            'value' => 'NA',
            'color' => 'muted',
            'label' => 'N/A'
        ],
        [
            'value' => 'PROCESSANDO',
            'color' => 'secondary',
            'label' => 'Processando'
        ],
        [
            'value' => 'CONCLUIDO',
            'color' => 'success',
            'label' => 'Concluído'
        ],
        [
            'value' => 'DENEGADO',
            'color' => 'danger',
            'label' => 'Denegado'
        ],
        [
            'value' => 'REJEITADO',
            'color' => 'danger',
            'label' => 'Rejeitado'
        ],
        [
            'value' => 'CANCELADO',
            'color' => 'danger',
            'label' => 'Cancelado'
        ],
        [
            'value' => 'SUBSTITUIDO',
            'color' => 'success',
            'label' => 'Substituído'
        ],
    ];

    if ($key === null) {
        return $values;
    }

    return current(array_filter($values, fn ($i) => $i['value'] == $key));
}

function affiliates_withdraw_status($key = null)
{
    $values = [
        // [
        //     'value' => 'NA',
        //     'color' => 'muted',
        //     'label' => 'N/A'
        // ],
        [
            'value' => 'PE',
            'color' => 'secondary',
            'label' => 'Pendente'
        ],
        [
            'value' => 'CA',
            'color' => 'danger',
            'label' => 'Cancelado'
        ],
        [
            'value' => 'FI',
            'color' => 'success',
            'label' => 'Concluído'
        ],
    ];

    if ($key === null) {
        return $values;
    }

    return current(array_filter($values, fn ($i) => $i['value'] == $key));
}

function select_options_affiliates_withdraw_status($selected = null)
{
    $options = array_reduce(affiliates_withdraw_status(null), function ($arr, $item) {
        $arr[$item['value']] = $item['label'];
        return $arr;
    }, []);
    return make_select_options(['' => 'Selecione...'] + $options, $selected);
}

function affiliates_value_rule($key = null)
{
    $values = [
        [
            'value' => 'settings',
            'color' => 'bg-muted',
            'label' => 'Via Config',
        ],
        [
            'value' => 'percent',
            'color' => 'bg-blue-500 text-white',
            'label' => 'Percentual',
        ],
        [
            'value' => 'fixed',
            'color' => 'bg-teal-500 text-white',
            'label' => 'Valor',
        ],
    ];

    if ($key === null) {
        return $values;
    }

    return current(array_filter($values, fn ($i) => $i['value'] == $key));
}

function select_options_affiliates_value_rule($selected = null)
{
    $options = array_reduce(affiliates_value_rule(null), function ($arr, $item) {
        $arr[$item['value']] = $item['label'];
        return $arr;
    }, []);
    return make_select_options(['' => 'Selecione...'] + $options, $selected);
}

function articles_collections_status($key = null)
{
    $values = [
        'PE' => ['value' => 'PE', 'label' => 'Em Análise', 'color' => 'warning'], // organizer
        'WP' => ['value' => 'WP', 'label' => 'Ag. Publicação', 'color' => 'muted'], // organizer
        'RE' => ['value' => 'RE', 'label' => 'Rejeitada', 'color' => 'danger'], // organizer
        'AC' => ['value' => 'AC', 'label' => 'Ativa/Aberta', 'color' => 'success'], // all
        'IP' => ['value' => 'IP', 'label' => 'Em Produção', 'color' => 'primary'], // all
        'PU' => ['value' => 'PU', 'label' => 'Publicada', 'color' => 'dark'], // all
        'FL' => ['value' => 'FL', 'label' => 'Art. Insuficiente', 'color' => 'danger'], // organizer
    ];

    if ($key === null) {
        return $values;
    }

    return current(array_filter($values, fn ($i) => $i['value'] == $key));
}

function articles_collections_translate_status($status)
{
    $status = array_get([0 => 'RE', 1 => 'AC', 'RP' => 'WP', 'WP' => 'IP'], $status, $status);
    return array_get(articles_collections_status(), $status);
}

function select_options_articles_collections_status($selected = null)
{
    $options = array_reduce(articles_collections_status(null), function ($arr, $item) {
        $arr[$item['value']] = $item['label'];
        return $arr;
    }, []);
    return make_select_options(['' => 'Selecione...'] + $options, $selected);
}

/**
 * Retorna um array com os tipos de chave pix
 * @return array
 */
function tipos_chave_pix($key = null)
{
    $values = array(
        ''   => 'Selecione...',
        'email' => 'Email',
        'cpf_cnpj' => 'CPF/CNPJ/ID',
        'telefone' => 'Telefone celular',
        'chave_aleatoria' => 'Chave aleatória'
    );

    return array_get($values, $key, $values);
}

function select_options_tipos_chave_pix($selected = null)
{
    return make_select_options(tipos_chave_pix(), $selected);
}

function select_options_br_banks($selected = null)
{
    $list = objectToArray(json_decode(
        file_get_contents(ROOT_PATH . 'public/json/bancos.json')
    ));

    if (empty($list)) {
        return '';
    }

    $list = array_reduce($list, function ($arr, $item) {
        $key = $item['COMPE'];
        $value = !empty($item['ShortName']) ? $item['ShortName'] : $item['LongName'];
        $arr[$key] = "{$key} - {$value}";
        return $arr;
    }, []);

    return make_select_options($list, $selected);
}

function select_options_author_role($selected = null)
{
    $options = array_reduce(author_role(), function ($arr, $item) {
        $arr[$item['value']] = $item['label'];
        return $arr;
    }, []);
    return make_select_options(['' => 'Selecione...'] + $options, $selected);
}

function select_options_article_status($selected = null, $group = 1, $filterFn = null)
{
    $options = article_status(null, $group);

    if (is_callable($filterFn)) {
        $options = array_filter($options, $filterFn, ARRAY_FILTER_USE_BOTH);
    }

    $options = array_reduce($options, function ($arr, $item) {
        $arr[$item['value']] = $item['label'];
        return $arr;
    }, []);

    return make_select_options(['' => 'Selecione...'] + $options, $selected);
}

function select_options_article_payment_status($selected = null)
{
    $options = array_reduce(article_payment_status(), function ($arr, $item) {
        $arr[$item['value']] = $item['label'];
        return $arr;
    }, []);
    return make_select_options(['' => 'Selecione...'] + $options, $selected);
}

function select_options_coletaneas_autores($selected = null, $is_for_authors = true)
{
    $options = articles_collections()
        ->left_join(TB_AUTHORS, TB_AUTHORS . '.id', '=', TB_ARTICLES_COLLECTIONS . '.author_id')
        ->order_by('name');

    if ($is_for_authors) {
        $options
            ->where(TB_ARTICLES_COLLECTIONS . '.status', 'AC')
            ->where(TB_ARTICLES_COLLECTIONS . '.id', '>', 1)
            ->where_not_null(TB_ARTICLES_COLLECTIONS . '.area_id')
            ->where_not_null(TB_ARTICLES_COLLECTIONS . '.subarea_id')
            ->where_not_null(TB_ARTICLES_COLLECTIONS . '.specialty_id');
    }

    $options = $options->get([
        TB_ARTICLES_COLLECTIONS . '.id',
        TB_ARTICLES_COLLECTIONS . '.area_id',
        TB_ARTICLES_COLLECTIONS . '.subarea_id',
        TB_ARTICLES_COLLECTIONS . '.specialty_id',
        TB_ARTICLES_COLLECTIONS . '.author_id',
        TB_ARTICLES_COLLECTIONS . '.name',
        TB_ARTICLES_COLLECTIONS . '.volume',
        TB_ARTICLES_COLLECTIONS . '.description',
        TB_AUTHORS . '.name as author_name',
    ]);

    if (empty($options)) {
        return null;
    }

    $options = array_map(function ($item) use ($selected) {
        if ($item->volume > 1) {
            $item->name .= ' - Vol ' . $item->volume;
        }

        $selected = $selected === $item->id ? ' selected' : '';
        $content = '<div style=\'font-size: 12px; overflow: hidden; white-space: normal;\'>' . $item->name . '</div><div style=\'font-size: 10px;\' class=\'text-muted\'>Organizador: ' . ($item->author_name ?? 'Dialética') . '</div>';
        return '<option value="' . $item->id . '"' . $selected . ' data-content="' . $content . '" data-item=\'' . json_encode($item) . '\'>' . $item->name . '</option>';
    }, $options);

    $options = ['<option value="">Selecione...</option>', ...$options];

    return implode('', $options);
}

function select_options_areas_conhecimentos($selected = null)
{
    $options = articles_areas()->where_status(1)->order_by('name')->lists('name', 'id');
    return make_select_options(['' => 'Selecione...'] + $options, $selected);
}

function select_options_subareas_conhecimentos($selected = null)
{
    $options = articles_subareas()->where_status(1)->order_by('name')->lists('name', 'id');
    return make_select_options(['' => 'Selecione...'] + $options, $selected);
}

function select_options_especialidades($selected = null)
{
    $options = articles_specialties()->where_status(1)->order_by('name')->lists('name', 'id');
    return make_select_options(['' => 'Selecione...'] + $options, $selected);
}

function linkable_options_subareas_conhecimentos()
{
    $options = articles_subareas()
        ->join(TB_ARTICLES_AREAS, TB_ARTICLES_AREAS . '.id', '=', TB_ARTICLES_SUBAREAS . '.area_id')
        ->where(TB_ARTICLES_SUBAREAS . '.status', 1)
        ->where(TB_ARTICLES_AREAS . '.status', 1)
        ->order_by(TB_ARTICLES_AREAS . '.name')
        ->order_by(TB_ARTICLES_SUBAREAS . '.name')
        ->get([
            TB_ARTICLES_SUBAREAS . '.id as value',
            DB::raw('trim(' . TB_ARTICLES_SUBAREAS . '.name) as label'),
            TB_ARTICLES_AREAS . '.id as area_group_value',
            DB::raw('trim(' . TB_ARTICLES_AREAS . '.name) as area_group_label')
        ]);

    return array_values(array_reduce($options, function ($arr, $item) {
        $key = $item->area_group_value;

        if (!isset($arr[$key])) {
            $arr[$key] = [
                'area_group_value' => $item->area_group_value,
                'area_group_label' => $item->area_group_label,
                'items' => []
            ];
        }

        $arr[$key]['items'][] = (array)$item;
        return $arr;
    }, []));
}

function linkable_options_articles_specialties()
{
    $options =  articles_specialties()
        ->join(TB_ARTICLES_AREAS, TB_ARTICLES_AREAS . '.id', '=', TB_ARTICLES_SPECIALTIES . '.area_id')
        ->join(TB_ARTICLES_SUBAREAS, TB_ARTICLES_SUBAREAS . '.id', '=', TB_ARTICLES_SPECIALTIES . '.subarea_id')
        ->where(TB_ARTICLES_SPECIALTIES . '.status', 1)
        ->where(TB_ARTICLES_AREAS . '.status', 1)
        ->where(TB_ARTICLES_SUBAREAS . '.status', 1)
        ->order_by(TB_ARTICLES_AREAS . '.name')
        ->order_by(TB_ARTICLES_SUBAREAS . '.name')
        ->order_by(TB_ARTICLES_SPECIALTIES . '.name')
        ->get([
            TB_ARTICLES_SPECIALTIES . '.id as value',
            DB::raw('trim(' . TB_ARTICLES_SPECIALTIES . '.name) as label'),
            TB_ARTICLES_AREAS . '.id as area_group_value',
            DB::raw('trim(' . TB_ARTICLES_AREAS . '.name) as area_group_label'),
            TB_ARTICLES_SUBAREAS . '.id as subarea_group_value',
            DB::raw('trim(' . TB_ARTICLES_SUBAREAS . '.name) as subarea_group_label')
        ]);

    return array_values(array_reduce($options, function ($arr, $item) {
        $key = $item->area_group_value . '-' . $item->subarea_group_value;

        if (!isset($arr[$key])) {
            $arr[$key] = [
                'area_group_value' => $item->area_group_value,
                'area_group_label' => $item->area_group_label,
                'subarea_group_value' => $item->subarea_group_value,
                'subarea_group_label' => $item->subarea_group_label,
                'items' => []
            ];
        }

        $arr[$key]['items'][] = (array)$item;
        return $arr;
    }, []));
}

function add_job(string $job, array $data, int $user_id = null, $schedule_date = null)
{
    if (!$user_id && isset($_SESSION)) {
        $user = (array) preg_match('/\/sistema/i', urlCurrent()) ? array_get($_SESSION, 'user') : array_get($_SESSION, 'author');
        $user_id = array_get($user, 'id');
    }

    $data = json_encode($data);

    return jobs()->insert_get_id(compact('user_id', 'job', 'data', 'schedule_date'));
}

function collection_attempt_days_limit($id, $articles_limit)
{
    return true;

    $collection = articles_collections()->find($id);
    if ($collection) {
        $articles_count = articles()
            ->where_collection_id($collection->id)
            ->where_null('deleted_at')
            ->where('status', '>', 32)
            ->count();

        $status = $articles_count > $articles_limit ? 'WP' : 'FL';

        articles_collections()->update(compact('status'), $id);

        if ($status === 'FL') {
            $articles = articles()
                ->where_collection_id($collection->id)
                ->get(['id', 'title']);

            articles()
                ->where_collection_id($collection->id)
                ->update(['collection_id' => 1]);

            if (!empty($articles)) {
                $articles_list = implode('<br />', array_map(
                    fn ($article) => '- <a href="' . url('sistema/artigos/' . $article->id) . '">' . $article->title . '</a>',
                    $articles
                ));

                add_job('sendMail', [
                    'to' => env('DEV_MODE') ? 'marcioantunes.ma@gmail.com' : 'lucas.martins@editoradialetica.com',
                    'subject' => 'Artigos desvinculados da coletânea',
                    'message' => '
                        <p>Olá, tudo bom?</p>                
                        <p>A coletânea <a href="' . url('sistema/coletaneas/' . $collection->id . '/editar') . '">' . $collection->name . '</a> 
                        não atingiu o limite de artigos suficiente para viabilizar a publicação!</p>
                        <p>Por esta razão, os artigos abaixo foram desvinculados da coletânea e atribuídos como "Fluxo Contínuo"</p>
                        <p>' . $articles_list . '</p>
                    '
                ]);
            }
        }

        $collection->status = $status;

        collection_on_change_status($collection);
    }
}

function collection_on_change_status($collection)
{
    if (is_array($collection)) {
        $collection = (object)$collection;
    }

    $collection = articles_collections()->where_id($collection->id)->first();
    $created_by = DB::table(TB_USERS)->find($collection->created_by);

    // Não notificar autores quando a coletânea é de admin
    if ($collection->id === 1 || ($created_by && $created_by->type == 'user')) {
        return true;
    }

    // $view = array_get([
    //     'NA' => 'mail.collection-organizer-new-article',
    //     'AP' => 'mail.collection-organizer-status-submitted',
    //     'AC' => 'mail.collection-organizer-status-approved',
    //     'RE' => 'mail.collection-organizer-status-rejected',
    //     'IP' => 'mail.collection-organizer-status-production',
    //     // 'FL' => 'mail.collection-organizer-status-failed',
    //     // 'PU' => 'mail.collection-organizer-status-published',
    // ], $collection->status);

    $view = array_get([
        'NA' => 'd-1628a4e4c0b344ff9a379e6627878b50',
        'PE' => 'd-b7bb155d8d6f415380558ff22b880d68',
        'AP' => 'd-7082253d1e5b42cc99e8626c24a02051',
        'AC' => 'd-7082253d1e5b42cc99e8626c24a02051',
        'RE' => 'd-ef50cdb29cdb4a9490c9f94707690ae8',
        'IP' => 'd-47ff0e722740468f878f5f21a9ee5426',
        'FL' => 'd-e32f61376798464bbc1a1e77055b373d'
        // 'PU' => 'mail.collection-organizer-status-published',
    ], $collection->status);

    $settings = settings()->lists('value', 'key');

    $collection_days_limit = array_get($settings, 'collection_days_limit', 30);
    $days_approve_article = array_get($settings, 'days_approve_article', 7);
    $days_approve_collection = array_get($settings, 'days_approve_collection', 7);
    $coupon_affiliate_percent = array_get($settings, 'coupon_affiliate_percent', 10);
    $coupon_discount_percent = array_get($settings, 'coupon_discount_percent', 10);
    $minimum_articles_in_collection = array_get($settings, 'minimum_articles_in_collection', 4);

    // if ($collection->status === 'AC') {
    //     $job_data = ['id' => $collection->id, 'articles_limit' => $minimum_articles_in_collection];
    //     $job_schedule_date = date('Y-m-d 06:00:00', strtotime('+' . $collection_days_limit . ' days'));
    //     add_job('collection_attempt_days_limit', $job_data, null, $job_schedule_date);
    // }

    if ($collection->status === 'IP ---- IVALID' && $collection->author_id) {
        $volume = articles_collections()->where_name($collection->name)->count() + 1;

        $new_collection_data = array_only((array)$collection, [
            'created_by',
            'area_id',
            'subarea_id',
            'specialty_id',
            'author_id',
            'name',
            'description',
            'isbn',
            'isbn_e_book',
            'doi',
            'book_url',
            'accept_publication_rules',
            'cover_image',
            'approved_at',
            'approved_by',
        ]);

        $new_collection_data['volume'] = $volume;
        $new_collection_data['status'] = 'AC';
        $new_collection_data['token'] = get_collection_unique_token();

        $collection_id = articles_collections()->insert_get_id($new_collection_data);

        // $collection_id = articles_collections()
        //     ->insert_get_id([
        //         'name' => $collection->name,
        //         'description' => $collection->description,
        //         'created_by' => $collection->created_by,
        //         'area_id' => $collection->area_id,
        //         'subarea_id' => $collection->subarea_id,
        //         'specialty_id' => $collection->specialty_id,
        //         'author_id' => $collection->author_id,
        //         'accept_publication_rules' => $collection->accept_publication_rules,
        //         'cover_image' => $collection->cover_image,
        //         'approved_at' => $collection->approved_at,
        //         'approved_by' => $collection->approved_by,
        //         'expires_at' => date('Y-m-d H:i:s', strtotime('+' . (array_get($settings, 'collection_days_limit', 30)) . ' days')),
        //         'volume' => $volume,
        //         'status' => 'AC',
        //         'token' => get_collection_unique_token()
        //     ]);

        $author_ids = array_values(
            articles_collections_authors()
                ->where_collection_id($collection->id)
                ->where_null('deleted_at')
                ->lists('author_id', 'author_id')
        );

        sync_collection_authors($collection_id, $author_ids);

        collection_on_change_status(articles_collections()->find($collection_id));
    }

    if (empty($view)) {
        return;
    }

    $authors = articles_collections_authors()
        ->join(TB_AUTHORS, TB_AUTHORS . '.id', '=', TB_ARTICLES_COLLECTIONS_AUTHORS . '.author_id')
        ->left_join(TB_AFFILIATES_COUPONS, TB_AFFILIATES_COUPONS . '.user_id', '=', TB_ARTICLES_COLLECTIONS_AUTHORS . '.author_id')
        ->where(TB_ARTICLES_COLLECTIONS_AUTHORS . '.collection_id', $collection->id)
        ->where_null(TB_ARTICLES_COLLECTIONS_AUTHORS . '.deleted_at')
        ->where_null(TB_AUTHORS . '.deleted_at')
        ->get([
            TB_AUTHORS . '.*',
            TB_AFFILIATES_COUPONS . '.token as coupon'
        ]);

    if (empty($authors)) {
        return;
    }

    foreach ($authors as $author) {
        // $message = view($view, [
        //     'name' => $author->name,
        //     'title' => $collection->name,
        //     'coupon' => $author->coupon,
        //     'days_approve_article' => $days_approve_article,
        //     'days_approve_collection' => $days_approve_collection,
        //     'minimum_articles_in_collection' => $minimum_articles_in_collection,
        //     'coupon_affiliate_percent' => toMoney($coupon_affiliate_percent),
        //     'coupon_discount_percent' => toMoney($coupon_discount_percent),
        // ]);

        $message = [
            'id' => $view,
            'vars' => [
                'first_name' => get_first_name($author->name),
                'titulo_coletanea' => $collection->name,
                'titulo_trabalho' => isset($collection->article_tile) ? $collection->article_tile : '',
                'link_coletanea' => url("/coletanea/{$collection->token}"),
                'cupom_autor' => $author->coupon,
            ]
        ];

        add_job('sendMail', [
            'to' => env('DEV_MODE') ? 'marcioantunes.ma@gmail.com' : $author->email,
            'subject' => env('APP_NAME') . ' - ' . array_get([
                'NA' => 'Novo artigo submetido',
                'AP' => 'A sua proposta de coletânea foi submetida com sucesso!',
                'AC' => 'A sua proposta de coletânea foi aprovada!',
                'RE' => 'A sua proposta de coletânea previsa de uma revisão!',
                'IP' => 'A editoração de sua coletânea se iniciou!',
                'FL' => 'Faltaram artigos para a sua coletânea!',
                // 'PU' => 'A sua coletânea foi publicada!',
            ], $collection->status),
            'message' => $message
        ]);
    }
}

function sendMail($to, $subject, $message)
{
    $subject = (env('DEV_MODE') ? 'DEV - ' : '') . $subject;

    if (preg_match('/sendgrid/', config('smtp.host'))) {
        return sendMailSendGrid($to, $subject, $message);
    }

    require_once(ROOT_PATH . "vendor/phpmailer.php");

    $mail = new PHPMailer(true);

    $mail->CharSet = PHPMailer::CHARSET_UTF8;

    $mail->IsSMTP();

    $mail->Host       = config('smtp.host');
    $mail->Port       = config('smtp.port', 465);
    $mail->SMTPAuth   = config('smtp.auth', true);
    if (config('smtp.secure')) {
        $mail->SMTPSecure = config('smtp.secure', 'ssl');
    }
    $mail->Username   = config('smtp.sender_user');
    $mail->Password   = config('smtp.sender_pass');

    $mail->SetFrom(
        config('smtp.sender_email', ''),
        config('smtp.sender_name', '')
    );

    // if ($to !== 'marcioantunes.ma@gmail.com') {
    // $mail->AddBCC('marcioantunes.ma@gmail.com', 'Marcio Antunes');
    $mail->AddBCC('rafaelkscharf@gmail.com', 'Rafael Klein Scharf');
    $mail->AddBCC('vitor.medrado@editoradialetica.com', 'Vitor Medrado');
    //}

    $mail->AddAddress($to);
    $mail->Subject = $subject;
    $mail->MsgHTML($message);

    try {
        $mail->Send();
    } catch (\Exception $e) {
        throw new \Exception($e->getMessage());
    }
}

function sendMailSendGrid($to, $subject, $message)
{
    require_once(ROOT_PATH . '/vendor/curl.php');

    $body = [
        "personalizations" => [
            [
                "to" => [
                    [
                        "email" => $to
                    ]
                ],
            ]
        ],
        "from" => [
            "email" => config('smtp.sender_email', ''),
            "name" => config('smtp.sender_name', '')
        ]
    ];

    if (is_array($message)) {
        $templates = objectToArray(json_decode(file_get_contents(ROOT_PATH . 'app/mail/sg-templates-map.json')));

        $body['template_id'] = $message['id'];
        $body['personalizations'][0]['dynamic_template_data'] = $message['vars'];
        $template = array_first($templates, fn ($k, $v) => $v['id'] === $message['id'], []);

        if ($from = array_get($template, 'from')) {
            $body['personalizations'][0]['from'] = $from;
        }
    } else {
        $body['subject'] = $subject;
        $body['content'] = [
            [
                "type" => "text/html",
                "value" => $message
            ]
        ];
    }

    $bcc_emails = array_filter([
        'marcioantunes.ma@gmail.com',
        'vitor.medrado@editoradialetica.com'
    ], fn ($bcc) => $to !== $bcc);

    $bcc = array_values(array_map(fn ($email) => compact('email'), $bcc_emails));

    if (!empty($bcc)) {
        array_set($body, 'personalizations.0.bcc', $bcc);
    }

    try {
        file_put_contents(ROOT_PATH . 'storage/logs/mail/send-' . date('Ymd') . '.log', json_encode($body));

        Curl::to('https://api.sendgrid.com/v3/mail/send')
            ->withHeader('Authorization: Bearer ' . config('smtp.sender_pass'))
            ->enableDebug(ROOT_PATH . 'storage/logs/mail/' . date('Ymd') . '.log')
            ->withData($body)
            ->asJson()
            ->post();
    } catch (\Exception $e) {
        throw new \Exception($e->getMessage());
    }
}

function get_words_count($file)
{
    if (!file_exists($file)) {
        throw new \InvalidArgumentException('O arquivo ' . $file . ' não existe!');
    }

    $ext = File::extension($file);

    if (!in_array($ext, ['doc', 'docx'])) {
        throw new \InvalidArgumentException('Apenas arquivos DOC e DOCX são permitidos');
    }

    // First convert to PDF
    $bin = file_exists('/usr/local/bin/libreoffice') ? '/usr/local/bin/libreoffice' : '/usr/bin/libreoffice';


    // $cmd = 'export HOME=/tmp && ' . $bin . ' --nologo --norestore --invisible --nolockcheck --nodefault --headless --convert-to pdf ' . $file . ' --outdir ' . dirname($file);
    // exec($cmd);
    // 
    // // Run words count 
    // $file = str_replace('.' . $ext, '.pdf', $file);
    // $cmd = 'ps2ascii ' . $file . ' | wc -w';
    // exec($cmd, $words);
    // 
    // $string = 0;
    // $words = array_get($words, 0, 0);
    // return compact('string', 'words');

    $dir = dirname($file);
    $tgt = str_replace('.' . $ext, '.pdf', $file);
    // $cmd = "export HOME=/tmp && {$bin} --writer --nologo --norestore --invisible --nolockcheck --nodefault --headless --convert-to pdf {$file} --outdir {$dir}";
    // exec($cmd);

    // sleep(1);

    if (!file_exists($tgt)) {
        try {
            $result = ConvertApi::convert('pdf', ['File' => $file]);
            $result->getFile()->save($tgt);
        } catch (\Exception $e) {
            throw new \InvalidArgumentException("O arquivo de conversão não foi localizado -> " . $e->getMessage());
        }

        // throw new \InvalidArgumentException("O arquivo de conversão não foi localizado");
    }

    exec("ps2ascii {$tgt} | wc -w", $words);
    if (!array_get($words, 0)) {
        throw new \InvalidArgumentException('Falha na conversão do arquivo');
    }

    $string = 0;
    $words = array_get($words, 0, 0);
    return compact('string', 'words');
}

function reviewWordsCount($id, $logged_author_id, $full_path)
{
    try {
        $words_count_result = get_words_count($full_path);

        $words_count = $words_count_result['words'] ?? 0;

        DB::$connections = [];

        $type = articles_types()->find(3);

        $amount = max($type->price * $words_count, $type->minimum_price);

        reviews()
            ->where_author_id($logged_author_id)
            ->update([
                'words_count' => $words_count,
                'gross_amount' => $amount,
                'discount_amount' => 0,
                'amount' => $amount,
                'status' => 30
            ], $id);
    } catch (\Exception $e) {
        throw new \Exception($e->getMessage());
    }
}

function response_json_fail($message, $data = [], $status = 500)
{
    if (is_integer($data)) {
        $status = $data;
        $data = [];
    }

    return response_json(array_merge([
        'success' => false,
        'message' => $message,
    ], (array) $data), $status);
}

function response_json_create_fail($data = [], $status = 500)
{
    return response_json_fail('Ocorreu um erro ao criar o registro', $data, $status);
}

function response_json_update_fail($data = [], $status = 500)
{
    return response_json_fail('Ocorreu um erro ao atualizar o registro', $data, $status);
}

function response_json_delete_fail($data = [], $status = 500)
{
    return response_json_fail('Ocorreu um erro ao remover o registro', $data, $status);
}

function response_json_success($message, $data = [], $status = 200)
{
    if (is_integer($data)) {
        $status = $data;
        $data = [];
    }

    return response_json(array_merge([
        'success' => true,
        'message' => $message,
    ], (array) $data), $status);
}

function response_json_create_success($data = [], $status = 200)
{
    return response_json_success('Registro cadastrado com sucesso', $data, $status);
}

function response_json_update_success($data = [], $status = 200)
{
    return response_json_success('Registro atualizado com sucesso', $data, $status);
}

function response_json_delete_success($data = [], $status = 200)
{
    return response_json_success('Registro removido com sucesso', $data, $status);
}
class NotFoundJsonResponseException extends \Exception implements Responsable
{
    public $response;

    public function __construct($response)
    {
        $this->response = $response;
    }

    public function getResponse()
    {
        return $this->response;
    }
}

function validate_exists($model, $id)
{
    if (!$model->where_null(wrap_table($model, 'deleted_at'))->find($id)) {
        throw new NotFoundJsonResponseException(
            response_json([
                'message' => 'Não existe um registo com o ID informado!',
            ], 404)
        );
    }
}

function find_or_fail($model, $id, $columns = ['*'])
{
    $item = $model->where_null(wrap_table($model, 'deleted_at'))->find($id, $columns);

    if (!$item) {
        throw new NotFoundJsonResponseException(
            response_json([
                'message' => 'Nenhum registro encontrado!',
            ], 404)
        );
    }

    return $item;
}

function wrap_table($model, $column)
{
    if ($model->from) {
        return $model->from . '.' . $column;
    }

    return $column;
}

function first_or_fail($model, $columns = ['*'])
{
    $item = $model->where_null(wrap_table($model, 'deleted_at'))->first($columns);

    if (!$item) {
        throw new NotFoundJsonResponseException(
            response_json([
                'message' => 'Nenhum registro encontrado!',
            ], 404)
        );
    }

    return $item;
}

function make_datatable_options($datagrid)
{
    array_set($datagrid, 'options.columns', array_map(function ($col) {
        $col['data'] = $col['key'];
        $col['className'] = array_get($col, 'attrs.class', '');
        $col['orderable'] = array_get($col, 'orderable', array_get($col, 'sortable', true));
        $col['searchable'] = array_get($col, 'filterable', true);
        return $col;
    }, array_get($datagrid, 'columns', [])));

    return $datagrid;
}

function get_first_name($name)
{
    $parts = explode(' ', trim($name));

    return array_get($parts, 0, array_get($parts, 1));
}

function installmentsArray(float $amount, int $months = 4): array
{
    $installments = [];
    for ($i = $months; $i >= 1; $i--) {
        $installments[$i] = $amount / $i;
    }

    return $installments;
}
