<?php

function get_minimum_withdrawal_amount()
{
    $minimum_withdrawal_amount = settings()->where_key('minimum_withdrawal_amount')->first();
    return $minimum_withdrawal_amount ? $minimum_withdrawal_amount->value : 200;
}

function get_available_affiliates_coupons_entries()
{
    $data = DB::table('affiliates_coupons_entries')
        ->where('author_id', logged_author()->id)
        ->whereNull('deleted_at')
        ->where('status', '!=', 'CA')
        ->orderBy('created_at')
        ->select([
            'author_id',
            'affiliate_coupon_id',
            DB::raw("SUM(CASE
                WHEN type = 'D' AND status != 'CA' AND paid_at IS NULL THEN 1
                ELSE 0
            END) AS pending"),
            DB::raw("SUM(CASE
                WHEN type = 'C' THEN
                    CASE
                        WHEN available_at <= CURDATE() AND status != 'CA' THEN ABS(amount)
                        ELSE 0
                    END
                WHEN type = 'D' AND status != 'CA' THEN ABS(amount) * -1
                ELSE 0
            END) AS amount")
        ])
        ->first();

    if ($data) {
        $data->pending = (int)$data->pending;
        $data->amount = (float)$data->amount;
    }

    return $data;
}

Route::group([
    'prefix' => AUTHOR_COMMISSIONS_BASE_URL,
    'before' => 'author-auth'
], function () {
    Route::get('', function () {
        $datagrid = [
            'data' => [],
            'use_datatable' => true,
            'filterable' => false,
            'options' => [
                'dom' => '<"table-responsive"t>',
                'order' => [[0, 'asc']],
                'processing' =>  true,
                'serverSide' =>  true,
                'deferRender' => true,
                'ajax' => [
                    'url' => url(AUTHOR_COMMISSIONS_BASE_URL),
                    'type' => 'POST'
                ],
                'pageLength' => 5000000
            ],
            'columns' => [
                [
                    'key' => 'created_at',
                    'label' => 'Data Cadastro',
                    'attrs' => ['class' => 'text-start w-200px'],
                    'sortable' => false
                ],
                [
                    'key' => 'effective_at',
                    'label' => 'Data Efetiva',
                    'attrs' => ['class' => 'text-start w-200px'],
                    'sortable' => false
                ],
                [
                    'key' => 'description',
                    'label' => 'Descrição',
                    'attrs' => ['class' => 'text-start w-300px'],
                    'sortable' => false
                ],
                [
                    'key' => 'amount',
                    'label' => 'Valor',
                    'attrs' => ['class' => 'text-end w-150px'],
                    'sortable' => false
                ],
                // [
                //     'key' => 'status',
                //     'label' => 'Status',
                //     'attrs' => ['class' => 'text-center w-120px'],
                //     'sortable' => false
                // ],
                [
                    'key' => 'total',
                    'label' => 'Saldo',
                    'attrs' => ['class' => 'text-end w-150px'],
                    'sortable' => false
                ]
            ],
        ];

        $datagrid = make_datatable_options($datagrid);

        $available = get_available_affiliates_coupons_entries();

        $available_total =  $available && $available->pending === 0 ? $available->amount : 0;

        $cash_out_available = $available_total >= get_minimum_withdrawal_amount();

        $data = compact('datagrid', 'cash_out_available', 'available_total');

        $bank_account = authors()->find(logged_author()->id, [
            'bank_account',
        ]);

        $bank_account = $bank_account ? (array) json_decode($bank_account->bank_account) : [];

        $data['account_document'] = array_get($bank_account, 'account_document');
        $data['account_name'] = array_get($bank_account, 'account_name');
        $data['account_pix_type'] = array_get($bank_account, 'account_pix_type');
        $data['account_pix_key'] = array_get($bank_account, 'account_pix_key');

        return view(AUTHOR_COMMISSIONS_VIEW_PATH . '.index', $data);
    });

    Route::post('', function () {
        $rows = affiliates_coupons_entries()
            ->left_join(TB_ARTICLES, TB_ARTICLES . '.id', '=', TB_AFFILIATES_COUPONS_ENTRIES . '.article_id')
            ->left_join(TB_AUTHORS, TB_AUTHORS . '.id', '=', TB_ARTICLES . '.author_id')
            ->where(TB_AFFILIATES_COUPONS_ENTRIES . '.author_id', logged_author()->id)
            ->where_null(TB_AFFILIATES_COUPONS_ENTRIES . '.deleted_at')
            ->where(TB_AFFILIATES_COUPONS_ENTRIES . '.status', '!=', 'CA')
            ->order_by(TB_AFFILIATES_COUPONS_ENTRIES . '.id')
            ->select([
                TB_AFFILIATES_COUPONS_ENTRIES . '.*',
                TB_ARTICLES . '.title as article_title',
                TB_AUTHORS . '.name as article_author_name'
            ]);

        // Data
        $rows = $rows->paginate(5000000);

        $total = 0;

        return response_json([
            'draw' => input('draw', 1),
            'recordsTotal' => count($rows->results),
            'recordsFiltered' => $rows->total,
            'data' => array_map(function ($row) use (&$total) {
                $row->description = $row->type === 'C' ? 'CRED - Comissão ref. cupom artigo' : 'DEB - Transferência de saldo';

                $amount = $row->type === 'C' ? abs($row->amount) : abs($row->amount) * -1;

                if ($row->type === 'C') {
                    $total += $row->amount;

                    $row->description =  $row->description . '<div class="text-muted">
                        <small>
                            Autor: ' . $row->article_author_name . '
                            <br />
                            Artigo: ' . $row->article_title . '
                        </small>
                    </div>';

                    $effective_at = $row->available_at;

                    $available_at_int = (int) str_replace('-', '', $row->available_at);
                    if ((int)date('Ymd') >= $available_at_int) {
                        $row->amount = '<span class="fw-bold text-success">' . toMoney($amount) . '</span>';
                    } else {
                        $row->amount = '<abbr data-bs-toggle="tooltip" title="Este saldo estará disponível em ' . datetimeFromMySql($row->available_at) . '" class="fw-bold text-muted">' . toMoney($amount) . '</abbr>';
                    }
                } else {
                    $total -= abs($row->amount);

                    $effective_at = $row->paid_at ? $row->paid_at : date('Y-m-d', strtotime('+5 days'));

                    $row->amount = '<span class="fw-bold text-danger">' . toMoney($amount) . '</span>';

                    $row->description = '<div>
                        ' . affiliates_withdraw_status_badge($row) . ' 
                        <span class="ms-2">' . $row->description . '</span>
                    </div>';

                    if ($row->status === 'FI') {
                        $row->payment_attachment = secure_json_decode($row->payment_attachment);
                        if (array_get($row->payment_attachment, 'path')) {
                            $row->description .= '<div>
                                <a href="/download-article-attachment?path=' . $row->payment_attachment['path'] . '&name=' . $row->payment_attachment['name'] . '" target="_blank">
                                    Comprovante de pagamento
                                </a>
                            </div>';
                        }
                    }
                }

                // $row->status = affiliates_withdraw_status_badge($row); //payment_attachment

                $row->effective_at = substr(datetimeFromMySql($effective_at), 0, 10);
                $row->created_at = substr(datetimeFromMySql($row->created_at), 0, 10);
                $row->total = '<span class="fw-bold">' . toMoney($total) . '</span>';
                return $row;
            }, $rows->results),
            // 'sql' => DB::profile()
        ]);
    });

    Route::get('check-available', function () {
        $available = get_available_affiliates_coupons_entries();

        $available_total =  $available && $available->pending === 0 ? $available->amount : 0;

        $cash_out_available = $available_total >= get_minimum_withdrawal_amount();

        return response_json(compact('cash_out_available', 'available_total', 'available'));
    });

    Route::post('/saque', function () {
        $available = get_available_affiliates_coupons_entries();

        if (!$available) {
            return response_json_fail('Não há saldo suficiente para saque!');
        }

        if ($available->pending) {
            return response_json_fail('Já existe uma solicitação de saque pendente! Só é permito uma solicitação por vez!');
        }

        $minimum_withdrawal_amount = settings()->where_key('minimum_withdrawal_amount')->first();
        $minimum_withdrawal_amount = $minimum_withdrawal_amount ? $minimum_withdrawal_amount->value : 200;

        if ($available->amount < $minimum_withdrawal_amount) {
            return response_json_fail('Seu saldo atual(R$ ' . toMoney($available->amount) . ') é menor que o valor mínimo para saque(' . toMoney($minimum_withdrawal_amount) . ')!');
        }

        try {
            affiliates_coupons_entries()
                ->insert([
                    'author_id' => $available->author_id,
                    'affiliate_coupon_id' => $available->affiliate_coupon_id,
                    'type' => 'D',
                    'amount' => $available->amount,
                    'status' => 'PE'
                ]);


            $available = get_available_affiliates_coupons_entries();

            $available_total =  $available && $available->pending === 0 ? $available->amount : 0;

            $cash_out_available = $available_total >= get_minimum_withdrawal_amount();

            return response_json_success('Saque solicitado com sucesso', compact('cash_out_available', 'available_total'));
        } catch (\Exception $e) {
            return response_json_fail('Falha ao solicitar o saque! Tente mais tarde', [$e]);
        }
    });
});
