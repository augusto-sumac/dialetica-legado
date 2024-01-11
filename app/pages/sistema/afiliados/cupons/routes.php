<?php

function affiliates_coupons_validation_rules($id = null)
{
    $rules = [
        'type' => 'required|in:ARTICLES,REVIEWS,ALL',
        // 'token' => 'required|max:40|unique:' . TB_AFFILIATES_COUPONS . ($id ? ',id,' . $id : ''),
        'token' => 'required|match:/^[a-zA-Z\d\-\_\$\#]+$/|max:40|unique:' . TB_AFFILIATES_COUPONS . ($id ? ',id,' . $id : ''),
        'discount_rule' => 'required|in:settings,percent,fixed',
        'discount_value' => 'numeric',
        'affiliate_rule' => 'required|in:settings,percent,fixed',
        'affiliate_value' => 'numeric',

        'start_at_date' => 'date_format:d/m/Y',
        'start_at_time' => [
            'match:#^([01][0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$#',
        ],

        'expires_at_date' => 'date_format:d/m/Y',
        'expires_at_time' => [
            'match:#^([01][0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$#',
        ],

        'max_uses' => 'numeric',
        'max_uses_user' => 'numeric',

        'user_id' => 'exists:' . TB_AUTHORS . ',id',
    ];

    if (input('discount_rule') !== 'settings') {
        $rules['discount_value'] = 'required';
    }

    if (input('affiliate_rule') !== 'settings') {
        $rules['affiliate_value'] = 'required';
    }

    return $rules;
}

function get_affiliates_coupons_data(array $rules)
{
    $except = [
        'expires_at_date',
        'expires_at_time',
        'start_at_date',
        'start_at_time',
    ];

    $keys = array_keys($rules);

    $data = array_only(input(), $keys);

    $data['discount_value'] = toNumber($data['discount_value']);
    $data['affiliate_value'] = toNumber($data['affiliate_value']);
    $data['start_at'] = datetimeToMySql($data['start_at_date'] . ' ' . $data['start_at_time']);
    $data['expires_at'] = datetimeToMySql($data['expires_at_date'] . ' ' . $data['expires_at_time']);

    return array_map(fn ($value) => empty(trim($value)) ? null : trim($value), array_except($data, $except));
}

