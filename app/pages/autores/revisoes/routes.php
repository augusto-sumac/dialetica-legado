<?php


const AUTHOR_REVIEWS_STEPS = [
    [
        'key' => 'titulo',
        'label' => 'Título',
        'component' => AUTHOR_REVIEWS_VIEW_PATH . '.components.steps.titulo',
        'prev' => false,
        'next' => 'Arquivo'
    ],
    [
        'key' => 'arquivo',
        'label' => 'Arquivo',
        'component' => AUTHOR_REVIEWS_VIEW_PATH . '.components.steps.arquivo',
        'prev' => 'Título',
        'next' => 'Revisão',
    ],
    [
        'key' => 'revisao',
        'label' => 'Revisão',
        'component' => AUTHOR_REVIEWS_VIEW_PATH . '.components.steps.revisao',
        'prev' => 'Arquivo',
        'next' => 'Pagamento',
    ],
    // [
    //     'key' => 'pagamento',
    //     'label' => 'Pagamento',
    //     'component' => AUTHOR_REVIEWS_VIEW_PATH . '.components.steps.pagamento',
    //     'prev' => 'Arquivo',
    //     'next' => 'Confirmação',
    // ],
    [
        'key' => 'confirmacao',
        'label' => 'Confirmação',
        'component' => AUTHOR_REVIEWS_VIEW_PATH . '.components.steps.confirmacao'
    ],
];

function form_review_add($step)
{
    return view(AUTHOR_REVIEWS_VIEW_PATH . '.form', [
        'steps' => AUTHOR_REVIEWS_STEPS,
        'step' => (int) $step,
        'id' => null,
        'attachment' => [],
    ]);
}

function getReviewDueDate($review)
{
    $review = (object)$review;
    $dueDate = isset($review->paid_at) && $review->paid_at ? $review->paid_at : date('Y-m-d');
    return date('d/m/Y', strtotime('+30 days', strtotime($dueDate)));
}

