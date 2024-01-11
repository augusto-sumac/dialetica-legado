<?php
require_once(__DIR__ . '/bootstrap.php');
require_once(dirname(__DIR__) . '/vendor/CieloPayment.php');
require_once(dirname(__DIR__) . '/app/pages/autores/payments/helpers.php');

function job()
{
    logg('Find next payment');

    $items = articles_payments()
        ->join(TB_ARTICLES, TB_ARTICLES . '.id', '=', TB_ARTICLES_PAYMENTS . '.source_id')
        ->where(function ($q) {
            $q
                ->where(function ($sub) {
                    $sub
                        ->raw_where("json_unquote(json_extract(service_response_payload, '$.Payment.Type')) = 'Pix'")
                        ->where(
                            fn ($s) => $s
                                ->where_null('status_checked_at')
                                ->or_where('status_checked_at', '<=', date('Y-m-d H:i:s', strtotime('-30 minutes')))
                        )
                        ->where_in('service_status', ['NA', 'PROCESSANDO', '12', 'ERROR']);
                })
                ->or_where(function ($sub) {
                    $sub
                        ->raw_where("json_unquote(json_extract(service_response_payload, '$.Payment.Type')) = 'Boleto'")
                        ->where(
                            fn ($s) => $s
                                ->where_null('status_checked_at')
                                ->or_where('status_checked_at', '<=', date('Y-m-d H:i:s', strtotime('-60 minutes')))
                        )
                        ->where_in('service_status', ['NA', 'PROCESSANDO', '1', 'ERROR']);
                });
        })
        ->take(100)
        ->get([
            TB_ARTICLES_PAYMENTS . '.*',
            DB::raw("json_unquote(json_extract(" . TB_ARTICLES_PAYMENTS . ".service_response_payload, '$.Payment.QrCodeBase64Image')) as payment_qrcode"),
            DB::raw("json_unquote(json_extract(" . TB_ARTICLES_PAYMENTS . ".service_response_payload, '$.Payment.Type')) as payment_type"),
            TB_ARTICLES_PAYMENTS . '.id as tb_payments_id',
            TB_ARTICLES . '.id as tb_articles_id',
            TB_ARTICLES . '.status as tb_articles_status',
            TB_ARTICLES . '.affiliate_coupon_id as tb_articles_affiliate_coupon_id',
            TB_ARTICLES . '.collection_id as tb_articles_collection_id',
            TB_ARTICLES . '.author_id as tb_articles_author_id',
            TB_ARTICLES . '.type_id as tb_articles_type_id',
            TB_ARTICLES . '.title as tb_articles_title',
            TB_ARTICLES . '.created_at as tb_articles_created_at',
            TB_ARTICLES . '.amount as tb_articles_amount',
            TB_ARTICLES . '.gross_amount as tb_articles_gross_amount',
        ]);


    if (!$items) {
        logg('Has no pending payments');
        print_r("\n");
        sleep(10);
        return;
    }

    logg('Found ' . count($items) . ' payments for process');

    $articlesService = new CieloPayment(
        config('cielo_merchant_id'),
        config('cielo_merchant_key'),
        config('cielo_sandbox')
    );

    $reviewsService  = new CieloPayment(
        config('cielo_reviews_merchant_id'),
        config('cielo_reviews_merchant_key'),
        config('cielo_reviews_sandbox')
    );

    foreach ($items as $item) {
        $sql = [];

        $article = (object)[
            'id' => $item->tb_articles_id,
            'status' => $item->tb_articles_status,
            'affiliate_coupon_id' => $item->tb_articles_affiliate_coupon_id,
            'collection_id' => $item->tb_articles_collection_id,
            'author_id' => $item->tb_articles_author_id,
            'type_id' => $item->tb_articles_type_id,
            'title' => $item->tb_articles_title,
            'created_at' => $item->tb_articles_created_at,
            'amount' => $item->tb_articles_amount,
            'gross_amount' => $item->tb_articles_gross_amount,
        ];

        $service = $article->type_id === 3 ? $reviewsService : $articlesService;

        $setError = fn () => articles_payments()->update([
            'service_status' => 'ERROR',
            'status_checked_at' => date('Y-m-d H:i:s')
        ], $item->id);

        $data = objectToArray(secure_json_decode($item->service_response_payload));
        if (!array_get($data, 'Payment')) {
            $setError();
            continue;
        }

        $paymentId = $item->service_id;
        if (!$paymentId) {
            $setError();
            continue;
        }

        logg("Process Payment Id: {$paymentId}");

        try {
            $service_response_payload = $service->consult($paymentId);
            $service_response_payload = objectToArray($service_response_payload);
            $service_status = (int) array_get($service_response_payload, 'Payment.Status');

            if (
                $item->payment_qrcode
                && $item->payment_type === 'Pix'
                && array_get($service_response_payload, 'Payment')
                && $service_status !== 2
            ) {
                $service_response_payload['Payment']['QrCodeBase64Image'] = $item->payment_qrcode;
            }

            $service_response_payload = json_encode($service_response_payload);

            articles_payments()
                ->update([
                    'service_status' => $service_status,
                    'service_response_payload' => $service_response_payload,
                    'status_checked_at' => date('Y-m-d H:i:s')
                ], $item->id);

            $sql[] = db_last_query();

            DB::table(TB_ARTICLES)
                ->where_id($article->id)
                ->update([
                    'status' => $service_status === 2 ? ($article->type_id === 3 ? 33 : 32) : $article->status
                ]);

            $sql[] = db_last_query();

            if ($service_status === 2) {
                DB::table(TB_ARTICLES)
                    ->where_id($article->id)
                    ->update([
                        'payment_id' => $item->tb_payments_id
                    ]);

                sendPaymentMail($article);
                createCouponEntry(
                    get_coupon_data($article->affiliate_coupon_id, $article),
                    $article
                );
                dispatch_on_collection_change($article);
                $sql[] = db_last_query();
            }
        } catch (\Exception $e) {
            $setError();
            logg($e->getMessage());
        }

        logg($sql);
    }
}
$start_running = (int) date('YmdHis');
$loop = 1;
while (true) {
    logg('LOOP ' . $loop++);

    job();

    echo "\n";
    logg("Wait for next loop");
    echo "\n";

    sleep(60);
}
