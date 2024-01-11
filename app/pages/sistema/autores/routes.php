<?php

use App\Models\Author;

Route::group([
    'prefix' => AUTHORS_BASE_URL,
    'before' => 'auth'
], function () {
    Route::get('', function () {
        $datagrid = [
            'data' => [],
            'use_datatable' => true,
            'options' => [
                'order' => [[1, 'desc']],
                'processing' =>  true,
                'serverSide' =>  true,
                'deferRender' => true,
                'ajax' => [
                    'url' => url(AUTHORS_BASE_URL),
                    'type' => 'POST'
                ],
                'pageLength' => 50
            ],
            'columns' => [
                [
                    'key' => 'id',
                    'label' => '#ID',
                    'attrs' => ['class' => 'text-start w-120px']
                ],
                [
                    'key' => 'name',
                    'label' => 'Nome',
                    'attrs' => ['class' => 'text-start w-250px']
                ],
                [
                    'key' => 'qtde_obras',
                    'label' => 'Obras',
                    'attrs' => ['class' => 'text-center w-120px']
                ],
                [
                    'key' => 'has_address',
                    'label' => 'End. Cad.',
                    'attrs' => ['class' => 'text-center w-120px']
                ],
                [
                    'key' => 'created_at',
                    'label' => 'Dt Cadastro',
                    'attrs' => ['class' => 'text-end w-180px']
                ],
                [
                    'key' => 'status',
                    'label' => 'Status',
                    'attrs' => ['class' => 'text-end w-110px']
                ]
            ]
        ];

        $datagrid = make_datatable_options($datagrid);

        return view(AUTHORS_VIEW_PATH . '.index', compact('datagrid'));
    });

    Route::post('', function () {
        $sql_count_obras = articles()
            ->select([
                DB::raw('count(' . TB_ARTICLES . '.id)')
            ])
            ->where(TB_ARTICLES . '.author_id', DB::raw(TB_AUTHORS . '.id'))
            ->toSql();

        $sql_has_address = users_addresses()
            ->select([
                DB::raw('count(' . TB_USERS_ADDRESSES . '.id)')
            ])
            ->where(TB_USERS_ADDRESSES . '.user_id', DB::raw(TB_AUTHORS . '.id'))
            ->toSql();

        $rows = Author::select([
            TB_AUTHORS . '.id',
            TB_AUTHORS . '.status',
            TB_AUTHORS . '.name',
            TB_AUTHORS . '.created_at',
            DB::raw('(' . $sql_count_obras . ') as qtde_obras'),
            DB::raw('(' . $sql_has_address . ') as has_address')
        ]);

        // Sort
        $cols = input('columns', []);
        foreach (input('order', []) as $order) {
            $col = array_get($cols, $order['column'] . '.data');
            if ($col) {
                if ($col === 'qtde_obras') {
                    $col = '(' . $sql_count_obras . ')';
                }
                if ($col === 'has_address') {
                    $col = '(' . $sql_has_address . ')';
                }
                $rows->order_by(DB::raw($col . ' ' . strtolower($order['dir'])));
            }
        }

        // Search
        if ($search = input('search.value', input('search.search'))) {
            $rows->where(function ($q) use ($search) {
                $q->or_like(TB_AUTHORS . '.id', $search);
                $q->or_like(TB_AUTHORS . '.name', $search);
            });
        }

        // Data
        $rows = $rows->paginate(input('per_page', 15));

        return response_json([
            'draw' => input('draw', 1),
            'recordsTotal' => Author::count('id'),
            'recordsFiltered' => $rows->total,
            'sql' => DB::profile(),
            'data' => array_map(function ($row) {
                $row_id = $row->id;
                $row_status = $row->status;

                $status_text = (int) $row_status === 1 ? 'Ativo' : 'Inativo';
                $status_class = (int) $row_status === 1 ? 'success' : 'danger';
                $row->status = '<a href="' . url(AUTHORS_BASE_URL . '/' . $row_id . '/toggle-status') . '" data-status="' . $row_status . '" class="toggle-status badge bg-' . $status_class . '" title="Alterar Status">' . $status_text . '</a>';

                $row->id = '<a href="' . url(AUTHORS_BASE_URL . '/' . $row_id) . '" title="Dados do Autor">' . str_pad_id($row_id) . '</a>';

                $row->created_at = datetimeFromMySql($row->created_at);

                $addr_text = (int) $row->has_address > 0 ? 'Sim' : 'Não';
                $addr_class = (int) $row->has_address > 0 ? 'info' : 'warning';
                $row->has_address = '<span class="badge bg-' . $addr_class . '">' . $addr_text . '</span>';

                return $row->to_array();
            }, $rows->results)
        ]);
    });

    Route::get('/adicionar', render_page(AUTHORS_VIEW_PATH . '.form'));

    Route::post('/adicionar', fn () => process_register(false));

    Route::get('/{id:\d+}', function ($id) {
        $item = (array) find_or_fail(authors(), $id, [
            'id',
            'name',
            'email',
            'phone',
            'document',
            'role',
            'curriculum_url',
            'curriculum'
        ]);

        $address = (array)authors_addresses()
            ->where_user_id($item['id'])
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
            ->where_user_id($id)
            ->where_null('deleted_at')
            ->first(['id', 'token']);

        $item['address'] = (object)$address;

        return view(AUTHORS_VIEW_PATH . '.detail', $item);
    });

    Route::post('/{id:\d+}', function ($id) {
        $rules = [
            'name' => 'required|max:150',
            'email' => 'required|email|max:200',
            'document' => 'required|max:30',
            // 'document' => 'required|cpf',
            'phone' => 'required|max:20',
            // 'phone' => 'required|match:/\([0-9]{2}\) [0-9]{5}-[0-9]{4}/',
            'role' => 'required',
            'curriculum_url' => 'required|max:150',
            'curriculum' => 'required|max:4000',
        ];

        validate($rules);

        $exists = authors()->where_email(input('email'))->first();

        if ($exists && (int)$exists->id !== (int)input('id')) {
            return response_json([
                'message' => 'Existem campos inválidos',
                'errors' => ['email' => 'Já existe um registro com o email informado!']
            ], 422);
        }

        $item = array_only(input(), array_keys($rules));

        try {
            authors()->update($item, $id);

            return response_json_update_success([
                'item' => $item
            ]);
        } catch (\Exception $e) {
            return response_json_update_fail();
        }
    });

    Route::post('/{id:\d+}/address', function ($user_id) {
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

        $id = input('address_id');

        $item['user_id'] = $user_id;

        if (!array_get($item, 'city_ibge_id')) {
            $item['city_ibge_id'] = 0;
        }

        $exists = authors_addresses()
            ->where_user_id($user_id)
            ->where_zip_code($item['zip_code'])
            ->first();

        if ($exists) {
            $id = $exists->id;
        }

        try {
            if ($id) {
                if ($exists) {
                    $item['updated_at'] = date('Y-m-d H:i:s');
                }

                authors_addresses()
                    ->where_user_id($user_id)
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
                'message' => 'Erro ao atualizar seu endereço',
                'e' => $e->getMessage()
            ], 500);
        }
    });

    Route::post('/{id:\d+}/password', function ($user_id) {
        $user = find_or_fail(authors(), $user_id);

        $rules = [
            'password' => 'required|min:6|max:40|confirmed',
        ];

        validate($rules);

        $item = [
            'password' => Hash::make(input('password'))
        ];

        try {
            authors()->where_id($user_id)->update($item);

            $mensagem = view('pages.autores.auth.mensagem-redefinir-senha', array(
                'nome' => get_first_name($user->name),
                'email' => $user->email,
                'senha' => input('password')
            ));

            add_job('sendMail', [
                'to' => env('DEV_MODE') ? 'marcioantunes.ma@gmail.com' : $user->email,
                'subject' => 'Recuperação de Senha',
                'message' => $mensagem
            ]);

            return response_json([
                'message' => 'Senha atualizada com sucesso'
            ]);
        } catch (\Exception $e) {
            return response_json([
                'message' => 'Erro ao atualizar sua senha',
            ], 500);
        }
    });

    Route::post('/{id:\d+}/toggle-status', function ($id) {
        $status = (int)input('status') === 1 ? 0 : 1;

        try {
            authors()->where_id($id)->update(compact('status'));

            return response_json_update_success([
                'status' => $status
            ]);
        } catch (\Exception $e) {
            return response_json_update_fail();
        }
    });

    Route::post('/{id:\d+}/update-affiliate-coupon', function ($id) {
        $token = input('token');

        if (empty($token)) {
            return response_json([
                'message' => 'Não pode ser vazio'
            ], 500);
        }

        if (strlen($token) > 30) {
            return response_json([
                'message' => 'Não pode possuir mais que 30 caracteres'
            ], 500);
        }

        $separator = '-';
        $token = Str::ascii($token);

        // Remove all characters that are not the separator, letters, numbers, or whitespace.
        $token = preg_replace('![^' . preg_quote($separator) . '\pL\pN\s]+!u', '', $token);

        // Replace all separator characters and whitespace by a single separator
        $token = preg_replace('![' . preg_quote($separator) . '\s]+!u', $separator, $token);

        $token = trim($token, $separator);

        $exists = affiliates_coupons()
            ->where('user_id', '!=', $id)
            ->where_token($token)
            ->first();

        if ($exists) {
            return response_json([
                'message' => 'Este cupom já está em uso! Escolha outro!'
            ], 500);
        }

        try {
            affiliates_coupons()
                ->where_user_id($id)
                ->update(compact('token'));

            return response_json_update_success([
                'token' => $token
            ]);
        } catch (\Exception $e) {
            return response_json_update_fail();
        }
    });
});