Route::group([
    'prefix' => AUTHOR_REVIEWS_BASE_URL,
    'before' => 'author-auth'
], function () {
    Route::get('', function () {
        $rows = reviews()
            ->join(TB_USERS, TB_USERS . '.id', '=', TB_ARTICLES . '.author_id')
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
                // DB::raw('date_add(' . TB_ARTICLES . '.created_at, interval 30 day) as due_date'),
                TB_ARTICLES . '.status',
                TB_ARTICLES . '.amount',
                'payment.service_status as payment_status',
                DB::raw('(case when payment.service_status = 2 then payment.finished_at else null end) as paid_at'),
                TB_USERS . '.name as nome_autor'
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
            $row->due_date = getReviewDueDate($row);
            $row->paid_at = datetimeFromMySql($row->paid_at);

            $row->payment_status = (int) $row->payment_status !== 2 ? null : $row->payment_status;

            return $row;
        }, $rows);

        return view(AUTHOR_REVIEWS_VIEW_PATH . '.index', compact('rows'));
    });

    Route::get('/{id:\d+}', function ($id) {
        $item = reviews()
            ->join(TB_USERS, TB_USERS . '.id', '=', TB_ARTICLES . '.author_id')
            ->left_join(
                TB_ARTICLES_PAYMENTS . ' as payment',
                'payment.id',
                '=',
                TB_ARTICLES . '.payment_id'
            )
            ->where(TB_ARTICLES . '.author_id', logged_author()->id)
            ->where(TB_ARTICLES . '.id', $id)
            ->select([
                'payment.service_status as payment_status',
                DB::raw('(case when payment.service_status = 2 then payment.finished_at else null end) as paid_at'),
                TB_USERS . '.name as nome_autor',
                TB_ARTICLES . '.*',
                // DB::raw('date_add(' . TB_ARTICLES . '.created_at, interval 30 day) as due_date'),
            ])
            ->first();

        if (!$item) {
            return redirect(AUTHOR_REVIEWS_BASE_URL);
        }

        if ($item->status === 0) {
            return redirect(AUTHOR_REVIEWS_BASE_URL . '/editar/' . $item->id);
        }

        $item->due_date = getReviewDueDate($item);

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

        $item->attachment = secure_json_decode($item->attachment, true);
        $item->proof_attachment = secure_json_decode($item->proof_attachment, true);
        $item->final_attachment = secure_json_decode($item->final_attachment, true);

        $item->payment_status = (int) $item->payment_status !== 2 ? null : $item->payment_status;

        return view(AUTHOR_REVIEWS_VIEW_PATH . '.detail', (array)$item);
    });

    Route::get('/adicionar', fn ($step = 0) => form_review_add($step));

    Route::get('/editar/{id:\d+}', function ($id) {
        $item = reviews()
            ->where(TB_ARTICLES . '.author_id', logged_author()->id)
            ->where(TB_ARTICLES . '.id', $id)
            ->select([
                TB_ARTICLES . '.*'
            ])
            ->first();

        $item->author = authors()->find($item->author_id);

        $item->attachment = secure_json_decode($item->attachment, true);

        $steps = AUTHOR_REVIEWS_STEPS;

        if (in_array($item->status, [30, 31, 32])) {
            $steps[0]['prev'] = false;
            $steps[0]['next'] = false;
            $steps[1]['prev'] = false;
            $steps[1]['next'] = false;
            $steps[2]['prev'] = false;
            $steps[2]['next'] = false;
            $steps[3]['prev'] = false;
        }

        return view(AUTHOR_REVIEWS_VIEW_PATH . '.form', [
            'steps' => $steps,
            'step' => $item->status === 30 ? 3 : 0,
            'id' => $id
        ] + (array) $item);
    });

    Route::get('/review/{id:\d+}', function ($id) {
        $item = reviews()
            ->where(TB_ARTICLES . '.author_id', logged_author()->id)
            ->where(TB_ARTICLES . '.id', $id)
            ->select([
                TB_ARTICLES . '.*'
            ])
            ->first();

        $item->author = authors()->find($item->author_id);

        $item->attachment = secure_json_decode($item->attachment, true);

        return view(AUTHOR_REVIEWS_VIEW_PATH . '.review', (array)$item);
    });

    Route::post('/titulo', function () {
        $id = input('id');

        if ($id) {
            find_or_fail(reviews()->where_author_id(logged_author()->id), $id);
        }

        $rules = [
            'title' => 'required|max:255',
            'accept_publication_rules' => 'required|in:1',
        ];

        validate($rules);

        $item = array_only(input(), array_keys($rules));

        $item = array_merge(
            $item,
            [
                'status' => 0,
                'author_id' => logged_author()->id,
                'type_id' => 3,
            ]
        );

        try {
            if ($id) {
                reviews()->where_author_id(logged_author()->id)->update($item, $id);
            } else {
                $id = reviews()->insert_get_id($item);
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

    Route::post('/arquivo', function () {
        $id = (int) input('id');

        find_or_fail(reviews()->where_author_id(logged_author()->id), $id);

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

        $full_path = ROOT_PATH . $dir . '/' . $path;

        $upload_result = File::upload('file', ROOT_PATH . $dir, $path);

        if ($upload_result === false) {
            return response_json_update_fail(['stage' => 'upload', 'message' => 'Falha no envio do arquivo']);
        }

        $size = getFileSize(File::size($full_path));

        $target_path = ROOT_PATH . $dir . '/' . $path;

        $words_count_result = [];

        $removePdf = function () use ($target_path, $ext) {
            $pathToRemove = str_replace($ext, '.pdf', $target_path);
            if (file_exists($pathToRemove)) {
                unlink($pathToRemove);
            }
        };

        try {
            $words_count_result = get_words_count($full_path);
            $removePdf();
        } catch (\Exception $e) {
            $removePdf();
            return response_json_update_fail(['stage' => 'get_words_count', 'message' => $e->getMessage()]);
        }

        $words_count = $words_count_result['words'] ?? 0;

        if ($words_count < 100) {
            $removePdf();
            return response_json_update_fail(['stage' => 'convert_file', 'message' => 'O arquivo original está vazio ou é inválido']);
        }

        try {
            $result = Spaces::upload($target_path, (env('DEV_MODE', false) ? 'dev-' : '') . 'articles/' . $path);
            $path = array_get((array)$result, 'ObjectURL');
            if (file_exists($target_path)) {
                unlink($target_path);
            }

            if (!$path) {
                return response_json_update_fail('Falha ao gravar o arquivo');
            }
        } catch (\Exception $e) {
            if (file_exists($target_path)) {
                unlink($target_path);
            }
            return response_json_update_fail($e->getMessage());
        }

        DB::$connections = [];

        try {
            $type = articles_types()->find(3);

            $amount = max($type->price * $words_count, $type->minimum_price);

            reviews()
                ->where_author_id(logged_author()->id)
                ->update([
                    'words_count' => $words_count,
                    'gross_amount' => $amount,
                    'discount_amount' => 0,
                    'amount' => $amount,
                    'status' => 30
                ], $id);
        } catch (\Exception $e) {
            return response_json_update_fail(['stage' => 'update_words_count', 'err' => $e->getMessage()]);
        }

        try {
            reviews()
                ->where_author_id(logged_author()->id)
                ->update([
                    'attachment' => json_encode(compact('name', 'path', 'size'))
                ], $id);

            return response_json_update_success([
                'item' => compact(
                    'id',
                    'name',
                    'path',
                    'size',
                    'words_count',
                    'words_count_result'
                )
            ]);
        } catch (\Exception $e) {
            return response_json_update_fail(['stage' => 'update_review', 'err' => $e->getMessage()]);
        }
    });

    Route::get('/{id:\d+}/processamento/{job_id:\d+}', function ($id, $job_id) {
        $job = jobs()->find($job_id);

        if (!$job->finished_at) {
            return response_json(['status' => 'wait']);
        }

        if (!$job->finished_at) {
            return response_json(['status' => 'wait']);
        }

        return response_json(['status' => 'finished']);
    });

    Route::post('/revisao', function () {
        $id = input('id');

        find_or_fail(reviews()->where_author_id(logged_author()->id), $id);

        try {
            reviews()
                ->where_author_id(logged_author()->id)
                ->update([
                    'status' => 30
                ], $id);

            return response_json_update_success([
                'item' => compact('id')
            ]);
        } catch (\Exception $e) {
            return response_json_update_fail();
        }
    });
});
