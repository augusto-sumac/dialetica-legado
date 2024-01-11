<?php

function articles_collections_validation_rules($id = null, $include_authors = true, $collection = null)
{
    $required = $id > 1 ? 'required|' : '';

    $rules =  [
        'name' => 'required|max:100|unique:' . TB_ARTICLES_COLLECTIONS . ($id ? ',id,' . $id : ''),
        'description' => 'required|max:2000',

        'area_id' => $required . 'exists:' . TB_ARTICLES_AREAS . ',id',
        'subarea_id' => $required . 'exists:' . TB_ARTICLES_SUBAREAS . ',id',
        'specialty_id' => $required . 'exists:' . TB_ARTICLES_SPECIALTIES . ',id',

        'isbn' => 'max:50',
        'isbn_e_book' => 'max:50',
        'doi' => 'max:50',
        'book_url' => 'max:255|url',

        'author_id' => '',
        'cover_image' => '',
    ];

    if ($collection && $collection->volume > 1) {
        $rules = array_except($rules, [
            'name',
            'description',
            'area_id',
            'subarea_id',
            'specialty_id',
        ]);
    }

    if ($include_authors) {
        foreach (input('author_ids', []) as $key => $author_id) {
            $rules['author_ids.' . $key] = 'exists:' . TB_USERS . ',id,' . $author_id;
        }
    }

    return $rules;
}

function sync_collection_authors($collection_id, $author_ids = [])
{
    $authors = $author_ids ? $author_ids : array_unique(array_values(input('author_ids', [])));

    InsertOrUpdateMany::prepare(
        TB_ARTICLES_COLLECTIONS_AUTHORS,
        ['collection_id', 'author_id', 'accepted_at', 'deleted_at'],
        array_map(fn ($author_id) => [
            'collection_id' => $collection_id,
            'author_id' => $author_id,
            'accepted_at' => isset(logged_author()->id) ? ((int) $author_id === logged_author()->id ? date('Y-m-d H:i:s') : null) : null,
            'deleted_at' => null
        ], $authors),
        ['deleted_at', 'accepted_at'],
        null,
        true
    );

    if (!empty($authors)) {
        articles_collections_authors()
            ->where_collection_id($collection_id)
            ->where_not_in('author_id', $authors)
            ->update([
                'deleted_at' => date('Y-m-d H:i:s')
            ]);

        $collection = articles_collections()
            ->join(TB_AUTHORS, TB_AUTHORS . '.id', '=', TB_ARTICLES_COLLECTIONS . '.author_id')
            ->where(TB_ARTICLES_COLLECTIONS . '.id', $collection_id)
            ->first([
                TB_AUTHORS . '.name as organizer_name',
                TB_ARTICLES_COLLECTIONS . '.name',
            ]);

        $invited_users = array_reduce(
            authors()->where_in('id', $authors)->get(['id', 'name', 'email']),
            function ($arr, $item) {
                $arr[$item->id] = $item;
                return $arr;
            },
            []
        );

        foreach ($authors as $author) {
            $invited_name = get_first_name(array_get($invited_users, $author)->name);

            add_job('sendMail', [
                'to' => env('DEV_MODE') ? 'marcioantunes.ma@gmail.com' : array_get($invited_users, $author)->email,
                'subject' => 'Plataforma - Organizador convidado',
                'message' => [
                    'id' => '',
                    'vars' => [
                        'convidado_first_name' => $invited_name,
                        'unique_name' => get_first_name($collection->organizer_name),
                        'titulo_coletanea' => $collection->name,
                    ]
                ]
            ]);
        }
    }
}

function sync_authors_coupons($collection_id)
{
    $select_users_coupons = affiliates_coupons()
        ->where(TB_AFFILIATES_COUPONS . '.user_id', DB::raw(TB_ARTICLES_COLLECTIONS_AUTHORS . '.author_id'))
        ->select(['id'])
        ->toSql();

    $authors = articles_collections_authors()
        ->where_collection_id($collection_id)
        ->where_null('deleted_at')
        ->raw_where('not exists(' . $select_users_coupons . ')')
        ->get();

    if (empty($authors)) {
        return;
    }

    foreach ($authors as $author) {
        affiliates_coupons()
            ->insert([
                'user_id' => $author->author_id,
                'token' => generate_unique_coupon()
            ]);
    }
}

