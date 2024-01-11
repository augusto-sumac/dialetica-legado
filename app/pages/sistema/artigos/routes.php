<?php

function article_invoice_link($id)
{
    return '<a href="' . url(ARTICLES_BASE_URL . '/' . $id . '/invoice/pdf') . '" class="btn btn-light btn-sm" target="invoice-pdf">
        <span class="fas fa-download"></span> Baixar PDF
    </a>';
}

function article_badge($status, $title = 'Status')
{
    return '<span class="badge bg-' . $status['color'] . '" title="' . $title . '">' . $status['label'] . '</span>';
}

function article_status_badge($row)
{
    $row = (array)$row;
    $status = article_status(array_get($row, 'status'));
    if (!isset($status['color'])) {
        $status = ['color' => 'muted', 'label' => 'Rascunho'];
    }

    return article_badge($status);
}

function article_payment_status_badge($row)
{
    $row = (array)$row;
    $status = article_payment_status(array_get($row, 'payment_status', -99));
    if (!isset($status['color'])) {
        $status = ['color' => 'muted', 'label' => array_get($row, 'payment_id') ? 'Aguardando' : ''];
    }

    if (array_get($row, 'status') < 30) {
        $status['color'] = 'warning';
        $status['label'] = 'Aguardando Análise do Artigo';
    }

    return article_badge($status, 'Status Pagamento');
}

function article_invoice_status_badge($row, $include_link = true)
{
    $row = (array)$row;
    $status = article_invoice_status(array_get($row, 'nf_status'));
    if (!isset($status['color'])) {
        $status = ['color' => 'muted', 'label' => array_get($row, 'invoice_id') ? 'Aguardando' : ''];
    }

    $link = '';

    if ($include_link && array_get($row, 'nf_id') && array_get($row, 'nf_status') === 'CONCLUIDO') {
        $link .= '<div class="mt-3">' . article_invoice_link(array_get($row, 'id')) . '</div>';
    }

    return article_badge($status, 'Status Nota Fiscal') . $link;
}

function article_on_change_status($item)
{
    $author = authors()->find($item->author_id);
    $collection = articles_collections()->find($item->collection_id);
    $created_by = DB::table(TB_USERS)->find($collection->created_by);

    // Não notificar autores quando a coletânea é de admin
    if ($collection->id > 1 && $created_by && $created_by->type == 'user') {
        return true;
    }

    // $view = array_get(
    //     [
    //         10 => 'refused',
    //         11 => 'refused',
    //         30 => 'payment',
    //         40 => 'production',
    //         // 50 => 'published',
    //     ],
    //     $item->status
    // );

    $view = array_get(
        [
            10 => 'd-8812fb637e054e54845c47546fad3f76',
            11 => 'd-8812fb637e054e54845c47546fad3f76',
            30 => 'd-3c708bf9614a4c328d53199e405682d1',
            40 => 'd-95822ef0355549a4a5dab83901cf7262',
            50 => 'd-77a23f82c2894e83a7a30a228fe9c265',
        ],
        $item->status
    );

    if ($view) {
        // $data = [
        //     'name' => get_first_name($author->name),
        //     'email' => $author->email,
        //     'title' => $item->title,
        //     'store_url' => $item->store_url,
        //     'store_coupon' => $item->store_coupon,
        // ];

        // $view = 'mail.article-change-status-' . $view;

        // $message = view($view, $data);

        $message = [
            'id' => $view,
            'vars' => [
                'first_name' => get_first_name($author->name),
                'titulo_trabalho' => $item->title,
                'titulo_coletanea' => $collection->name,
                'DOI_coletanea' => $collection->doi,
                'ISBN_fisico' => $collection->isbn,
                'ISBN_ebook' => $collection->isbn_e_book,
                'DOI_artigo' => $item->doi,
            ]
        ];

        add_job('sendMail', [
            'to' => env('DEV_MODE') ? 'marcioantunes.ma@gmail.com' : $author->email,
            'subject' => env('APP_NAME') . ' - ' . array_get([
                10 => 'Este é o resultado da avaliação do seu artigo',
                30 => 'Seu artigo foi aprovado para publicação Parabéns',
                40 => 'Seu pagamento foi confirmado e seu artigo será publicado',
                // 50 => 'Seu artigo foi publicado Parabéns',
            ], $item->status),
            'message' => $message
        ]);
    }
}

