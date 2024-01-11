<?php

function get_coupon_data($couponTokenOrId, $article)
{
    $coupon = affiliates_coupons()
        ->where_null('deleted_at')
        ->where(is_numeric($couponTokenOrId) ? 'id' : 'token', trim($couponTokenOrId))
        ->where(fn ($q) => $q->where_null('start_at')->or_where('start_at', '<=', $article->created_at))
        ->where(fn ($q) => $q->where_null('expires_at')->or_where('expires_at', '>=', $article->created_at));

    $whereTypeIn = ['ALL'];

    if ($article->type_id === 1) {
        $whereTypeIn[] = 'ARTICLES';
    } else {
        $whereTypeIn[] = 'REVIEWS';
    }

    $coupon = $coupon->where_in('type', $whereTypeIn)->first();

    if (!$coupon) {
        $coupon = new stdClass;
        $coupon->valid = false;
    } else {
        $coupon->valid = true;

        $queryAffiliatesCouponsEntries = fn ($c) => affiliates_coupons_entries()
            ->where_null(TB_AFFILIATES_COUPONS_ENTRIES . '.deleted_at')
            ->where(TB_AFFILIATES_COUPONS_ENTRIES . '.affiliate_coupon_id', $c->id)
            ->where(TB_AFFILIATES_COUPONS_ENTRIES . '.type', 'C');

        if ($coupon->max_uses) {
            $uses = $queryAffiliatesCouponsEntries($coupon)->count('id');

            if ($uses >= $coupon->max_uses) {
                $coupon = new stdClass;
                $coupon->valid = false;
                $coupon->message = 'O limite de uso deste cupom já foi atingido';
            }
        }

        if ($coupon->valid && $coupon->max_uses_user) {
            $uses = $queryAffiliatesCouponsEntries($coupon)
                ->join(TB_ARTICLES, TB_ARTICLES . '.id', '=', TB_AFFILIATES_COUPONS_ENTRIES . '.article_id')
                ->where_null(TB_ARTICLES . '.deleted_at')
                ->where(TB_ARTICLES . '.author_id', logged_author()->id)
                ->count(TB_ARTICLES . '.id');

            if ($uses >= $coupon->max_uses_user) {
                $coupon = new stdClass;
                $coupon->valid = false;
                $coupon->message = 'Você já usou o limite máximo deste cupom';
            }
        }
    }

    $settings = settings()->lists('value', 'key');

    $gross_amount = isset($article->gross_amount) && $article->gross_amount >= $article->amount ? $article->gross_amount : $article->amount;
    $discount_amount = 0;

    if ($coupon->valid && $coupon->discount_rule === 'fixed') {
        $discount_amount = (float) $coupon->discount_value;
        $discount_percent = toNumber(toMoney(($discount_amount / $gross_amount) * 100));
        $coupon->discount_value = $discount_percent;
    } else {
        $discount_percent = $coupon->valid && $coupon->discount_rule === 'percent' ? $coupon->discount_value : array_get($settings, 'coupon_discount_percent', 0);
        $discount_percent = $coupon->valid && $discount_percent ? $discount_percent : 0;
        if ($discount_percent) {
            $discount_amount = $gross_amount ? $gross_amount * ($discount_percent / 100) : 0;
        }
    }

    $amount = $gross_amount - $discount_amount;

    $affiliate_amount = 0;

    if ($coupon->valid && $coupon->affiliate_rule === 'fixed') {
        $affiliate_amount = (float) $coupon->affiliate_value;
        $affiliate_percent = toNumber(toMoney(($affiliate_amount / $gross_amount) * 100));
        $coupon->affiliate_value = $affiliate_percent;
    } else {
        $affiliate_percent = $coupon->valid && $coupon->affiliate_rule === 'percent' ? $coupon->affiliate_value : array_get($settings, 'coupon_affiliate_percent', 0);
        $affiliate_percent = $coupon->valid && $affiliate_percent ? $affiliate_percent : 0;
        if ($affiliate_percent) {
            $affiliate_amount = $amount ? $amount * ($affiliate_percent / 100) : 0;
        }
    }

    $installments = [];
    for ($i = 4; $i >= 1; $i--) {
        $value = ($gross_amount - $discount_amount) / $i;
        $installments[] = '<option value="' . $i . '"' . ($i === 4 ? 'selected' : '') . '>
                <strong>' . $i . 'x</strong> de <strong>R$ ' . toMoney($value) . '</strong>
            </option>';
    }

    $coupon->installments = implode('', $installments);
    $coupon->discount_percent = $discount_percent;
    $coupon->discount_amount = $discount_amount;
    $coupon->affiliate_percent = $affiliate_percent;
    $coupon->affiliate_amount = $affiliate_amount;

    if ($coupon->valid) {
        $coupon->article_gross_amount = (float) $gross_amount;
        $coupon->article_discount_amount = (float) $discount_amount;
        $coupon->article_amount = (float) $amount;
        $coupon->article_affiliate_coupon_id = $coupon->id;

        $coupon->discount_percent = (float) $coupon->discount_percent;
        $coupon->discount_value = (float) $coupon->discount_value;
    }

    return $coupon;
}

