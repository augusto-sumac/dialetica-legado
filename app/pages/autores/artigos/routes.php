<?php

const AUTHOR_ARTICLES_STEPS = [
    [
        'key' => 'titulo',
        'label' => 'Título',
        'component' => AUTHOR_ARTICLES_VIEW_PATH . '.components.steps.titulo',
        'prev' => false,
        'next' => 'Área'
    ],
    [
        'key' => 'coletanea',
        'label' => 'Coletânea',
        'component' => AUTHOR_ARTICLES_VIEW_PATH . '.components.steps.coletanea',
        'prev' => 'Título',
        'next' => 'Autores',
    ],
    [
        'key' => 'area',
        'label' => 'Área',
        'component' => AUTHOR_ARTICLES_VIEW_PATH . '.components.steps.area',
        'prev' => 'Título',
        'next' => 'Autores',
    ],
    [
        'key' => 'autores',
        'label' => 'Autores',
        'component' => AUTHOR_ARTICLES_VIEW_PATH . '.components.steps.autores',
        'prev' => 'Área',
        'next' => 'Arquivo',
    ],
    [
        'key' => 'arquivo',
        'label' => 'Arquivo',
        'component' => AUTHOR_ARTICLES_VIEW_PATH . '.components.steps.arquivo',
        'prev' => 'Autores',
        'next' => 'Revisão',
    ],
    [
        'key' => 'revisao',
        'label' => 'Revisão',
        'component' => AUTHOR_ARTICLES_VIEW_PATH . '.components.steps.revisao',
        'prev' => 'Arquivo',
        'next' => 'Concluir',
    ],
    [
        'key' => 'confirmacao',
        'label' => 'Confirmação',
        'component' => AUTHOR_ARTICLES_VIEW_PATH . '.components.steps.confirmacao'
    ],
];

function form_adicionar($step)
{
    $steps = AUTHOR_ARTICLES_STEPS;

    $steps = array_filter($steps, fn ($step) => $step['key'] !== (array_get($_SESSION, 'COLLECTION_ID') ? 'area' : 'coletanea'));

    $collection = null;
    if (array_get($_SESSION, 'COLLECTION_ID')) {
        $collection = articles_collections()->find(array_get($_SESSION, 'COLLECTION_ID'));
        $steps[0]['next'] = 'Coletânea';
    }

    return view(AUTHOR_ARTICLES_VIEW_PATH . '.form', [
        'steps' => $steps,
        'step' => (int) $step,
        'id' => null,
        'attachment' => [],
        'co_authors' => [],
        'tags' => ['', '', ''],
        'collection' => $collection
    ]);
}

Route::get('/coletanea/{token}', function ($token) {
    // $collection = articles_collections()->where_token($token)->where_status('AC')->first();
    $collection = articles_collections()->where_token($token)->first();
    if ($collection->status !== 'AC') {
        $collection = articles_collections()->where_name($collection->name)->where_status('AC')->first();
    }

    if ($collection && $collection->id) {
        $_SESSION['COLLECTION_ID'] = $collection->id;
        $_SESSION['COLLECTION_URL'] = urlCurrent();

        return redirect(AUTHOR_ARTICLES_BASE_URL . '/adicionar');
    }

    AlertError('A coletânea não está mais disponível!');

    return redirect('/');
});

