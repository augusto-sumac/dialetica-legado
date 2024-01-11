<?php
require_once(__DIR__ . '/helpers.php');

function payment_validate_and_store_address($article)
{
    $rules = [
        'zip_code' => 'required|max:20',
        'street' => 'required|max:150',
        'number' => 'required|max:30',
        'complement' => 'max:150',
        'district' => 'required|max:100',
        'city_ibge_id' => 'numeric',
        'city' => 'required|max:150',
        'state' => 'required|max:2',
    ];

    $update_address = input('update_address', null);
    $author_address_id = input('address_id', null);

    if (!$update_address/* || $author_address_id*/) {
        $rules = [];
    }

    validate($rules);

    $data = array_only(input(), array_keys($rules));

    $data['user_id'] = logged_author()->id;
    $data['zip_code'] = only_numbers(array_get($data, 'zip_code'));

    try {
        if ($update_address) {
            $exists = authors_addresses()
                ->where_user_id($data['user_id'])
                ->where_zip_code($data['zip_code'])
                ->first();

            if ($exists) {
                $author_address_id = $exists->id;
            } else {
                $author_address_id = authors_addresses()->insert_get_id($data);
            }
        }
    } catch (\Exception $e) {
    }

    DB::table(TB_ARTICLES)->update(compact('author_address_id'), $article->id);

    return $author_address_id;
}

class ModelAccessor
{
    public function __get($name)
    {
        return null;
    }
}

Route::group(['prefix' => AUTHOR_COUPONS_BASE_URL], function () {
    Route::get('{token}/validate/{article}', function ($token, $article = null) {
        $article = find_or_fail(DB::table(TB_ARTICLES), $article);
        return response_json((array) get_coupon_data($token, $article));
    });
});

