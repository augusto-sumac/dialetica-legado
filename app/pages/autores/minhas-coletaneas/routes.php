<?php

function authors_collections_validation_rules($id = null, $include_authors = true)
{
    $rules = [
        'name' => 'required|max:140|unique:' . TB_ARTICLES_COLLECTIONS . ($id ? ',id,' . $id : ''),
        'description' => 'required|max:2000',
        'area_id' => 'required|exists:' . TB_ARTICLES_AREAS . ',id',
        'subarea_id' => 'required|exists:' . TB_ARTICLES_SUBAREAS . ',id',
        'specialty_id' => 'required|exists:' . TB_ARTICLES_SPECIALTIES . ',id',
        'accept_publication_rules.articles_qty' => 'required',
        'accept_publication_rules.responsibility' => 'required'
    ];

    if ($include_authors) {
        foreach (input('author_ids', []) as $key => $author_id) {
            $rules['author_ids.' . $key] = 'required|exists:' . TB_USERS . ',id,' . $author_id;
        }
    }

    return $rules;
}

function get_select_count_articles()
{
    return articles()
        ->where(TB_ARTICLES . '.collection_id', '=', DB::raw(TB_ARTICLES_COLLECTIONS . '.id'))
        ->toSql([
            DB::raw('count(' . TB_ARTICLES . '.id)')
        ]);
}

function get_select_articles_collections_authors()
{
    return articles_collections_authors()
        ->where(TB_ARTICLES_COLLECTIONS_AUTHORS . '.author_id', '=', DB::raw(logged_author()->id))
        ->where(TB_ARTICLES_COLLECTIONS_AUTHORS . '.collection_id', '=', DB::raw(TB_ARTICLES_COLLECTIONS . '.id'))
        ->toSql(TB_ARTICLES_COLLECTIONS_AUTHORS . '.author_id');
}

function get_collection_articles($collection)
{
    $articles = articles()
        ->join(TB_AUTHORS, TB_AUTHORS . '.id', '=', TB_ARTICLES . '.author_id')
        ->where(TB_ARTICLES . '.status', '>=', 10)
        ->where(TB_ARTICLES . '.collection_id', $collection->id)
        ->where_null(TB_ARTICLES . '.deleted_at')
        ->order_by(TB_ARTICLES . '.created_at')
        ->get([
            TB_ARTICLES . '.id',
            TB_ARTICLES . '.collection_id',
            TB_ARTICLES . '.title',
            TB_ARTICLES . '.created_at',
            TB_ARTICLES . '.status',
            DB::raw('(case when ' . TB_ARTICLES . '.status >= 32 then 1 else 0 end) as is_paid'),
            TB_AUTHORS . '.name as author_name'
        ]);

    $articles = array_map(function ($row) {
        $status = article_status($row->status);

        if ($row->status === 11) {
            $icon_bg = 'bg-danger';
            $icon_color = 'text-white';
            $icon_icon = 'fas fa-ban';
            $icon_title = $status['label'];
        } elseif ($row->status === 20) {
            $icon_bg = 'bg-primary';
            $icon_color = 'text-white';
            $icon_icon = 'fas fa-hourglass-half';
            $icon_title = 'Aguardando Aprovação Dialética';
        } elseif ($row->status === 30) {
            $icon_bg = 'bg-info';
            $icon_color = 'text-white';
            $icon_icon = 'fas fa-dollar-sign';
            $icon_title = 'Aguardando Pagamento';
        } elseif ($row->status === 31) {
            $icon_bg = 'bg-danger';
            $icon_color = 'text-white';
            $icon_icon = 'fas fa-dollar-sign';
            $icon_title = 'Falha Pagamento';
        } elseif ($row->status === 32) {
            $icon_bg = 'bg-muted';
            $icon_color = 'text-white';
            $icon_icon = 'fas fa-hourglass-half';
            $icon_title = 'Aguardando Aprovação';
        } else {
            $icon_bg = 'bg-success';
            $icon_color = 'text-white';
            $icon_icon = 'fas fa-check';
            $icon_title = 'Aprovado';
        }

        $row->status_icon = '<span class="articles-list-status-icon ' . $icon_bg . ' ' . $icon_color . '" data-bs-toggle="tooltip" title="' . $icon_title . '">
            <span class="' . $icon_icon . '"></span>
        </span>';

        $row->allow_approve = $row->status === 32;

        return $row;
    }, $articles);

    $paid_articles = count(array_filter($articles, fn ($r) => $r->is_paid && $r->status >= 41));

    $paid_articles_limit = settings()->where_key('minimum_articles_in_collection')->first();
    $paid_articles_limit = $paid_articles_limit ? $paid_articles_limit->value : 4;

    return compact('collection', 'articles', 'paid_articles', 'paid_articles_limit');
}

