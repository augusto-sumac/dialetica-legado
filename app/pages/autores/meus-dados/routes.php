<?php

Route::group([
    'prefix' => AUTHOR_ME_BASE_URL,
    'before' => 'author-auth'
], function () {
    Route::get('', function () {
        $item = (array) authors()->where_id(logged_author()->id)
            ->first([
                'name',
                'email',
                'phone',
                'document',
                'role',
                'bank_account',
                'curriculum_url',
                'curriculum'
            ]);

        $address = (array)authors_addresses()
            ->where_user_id(logged_author()->id)
            ->where_not_null('zip_code')
            ->order_by('updated_at', 'desc')
            ->first([
                'id',
                'id as address_id',
                'zip_code',
                'street',
                'number',
                'district',
                'complement',
                'city_ibge_id',
                'city',
                'state',
            ]);

        if (!$address) {
            $address = new ModelAccessor;
        }

        $item['affiliate_coupon'] = affiliates_coupons()
            ->where_user_id(logged_author()->id)
            ->where_null('deleted_at')
            ->first(['id', 'token']);

        if (!empty($item['affiliate_coupon'])) {
            $settings = settings()->lists('value', 'key');
            $item['affiliate_coupon']->coupon_discount_percent = toMoney(array_get($settings, 'coupon_discount_percent', 10));
            $item['affiliate_coupon']->coupon_affiliate_percent = toMoney(array_get($settings, 'coupon_affiliate_percent', 10));
        }

        $item['has_affiliate_coupon'] = !empty($item['affiliate_coupon']);

        $bank_account = (array) json_decode($item['bank_account'] ?? '[]');

        $item['account_document'] = array_get($bank_account, 'account_document', null);
        if (empty($item['account_document'])) {
            $item['account_document'] = array_get($item, 'document', null);
        }
        $item['account_name'] = array_get($bank_account, 'account_name', null);
        if (empty($item['account_name'])) {
            $item['account_name'] = array_get($item, 'name');
        }

        $item['account_pix_type'] = array_get($bank_account, 'account_pix_type', null);
        $item['account_pix_key'] = array_get($bank_account, 'account_pix_key', null);

        $item['address'] = (object) $address;

        return view(AUTHOR_ME_VIEW_PATH . '.index', $item);
    });

    Route::post('', function () {
        $rules = [
            'name' => 'required|max:150',
            'email' => 'required|email|max:200',
            'document' => 'required|max:30',
            // 'document' => 'required|cpf',
            'phone' => 'required|max:20',
            // 'phone' => 'required|match:/\([0-9]{2}\) [0-9]{5}-[0-9]{4}/',
            'role' => 'required',
            'curriculum_url' => 'max:255',
            'curriculum' => 'max:4000'
        ];

        validate($rules);

        $item = array_only(input(), array_keys($rules));

        try {
            authors()->where_id(logged_author()->id)->update($item);

            foreach (array_keys($rules) as $key) {
                $_SESSION['author']->{$key} = input($key);
            }

            return response_json([
                'message' => 'Dados atualizados com sucesso',
                'item' => $item
            ]);
        } catch (\Exception $e) {
            return response_json([
                'message' => 'Erro ao atualizar seus dados',
            ], 500);
        }
    });

    Route::post('/alterar-senha', function () {
        $rules = [
            'password' => 'required|min:6|max:40|confirmed',
        ];

        validate($rules);

        $item = [
            'password' => Hash::make(input('password'))
        ];

        try {
            authors()->where_id(logged_author()->id)->update($item);

            return response_json([
                'message' => 'Senha atualizada com sucesso'
            ]);
        } catch (\Exception $e) {
            return response_json([
                'message' => 'Erro ao atualizar sua senha',
                'e' => logged_author()
            ], 500);
        }
    });

    Route::post('/address', function () {
        $rules = [
            'zip_code' => 'required|max:20',
            // 'zip_code' => 'required|match:/[0-9]{5}-[0-9]{3}/i',
            'street' => 'required|max:150',
            'number' => 'required|max:30',
            'complement' => 'max:150',
            'district' => 'required|max:100',
            'city_ibge_id' => 'numeric',
            'city' => 'required|max:150',
            'state' => 'required|max:2',
        ];

        validate($rules);

        $item = array_only(input(), array_keys($rules));

        $item['user_id'] = logged_author()->id;

        $id = input('id');

        try {
            if ($id) {
                authors_addresses()
                    ->where_user_id(logged_author()->id)
                    ->where_id($id)
                    ->update($item);
            } else {
                $id = authors_addresses()->insert_get_id($item);
            }

            $item['id'] = $id;

            return response_json([
                'message' => 'Endereço atualizado com sucesso',
                'item' => $item
            ]);
        } catch (\Exception $e) {
            return response_json([
                'message' => 'Erro ao atualizar seu endereço'
            ], 500);
        }
    });

    Route::get('/minha-conta', function () {
        $bank_account = authors()->find(logged_author()->id, [
            'bank_account',
        ]);

        $bank_account = $bank_account ? (array) json_decode($bank_account->bank_account) : [];

        $data = [];

        $data['account_document'] = array_get($bank_account, 'account_document', input('account_document'));
        $data['account_name'] = array_get($bank_account, 'account_name', input('account_name'));
        $data['account_pix_type'] = array_get($bank_account, 'account_pix_type', input('account_pix_type'));
        $data['account_pix_key'] = array_get($bank_account, 'account_pix_key', input('account_pix_key'));
        $data['in_modal'] = input('in_modal');

        return view('pages.autores.meus-dados.components.form-account', $data);
    });

    Route::post('/minha-conta', function () {
        $rules = [
            'account_document' => 'required|max:30',
            // 'account_document' => 'required|cpf_cnpj',
            'account_name' => 'required|max:255',
            'account_pix_type' => 'required|max:40',
            'account_pix_key' => 'required|max:255',
        ];

        validate($rules);

        $bank_account = array_only(input(), array_keys($rules));

        $item = [
            'bank_account' => json_encode($bank_account)
        ];

        try {
            authors()->where_id(logged_author()->id)->update($item);

            return response_json([
                'message' => 'Conta atualizada com sucesso',
                'bank_account' => $bank_account
            ]);
        } catch (\Exception $e) {
            return response_json([
                'message' => 'Erro ao atualizar sua conta',
                'e' => logged_author()
            ], 500);
        }
    });
});