function get_collection_unique_token()
{
    $token = Str::random(20, 'alpha');
    while (articles_collections()->where_token($token)->first()) {
        $token = Str::random(20, 'alpha');
    }

    return $token;
}

function article_collection_status_badge($row)
{
    $row = (array)$row;

    $status = articles_collections_translate_status($row['status']);

    if (!isset($status['color'])) {
        $status = ['color' => 'warning', 'label' => 'Em Análise'];
    }

    return article_badge($status, 'Status');
}

Route::group([
    'prefix' => ARTICLES_COLLECTIONS_BASE_URL,
    'before' => 'auth'
], function () {
    Route::get('', function () {
        $datagrid = make_datatable_options([
            'data' => [],
            'use_datatable' => true,
            'slot_form' => ARTICLES_COLLECTIONS_VIEW_PATH . '.components.datagrid-filter',
            'options' => [
                'order' => [[0, 'desc']],
                'processing' =>  true,
                'serverSide' =>  true,
                'deferRender' => true,
                'ajax' => [
                    'url' => url(ARTICLES_COLLECTIONS_BASE_URL),
                    'type' => 'POST'
                ],
                'pageLength' => 50
            ],
            'columns' => [
                [
                    'key' => 'id',
                    'label' => '#ID',
                    'attrs' => ['class' => 'text-start w-120px'],
                ],
                [
                    'key' => 'name',
                    'label' => 'Coletânea',
                    // 'attrs' => ['class' => 'w-200px'],
                ],
                [
                    'key' => 'created_by',
                    'label' => 'Origem',
                    'attrs' => ['class' => 'w-100px'],
                ],
                [
                    'key' => 'created_at',
                    'label' => 'Dt Cadastro',
                    'attrs' => ['class' => 'text-end w-150px'],
                ],
                [
                    'key' => 'expires_at',
                    'label' => 'Dt Limite',
                    'attrs' => ['class' => 'text-end w-150px'],
                ],
                [
                    'key' => 'status',
                    'label' => 'Status',
                    'attrs' => ['class' => 'text-end w-150px'],
                ],
            ]
        ]);

        return view(ARTICLES_COLLECTIONS_VIEW_PATH . '.index', compact('datagrid'));
    });

    Route::post('', function () {
        $sqlValidOrganizer = articles_collections_authors()
            ->where_null(TB_ARTICLES_COLLECTIONS_AUTHORS . '.deleted_at')
            ->where(TB_ARTICLES_COLLECTIONS_AUTHORS . '.collection_id', '=', DB::raw(TB_ARTICLES_COLLECTIONS . '.id'))
            ->where(TB_ARTICLES_COLLECTIONS_AUTHORS . '.author_id', '=', DB::raw(TB_ARTICLES_COLLECTIONS . '.author_id'))
            ->toSql([TB_ARTICLES_COLLECTIONS_AUTHORS . '.collection_id']);

        $rows = articles_collections()
            ->left_join(TB_USERS, TB_USERS . '.id', '=', TB_ARTICLES_COLLECTIONS . '.created_by')
            ->where_null(TB_ARTICLES_COLLECTIONS . '.deleted_at')
            ->select([
                TB_ARTICLES_COLLECTIONS . '.*',
                DB::raw("(case when exists($sqlValidOrganizer) then 1 else 0 end) as is_valid_author"),
                DB::raw("(case when " . TB_USERS . ".type = 'author' then 1 else 0 end) as is_public"),
                TB_USERS . '.name as author_name',
            ]);

        $total = clone $rows;

        // Sort
        $cols = input('columns', []);
        foreach (input('order', []) as $order) {
            $col = array_get($cols, $order['column'] . '.data');

            if ($col === 'created_by') {
                $col = TB_USERS . '.type';
            }

            if ($col) {
                $rows->order_by(DB::raw($col . ' ' . strtolower($order['dir'])));
            }
        }

        // Search
        if ($search = input('search.value', input('search.search'))) {
            $rows->where(function ($q) use ($search) {
                if (is_numeric($search)) {
                    $q->or_where(TB_ARTICLES_COLLECTIONS . '.id', (int) $search);
                } else {
                    $q->or_like(TB_ARTICLES_COLLECTIONS . '.name', $search);
                }
            });
        }

        // Filters
        if ($status = input('filters.status')) {
            if (in_array('99', $status)) {
                $rows->where(
                    fn ($q) => $q
                        ->where_in(TB_ARTICLES_COLLECTIONS . '.status', $status)
                        ->or_where(
                            fn ($q) => $q
                                ->where(TB_ARTICLES_COLLECTIONS . '.status', 'AC')
                                ->where(
                                    fn ($q) => $q
                                        ->where_not_null(TB_ARTICLES_COLLECTIONS . '.expires_at')
                                        ->where(TB_ARTICLES_COLLECTIONS . '.expires_at', '<', date('Y-m-d'))
                                )
                        )
                );
            } else {
                $rows->where_in(TB_ARTICLES_COLLECTIONS . '.status', $status);
            }
        }

        if ($origin = input('filters.origin')) {
            $rows->where(TB_USERS . ".type", ($origin === 'P' ? '=' : '!='), 'author');
        }

        foreach (['created_at', 'expires_at'] as $date_key) {
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

        if (input('export')) {

            $content = [
                '<table>',
                '<thead>
                    <tr>
                        <th>#ID</th>
                        <th>Autor</th>
                        <th>Coletânea</th>
                        <th>Origem</th>
                        <th>Dt Cadastro</th>
                        <th>Dt Limite</th>
                        <th>Status</th>
                    </tr>
                </thead>',
                '<tbody>'
            ];

            foreach ($rows->get() as $row) {
                $content[] = '<tr>
                    <td>' . $row->id . '</td>
                    <td>' . $row->author_name . '</td>
                    <td>' . $row->name . '</td>
                    <td>' . ($row->is_public ? 'Público' : 'Admin') . '</td>
                    <td>' . datetimeFromMySql($row->created_at) . '</td>
                    <td>' . datetimeFromMySql($row->expires_at) . '</td>
                    <td>' . article_collection_status_badge($row) . '</td>
                </tr>';
            }

            $content = implode('', $content) . '</tbody></table>';

            File::mkdir(STORAGE_PATH . 'reports');

            $path = date('YmdHis') . '-relatorio-de-coletaneas-cadastrados-' . md5(time() . json_encode(input())) . '.xls';
            $file = STORAGE_PATH . 'reports/' . $path;

            file_put_contents($file, view('components.excel-export', [
                'worksheet' => 'Artigos',
                'table' => $content
            ]));

            $target_path = $file;
            try {
                $result = Spaces::upload($target_path, (env('DEV_MODE', false) ? 'dev-' : '') . 'reports/' . $path);
                $path = array_get((array)$result, 'ObjectURL');
                @unlink($target_path);
                if (!$path) {
                    return response_json_update_fail('Falha ao gravar o arquivo');
                }
            } catch (\Exception $e) {
                @unlink($target_path);
                return response_json_update_fail($e->getMessage());
            }

            return response_json([
                'file' => $path
            ]);
        }

        // Data
        $rows = $rows->paginate(input('per_page', 15));

        return response_json([
            'draw' => input('draw', 1),
            'recordsTotal' => $total->count(),
            'recordsFiltered' => $rows->total,
            'data' => array_map(function ($row) {
                $row->status = article_collection_status_badge($row);

                $row->expires_at = $row->is_valid_author && $row->expires_at ? date('d/m/Y', strtotime($row->expires_at)) : '';
                $row->created_at = datetimeFromMySql($row->created_at);

                $row->id = '<a href="' . url(ARTICLES_COLLECTIONS_BASE_URL . '/' . $row->id . '/editar') . '" title="Editar Cadastro">' . str_pad_id($row->id) . '</a>';

                if ($row->volume > 1) {
                    $row->name = "{$row->name} - VOL {$row->volume}";
                }

                $is_public = (int) $row->is_public === 1;

                $row->created_by = '<span class="badge bg-' . ($is_public ? 'info' : 'muted') . '">' . ($is_public ? 'Público' : 'Admin') . '</span>';

                return $row;
            }, $rows->results)
        ]);
    });

    Route::get('/adicionar', function () {
        return view(ARTICLES_COLLECTIONS_VIEW_PATH . '.form', [
            'id' => null,
            'author_id' => null,
            'authors' => [],
            'app_store_id' => makeCollectionAppStoreId()
        ]);
    });

    Route::post('/adicionar', function () {
        validate(articles_collections_validation_rules());

        $item = array_only(input(), array_keys(articles_collections_validation_rules(null, false)));

        $item['status'] = 'AC';
        $item['token'] = get_collection_unique_token();
        $item['created_by'] = logged_user()->id;
        $item['accept_publication_rules'] = json_encode([
            'articles_qty' => 1,
            'responsibility' => 1
        ], JSON_OBJECT_AS_ARRAY);

        if (array_get($item, 'author_id')) {
            $settings = settings()->where_key('collection_days_limit')->lists('value', 'key');
            $days_limit = array_get($settings, 'collection_days_limit', 30);
            $item['expires_at'] = date('Y-m-d', strtotime("+{$days_limit} days"));
        }

        try {
            $item['id'] = articles_collections()->insert_get_id($item);

            sync_collection_authors($item['id']);

            collection_on_change_status($item);

            return response_json_create_success([
                'item' => (object) $item
            ]);
        } catch (\Exception $e) {
            return response_json_create_fail();
        }
    });

    Route::get('/{id}/editar', function ($id) {
        $item = articles_collections()
            ->left_join(TB_USERS . ' as u', 'u.id', '=', TB_ARTICLES_COLLECTIONS . '.created_by')
            ->where(TB_ARTICLES_COLLECTIONS . '.id', $id)
            ->first(
                [
                    TB_ARTICLES_COLLECTIONS . '.*',
                    DB::raw("(case when u.type = 'author' then 1 else 0 end) as is_public"),
                    'u.name as created_by_name',
                ]
            );

        if (!$item) {
            throw new NotFoundJsonResponseException(
                response_json([
                    'message' => 'Nenhum registro encontrado!',
                ], 404)
            );
        }

        $item->authors = articles_collections_authors()
            ->join(TB_AUTHORS, TB_AUTHORS . '.id', '=', TB_ARTICLES_COLLECTIONS_AUTHORS . '.author_id')
            ->where(TB_ARTICLES_COLLECTIONS_AUTHORS . '.collection_id', $item->id)
            ->where_null(TB_ARTICLES_COLLECTIONS_AUTHORS . '.deleted_at')
            ->get([
                TB_AUTHORS . '.id',
                TB_AUTHORS . '.name',
                TB_AUTHORS . '.created_at',
                TB_ARTICLES_COLLECTIONS_AUTHORS . '.accepted_at',
            ]);

        $item->status_badge = articles_collections_translate_status($item->status);

        $item->articles = view('pages.autores.minhas-coletaneas.components.articles-table', get_collection_articles($item));

        $item->app_store_id = makeCollectionAppStoreId();

        $is_public = (int) $item->is_public === 1;

        $item->created_by = '<span class="badge bg-' . ($is_public ? 'info' : 'muted') . '">' . ($is_public ? 'Público' : 'Admin') . ' ( ' . trim($item->created_by_name) . ' )' . '</span>';

        return view(ARTICLES_COLLECTIONS_VIEW_PATH . '.form', (array) $item + ['collection' => $item]);
    });

    Route::post('/{id}/editar', function ($id) {
        $collection = find_or_fail(articles_collections(), $id);

        $rules = articles_collections_validation_rules($id, true, $collection);

        validate($rules);

        $item = array_only(input(), array_keys($rules));

        if ($collection->volume === 1) {
            foreach (['area_id', 'subarea_id', 'specialty_id'] as $key) {
                $item[$key] = empty($item[$key]) ? null : $item[$key];
            }
        }

        try {
            articles_collections()->update($item, $id);
            $item['id'] = $id;

            sync_collection_authors($item['id']);

            return response_json_update_success([
                'item' => (object) $item
            ]);
        } catch (\Exception $e) {
            return response_json_update_fail();
        }
    });

    Route::put('/{id:\d+}/change-status', function ($id) {
        $item = find_or_fail(articles_collections(), $id);

        $status = input('status');

        try {
            sync_authors_coupons($id);
        } catch (\Exception $e) {
            // ...
        }

        try {
            articles_collections()->update(compact('status'), $id);

            $item->status = $status;

            collection_on_change_status($item);

            $item->status = $status;
            $item->status_badge = articles_collections_translate_status($status);

            return response_json_update_success($item);
        } catch (\Exception $e) {
            return response_json_update_fail();
        }
    });

    Route::post('/{id:\d+}/reiniciar', function ($id) {
        $item = find_or_fail(articles_collections(), $id);

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
});