function approve_or_reject_article($collection_id, $article_id, $status)
{
    $item = find_or_fail(articles()->where_collection_id($collection_id), $article_id);

    try {
        articles()->update(['status' => $status], $item->id);

        $item->status = $status;

        article_on_change_status($item);

        return response_json_update_success([
            'status' => $item->status
        ]);
    } catch (\Exception $e) {
        return response_json_update_fail();
    }
}

Route::group([
    'prefix' => AUTHOR_COLLECTIONS_BASE_URL,
    'before' => 'author-auth'
], function () {
    Route::get('', function () {
        $rows = articles_collections()
            ->join(TB_AUTHORS, function ($join) {
                $join->on(TB_AUTHORS . '.id', '=', TB_ARTICLES_COLLECTIONS . '.author_id')
                    ->on(TB_AUTHORS . '.type', '=', DB::raw("'author'"));
            })
            ->left_join(TB_ARTICLES_AREAS, TB_ARTICLES_AREAS . '.id', '=', TB_ARTICLES_COLLECTIONS . '.area_id')
            ->left_join(TB_ARTICLES_SUBAREAS, TB_ARTICLES_SUBAREAS . '.id', '=', TB_ARTICLES_COLLECTIONS . '.subarea_id')
            ->left_join(TB_ARTICLES_SPECIALTIES, TB_ARTICLES_SPECIALTIES . '.id', '=', TB_ARTICLES_COLLECTIONS . '.specialty_id')
            ->raw_where('exists(' . get_select_articles_collections_authors() . ')')
            ->get([
                TB_ARTICLES_COLLECTIONS . '.*',
                DB::raw("coalesce(" . TB_ARTICLES_AREAS . ".name, 'Não Informada') as nome_area"),
                DB::raw("coalesce(" . TB_ARTICLES_SUBAREAS . ".name, 'Não Informada') as nome_sub_area"),
                DB::raw("coalesce(" . TB_ARTICLES_SPECIALTIES . ".name, 'Não Informada') as nome_especialidade"),
                DB::raw('(' . get_select_count_articles() . ') as articles')
            ]);

        $rows = array_map(function ($row) {
            $url = url(AUTHOR_COLLECTIONS_BASE_URL . '/' . $row->id . '/detail');
            if ($row->volume > 1) {
                $row->name .= ' - VOL ' . $row->volume;
            }

            $row->url = '<a href="' . $url . '" title="Editar Cadastro" class="h2">' . $row->name . '</a>';

            $row->status_badge = articles_collections_translate_status($row->status);

            return $row;
        }, $rows);

        return view(AUTHOR_COLLECTIONS_VIEW_PATH . '.index', compact('rows'));
    });

    Route::get('/adicionar', function () {
        $keys = [
            'coupon_discount_percent' => fn ($v) => toMoney($v),
            'coupon_affiliate_percent' => fn ($v) => toMoney($v),
            'minimum_withdrawal_amount' => fn ($v) => toMoney($v),
            'minimum_articles_in_collection' => fn ($v) => (int) $v
        ];

        $settings = (array) settings()
            ->where_in('key', array_keys($keys))
            ->lists('value', 'key');

        foreach ($keys as $key => $parser) {
            $settings[$key] = $parser(array_get($settings, $key, 0));
        }

        return view(AUTHOR_COLLECTIONS_VIEW_PATH . '.form', [
            'id' => null,
            'app_store_id' => makeCollectionAppStoreId(),
            'authors' => []
        ] + $settings);
    });

    Route::post('/adicionar', function () {
        $rules = [
            'author_role' => 'required',
            'author_curriculum_url' => 'required|max:255',
            'author_curriculum' => 'required|max:4000'
        ];

        validate($rules + authors_collections_validation_rules());

        $input_keys = array_keys(authors_collections_validation_rules(null, false));

        $item = array_only(input(), $input_keys);

        $item['status'] = 'PE';
        $item['author_id']  = logged_author()->id;
        $item['created_by'] = logged_author()->id;
        $item['token'] = get_collection_unique_token();
        $item['accept_publication_rules'] = json_encode(input('accept_publication_rules'), JSON_OBJECT_AS_ARRAY);

        $settings = settings()->where_key('collection_days_limit')->lists('value', 'key');
        $days_limit = array_get($settings, 'collection_days_limit', 30);
        $item['expires_at'] = date('Y-m-d', strtotime("+{$days_limit} days"));

        try {
            $item['id'] = articles_collections()->insert_get_id($item);
        } catch (\Exception $e) {
            return response_json_create_fail();
        }

        collection_on_change_status($item);

        try {
            $author_ids = input('author_ids', []);
            $author_ids[] = logged_author()->id;
            sync_collection_authors($item['id'], $author_ids);
        } catch (\Exception $e) {
            // ..
        }

        if (input('author_id')) {
            try {
                authors()
                    ->update(
                        [
                            'role' => input('author_role'),
                            'curriculum_url' => input('author_curriculum_url'),
                            'curriculum' => input('author_curriculum'),
                        ],
                        logged_author()->id
                    );

                $user = $_SESSION['author'];
                $user->role = input('author_role');
                $user->curriculum_url = input('author_curriculum_url');
                $user->curriculum = input('author_curriculum');

                $_SESSION['author'] = $user;
            } catch (\Exception $e) {
                // ...
            }
        }

        return response_json_create_success([
            'item' => (object) $item,
            'sql' => db_last_query()
        ]);
    });

    Route::get('/{id:\d+}/detail', function ($id) {
        $item = articles_collections()
            ->join(TB_AUTHORS, function ($join) {
                $join->on(TB_AUTHORS . '.id', '=', TB_ARTICLES_COLLECTIONS . '.author_id')
                    ->on(TB_AUTHORS . '.type', '=', DB::raw("'author'"));
            })
            ->left_join(TB_ARTICLES_AREAS, TB_ARTICLES_AREAS . '.id', '=', TB_ARTICLES_COLLECTIONS . '.area_id')
            ->left_join(TB_ARTICLES_SUBAREAS, TB_ARTICLES_SUBAREAS . '.id', '=', TB_ARTICLES_COLLECTIONS . '.subarea_id')
            ->left_join(TB_ARTICLES_SPECIALTIES, TB_ARTICLES_SPECIALTIES . '.id', '=', TB_ARTICLES_COLLECTIONS . '.specialty_id')
            ->raw_where('exists(' . get_select_articles_collections_authors() . ')')
            ->where(TB_ARTICLES_COLLECTIONS . '.id', $id)
            ->first([
                TB_ARTICLES_COLLECTIONS . '.*',
                DB::raw("coalesce(" . TB_ARTICLES_AREAS . ".name, 'Não Informada') as nome_area"),
                DB::raw("coalesce(" . TB_ARTICLES_SUBAREAS . ".name, 'Não Informada') as nome_sub_area"),
                DB::raw("coalesce(" . TB_ARTICLES_SPECIALTIES . ".name, 'Não Informada') as nome_especialidade"),
            ]);

        $item->authors = articles_collections_authors()
            ->join(TB_AUTHORS, TB_AUTHORS . '.id', '=', TB_ARTICLES_COLLECTIONS_AUTHORS . '.author_id')
            ->where(TB_ARTICLES_COLLECTIONS_AUTHORS . '.collection_id', $item->id)
            ->where_null(TB_ARTICLES_COLLECTIONS_AUTHORS . '.deleted_at')
            ->get([
                TB_AUTHORS . '.id',
                TB_AUTHORS . '.name',
                TB_AUTHORS . '.created_at',
                TB_ARTICLES_COLLECTIONS_AUTHORS . '.accepted_at'
            ]);

        $item->articles = view('pages.autores.minhas-coletaneas.components.articles-table', get_collection_articles($item));

        $item->status_badge = articles_collections_translate_status($item->status);

        $item->app_store_id = makeCollectionAppStoreId();

        $item->affiliate_coupon = affiliates_coupons()
            ->where_user_id(logged_author()->id)
            ->where_null('deleted_at')
            ->first(['id', 'token']);

        if (!empty($item->affiliate_coupon)) {
            $settings = settings()->lists('value', 'key');
            $item->affiliate_coupon->coupon_discount_percent = toMoney(array_get($settings, 'coupon_discount_percent', 10));
            $item->affiliate_coupon->coupon_affiliate_percent = toMoney(array_get($settings, 'coupon_affiliate_percent', 10));
        }

        $item->has_affiliate_coupon = !empty($item->affiliate_coupon);

        if ($item->volume > 1) {
            $item->name .= ' - VOL ' . $item->volume;
        }

        return view(AUTHOR_COLLECTIONS_VIEW_PATH . '.detail', (array) $item);
    });

    Route::put('/{id:\d+}/publicar', function ($id) {
        $item = find_or_fail(
            articles_collections()
                ->raw_where('exists(' . get_select_articles_collections_authors() . ')'),
            $id
        );

        try {
            articles_collections()
                ->update([
                    'status' => 'RP'
                ], $item->id);

            return response_json_update_success([
                'status' => 'RP',
                'status_badge' => articles_collections_translate_status('RP')
            ]);
        } catch (\Exception $e) {
            return response_json_update_fail();
        }
    });

    Route::get('/{id:\d+}/artigos', function ($id) {
        $item = find_or_fail(
            articles_collections()
                ->raw_where('exists(' . get_select_articles_collections_authors() . ')'),
            $id
        );

        return view('pages.autores.minhas-coletaneas.components.articles-table', get_collection_articles($item));
    });

    Route::post('/{id:\d+}/reiniciar', function ($id) {
        $item = find_or_fail(
            articles_collections()
                ->raw_where('exists(' . get_select_articles_collections_authors() . ')'),
            $id
        );

        try {
            articles_collections()
                ->update([
                    'status' => 'AC',
                    'created_at' => date('Y-m-d H:i:s')
                ], $item->id);

            $item->status = 'AC';

            collection_on_change_status($item);

            return response_json_update_success([
                'status' => 'AC',
                'status_badge' => articles_collections_translate_status('AC')
            ]);
        } catch (\Exception $e) {
            return response_json_update_fail();
        }
    });

    Route::put('/{id:\d+}/aprovar-artigo/{id:\d+}', fn ($collection_id, $article_id) => approve_or_reject_article($collection_id, $article_id, 41));

    Route::put('/{id:\d+}/rejeitar-artigo/{id:\d+}', fn ($collection_id, $article_id) => approve_or_reject_article($collection_id, $article_id, 11));

    Route::get('/busca-autor/{document:\d+}', function ($document) {
        $item = first_or_fail(
            authors()
                ->where_document(only_numbers($document))
                ->or_where('document', mask($document, 'cpf_cnpj')),
            ['id', 'name']
        );

        return response_json((array)$item);
    });
});
