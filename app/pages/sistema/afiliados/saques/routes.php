<?php

function affiliates_withdraw_status_badge($row)
{
    $row = (array) $row;
    $status = affiliates_withdraw_status(array_get($row, 'status'));
    if (!isset($status['color'])) {
        $status = ['color' => 'muted', 'label' => 'N/A'];
    }

    return '<span class="badge bg-' . $status['color'] . '" title="Status Saque">' . $status['label'] . '</span>';
}

Route::group([
    'prefix' => AFFILIATES_BASE_URL,
    'before' => 'auth'
], function () {
    Route::get('', function () {
        $datagrid = make_datatable_options([
            'data' => [],
            'use_datatable' => true,
            'slot_form' => AFFILIATES_VIEW_PATH . '.components.datagrid-filter',
            'options' => [
                'order' => [[1, 'asc']],
                'processing' =>  true,
                'serverSide' =>  true,
                'deferRender' => true,
                'ajax' => [
                    'url' => url(AFFILIATES_BASE_URL),
                    'type' => 'POST'
                ],
                'pageLength' => 50,
            ],
            'columns' => [
                [
                    'key' => 'id',
                    'label' => '',
                    'attrs' => ['class' => 'w-50px p-0 text-center'],
                    'sortable' => false
                ],
                [
                    'key' => 'created_at',
                    'label' => 'Data Sol.',
                    'attrs' => ['class' => 'w-120px']
                ],
                [
                    'key' => 'amount',
                    'label' => 'Valor',
                    'attrs' => ['class' => 'w-100px text-end'],
                ],
                [
                    'key' => 'author_name',
                    'label' => 'Beneficiário',
                    'attrs' => ['class' => 'w-250px text-ellipsis']
                ],
                [
                    'key' => 'paid_at',
                    'label' => 'Data Pag.',
                    'attrs' => ['class' => 'w-120px']
                ],
                [
                    'key' => 'status',
                    'label' => 'Status',
                    'attrs' => ['class' => 'w-100px text-end'],
                ],
            ]
        ]);

        return view(AFFILIATES_VIEW_PATH . '.index', compact('datagrid'));
    });

    Route::post('', function () {
        $rows = affiliates_coupons_entries()
            ->join(TB_AUTHORS, TB_AUTHORS . '.id', '=', TB_AFFILIATES_COUPONS_ENTRIES . '.author_id')
            ->where(TB_AFFILIATES_COUPONS_ENTRIES . '.type', 'D')
            ->where_null(TB_AFFILIATES_COUPONS_ENTRIES . '.deleted_at')
            ->select([
                TB_AFFILIATES_COUPONS_ENTRIES . '.id',
                TB_AFFILIATES_COUPONS_ENTRIES . '.amount',
                TB_AFFILIATES_COUPONS_ENTRIES . '.status',
                TB_AFFILIATES_COUPONS_ENTRIES . '.created_at',
                TB_AFFILIATES_COUPONS_ENTRIES . '.paid_at',
                TB_AFFILIATES_COUPONS_ENTRIES . '.author_id',
                TB_AFFILIATES_COUPONS_ENTRIES . '.payment_attachment',
                TB_AUTHORS . '.name as author_name',
                TB_AUTHORS . '.bank_account',
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
                    $q->or_where(TB_AFFILIATES_COUPONS_ENTRIES . '.id', (int) $search);
                    $q->or_like(TB_AFFILIATES_COUPONS_ENTRIES . '.amount', toNumber($search));
                } else {
                    $q->or_like(TB_AUTHORS . '.name', $search);
                }
            });
        }

        // Filters
        if ($status = input('filters.status')) {
            $rows->where_in(TB_AFFILIATES_COUPONS_ENTRIES . '.status', $status);
        }

        $created_at_start = input('filters.created_at.start');
        $created_at_end = input('filters.created_at.end');
        if ($created_at_start && $created_at_end) {
            $rows->where_between(TB_AFFILIATES_COUPONS_ENTRIES . '.created_at', datetimeToMySql($created_at_start) . ' 00:00', datetimeToMySql($created_at_end) . ' 23:59');
        } elseif ($created_at_start) {
            $rows->where(TB_AFFILIATES_COUPONS_ENTRIES . '.created_at', '>=', datetimeToMySql($created_at_start));
        } elseif ($created_at_end) {
            $rows->where(TB_AFFILIATES_COUPONS_ENTRIES . '.created_at', '<=', datetimeToMySql($created_at_end));
        }

        $paid_at_start = input('filters.paid_at.start');
        $paid_at_end = input('filters.paid_at.end');
        if ($paid_at_start && $paid_at_end) {
            $rows->where_between(TB_AFFILIATES_COUPONS_ENTRIES . '.paid_at', datetimeToMySql($paid_at_start) . ' 00:00', datetimeToMySql($paid_at_end) . ' 23:59');
        } elseif ($paid_at_start) {
            $rows->where(TB_AFFILIATES_COUPONS_ENTRIES . '.paid_at', '>=', datetimeToMySql($paid_at_start));
        } elseif ($paid_at_end) {
            $rows->where(TB_AFFILIATES_COUPONS_ENTRIES . '.paid_at', '<=', datetimeToMySql($paid_at_end));
        }

        // Data
        $rows = $rows->paginate(input('per_page', 15));

        return response_json([
            'draw' => input('draw', 1),
            'recordsTotal' => $total->count(),
            'recordsFiltered' => $rows->total,
            'data' => array_map(function ($row) {
                $row->status_badge = affiliates_withdraw_status($row->status);
                $row->created_at = datetimeFromMySql($row->created_at);
                $row->paid_at = datetimeFromMySql($row->paid_at);
                $row->amount = toMoney(abs($row->amount));
                $row->bank_account = (object) secure_json_decode($row->bank_account);
                $row->payment_attachment = empty($row->payment_attachment) ? '{}' : json_decode($row->payment_attachment);
                $json = json_encode($row);

                $row->author_name = '<a href="' . url('sistema/autores/' . $row->author_id) . '" target="author">' . $row->author_name . '</a>';

                $row->status = affiliates_withdraw_status_badge($row);

                $row->id = '<button type="button" class="btn btn-sm btn-dark btn-show-payment-modal" data-row=\'' . $json . '\' title="Detalhes da solicitação">
                    <span class="fas fa-eye"></span>
                </button>';
                return $row;
            }, $rows->results)
        ]);
    });

    Route::post('/confirm-payment', function () {
        $id = (int) input('id');

        find_or_fail(affiliates_coupons_entries(), $id);

        $ext = '.' . File::extension(array_get($_FILES, 'file.name'), 'doc');
        $name = Str::limit(str_replace($ext, '', array_get($_FILES, 'file.name')), 100, '');

        array_set($GLOBALS, 'APP_INPUT_DATA.name', $name);
        array_set($GLOBALS, 'APP_INPUT_DATA.ext', $ext);

        validate([
            'ext' => 'in:.pdf,.png,.jpg,.jpeg'
        ]);

        $dir = 'storage/affiliates';

        $path = 'payment-attachment-' . md5($id . '-' . time() . '-' . $name) . $ext;

        File::upload('file', ROOT_PATH . $dir, $path);

        $size = getFileSize(File::size(ROOT_PATH . $dir . '/' . $path));

        $target_path = ROOT_PATH . $dir . '/' . $path;
        try {
            $result = Spaces::upload($target_path, (env('DEV_MODE', false) ? 'dev-' : '') . 'affiliates/' . $path);
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
            affiliates_coupons_entries()
                ->update([
                    'paid_at' => date('Y-m-d H:m:s'),
                    'paid_by_user_id' => logged_user()->id,
                    'status' => 'FI',
                    'payment_attachment' => json_encode(compact('name', 'path', 'size'))
                ], $id);

            return response_json_update_success([
                'item' => compact('id', 'name', 'path', 'size')
            ]);
        } catch (\Exception $e) {
            return response_json_update_fail($e->getMessage());
        }
    });
});