Route::group([
    'prefix' => AUTHOR_ARTICLES_BASE_URL,
    'before' => 'author-auth'
], function () {
    Route::get('', function () {
        unset($_SESSION['COLLECTION_ID']);

        $rows = articles()
            ->join(TB_USERS, TB_USERS . '.id', '=', TB_ARTICLES . '.author_id')
            ->left_join(TB_ARTICLES_AREAS, TB_ARTICLES_AREAS . '.id', '=', TB_ARTICLES . '.area_id')
            ->left_join(
                TB_ARTICLES_PAYMENTS . ' as payment',
                'payment.id',
                '=',
                TB_ARTICLES . '.payment_id'
            )
            ->where(TB_ARTICLES . '.author_id', logged_author()->id)
            ->where_null(TB_ARTICLES . '.deleted_at')
            ->order_by(TB_ARTICLES . '.created_at', 'desc')
            ->get([
                TB_ARTICLES . '.id',
                TB_ARTICLES . '.title',
                TB_ARTICLES . '.created_at',
                TB_ARTICLES . '.status',
                TB_ARTICLES . '.amount',
                'payment.service_status as payment_status',
                DB::raw('(case when payment.service_status = 2 then payment.finished_at else null end) as paid_at'),
                TB_USERS . '.name as nome_autor',
                DB::raw("coalesce(" . TB_ARTICLES_AREAS . ".name, 'Área Não Informado') as nome_area")
            ]);

        $rows = array_map(function ($row) {
            $article_status = article_status($row->status);
            if (!array_get($article_status, 'color')) {
                $article_status = ['color' => '', 'label' => ''];
            }

            $article_payment_status = article_payment_status($row->payment_status ?? -1);
            if (!array_get($article_payment_status, 'color')) {
                $article_payment_status = ['color' => '', 'label' => ''];
            }

            $row->status_badge = '<span class="badge bg-' . $article_status['color'] . '" title="Status">' . $article_status['label'] . '</span>';
            $row->payment_status_badge = '<span class="badge bg-' . $article_payment_status['color'] . '" title="Status Pagamento">' . $article_payment_status['label'] . '</span>';

            $row->created_at = datetimeFromMySql($row->created_at);
            $row->paid_at = datetimeFromMySql($row->paid_at);

            $row->nome_area = implode('<br />', [
                '<small class="fw-bold">' . $row->nome_area . '</small>'
            ]);

            $row->payment_status = (int) $row->payment_status !== 2 ? null : $row->payment_status;

            return $row;
        }, $rows);

        return view(AUTHOR_ARTICLES_VIEW_PATH . '.index', compact('rows'));
    });

    Route::get('/{id:\d+}/{id:\d+?}', function ($id, $collection_id = null) {
        $item = articles()
            ->join(TB_USERS, TB_USERS . '.id', '=', TB_ARTICLES . '.author_id')
            ->left_join(TB_ARTICLES_AREAS, TB_ARTICLES_AREAS . '.id', '=', TB_ARTICLES . '.area_id')
            ->left_join(TB_ARTICLES_SUBAREAS, TB_ARTICLES_SUBAREAS . '.id', '=', TB_ARTICLES . '.subarea_id')
            ->left_join(TB_ARTICLES_SPECIALTIES, TB_ARTICLES_SPECIALTIES . '.id', '=', TB_ARTICLES . '.specialty_id')
            ->left_join(TB_ARTICLES_COLLECTIONS, TB_ARTICLES_COLLECTIONS . '.id', '=', TB_ARTICLES . '.collection_id')
            ->left_join(
                TB_ARTICLES_PAYMENTS . ' as payment',
                'payment.id',
                '=',
                TB_ARTICLES . '.payment_id'
            )

            ->where(TB_ARTICLES . '.id', $id)
            ->select([
                'payment.service_status as payment_status',
                DB::raw('(case when payment.service_status = 2 then payment.finished_at else null end) as paid_at'),
                TB_USERS . '.name as nome_autor',
                DB::raw("coalesce(" . TB_ARTICLES_AREAS . ".name, 'Área Não Informado') as nome_area"),
                DB::raw("coalesce(" . TB_ARTICLES_SUBAREAS . ".name, 'Sub-área Não Informada') as nome_sub_area"),
                DB::raw("coalesce(" . TB_ARTICLES_SPECIALTIES . ".name, 'Especialidade Não Informada') as nome_especialidade"),
                DB::raw("coalesce(" . TB_ARTICLES_COLLECTIONS . ".name, 'Fluxo Contínuo') as nome_coletania"),
                TB_ARTICLES . '.*'
            ]);

        if ($collection_id) {
            $item->where(TB_ARTICLES . '.collection_id', $collection_id);
        } else {
            $item->where(TB_ARTICLES . '.author_id', logged_author()->id);
        }

        $item = $item->first();

        if (!$item) {
            return redirect(AUTHOR_ARTICLES_BASE_URL);
        }

        if ($item->status === 0) {
            return redirect(AUTHOR_ARTICLES_BASE_URL . '/editar/' . $item->id);
        }

        $article_status = article_status($item->status);
        if (!$article_status) {
            $article_status = ['color' => '', 'label' => ''];
        }

        $article_payment_status = article_payment_status($item->payment_status ?? -1);
        if (!array_get($article_payment_status, 'color')) {
            $article_payment_status = ['color' => '', 'label' => ''];
        }

        $item->status_badge = '<span class="badge bg-' . $article_status['color'] . '" title="Status">' . $article_status['label'] . '</span>';
        $item->payment_status_badge = '<span class="badge bg-' . $article_payment_status['color'] . '" title="Status Pagamento">' . $article_payment_status['label'] . '</span>';

        $item->author = authors()->find($item->author_id);

        $item->co_authors = articles_coauthors()
            ->where_article_id($item->id)
            ->where_null('deleted_at')
            ->get();

        $item->attachment = secure_json_decode($item->attachment, true);

        $item->collection = articles_collections()->find($item->collection_id);

        $item->route_collection_id = $collection_id;

        $item->payment_status = (int) $item->payment_status !== 2 ? null : $item->payment_status;

        if ($collection_id) {
            return view(AUTHOR_ARTICLES_VIEW_PATH . '.components.article-detail', (array)$item);
        }

        return view(AUTHOR_ARTICLES_VIEW_PATH . '.detail', (array)$item);
    });

    Route::get('/adicionar', fn ($step = 0) => form_adicionar($step));

    Route::get('/editar/{id:\d+}/{step:\d+?}', function ($id, $step = 0) {
        $item = articles()
            ->left_join(TB_ARTICLES_AREAS, TB_ARTICLES_AREAS . '.id', '=', TB_ARTICLES . '.area_id')
            ->left_join(TB_ARTICLES_SUBAREAS, TB_ARTICLES_SUBAREAS . '.id', '=', TB_ARTICLES . '.subarea_id')
            ->left_join(TB_ARTICLES_SPECIALTIES, TB_ARTICLES_SPECIALTIES . '.id', '=', TB_ARTICLES . '.specialty_id')
            ->left_join(TB_ARTICLES_COLLECTIONS, TB_ARTICLES_COLLECTIONS . '.id', '=', TB_ARTICLES . '.collection_id')
            ->where(TB_ARTICLES . '.author_id', logged_author()->id)
            ->where(TB_ARTICLES . '.id', $id)
            ->select([
                DB::raw("coalesce(" . TB_ARTICLES_AREAS . ".name, 'Área Não Informado') as area_name"),
                DB::raw("coalesce(" . TB_ARTICLES_SUBAREAS . ".name, 'Sub-área Não Informada') as subarea_name"),
                DB::raw("coalesce(" . TB_ARTICLES_SPECIALTIES . ".name, 'Especialidade Não Informada') as specialty_name"),
                DB::raw("coalesce(" . TB_ARTICLES_COLLECTIONS . ".name, 'Fluxo Contínuo') as collection_name"),
                TB_ARTICLES . '.*'
            ])
            ->first();

        $item->author = authors()->find($item->author_id);

        $item->co_authors = articles_coauthors()->where_article_id($id)->where_null('deleted_at')->get();

        $item->attachment = secure_json_decode($item->attachment, true);

        $item->tags = array_map('trim', explode(',', $item->tags));

        // Garantindo mínimo de 3 tags
        for ($i = count($item->tags); $i < 3; $i++) {
            $item->tags[] = '';
        }

        $item->collection = articles_collections()->find($item->collection_id);

        $steps = AUTHOR_ARTICLES_STEPS;

        $has_collection = $item->collection_token && $item->collection->token === $item->collection_token;

        $steps = array_filter($steps, fn ($step) => $step['key'] !== ($has_collection ? 'area' : 'coletanea'));
        if ($has_collection) {
            $steps[0]['next'] = 'Coletânea';
        }

        return view(AUTHOR_ARTICLES_VIEW_PATH . '.form', [
            'steps' => $steps,
            'step' => $step,
            'id' => $id
        ] + (array) $item);
    });

    Route::post('/{id:\d+}/reset-collection', function ($id) {
        find_or_fail(articles()->where_author_id(logged_author()->id), $id);

        articles()->update([
            'collection_id' => 1,
            'collection_token' => null
        ], $id);

        return response_json_update_success([]);
    });

    Route::get('/review/{id:\d+}', function ($id) {
        $item = articles()
            ->left_join(TB_ARTICLES_AREAS, TB_ARTICLES_AREAS . '.id', '=', TB_ARTICLES . '.area_id')
            ->left_join(TB_ARTICLES_SUBAREAS, TB_ARTICLES_SUBAREAS . '.id', '=', TB_ARTICLES . '.subarea_id')
            ->left_join(TB_ARTICLES_SPECIALTIES, TB_ARTICLES_SPECIALTIES . '.id', '=', TB_ARTICLES . '.specialty_id')
            ->left_join(TB_ARTICLES_COLLECTIONS, TB_ARTICLES_COLLECTIONS . '.id', '=', TB_ARTICLES . '.collection_id')
            ->where(TB_ARTICLES . '.author_id', logged_author()->id)
            ->where(TB_ARTICLES . '.id', $id)
            ->select([
                DB::raw("coalesce(" . TB_ARTICLES_AREAS . ".name, 'Área Não Informado') as area_name"),
                DB::raw("coalesce(" . TB_ARTICLES_SUBAREAS . ".name, 'Sub-área Não Informada') as subarea_name"),
                DB::raw("coalesce(" . TB_ARTICLES_SPECIALTIES . ".name, 'Especialidade Não Informada') as specialty_name"),
                DB::raw("coalesce(" . TB_ARTICLES_COLLECTIONS . ".name, 'Fluxo Contínuo') as collection_name"),
                TB_ARTICLES . '.*'
            ])
            ->first();

        $item->author = authors()->find($item->author_id);

        $item->co_authors = articles_coauthors()->where_article_id($id)->where_null('deleted_at')->get();

        $item->attachment = secure_json_decode($item->attachment, true);

        return view(AUTHOR_ARTICLES_VIEW_PATH . '.review', (array)$item);
    });

    Route::post('/titulo', function () {
        $id = input('id');
        $article = null;

        if ($id) {
            $article = find_or_fail(articles()->where_author_id(logged_author()->id), $id);
        }

        $rules = [
            'title' => 'required|max:255',
            'resume' => 'required',
            'tags.0' => 'required',
            'tags.1' => 'required',
            'tags.2' => 'required',
            'accept_contract' => 'required|in:1',
            'accept_publication_rules' => 'required|in:1',
        ];

        validate($rules);

        $item = array_only(input(), ['title', 'resume', 'tags', 'accept_contract', 'accept_publication_rules']);

        $item['tags'] = implode(', ', array_filter($item['tags'], fn ($t) => strlen(trim($t))));

        $type = articles_types()->find(1);

        $item = array_merge(
            $item,
            [
                'status' => 0,
                'author_id' => logged_author()->id,
                'type_id' => 1,
                'collection_id' => $article && $article->collection_id ? $article->collection_id : array_get($_SESSION, 'COLLECTION_ID', 1),
                'gross_amount' => $type->price,
                'amount' => $type->price
            ]
        );

        try {
            if ($id) {
                articles()->where_author_id(logged_author()->id)->update($item, $id);
            } else {
                $id = articles()->insert_get_id($item);
            }

            $item['id'] = $id;

            return response_json_update_success(compact('item'));
        } catch (\Exception $e) {
            if ($item['id']) {
                return response_json_update_fail();
            }
            return response_json_create_fail();
        }
    });

    Route::post('/coletanea', function () {
        $id = input('id');

        find_or_fail(articles()->where_author_id(logged_author()->id), $id);

        $rules = [
            'collection_id' => 'exists:' . TB_ARTICLES_COLLECTIONS . ',id',
            'area_id' => 'required|exists:' . TB_ARTICLES_AREAS . ',id',
            'subarea_id' => 'required|exists:' . TB_ARTICLES_SUBAREAS . ',id',
            'specialty_id' => 'required|exists:' . TB_ARTICLES_SPECIALTIES . ',id',
        ];

        validate($rules);

        $item = array_only(input(), array_keys($rules));

        $collection = articles_collections()->find($item['collection_id']);
        $item['collection_token'] = $collection->token;

        try {
            articles()->where_author_id(logged_author()->id)->update($item, $id);

            $item['id'] = $id;

            return response_json_update_success(compact('item'));
        } catch (\Exception $e) {
            return response_json_update_fail();
        }
    });

    Route::post('/area', function () {
        $id = input('id');

        find_or_fail(articles()->where_author_id(logged_author()->id), $id);

        $rules = [
            'area_id' => 'required|exists:' . TB_ARTICLES_AREAS . ',id',
            'subarea_id' => 'required|exists:' . TB_ARTICLES_SUBAREAS . ',id',
            'specialty_id' => 'required|exists:' . TB_ARTICLES_SPECIALTIES . ',id',
        ];

        validate($rules);

        $item = array_only(input(), array_keys($rules));
        $item['collection_id'] = 1;
        $item['collection_token'] = null;

        try {
            articles()->where_author_id(logged_author()->id)->update($item, $id);

            $item['id'] = $id;

            return response_json_update_success(compact('item'));
        } catch (\Exception $e) {
            return response_json_update_fail();
        }
    });

    Route::post('/autores', function () {
        $id = input('id');

        find_or_fail(articles()->where_author_id(logged_author()->id), $id);

        $rules = [
            'role' => 'required|max:500',
            'curriculum' => 'required|max:4000',
            'curriculum_url' => 'required|max:255',
        ];

        foreach (input('authors', []) as $key => $value) {
            $rules["authors.{$key}.name"] = 'required|max:150';
            $rules["authors.{$key}.email"] = 'required|email|max:255';
            $rules["authors.{$key}.role"] = 'required|max:500';
            $rules["authors.{$key}.curriculum"] = 'required|max:4000';
            $rules["authors.{$key}.curriculum_url"] = 'required|max:255';
        }

        validate($rules);

        try {
            authors()
                ->update(
                    array_only(input(), ['role', 'curriculum', 'curriculum_url']),
                    logged_author()->id
                );

            foreach (['role', 'curriculum', 'curriculum_url'] as $key) {
                $_SESSION['author']->{$key} = input($key);
            }

            articles_coauthors()->where_article_id($id)->delete();

            $article_author_name = Str::slug(logged_author()->name);
            $article_author_email = Str::slug(logged_author()->email);

            // Filter to prevent duplicated authors
            $authors = array_filter(
                input('authors', []),
                fn ($author) =>
                Str::slug($author['name']) !== $article_author_name &&
                    Str::slug($author['email']) !== $article_author_email
            );

            foreach ($authors as $author) {
                $item = array_only($author, ['name', 'email', 'role']);
                $item['article_id'] = $id;
                articles_coauthors()->insert($item);
            }

            return response_json_update_success([
                'item' => ['id' => $id]
            ]);
        } catch (\Exception $e) {
            return response_json_update_fail();
        }
    });

    Route::post('/arquivo', function () {
        $id = (int) input('id');

        find_or_fail(articles()->where_author_id(logged_author()->id), $id);

        if ($attachment = secure_json_decode(input('attachment'), true)) {
            return response_json_update_success([
                'item' => ['id' => $id] + $attachment
            ]);
        }

        $ext = '.' . File::extension(array_get($_FILES, 'file.name'));
        $name = Str::limit(str_replace($ext, '', array_get($_FILES, 'file.name')), 100, '');

        array_set($GLOBALS, 'APP_INPUT_DATA.name', $name);
        array_set($GLOBALS, 'APP_INPUT_DATA.ext', $ext);

        validate([
            'ext' => 'in:.doc,.docx',
            'name' => 'required'
        ]);

        $dir = 'storage/articles';

        $path = 'article-' . md5($id . '-' . time() . '-' . $name) . $ext;

        $target_path = ROOT_PATH . $dir . '/' . $path;

        $upload_result = File::upload('file', ROOT_PATH . $dir, $path);

        if ($upload_result === false) {
            return response_json_update_fail(['stage' => 'upload', 'message' => 'Falha no envio do arquivo']);
        }

        $size = getFileSize(File::size(ROOT_PATH . $dir . '/' . $path));

        try {
            $result = Spaces::upload($target_path, (env('DEV_MODE', false) ? 'dev-' : '') . 'articles/' . $path);
            $path = array_get((array)$result, 'ObjectURL');
            @unlink($target_path);
            if (!$path) {
                return response_json_update_fail('Falha ao gravar o arquivo');
            }
        } catch (\Exception $e) {
            @unlink($target_path);
            return response_json_update_fail($e->getMessage());
        }

        try {
            articles()->where_author_id(logged_author()->id)->update([
                'attachment' => json_encode(compact('name', 'path', 'size'))
            ], $id);

            return response_json_update_success([
                'item' => compact('id', 'name', 'path', 'size')
            ]);
        } catch (\Exception $e) {
            return response_json_update_fail($e->getMessage());
        }
    });

    Route::post('/revisao', function () {
        $id = input('id');

        $article = find_or_fail(articles()->where_author_id(logged_author()->id), $id);

        $author = authors()->find($article->author_id);

        $item = [
            'status' => 20
        ];

        try {
            articles()->where_author_id(logged_author()->id)->update($item, $id);

            $item['id'] = $id;

            // $message = view('mail.article-created', [
            //     'name' => get_first_name($author->name),
            //     'title' => $article->title,
            // ]);

            $message = [
                'id' => 'd-2b82fb7a6ab44533991d708fc9cdf24b',
                'vars' => [
                    'first_name' => get_first_name($author->name),
                    'titulo_trabalho' => $article->title,
                ]
            ];

            $data = [
                'to' => env('DEV_MODE') ? 'marcioantunes.ma@gmail.com' :  $author->email,
                'subject' => 'Recebemos seu artigo - Editora Dialética',
                'message' => $message
            ];

            add_job('sendMail', $data);

            unset($_SESSION['COLLECTION_ID']);

            return response_json_update_success(compact('item'));
        } catch (\Exception $e) {
            return response_json_update_fail();
        }
    });

    Route::get('/{id:\d+}/certificado', function ($id) {
        $article = first_or_fail(
            articles()
                ->where_author_id(logged_author()->id)
                ->where(TB_ARTICLES . '.id', $id)
                ->join(
                    TB_ARTICLES_PAYMENTS . ' as payment',
                    'payment.id',
                    '=',
                    TB_ARTICLES . '.payment_id'
                ),
            [
                TB_ARTICLES . '.id',
                TB_ARTICLES . '.title',
                TB_ARTICLES . '.author_id',
                'payment.finished_at as payment_date',
            ]
        );

        $author = find_or_fail(authors(), $article->author_id, ['name']);

        $authors_names = [
            md5(Str::upper($author->name)) => Str::title($author->name)
        ];

        $co_authors = articles_coauthors()
            ->where_article_id($article->id)
            ->where_null('deleted_at')
            ->get(['name']);

        foreach ($co_authors as $co_author) {
            $authors_names[md5(Str::upper($co_author->name))] = Str::title($co_author->name);
        }

        $authors_names = array_values($authors_names);

        $authors_names = array_map(function ($name) {
            return '<strong>' . $name . '</strong>';
        }, $authors_names);

        $last_author = array_pop($authors_names);
        $authors_names = $authors_names ? implode(', ', $authors_names) . ' e ' . $last_author : $last_author;

        $article->authors_names = $authors_names;

        $article->year = substr($article->payment_date, 0, 4);
        $article->month = substr($article->payment_date, 5, 2);
        $article->day = substr($article->payment_date, 8, 2);

        return view('layouts.certificate', (array) $article);
    });
});