Route::group([
    'prefix' => 'payments'
], function () {

    Route::get('{articleId:\d+}', function ($articleId) {
        $article = find_or_fail(DB::table(TB_ARTICLES)->where_author_id(logged_author()->id), $articleId);

        $article_type = articles_types()->find($article->type_id);
        $payment = articles_payments()->find($article->payment_id);

        if ($article->author_address_id) {
            $address = users_addresses()->find($article->author_address_id);
        } else {
            $address = users_addresses()
                ->where_user_id($article->author_id)
                ->order_by('created_at', 'desc')
                ->first();
        }

        $installments = installmentsArray($article->amount);

        if (!$address) {
            $address = new ModelAccessor;
        }

        if (!$payment) {
            $payment = new ModelAccessor;
        }

        $back_route = url(($article->type_id === 1 ? AUTHOR_ARTICLES_BASE_URL : AUTHOR_REVIEWS_BASE_URL) . '/' . $article->id);

        return view(AUTHOR_PAYMENTS_VIEW_PATH . '.form', compact('article', 'article_type', 'address', 'installments', 'back_route'));
    });

    Route::post('{articleId:\d+}', function ($articleId) {
        $article = find_or_fail(DB::table(TB_ARTICLES)->where_author_id(logged_author()->id), $articleId);

        $paymentType = input('payment_type', 'C');

        $article->author_address_id = payment_validate_and_store_address($article);

        $cardData = [];

        if ($paymentType === 'C') {
            $GLOBALS['APP_INPUT_DATA']['nu'] = only_numbers(input('nu'));

            $rules = [
                'br' => 'required|in:master,visa,elo,amex,diners,hipercard',
                'na' => 'required',
                'do' => 'required|max:30',
                'nu' => 'required|match:/[0-9]{12,19}/i',
                'ex' => 'required|match:/[0-9]{2}\/[0-9]{2}/i',
                'cv' => 'required|match:/[0-9]+/i',
                'in' => 'numeric'
            ];

            validate($rules);

            $cardData = array_only(input(), array_keys($rules));

            if (!isset($cardData['in']) || !in_array($cardData['in'], [1, 2, 3, 4])) {
                $cardData['in'] = 4;
            }
        }

        $coupon = get_coupon_data(input('discount_coupon'), $article);
        if ($coupon?->valid) {
            $article->gross_amount = $coupon->article_gross_amount;
            $article->discount_amount = $coupon->article_discount_amount;
            $article->amount = $coupon->article_amount;
            $article->affiliate_coupon_id = $coupon->article_affiliate_coupon_id;
        }

        $payment = new stdClass();
        $payment->service = 'Cielo';
        $payment->type = 'payment';
        $payment->operation = 'create';
        $payment->source = 'articles';
        $payment->user_id = logged_author()->id;
        $payment->source_id = $article->id;
        $payment->service_request_payload = null;
        $payment->service_response_payload = null;

        DB::beginTransaction();

        try {
            $responsePayment = new stdClass;

            $payment->started_at = date('Y-m-d H:i:s');

            if ((float) $article->amount === 0.00) {
                $payment->service_id = 'AP' . Str::random(16);
                $payment->service_status = 2;
                $payment->finished_at = $payment->started_at;

                $responsePayment = (object) [
                    'Type' => 'Discount'
                ];
            } else {
                try {
                    require_once(ROOT_PATH . 'vendor/CieloPayment.php');

                    $configKey = $article->type_id === 1 ? 'cielo' : 'cielo_reviews';

                    $service = new CieloPayment(
                        config("{$configKey}_merchant_id"),
                        config("{$configKey}_merchant_key"),
                        config("cielo_sandbox")
                    );

                    $result = $service->create($article, $paymentType, $cardData);
                } catch (\Exception $e) {
                    return response_json([
                        'item' => $article,
                        'message' => 'Falha ao processar o pagamento',
                        'error_message' => $e->getMessage()
                    ], 500);
                }

                $payment->service_id = $result->Payment->PaymentId;
                $payment->service_status = (int) $result->Payment->Status;
                $payment->service_request_payload = null;
                $payment->service_response_payload = json_encode($result);
                $payment->finished_at = date('Y-m-d H:i:s');

                $responsePayment = $result->Payment;
            }

            $payment->id = articles_payments()->insert_get_id((array) $payment);

            if ($paymentType === 'C' && $payment->service_status !== 2) {
                DB::commit();

                return response_json([
                    'item' => $article,
                    'message' => 'Falha ao processar o pagamento',
                    'error_message' => $result->Payment->ReturnMessage
                ], 500);
            }

            $article->status = $payment->service_status === 2 ? ($article->type_id === 1 ? 32 : 33) : $article->status;
            $article->payment_id = $payment->id;
            DB::table(TB_ARTICLES)->update(array_only((array)$article, [
                'payment_id',
                'gross_amount',
                'discount_amount',
                'amount',
                'status',
                'affiliate_coupon_id',
                'author_address_id'
            ]), $article->id);

            if ($payment->service_status === 2) {
                sendPaymentMail($article);
                createCouponEntry($coupon, $article);
                dispatch_on_collection_change($article);
            }

            DB::commit();

            return response_json_update_success([
                'item' => $article,
                'message' => 'Pagamento Confirmado',
                'payment' => $responsePayment
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e);
        }
    });

    Route::get('{articleId:\d+}/status', function ($articleId) {
        $article = find_or_fail(DB::table(TB_ARTICLES)->where_author_id(logged_author()->id), $articleId);
        $payment = find_or_fail(articles_payments(), $article->payment_id);

        try {
            require_once(ROOT_PATH . 'vendor/CieloPayment.php');
            $service = new CieloPayment(config('cielo_merchant_id'), config('cielo_merchant_key'), config('cielo_sandbox'));
            $result = $service->consult($payment->service_id);
            if ((int) $result?->Payment?->Status === 2) {
                articles_payments()->update([
                    'service_status' => 2
                ], $article->payment_id);
            }

            return response_json_update_success($result);
        } catch (\Exception $e) {
            return response_json_fail($e->getMessage());
        }
    });

    Route::delete('{articleId:\d+}/cancel', function ($articleId) {
        $article = find_or_fail(DB::table(TB_ARTICLES)->where_author_id(logged_author()->id), $articleId);
        $payment = find_or_fail(articles_payments(), $article->payment_id);

        try {
            require_once(ROOT_PATH . 'vendor/CieloPayment.php');
            $service = new CieloPayment(config('cielo_merchant_id'), config('cielo_merchant_key'), config('cielo_sandbox'));
            $result = $service->cancel($payment->service_id, $article->amount);
            if ((int) $result?->Payment?->Status === 2) {
                articles_payments()->update([
                    'service_status' => 'CANCELADO'
                ], $article->payment_id);
            }

            return response_json_update_success($result);
        } catch (\Exception $e) {
            return response_json_fail($e->getMessage());
        }
    });

    Route::get('{articleId:\d+}/info', function ($articleId) {
        return renderPaymentInfo($articleId);
    });
});