function createCouponEntry($coupon, $article)
{
    if ($coupon && $coupon->valid) {
        $coupon_affiliate_retention_days = settings()->where_key('coupon_affiliate_retention_days')->first();
        $coupon_affiliate_retention_days = $coupon_affiliate_retention_days ? $coupon_affiliate_retention_days->value : 15;

        $available_at = new \DateTime($article->created_at ?? date('Y-m-d'));
        $available_at = $available_at->add(new \DateInterval("P{$coupon_affiliate_retention_days}D"));
        $available_at = $available_at->format('Y-m-d');

        affiliates_coupons_entries()
            ->insert([
                'author_id' => $coupon->user_id,
                'article_id' => $article->id,
                'affiliate_coupon_id' => $coupon->id,
                'amount' => $coupon->affiliate_amount,
                'status' => 'PE',
                'available_at' => $available_at,
            ]);
    }
};

function sendPaymentMail($article)
{

    $author = authors()->find($article->author_id);

    $templates = [
        1 => 'd-95822ef0355549a4a5dab83901cf7262',
        3 => 'd-ef0dfce673a045668c41cf1efdd1f44c'
    ];

    $message = [
        'id' => array_get($templates, $article->type_id, $templates[1]),
        'vars' => [
            'first_name' => get_first_name($author->name),
            'titulo_trabalho' => $article->title,
        ]
    ];

    $data = [
        'to' => env('DEV_MODE') ? 'marcioantunes.ma@gmail.com' :  $author->email,
        'subject' => 'Confirmação de pagamento',
        'message' => $message
    ];

    add_job('sendMail', $data);
}

function renderPaymentInfo($articleId, $paymentStatus = null)
{
    $article = DB::table(TB_ARTICLES)->find($articleId);
    if (!$article) {
        return '';
    }

    $payment = articles_payments()->find($article->payment_id);
    if (!$payment) {
        return '';
    }

    if ((int) $payment->service_status === 2) {
        return '';
    }

    $data = objectToArray(secure_json_decode($payment->service_response_payload));
    if (!array_get($data, 'Payment')) {
        return '';
    }

    $amount = array_get($data, 'Payment.Amount', 0);
    if ($amount) {
        $amount = $amount / 100;
    }
    $url = array_get($data, 'Payment.Url');
    $barcode = array_get($data, 'Payment.BarCodeNumber');
    $qrCode = array_get($data, 'Payment.QrCodeBase64Image');
    $isPix = array_get($data, 'Payment.Type') === 'Pix';
    $isBillet = array_get($data, 'Payment.Type') === 'Boleto';

    return view(AUTHOR_PAYMENTS_VIEW_PATH . '.info', compact('amount', 'url', 'barcode', 'qrCode', 'isPix', 'isBillet', 'paymentStatus', 'articleId'));
}


function dispatch_on_collection_change($article)
{
    try {
        if ($article->collection_id > 1) {
            $collection = articles_collections()->find($article->collection_id);
            if ($collection && $collection->author_id) {
                $collection->status = 'NA';
                $collection->article_tile = $article->title;
                collection_on_change_status($collection);
            }
        }
    } catch (\Exception $e) {
        // ...
    }
}
