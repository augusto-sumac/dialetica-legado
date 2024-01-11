<?php

function find_or_fail_collection($id)
{
    $referer = array_get($_SERVER, 'HTTP_REFERER', '');

    if (preg_match('/\/sistema/i', $referer)) {
        return find_or_fail(articles_collections(), $id);
    }

    return find_or_fail(
        articles_collections()
            ->raw_where('exists(' . get_select_articles_collections_authors() . ')'),
        $id
    );
}

function collection_change_status($id, $status = null)
{
    $item = find_or_fail_collection($id);

    if (!$status = input('status', $status)) {
        return response_json_update_fail(['message' => 'Status inválido']);
    }

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
        return response_json_update_fail([$e->getMessage()]);
    }
}

function collection_approve_or_reject_article($collection_id, $article_id, $status)
{
    find_or_fail_collection($collection_id);

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

function makeCollectionAppStoreId()
{
    $p1 = Str::upper(Str::random(10, 'alpha'));
    $p2 = Str::upper(Str::random(10, 'alpha'));
    $p3 = Str::upper(Str::random(10, 'alpha'));
    return "__{$p1}APP{$p2}DIAL{$p3}__";
}

function uuid($data = null)
{
    // Generate 16 bytes (128 bits) of random data or use the data passed into the function.
    $data = $data ?? random_bytes(16);
    assert(strlen($data) == 16);

    // Set version to 0100
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    // Set bits 6-7 to 10
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

    // Output the 36 character UUID.
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

Route::group([
    'prefix' => 'collections',
    'before' => 'is-logged'
], function () {
    Route::get('/{id:\d+}/articles', function ($id) {
        $collection = find_or_fail_collection($id);

        $articles = articles()
            ->join(TB_AUTHORS, TB_AUTHORS . '.id', '=', TB_ARTICLES . '.author_id')
            ->left_join(
                TB_ARTICLES_PAYMENTS . ' as invoice',
                'invoice.id',
                '=',
                TB_ARTICLES . '.invoice_id'
            )
            ->where(TB_ARTICLES . '.status', '>=', 10)
            ->where_collection_id($id)
            ->where_null(TB_ARTICLES . '.deleted_at')
            ->order_by(TB_ARTICLES . '.created_at')
            ->get([
                TB_ARTICLES . '.id',
                TB_ARTICLES . '.collection_id',
                TB_ARTICLES . '.title',
                TB_ARTICLES . '.created_at',
                TB_ARTICLES . '.status',
                DB::raw('(case when ' . TB_ARTICLES . '.status >= 32 then 1 else 0 end) as is_paid'),
                TB_AUTHORS . '.name as author_name',
                DB::raw("(case 
                    when invoice.id is null 
                    then 0
                    when coalesce(invoice.service_status, '') = 'CONCLUIDO' 
                    then 0 
                    else 1 
                end) as await_invoice"),
                'invoice.service_id as service_invoice_id',
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
            } elseif ($row->status === 32 || $row->status === 41) {
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

            $row->allow_approve = $row->status === 32 || $row->status === 41;

            $row->await_invoice = (int) $row->await_invoice === 1;

            $row->service_invoice_id = $row->await_invoice ? null : $row->service_invoice_id;

            $row->allow_dial_approve = $row->status === 20;

            return $row;
        }, $articles);

        $settings = settings()->lists('value', 'key');
        $paid_articles_limit = array_get($settings, 'minimum_articles_in_collection', 4);

        $paid_articles = count(array_filter($articles, fn ($r) => $r->is_paid && $r->status >= 41));
        $allow_publish = $paid_articles >= $paid_articles_limit && $collection->status === 'AP';

        return response_json(compact('articles', 'paid_articles', 'paid_articles_limit', 'allow_publish'));
    });

    Route::put('/{id:\d+}/publish', fn ($id) => collection_change_status($id, 'WP'));

    Route::put('/{id:\d+}/change-status', fn ($id) => collection_change_status($id));

    Route::put('/{id:\d+}/approve/{id:\d+}', fn ($collection_id, $article_id) => collection_approve_or_reject_article($collection_id, $article_id, 42));

    Route::put('/{id:\d+}/reject/{id:\d+}', fn ($collection_id, $article_id) => collection_approve_or_reject_article($collection_id, $article_id, 11));

    Route::get('/author-by-document/{document:\d+}', function ($document) {
        $item = first_or_fail(
            authors()
                ->where_document(only_numbers($document))
                ->or_where('document', mask($document, 'cpf_cnpj')),
            ['id', DB::raw('upper(trim(name)) as name')]
        );

        return response_json((array)$item);
    });

    Route::put('/{id:\d+}/expires/{expires_at}', function ($id, $expires_at) {
        try {
            if (date_create_from_format('Y-m-d', $expires_at)) {
                articles_collections()
                    ->update(compact('expires_at'), $id);
            }

            return response_json_update_success();
        } catch (\Exception $e) {
            return response_json_update_fail();
        }
    });

    Route::put('/{id:\d+}/organizer/{id:\d+}', function ($id, $author_id) {
        try {
            InsertOrUpdateMany::prepare(
                TB_ARTICLES_COLLECTIONS_AUTHORS,
                ['collection_id', 'author_id'],
                [[
                    'collection_id' => $id,
                    'author_id' => $author_id,
                    'deleted_at' => null
                ]],
                ['deleted_at'],
                null,
                true
            );

            return response_json_update_success();
        } catch (\Exception $e) {
            return response_json_update_fail();
        }
    });

    Route::get('/{id:\d+}/organizer/{id:\d+}/info', function ($id, $author_id) {
        try {
            $author = find_or_fail(authors(), $author_id, ['id', 'name', 'document', 'email', 'role', 'curriculum', 'curriculum_url']);

            $author->document = mask($author->document, 'cpf_cnpj');

            $author->collection_id = (int) $id;

            return response_json($author);
        } catch (\Exception $e) {
            return response_json_update_fail();
        }
    });

    Route::delete('/{id:\d+}/organizer/{id:\d+}', function ($id, $author_id) {
        try {
            articles_collections_authors()
                ->where_collection_id($id)
                ->where_author_id($author_id)
                ->update(['deleted_at' => date('Y-m-d H:i:s')]);

            return response_json_update_success();
        } catch (\Exception $e) {
            return response_json_update_fail();
        }
    });

    Route::put('/cover-image', function () {
        try {
            $cover_image = input('cover_image');

            File::mkdir(ROOT_PATH . 'public/uploads/collections');

            $file_chunks = explode(";base64,", $cover_image);

            $fileType = explode("image/", $file_chunks[0]);
            $image_type = $fileType[1];
            $base64Img = base64_decode($file_chunks[1]);

            // $file = null;
            // while (!$file || file_exists(ROOT_PATH . $file)) {
            //     $file = '/public/uploads/collections/' . uuid() . '.' . $image_type;
            // }

            File::mkdir(STORAGE_PATH . 'cover-image');

            $path = date('YmdHis') . '-' . md5(time() . uuid() . json_encode(input())) . '.' . $image_type;
            $file = STORAGE_PATH . 'cover-image/' . $path;

            file_put_contents($file, $base64Img);

            $target_path = $file;
            try {
                $result = Spaces::upload($target_path, (env('DEV_MODE', false) ? 'dev-' : '') . 'cover-images/' . $path);
                $path = array_get((array)$result, 'ObjectURL');
                @unlink($target_path);
                if (!$path) {
                    return response_json_update_fail('Falha ao gravar o arquivo');
                }
            } catch (\Exception $e) {
                @unlink($target_path);
                return response_json_update_fail($e->getMessage());
            }

            if (input('id')) {
                try {
                    articles_collections()
                        ->update([
                            'cover_image' => $path
                        ], input('id'));
                } catch (\Exception $e) {
                    // ...
                }
            }

            return response_json_success('ok', [
                'cover_image' => "$path"
            ]);
        } catch (\Exception $e) {
            return response_json_fail(null);
        }
    });

    Route::get('/check-pending-invites', function () {
        if (!logged_author()) {
            return response_json(null);
        }

        $main_table = TB_ARTICLES_COLLECTIONS;
        $join_table = TB_ARTICLES_COLLECTIONS_AUTHORS;
        $author_table = TB_AUTHORS;

        $collections = articles_collections()
            ->join($join_table, "{$join_table}.collection_id", '=', "{$main_table}.id")
            ->join($author_table, "{$author_table}.id", '=', "{$main_table}.author_id")
            ->where("{$join_table}.author_id", logged_author()->id)
            ->where_null("{$join_table}.accepted_at")
            ->get([
                "{$main_table}.id",
                "{$main_table}.name",
                "{$main_table}.created_at",
                DB::raw("(case when length({$author_table}.role) > 0 then 0 else 1 end) as require_role"),
                DB::raw("(case when length({$author_table}.curriculum) > 0 then 0 else 1 end) as require_curriculum"),
                DB::raw("(case when length({$author_table}.curriculum_url) > 0 then 0 else 1 end) as require_curriculum_url"),
            ]);

        return response_json($collections);
    });

    Route::get('/accept', function () {
        if (!logged_author()) {
            return redirect('/');
        }

        $main_table = TB_ARTICLES_COLLECTIONS;
        $join_table = TB_ARTICLES_COLLECTIONS_AUTHORS;
        $author_table = TB_AUTHORS;
        $area_table = TB_ARTICLES_AREAS;
        $subarea_table = TB_ARTICLES_SUBAREAS;
        $specialty_table = TB_ARTICLES_SPECIALTIES;

        $collections = articles_collections()
            ->join($join_table, "{$join_table}.collection_id", '=', "{$main_table}.id")
            ->join($author_table, "{$author_table}.id", '=', "{$main_table}.author_id")
            ->join($area_table, "{$area_table}.id", '=', "{$main_table}.area_id")
            ->join($subarea_table, "{$subarea_table}.id", '=', "{$main_table}.subarea_id")
            ->join($specialty_table, "{$specialty_table}.id", '=', "{$main_table}.specialty_id")
            ->where("{$join_table}.author_id", logged_author()->id)
            ->where_null("{$join_table}.accepted_at")
            ->get([
                "{$main_table}.id",
                "{$main_table}.name",
                "{$main_table}.description",
                "{$main_table}.created_at",
                "{$area_table}.name as area_name",
                "{$subarea_table}.name as subarea_name",
                "{$specialty_table}.name as specialty_name"
            ]);

        return view('pages.collections.accept', compact('collections'));
    });

    Route::put('/{id:\d+}/accept', function ($id) {
        if (!logged_author()) {
            return response_json(null);
        }

        articles_collections_authors()
            ->where('collection_id', $id)
            ->where('author_id', logged_author()->id)
            ->update([
                'accepted_at' => date('Y-m-d H:i:s')
            ]);

        sync_authors_coupons($id);

        return response_json(null);
    });

    Route::post('/organizer-data', function () {
        $rules = [
            'role' => 'required',
            'curriculum_url' => 'required|max:255',
            'curriculum' => 'required|max:4000'
        ];

        validate($rules);

        try {
            authors()
                ->update(
                    [
                        'role' => input('role'),
                        'curriculum_url' => input('curriculum_url'),
                        'curriculum' => input('curriculum'),
                    ],
                    logged_author()->id
                );

            $user = $_SESSION['author'];
            $user->role = input('role');
            $user->curriculum_url = input('curriculum_url');
            $user->curriculum = input('curriculum');

            $_SESSION['author'] = $user;

            return response_json_success(null);
        } catch (\Exception $e) {
            return response_json_fail(null);
        }
    });

    Route::post('/organizer', function () {
        $rules = [
            'document' => 'required|max:30',
            // 'document' => 'required|cpf',
            'name' => 'required|max:200',
            'email' => 'required|email|max:200',
        ];

        validate($rules);

        $exists = authors()->where_email(input('email'))->first();

        if ($exists) {
            return response_json([
                'message' => 'Existem campos inválidos',
                'errors' => ['email' => 'Já existe um registro com o email informado!']
            ], 422);
        }

        $user = array_only(input(), ['name', 'document', 'email']);

        $user['type'] = 'author';
        $user['password'] = Hash::make(Str::random(8) . md5(time()));

        $recovery_password_token = Str::random(8);

        while (
            authors()->where_recovery_password_token($recovery_password_token)->first()
        ) {
            $recovery_password_token = Str::random(8);
        }

        $user['recovery_password_token'] = $recovery_password_token;

        try {
            $user['id'] = authors()->insert_get_id($user);
        } catch (\Exception $e) {
            return response_json(array(
                'message' => 'Não foi possível cadastrar o organizador: ' . $e->getMessage(),
            ), 422);
        }

        try {
            $unique_name = get_first_name(logged_author()->id ? logged_author()->name : logged_user()->name);

            $link_organizador = url('/auth/register/' . $recovery_password_token);

            $message = [
                'id' => 'd-1a356757e6a8490d9abf701efa68c00d',
                'vars' => [
                    'convidado_first_name' => get_first_name($user['name']),
                    'unique_name' => $unique_name,
                    'titulo_coletanea' => input('collection_name'),
                    'link_organizador' => $link_organizador
                ]
            ];

            add_job('sendMail', [
                'to' => env('DEV_MODE') ? 'marcioantunes.ma@gmail.com' : $user['email'],
                'subject' => 'Convite para organizar obra',
                'message' => $message
            ]);
        } catch (\Exception $e) {
            // ...
        }

        return response_json(array(
            'message' => 'Enviamos um email com instruções para ' . $user['name'],
            'author' => array_only($user, ['id', 'name'])
        ));
    });
});