Route::group([
    'prefix' => AFFILIATES_COUPONS_BASE_URL,
    'before' => 'auth'
], function () {
    Route::get('', function () {
        $datagrid = make_datatable_options([
            'data' => [],
            'use_datatable' => true,
            'slot_form' => AFFILIATES_COUPONS_VIEW_PATH . '.components.datagrid-filter',
            'options' => [
                'order' => [[1, 'asc']],
                'processing' =>  true,
                'serverSide' =>  true,
                'deferRender' => true,
                'ajax' => [
                    'url' => url(AFFILIATES_COUPONS_BASE_URL),
                    'type' => 'POST'
                ],
                'pageLength' => 50,
            ],
            'columns' => [
                [
                    'key' => 'id_link',
                    'label' => '#',
                    'attrs' => ['class' => 'w-50px p-0 text-center']
                ],
                [
                    'key' => 'type_str',
                    'label' => 'Aplicação',
                    'attrs' => ['class' => 'w-120px text-ellipsis']
                ],
                [
                    'key' => 'author_link',
                    'label' => 'Afiliado',
                    'attrs' => ['class' => 'w-150px text-ellipsis']
                ],
                [
                    'key' => 'token',
                    'label' => 'Cod/Cupom',
                    'attrs' => ['class' => 'w-150px'],
                ],
                [
                    'key' => 'discount_rule_str',
                    'label' => 'TP Desc.',
                    'attrs' => ['class' => 'w-110px'],
                ],
                [
                    'key' => 'discount_value',
                    'label' => 'Vl Desc.',
                    'attrs' => ['class' => 'w-120px text-end'],
                ],
                [
                    'key' => 'affiliate_rule_str',
                    'label' => 'TP Com.',
                    'attrs' => ['class' => 'w-110px'],
                ],
                [
                    'key' => 'affiliate_value',
                    'label' => 'Vl Com.',
                    'attrs' => ['class' => 'w-120px text-end'],
                ],
                [
                    'key' => 'max_uses_str',
                    'label' => 'Lim. Uso',
                    'attrs' => ['class' => 'w-120px text-end'],
                ],
                [
                    'key' => 'created_at_str',
                    'label' => 'Dt Cad.',
                    'attrs' => ['class' => 'w-150px']
                ],
                [
                    'key' => 'start_at_str',
                    'label' => 'Dt Val. Ini.',
                    'attrs' => ['class' => 'w-150px']
                ],
                [
                    'key' => 'expires_at_str',
                    'label' => 'Dt Val. Fim',
                    'attrs' => ['class' => 'w-150px']
                ]
            ]
        ]);

        return view(AFFILIATES_COUPONS_VIEW_PATH . '.index', compact('datagrid'));
    });

    Route::post('', function () {
        $select_count_uses = affiliates_coupons_entries()
            ->where_null(TB_AFFILIATES_COUPONS_ENTRIES . '.deleted_at')
            ->where(TB_AFFILIATES_COUPONS_ENTRIES . '.affiliate_coupon_id', DB::raw(TB_AFFILIATES_COUPONS . '.id'))
            ->where_type('C')
            ->toSql([
                DB::raw('count(' . TB_AFFILIATES_COUPONS_ENTRIES . '.id)')
            ]);

        $rows = affiliates_coupons()
            ->left_join(TB_AUTHORS, TB_AUTHORS . '.id', '=', TB_AFFILIATES_COUPONS . '.user_id')
            ->where_null(TB_AFFILIATES_COUPONS . '.deleted_at')
            ->select([
                TB_AFFILIATES_COUPONS . '.*',
                TB_AUTHORS . '.name as author_name',
                TB_AUTHORS . '.document',
                DB::raw('(' . $select_count_uses . ') as uses')
            ]);

        $total = clone $rows;

        // Sort
        $cols = input('columns', []);
        foreach (input('order', []) as $order) {
            $col = array_get($cols, $order['column'] . '.data');
            $col = array_get([
                'id_link' => TB_AFFILIATES_COUPONS . '.id',
                'author_link' => TB_AUTHORS . '.name',
                'type_str' => TB_AFFILIATES_COUPONS . '.type',
                'created_at_str' => 'created_at',
                'start_at_str' => 'start_at',
                'expires_at_str' => 'expires_at',
                'discount_rule_str' => 'discount_rule',
                'affiliate_rule_str' => 'affiliate_rule',
                'max_uses_str' => 'max_uses',
            ], $col, TB_AFFILIATES_COUPONS . '.' . $col);

            if ($col) {
                $rows->order_by(DB::raw($col . ' ' . strtolower($order['dir'])));
            }
        }

        // Search
        if ($search = input('search.value', input('search.search'))) {
            $rows->where(function ($q) use ($search) {
                if (is_numeric($search)) {
                    $q->or_where(TB_AFFILIATES_COUPONS . '.id', (int) $search);
                } else {
                    $q->or_like(TB_AFFILIATES_COUPONS . '.token', $search);
                    $q->or_like(TB_AUTHORS . '.name', $search);
                }
            });
        }

        // Filters
        if ($value = input('filters.discount_rule')) {
            $rows->where_in(TB_AFFILIATES_COUPONS . '.discount_rule', $value);
        }

        if ($value = input('filters.affiliate_rule')) {
            $rows->where_in(TB_AFFILIATES_COUPONS . '.affiliate_rule', $value);
        }

        if ($value = input('filters.type')) {
            $rows->where_in(TB_AFFILIATES_COUPONS . '.type', $value);
        }

        foreach (['created_at', 'start_at', 'expires_at'] as $date_key) {
            $date_value_start = trim(datetimeToMySql(input('filters.' . $date_key . '.start')));
            $date_value_end = trim(datetimeToMySql(input('filters.' . $date_key . '.end')));
            $date_column = TB_AFFILIATES_COUPONS . '.' . $date_key;

            $date_value_start = !empty($date_value_start) ? $date_value_start . ' 00:00' : null;
            $date_value_end = !empty($date_value_end) ? $date_value_end . ' 23:59' : null;

            if ($date_value_start && $date_value_end) {
                $rows->where_between($date_column, $date_value_start, $date_value_end);
            } elseif ($date_value_start) {
                $rows->where($date_column, '>=', $date_value_start);
            } elseif ($date_value_end) {
                $rows->where($date_column, '<=', $date_value_end);
            }
        }

        // Data
        $rows = $rows->paginate(input('per_page', 15));

        return response_json([
            'draw' => input('draw', 1),
            'recordsTotal' => $total->count(),
            'recordsFiltered' => $rows->total,
            'data' => array_map(function ($row) {
                $row->max_uses_str = $row->max_uses ? $row->max_uses : 'Ilimitado';
                if ($row->uses) {
                    $row->max_uses_str .= ' <span class="text-info" title="Utilizado" data-bs-toggle="tooltip">(' . $row->uses . ')</span>';
                }

                $row->start_at = substr(datetimeFromMySql($row->start_at), 0, -3);
                $row->expires_at = substr(datetimeFromMySql($row->expires_at), 0, -3);
                $row->created_at = substr(datetimeFromMySql($row->created_at), 0, -3);

                $row->start_at_str = !empty($row->start_at) ? $row->start_at : 'Indefinido';
                $row->expires_at_str = !empty($row->expires_at) ? $row->expires_at : 'Indefinido';
                $row->created_at_str = !empty($row->created_at) ? $row->created_at : 'Indefinido';

                $rule = affiliates_value_rule($row->discount_rule ?? 'settings');
                $row->discount_rule_str = '<span class="badge ' . $rule['color'] . '">' . $rule['label'] . '</span>';
                $row->discount_value = toMoney(abs($row->discount_value));

                $rule = affiliates_value_rule($row->affiliate_rule ?? 'settings');
                $row->affiliate_rule_str = '<span class="badge ' . $rule['color'] . '">' . $rule['label'] . '</span>';
                $row->affiliate_value = toMoney(abs($row->affiliate_value));

                $type = array_get([
                    'ARTICLES' => ['label' => 'Artigos', 'color' => 'primary'],
                    'REVIEWS' => ['label' => 'Revisões', 'color' => 'info'],
                    'ALL' => ['label' => 'Livre', 'color' => 'muted']
                ], $row->type, ['label' => 'Livre', 'color' => 'muted']);

                $row->type_str = '<span class="badge bg-' . $type['color'] . '">' . $type['label'] . '</span>';

                $row->document = mask($row->document, 'cpf_cnpj');
                // $row->author_link = $row->author_name ? '<a href="' . url('sistema/autores/' . $row->user_id) . '" target="affiliate" title="Detalhe do afiliado">' . $row->author_name . '</a>' : 'Não Vinculado';
                $row->author_link = $row->author_name ? $row->author_name : '<span class="text-muted">Não Vinculado</span>';
                $row->id_link = '<a href="javascript:void(0)" onclick=\'$appCoupon.edit(' . json_encode($row) . ')\'>' . str_pad_id($row->id) . '</a>';
                return $row;
            }, $rows->results),
            'sql' => DB::profile()
        ]);
    });

    Route::post('/create', function () {
        $rules = affiliates_coupons_validation_rules();

        validate($rules, input(), [
            'expires_at_time_match' => 'Hora Inválida'
        ]);

        $item = get_affiliates_coupons_data($rules);

        try {
            $item['id'] = affiliates_coupons()->insert_get_id($item);

            return response_json_create_success([
                'item' => (object) $item
            ]);
        } catch (\Exception $e) {
            return response_json_create_fail(['message' => $e->getMessage()]);
        }
    });

    Route::post('/{id:\d+}/edit', function ($id) {
        find_or_fail(affiliates_coupons(), $id);

        $rules = affiliates_coupons_validation_rules($id);

        validate($rules, input(), [
            'expires_at_time_match' => 'Hora Inválida'
        ]);

        $item = get_affiliates_coupons_data($rules);

        try {
            $item['id'] = affiliates_coupons()->update($item, $id);

            return response_json_update_success([
                'item' => (object) $item
            ]);
        } catch (\Exception $e) {
            return response_json_update_fail(['message' => $e->getMessage()]);
        }
    });
});