Route::group([
    'prefix' => ARTICLES_BASE_URL,
    'before' => 'auth'
], function () {
    Route::get('', function () {
        $datagrid = [
            'data' => [],
            'use_datatable' => true,
            'slot_form' => ARTICLES_VIEW_PATH . '.components.datagrid-filter',
            'options' => [
                'order' => [[0, 'desc']],
                'processing' =>  true,
                'serverSide' =>  true,
                'deferRender' => true,
                'ajax' => [
                    'url' => url(ARTICLES_BASE_URL),
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
                    'key' => 'nome_area',
                    'label' => 'Área',
                    'attrs' => ['class' => 'text-uppercase w-200px']
                ],
                [
                    'key' => 'created_at',
                    'label' => 'Dt Envio',
                    'attrs' => ['class' => 'text-end w-120px']
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

        return view(ARTICLES_VIEW_PATH . '.index', compact('datagrid'));
    });

    Route::post('', function () {
        $rows = articles()
            ->left_join(TB_USERS, TB_USERS . '.id', '=', TB_ARTICLES . '.author_id')
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
                TB_ARTICLES . '.status',
                TB_ARTICLES . '.payment_id',
                TB_ARTICLES . '.invoice_id',
                TB_ARTICLES . '.amount',
                'payment.service_status as payment_status',
                DB::raw('(case when payment.service_status = 2 then payment.finished_at else null end) as paid_at'),
                TB_USERS . '.name as nome_autor',
                TB_USERS . '.email as email_autor',
                DB::raw("coalesce(invoice.service_status, '') as nf_status"),
                'invoice.service_id as nf_id',
                DB::raw("coalesce(" . TB_ARTICLES_AREAS . ".name, 'Área Não Informada') as nome_area"),
                DB::raw("coalesce(" . TB_ARTICLES_SUBAREAS . ".name, 'Sub-área Não Informada') as nome_sub_area"),
                DB::raw("coalesce(" . TB_ARTICLES_SPECIALTIES . ".name, 'Especialidade Não Informada') as nome_especialidade"),
                DB::raw("coalesce(" . TB_ARTICLES_COLLECTIONS . ".name, 'Fluxo Contínuo') as nome_coletania")
            ]);

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
        if ($areas = input('filters.area')) {
            $rows->where(TB_ARTICLES . '.area_id', $areas);
        }

        if ($sub_areas = input('filters.sub_area')) {
            $rows->where(TB_ARTICLES . '.subarea_id', $sub_areas);
        }

        if ($especialidades = input('filters.especialidade')) {
            $rows->where(TB_ARTICLES . '.specialty_id', $especialidades);
        }

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
            $rows->where_between(TB_ARTICLES . '.created_at', datetimeToMySql($created_at_start) . ' 00:00', datetimeToMySql($created_at_end) . ' 23:59');
        } elseif ($created_at_start) {
            $rows->where(TB_ARTICLES . '.created_at', '>=', datetimeToMySql($created_at_start));
        } elseif ($created_at_end) {
            $rows->where(TB_ARTICLES . '.created_at', '<=', datetimeToMySql($created_at_end));
        }

        if (input('export')) {

            $content = [
                '<table>',
                '<thead>
                    <tr>
                        <th>#ID</th>
                        <th>Autor</th>
                        <th>Email Autor</th>
                        <th>Obra</th>
                        <th>Coletânea</th>
                        <th>Área</th>
                        <th>Subarea</th>
                        <th>Especialidade</th>
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
                    <td>' . $row->nome_coletania . '</td>
                    <td>' . $row->nome_area . '</td>
                    <td>' . $row->nome_sub_area . '</td>
                    <td>' . $row->nome_especialidade . '</td>
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

            $path = date('YmdHis') . '-relatorio-de-artigos-cadastrados-' . md5(time() . json_encode(input())) . '.xls';
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
                $row->status = article_status_badge($row);
                $row->payment_status = article_payment_status_badge($row);
                $row->nf_status = article_invoice_status_badge($row);

                $row->id = '<a href="' . url(ARTICLES_BASE_URL . '/' . $row->id) . '" title="Dados da Obra">' . str_pad_id($row->id) . '</a>';

                $row->created_at = datetimeFromMySql($row->created_at);
                $row->paid_at = datetimeFromMySql($row->paid_at);

                $row->nome_area = implode('<br />', [
                    '<small class="fw-bold">' . $row->nome_area . '</small>',
                    '<small class="text-gray-600">' . $row->nome_sub_area . '</small>',
                    '<small class="text-purple-600">' . $row->nome_especialidade . '</small>',
                    '<small class="text-teal-600">' . $row->nome_coletania . '</small>',
                ]);

                return $row;
            }, $rows->results),
            'sql' => DB::profile()
        ]);
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

    Route::get('/{id:\d+}', function ($id) {
        $item = articles()
            ->left_join(TB_USERS, TB_USERS . '.id', '=', TB_ARTICLES . '.author_id')
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
                TB_ARTICLES . '.collection_id',
                TB_ARTICLES . '.doi',
                TB_ARTICLES . '.title',
                TB_ARTICLES . '.resume',
                TB_ARTICLES . '.tags',
                TB_ARTICLES . '.attachment',
                TB_ARTICLES . '.created_at',
                TB_ARTICLES . '.status',
                TB_ARTICLES . '.amount',
                TB_ARTICLES . '.invoice_id',
                TB_ARTICLES . '.author_address_id',
                'payment.service_status as payment_status',
                DB::raw('(case when payment.service_status = 2 then payment.finished_at else null end) as paid_at'),
                DB::raw("json_unquote(json_extract(payment.service_response_payload, '$.Payment.Tid')) as tid"),
                DB::raw("json_unquote(json_extract(payment.service_response_payload, '$.Payment.ProofOfSale')) as nsu"),
                TB_USERS . '.name as nome_autor',
                DB::raw("coalesce(invoice.service_status, '') as nf_status"),
                DB::raw("json_unquote(json_extract(invoice.service_response_payload, '$.mensagem')) as nf_message"),
                'invoice.service_id as nf_id',
                DB::raw("coalesce(" . TB_ARTICLES_AREAS . ".name, 'Área Não Informado') as nome_area"),
                DB::raw("coalesce(" . TB_ARTICLES_SUBAREAS . ".name, 'Sub-área Não Informada') as nome_sub_area"),
                DB::raw("coalesce(" . TB_ARTICLES_SPECIALTIES . ".name, 'Especialidade Não Informada') as nome_especialidade"),
                DB::raw("coalesce(" . TB_ARTICLES_COLLECTIONS . ".name, 'Fluxo Contínuo') as nome_coletania"),
                DB::raw(TB_ARTICLES_COLLECTIONS . ".author_id as collection_author_id")
            ])
            ->first();

        if (!$item) {
            return redirect(ARTICLES_BASE_URL);
        }

        $item->status_badge = article_status_badge($item);
        $item->status_pagamento_badge = article_payment_status_badge($item);
        $item->nf_status_badge = article_invoice_status_badge($item);

        $item->author = authors()->find($item->author_id);

        $item->co_authors = articles_coauthors()
            ->where_article_id($item->id)
            ->where_not_null('name')
            ->where_null('deleted_at')
            ->get();

        $item->attachment = secure_json_decode($item->attachment);

        $item->collections = articles_collections()->where_null('deleted_at')->get();
        $item->collections_options = array_reduce($item->collections, function ($arr, $item) {
            $arr[$item->id] = $item->name;
            return $arr;
        }, []);

        $item->addresses = [];

        if (!$item->author_address_id) {
            $item->addresses = authors_addresses()
                ->where_user_id($item->author_id)
                ->where_null('deleted_at')
                ->get();
        }

        return view(ARTICLES_VIEW_PATH . '.form', (array)$item);
    });

    Route::put('/{id:\d+}/change-status', function ($id) {
        $item = find_or_fail(articles(), $id);

        $collection_author_id = $item->collection_id ? articles_collections()->find($item->collection_id)->author_id : null;

        $current = article_status($item->status, 1, $collection_author_id);

        $status = (int)input('status');

        if (
            $status !== 9 && ($status !== array_get($current, 'prev') &&
                $status !== array_get($current, 'next')
            )
        ) {
            return response_json_fail('O status selecionado não é válido!');
        }

        $available_status = article_status(null, 1, $collection_author_id);

        if (!in_array($status, array_map(fn ($i) => $i['value'], $available_status))) {
            return response_json_fail('O status selecionado não é válido!');
        }

        try {
            articles()->where_id($id)->update(compact('status'));

            $item->status = $status;

            article_on_change_status($item);

            return response_json_update_success([
                'status' => $status
            ]);
        } catch (\Exception $e) {
            return response_json_update_fail([$e->getMessage()]);
        }
    });

    Route::get('/{id:\d+}/invoice/status', function ($id) {
        try {
            $row = articles()
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
                $row->nf_status_badge = article_invoice_status_badge($row);
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
            articles()->where_id($id)->update(['invoice_id' => $invoice->id]);
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

            articles()->where_id($id)->update(['invoice_id' => $invoice_id]);

            return response_json_success('NFS-e aguardando processamento');
        } catch (\Exception $e) {
            return response_json_fail($e->getMessage());
        }
    });

    Route::post('/{id:\d+}/extra', function ($id) {
        find_or_fail(articles(), $id);

        validate([
            'collection_id' => 'exists:' . TB_ARTICLES_COLLECTIONS . ',id',
            'doi' => 'max:50',
        ]);

        $data = [
            'collection_id' => input('collection_id'),
            'doi' => input('doi'),
        ];

        if (input('collection_id')) {
            $collection = articles_collections()->find(input('collection_id'));
            if ($collection) {
                $data['area_id'] = $collection->area_id;
                $data['subarea_id'] = $collection->subarea_id;
                $data['specialty_id'] = $collection->specialty_id;
            }
        }

        try {
            articles()
                ->where_id($id)
                ->update($data);

            return response_json_update_success();
        } catch (\Exception $e) {
            return response_json_update_fail();
        }
    });

    Route::post('/update-author-address', function () {
        $author_id = input('author_id');
        $author_address_id = input('author_address_id');

        articles()
            ->where_author_id($author_id)
            ->where_null('author_address_id')
            ->update(compact('author_address_id'));

        return response_json(null);
    });

    // Redirects
    Route::get('/{id:\d+}/invoice/pdf', fn ($id) => redirect("/articles/{$id}/invoice"));
    Route::get('/{id:\d+}/certificado', fn ($id) => redirect("/articles/{$id}/certificate"));

    Route::delete('/{id:\d+}', function ($id) {
        user_can_delete();

        find_or_fail(articles(), $id);

        articles()->update(['deleted_at' =>  date('Y-m-d H:i:s')], $id);

        return response_json(null, 204);
    });
});
