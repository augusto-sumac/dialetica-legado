<?php

function review_invoice_link($id)
{
    return '<a href="' . url(REVIEWS_BASE_URL . '/' . $id . '/invoice/pdf') . '" class="btn btn-light btn-sm" target="invoice-pdf">
        <span class="fas fa-download"></span> Baixar PDF
    </a>';
}

function article_review_badge($status, $title = 'Status')
{
    return '<span class="badge bg-' . $status['color'] . '" title="' . $title . '">' . $status['label'] . '</span>';
}

function article_review_status_badge($row)
{
    $row = (array)$row;
    $status = article_status(array_get($row, 'status'));
    if (!isset($status['color'])) {
        $status = ['color' => 'muted', 'label' => 'Rascunho'];
    }

    return article_review_badge($status);
}

function article_review_payment_status_badge($row)
{
    $row = (array)$row;
    $status = article_payment_status(array_get($row, 'payment_status'));
    if (!isset($status['color'])) {
        $status = ['color' => 'muted', 'label' => array_get($row, 'payment_id') ? 'Aguardando' : ''];
    }
    return article_review_badge($status, 'Status Pagamento');
}

function article_review_invoice_status_badge($row)
{
    $row = (array)$row;
    $status = article_invoice_status(array_get($row, 'nf_status'));
    if (!isset($status['color'])) {
        $status = ['color' => 'muted', 'label' => array_get($row, 'invoice_id') ? 'Aguardando' : ''];
    }

    $link = '';

    if (array_get($row, 'nf_id') && array_get($row, 'nf_status') === 'CONCLUIDO') {
        $link .= '<div class="mt-3">' . review_invoice_link(array_get($row, 'id')) . '</div>';
    }

    return article_review_badge($status, 'Status Nota Fiscal') . $link;
}

