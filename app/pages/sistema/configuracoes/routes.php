<?php

Route::group([
    'prefix' => SETTINGS_BASE_URL,
    'before' => 'auth'
], function () {
    Route::get('/', function () {

        $data = [
            'article_types' => articles_types()->lists('price', 'id')
        ];

        $settings = settings()->lists('value', 'key');

        $settings = array_merge([
            'maintenance_mode' => 0,
            'minimum_withdrawal_amount' => 200,
            'coupon_discount_percent' => 5,
            'coupon_affiliate_percent' => 5,
            'collection_days_limit' => 30,
            'days_approve_article' => 7,
            'days_approve_collection' => 7,
            'minimum_articles_in_collection' => 5
        ], $settings);

        foreach ($settings as $key => $value) {
            try {
                $json = json_decode($value);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $value = $json;
                }
            } catch (\Exception $e) {
                // ..
            }

            $settings[$key] = $value;
        }

        return view(SETTINGS_VIEW_PATH . '.index', $data + $settings);
    });

    Route::post('/system', function () {
        $keys = [
            'maintenance_mode',
        ];

        $rows = [];

        foreach (array_only(input(), $keys) as $key => $value) {
            if (is_array($value) || is_object($value)) {
                $value = json_encode($value);
            }

            $rows[] = compact('key', 'value');
        }

        try {
            if ($rows) {
                InsertOrUpdateMany::prepare(
                    TB_SETTINGS,
                    ['key', 'value'],
                    $rows,
                    ['value'],
                    null,
                    true
                );
            }
            return response_json_success('Configuração atualizada com sucesso');
        } catch (\Exception $e) {
            return response_json_fail('Falha ao atualizar configurações');
        }
    });

    Route::post('/articles-types', function () {
        $data = array_map(fn ($value) => toNumber($value), input('article_price', []));

        try {
            foreach ($data as $id => $price) {
                articles_types()->update(compact('price'), $id);
            }
            return response_json_success('Custos atualizados com sucesso');
        } catch (\Exception $e) {
            return response_json_fail('Falha ao atualizar os custos');
        }
    });

    Route::post('/affiliates', function () {
        $keys = [
            'minimum_withdrawal_amount',
            'coupon_discount_percent',
            'coupon_affiliate_percent',
        ];

        $rows = [];

        foreach (array_only(input(), $keys) as $key => $value) {
            $value = toNumber($value);
            $rows[] = compact('key', 'value');
        }

        try {
            if ($rows) {
                InsertOrUpdateMany::prepare(
                    TB_SETTINGS,
                    ['key', 'value'],
                    $rows,
                    ['value'],
                    null,
                    true
                );
            }
            return response_json_success('Configuração atualizada com sucesso');
        } catch (\Exception $e) {
            return response_json_fail('Falha ao atualizar configurações');
        }
    });

    Route::post('/collections', function () {
        $keys = [
            'collection_days_limit',
            'minimum_articles_in_collection',
            'days_approve_collection',
            'days_approve_article',
        ];

        $rows = [];

        foreach (array_only(input(), $keys) as $key => $value) {
            $value = toNumber($value);
            $rows[] = compact('key', 'value');
        }

        try {
            if ($rows) {
                InsertOrUpdateMany::prepare(
                    TB_SETTINGS,
                    ['key', 'value'],
                    $rows,
                    ['value'],
                    null,
                    true
                );
            }
            return response_json_success('Configuração atualizada com sucesso');
        } catch (\Exception $e) {
            return response_json_fail('Falha ao atualizar configurações');
        }
    });
});