Route::group([
    'prefix' => REVIEWS_BASE_URL,
    'before' => 'auth'
], function () {
    Route::get('', function () {
        $datagrid = [
            'data' => [],
            'use_datatable' => true,
            'slot_form' => REVIEWS_VIEW_PATH . '.components.datagrid-filter',
            'options' => [
                'order' => [[0, 'desc']],
                'processing' =>  true,
                'serverSide' =>  true,
                'deferRender' => true,
                'ajax' => [
                    'url' => url(REVIEWS_BASE_URL),
                    'type' => 'POST'
                ],
                'pageLength' => 50
            ],
            'columns' => [
                [
                    'key' => 'id',
                    'label' => '#ID',
                    'attrs' => ['class' => 'w-120px']
                ],
                [
                    'key' => 'nome_autor',
                    'label' => 'Autor',
                    'attrs' => ['class' => 'text-uppercase w-200px']
                ],
                [
                    'key' => 'title',
                    'label' => 'Obra',
                    'attrs' => ['class' => 'text-uppercase w-300px']
                ],
                [
                    'key' => 'created_at',
                    'label' => 'Dt Envio',
                    'attrs' => ['class' => 'text-end w-140px']
                ],
                [
                    'key' => 'due_date',
                    'label' => 'Dt Prev. Ent.',
                    'attrs' => ['class' => 'text-end w-140px']
                ],
                [
                    'key' => 'review_date',
                    'label' => 'Dt Conclusão',
                    'attrs' => ['class' => 'text-end w-140px']
                ],
                [
                    'key' => 'status',
                    'label' => 'Status',
                    'attrs' => ['class' => 'text-end w-180px']
                ],
                [
                    'key' => 'payment_status',
                    'label' => 'Pagto',
                    'attrs' => ['class' => 'text-end w-180px']
                ],
                [
                    'key' => 'nf_status',
                    'label' => 'NFS-e',
                    'attrs' => ['class' => 'text-end w-180px']
                ],
                [
                    'key' => 'paid_at',
                    'label' => 'Dt Pagto',
                    'attrs' => ['class' => 'text-end w-120px']
                ],
            ]
        ];

        $datagrid = make_datatable_options($datagrid);

        return view(REVIEWS_VIEW_PATH . '.index', compact('datagrid'));
    });

    Route::post('', function () {
        $rows = reviews()
            ->left_join(TB_USERS, TB_USERS . '.id', '=', TB_ARTICLES . '.author_id')
            ->left_join(
                TB_ARTICLES_PAYMENTS . ' as payment',
                'payment.id',
                '=',
                TB_ARTICLES . '.payment_id'
            )
            ->left_join(
                TB_ARTICLES_PAYMENTS . ' as invoice',
                'invoice.id',
                '=',
                TB_ARTICLES . '.invoice_id'
            )
            ->where_null(TB_ARTICLES . '.deleted_at')
            ->select([
                TB_ARTICLES . '.id',
                TB_ARTICLES . '.title',
                TB_ARTICLES . '.created_at',
                // DB::raw('date_add(' . TB_ARTICLES . '.created_at, interval 30 day) as due_date'),
                TB_ARTICLES . '.status',
                TB_ARTICLES . '.payment_id',
                TB_ARTICLES . '.invoice_id',
                TB_ARTICLES . '.review_date',
                TB_ARTICLES . '.amount',
                'payment.service_status as payment_status',
                DB::raw('coalesce(payment.finished_at, payment.created_at) as paid_at'),
                DB::raw("json_unquote(json_extract(payment.service_response_payload, '$.Payment.Tid')) as tid"),
                DB::raw("json_unquote(json_extract(payment.service_response_payload, '$.Payment.ProofOfSale')) as nsu"),
                TB_USERS . '.name as nome_autor',
                TB_USERS . '.email as email_autor',
                DB::raw("coalesce(invoice.service_status, '') as nf_status"),
                'invoice.service_id as nf_id'
            ])
            ->where_not_in(TB_ARTICLES . '.status', [0]);

        $total = clone $rows;

        // Sort
        $cols = input('columns', []);
        foreach (input('order', []) as $order) {
            $col = array_get($cols, $order['column'] . '.data');
            if ($col) {
                $rows->order_by(DB::raw($col . ' ' . strtolower($order['dir'])));
            }
        }

        // Search
        if ($search = input('search.value', input('search.search'))) {
            $rows->where(function ($q) use ($search) {
                if (is_numeric($search)) {
                    $q->or_where(TB_ARTICLES . '.id', (int) $search);
                } else {
                    $q->or_like(TB_ARTICLES . '.title', $search);
                    $q->or_like(TB_USERS . '.name', $search);
                }
            });
        }

        // Filters
        if ($status = input('filters.status')) {
            $rows->where_in(TB_ARTICLES . '.status', $status);
        }

        if ($status_pagamento = input('filters.status_pagamento')) {
            $rows->where(function ($sub) use ($status_pagamento) {
                $sub->where_in('payment.service_status', $status_pagamento);
                if (in_array(0, $status_pagamento)) {
                    $sub->or_where_null('payment.service_status');
                }
            });
        }

        if ($nf_status = input('filters.nf_status')) {
            $rows->where_in('invoice.service_status', $nf_status);
        }

        $created_at_start = input('filters.created_at.start');
        $created_at_end = input('filters.created_at.end');
        if ($created_at_start && $created_at_end) {
            $rows->where(
                fn ($q) => $q
                    ->where_between(TB_ARTICLES . '.created_at', datetimeToMySql($created_at_start) . ' 00:00', datetimeToMySql($created_at_end) . ' 23:59')
                    ->or_where_between(TB_ARTICLES . '.review_date', datetimeToMySql($created_at_start) . ' 00:00', datetimeToMySql($created_at_end) . ' 23:59')
            );
        } elseif ($created_at_start) {
            $rows->where(
                fn ($q) => $q
                    ->where(TB_ARTICLES . '.created_at', '>=', datetimeToMySql($created_at_start))
                    ->or_where(TB_ARTICLES . '.review_date', '>=', datetimeToMySql($created_at_start))
            );
        } elseif ($created_at_end) {
            $rows->where(
                fn ($q) => $q
                    ->where(TB_ARTICLES . '.created_at', '<=', datetimeToMySql($created_at_end))
                    ->or_where(TB_ARTICLES . '.review_date', '<=', datetimeToMySql($created_at_end))
            );
        }

        if (input('export')) {

            $content = [
                '<table>',
                '<thead>
                    <tr>
                        <th>#ID</th>
                        <th>Autor</th>
                        <th>Email Autor</th>
                        <th>Trabalho</th>
                        <th>Dt Envio</th>
                        <th>Status</th>
                        <th>Pagto</th>
                        <th>NFS-e</th>
                        <th>Dt Pagto</th>
                        <th>Vl Pago</th>
                    </tr>
                </thead>',
                '<tbody>'
            ];

            foreach ($rows->get() as $row) {
                $content[] = '<tr>
                    <td>' . $row->id . '</td>
                    <td>' . $row->nome_autor . '</td>
                    <td>' . $row->email_autor . '</td>
                    <td>' . $row->title . '</td>
                    <td>' . datetimeFromMySql($row->created_at) . '</td>
                    <td>' . article_status_badge($row) . '</td>
                    <td>' . article_payment_status_badge($row) . '</td>
                    <td>' . article_invoice_status_badge($row, false) . '</td>
                    <td>' . datetimeFromMySql($row->paid_at) . '</td>
                    <td>' . ($row->payment_status === 2 ? toMoney($row->amount) : '') . '</td>
                </tr>';
            }

            $content = implode('', $content) . '</tbody></table>';

            File::mkdir(STORAGE_PATH . 'reports');

            $path = date('YmdHis') . '-relatorio-de-revisoes-cadastrados-' . md5(time() . json_encode(input())) . '.xls';
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
                $row->status = article_review_status_badge($row);
                $row->payment_status = article_review_payment_status_badge($row);
                $row->nf_status = article_review_invoice_status_badge($row);

                $row->id = '<a href="' . url(REVIEWS_BASE_URL . '/' . $row->id) . '" title="Dados da Obra">' . str_pad_id($row->id) . '</a>';

                $row->created_at = datetimeFromMySql($row->created_at);
                $row->due_date = getReviewDueDate($row);
                $row->paid_at = datetimeFromMySql($row->paid_at);

                return $row;
            }, $rows->results),
            'sql' => DB::profile()
        ]);
    });

    Route::get('/{id:\d+}', function ($id) {
        $item = reviews()
            ->left_join(TB_USERS, TB_USERS . '.id', '=', TB_ARTICLES . '.author_id')
            ->left_join(
                TB_ARTICLES_PAYMENTS . ' as payment',
                'payment.id',
                '=',
                TB_ARTICLES . '.payment_id'
            )
            ->left_join(
                TB_ARTICLES_PAYMENTS . ' as invoice',
                'invoice.id',
                '=',
                TB_ARTICLES . '.invoice_id'
            )
            ->where(TB_ARTICLES . '.id', $id)
            ->select([
                TB_ARTICLES . '.id',
                TB_ARTICLES . '.author_id',
                TB_ARTICLES . '.title',
                TB_ARTICLES . '.attachment',
                TB_ARTICLES . '.proof_attachment',
                TB_ARTICLES . '.final_attachment',
                TB_ARTICLES . '.review_comment',
                TB_ARTICLES . '.review_date',
                TB_ARTICLES . '.created_at',
                // DB::raw('date_add(' . TB_ARTICLES . '.created_at, interval 30 day) as due_date'),
                TB_ARTICLES . '.status',
                TB_ARTICLES . '.amount',
                TB_ARTICLES . '.words_count',
                TB_ARTICLES . '.invoice_id',
                TB_ARTICLES . '.author_address_id',
                'payment.service_status as payment_status',
                DB::raw('coalesce(payment.finished_at, payment.created_at) as paid_at'),
                DB::raw("json_unquote(json_extract(payment.service_response_payload, '$.Payment.Tid')) as tid"),
                DB::raw("json_unquote(json_extract(payment.service_response_payload, '$.Payment.ProofOfSale')) as nsu"),
                TB_USERS . '.name as nome_autor',
                DB::raw("coalesce(invoice.service_status, '') as nf_status"),
                DB::raw("json_unquote(json_extract(invoice.service_response_payload, '$.mensagem')) as nf_message"),
                'invoice.service_id as nf_id',
            ])
            ->first();

        if (!$item) {
            return redirect(REVIEWS_BASE_URL);
        }

        $item->due_date = getReviewDueDate($item);

        $item->status_badge = article_review_status_badge($item);
        $item->status_pagamento_badge = article_review_payment_status_badge($item);
        $item->nf_status_badge = article_review_invoice_status_badge($item);

        $item->author = authors()->find($item->author_id);

        $item->attachment = secure_json_decode($item->attachment);
        $item->proof_attachment = secure_json_decode($item->proof_attachment);
        $item->final_attachment = secure_json_decode($item->final_attachment);

        return view(REVIEWS_VIEW_PATH . '.form', (array)$item);
    });

    Route::put('/{id:\d+}/change-status', function ($id) {
        $item = find_or_fail(reviews(), $id);

        $current = article_status($item->status);

        $status = (int)input('status');

        if (
            $status !== 9 && ($status !== array_get($current, 'prev') &&
                $status !== array_get($current, 'next'))
        ) {
            return response_json_fail('O status selecionado não é válido!');
        }

        if (!in_array($status, array_map(fn ($i) => $i['value'], article_status(null, $item->type_id)))) {
            return response_json_fail('O status selecionado não é válido!');
        }

        $update_data = compact('status');

        $next = article_status(array_get($current, 'next'));
        if (!array_get($next, 'next') && $status !== 9) {
            validate([
                'proof_attachment' => 'required',
                'final_attachment' => 'required',
                'review_comment' => 'required|max:1000'
            ], input());

            $update_data['review_date'] = date('Y-m-d H:i:s');
        }

        try {
            reviews()->update($update_data, $id);

            $author = authors()->find($item->author_id);

            $view = array_get(
                [
                    70 => 'finished',
                ],
                $status
            );

            if ($view) {
                // $data = [
                //     'name' => get_first_name($author->name),
                //     'email' => $author->email,
                //     'title' => $item->title
                // ];

                // $view = 'mail.review-' . $view;

                // $message = view($view, $data);

                $message = [
                    'id' => 'd-b267feb0478a43d5aa5b2e0edc7e292c',
                    'vars' => [
                        'first_name' => get_first_name($author->name),
                        'titulo_trabalho' => $item->title
                    ]
                ];

                add_job('sendMail', [
                    'to' => env('DEV_MODE') ? 'marcioantunes.ma@gmail.com' : $author->email,
                    'subject' => env('APP_NAME') . ' - ' . array_get([
                        70 => 'A revisão do seu trabalho foi concluída'
                    ], $status),
                    'message' => $message
                ]);
            }

            return response_json_update_success([
                'status' => $status
            ]);
        } catch (\Exception $e) {
            return response_json_update_fail();
        }
    });

    Route::post('/{id:\d+}/comment', function ($id) {
        find_or_fail(reviews(), $id);

        validate([
            'review_comment' => 'required|max:1000'
        ]);

        try {
            reviews()
                ->where_id($id)
                ->update([
                    'review_comment' => input('review_comment')
                ]);

            return response_json_update_success();
        } catch (\Exception $e) {
            return response_json_update_fail();
        }
    });

    Route::get('/{id:\d+}/invoice/status', function ($id) {
        try {
            $row = reviews()
                ->left_join(
                    TB_ARTICLES_PAYMENTS . ' as invoice',
                    'invoice.id',
                    '=',
                    TB_ARTICLES . '.invoice_id'
                )
                ->where(TB_ARTICLES . '.id', $id)
                ->first([
                    TB_ARTICLES . '.id',
                    'invoice.id as nf_id',
                    DB::raw("(
                        case 
                            when invoice.id is null 
                            then '' 
                            else coalesce(invoice.service_status, 'PROCESSANDO') 
                        end
                    ) as nf_status"),
                    DB::raw("json_unquote(json_extract(invoice.service_response_payload, '$.mensagem')) as nf_message")
                ]);

            if ($row->nf_status === 'PROCESSANDO') {
                $row->nf_status_badge = '<div class="spinner-border spinner-border-sm"></div> Processando ...';
            } else {
                $row->nf_status_badge = article_review_invoice_status_badge($row);
            }

            return response_json_success('', (array) $row);
        } catch (\Exception $e) {
            return response_json_fail($e->getMessage());
        }
    });

    Route::post('/{id:\d+}/invoice', function ($id) {
        $invoice = articles_invoices()
            ->where_source_id($id)
            ->where_operation('create')
            ->where_null('deleted_at')
            ->where_not_in('service_status', ['REJEITADO', 'CANCELADO'])
            ->order_by('id', 'desc')
            ->first();

        if ($invoice) {
            reviews()->where_id($id)->update(['invoice_id' => $invoice->id]);
            return response_json_success('NFS-e aguardando processamento');
        }

        try {
            $invoice_id = articles_invoices()->insert_get_id([
                'user_id' => logged_user()->id,
                'type' => 'invoice',
                'operation' => 'create',
                'source' => 'articles',
                'source_id' => $id,
                'service' => 'PlugNotas',
                'service_status' => 'PROCESSANDO'
            ]);

            reviews()->where_id($id)->update(['invoice_id' => $invoice_id]);

            return response_json_success('NFS-e aguardando processamento');
        } catch (\Exception $e) {
            return response_json_fail($e->getMessage());
        }
    });

    Route::get('/{id:\d+}/invoice/pdf', function ($id) {
        try {
            $row = reviews()
                ->join(
                    TB_ARTICLES_PAYMENTS . ' as invoice',
                    'invoice.id',
                    '=',
                    TB_ARTICLES . '.invoice_id'
                )
                ->where(TB_ARTICLES . '.id', $id)
                ->first([
                    TB_ARTICLES . '.id',
                    TB_ARTICLES . '.invoice_id',
                    'invoice.service_id'
                ]);

            if (!$row) {
                throw new NotFoundJsonResponseException(
                    response_json([
                        'message' => 'Não existe um registo com o ID informado!',
                    ], 404)
                );
            }
        } catch (\Exception $e) {
            return response_json_fail($e->getMessage());
        }

        require_once(ROOT_PATH . 'vendor/PlugNotas/PlugNotas.php');

        $service = (new PlugNotas(config('plug_notas_api_key'), config('plug_notas_sandbox')))->invoices();

        try {
            $file_base_path = '/tmp/' . $row->service_id . '.pdf';
            $file_full_path = $file_base_path;

            $service->download($row->service_id, $file_full_path);

            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="Obra-' . $row->id . '-' . $row->service_id . '.pdf"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file_full_path));
            readfile($file_full_path);
            exit();
        } catch (\Exception $e) {
            return response_json_fail($e->getMessage());
        }
    });

    Route::post('/{id:\d+}/arquivo', function ($id) {
        find_or_fail(reviews(), $id);

        $ext = '.' . File::extension(array_get($_FILES, 'file.name'));
        $name = Str::limit(str_replace($ext, '', array_get($_FILES, 'file.name')), 100, '');

        validate([
            'ext' => 'in:.doc,.docx,.pdf',
            'name' => 'required',
            'target' => 'required',
        ], array_merge(
            input(),
            compact('name', 'ext')
        ));

        $dir = 'storage/articles';

        $target = input('target');

        $path = 'article-' . $target . '-' . md5($id . '-' . time() . '-' . $name) . $ext;

        $full_path = ROOT_PATH . $dir . '/' . $path;

        File::upload('file', ROOT_PATH . $dir, $path);

        $size = getFileSize(File::size($full_path));

        $target_path = ROOT_PATH . $dir . '/' . $path;
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
            reviews()
                ->update([
                    $target . '_attachment' => json_encode(compact('name', 'path', 'size'))
                ], $id);

            return response_json_update_success([
                'item' => compact('id', 'name', 'path', 'size', 'target')
            ]);
        } catch (\Exception $e) {
            return response_json_update_fail($e->getMessage());
        }
    });

    Route::get('/download-report', function () {
        $file = input('file');

        if (preg_match('/http.*digitaloceanspaces/i', $file)) {
            return redirect($file);
        }

        header('Content-Description: File Transfer');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="' . substr($file, 0, -37) . '.xls"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize(STORAGE_PATH . $file));
        readfile(STORAGE_PATH . $file);
        exit();
    });

    Route::delete('/{id:\d+}', function ($id) {
        user_can_delete();

        find_or_fail(reviews(), $id);

        reviews()->update(['deleted_at' =>  date('Y-m-d H:i:s')], $id);

        return response_json(null, 204);
    });
});
