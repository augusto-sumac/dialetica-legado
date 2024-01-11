<?php

function users_validation_rules($id = null)
{
    return [
        'name' => 'required|max:100',
        'email' => 'required|email|max:100|unique:' . TB_USERS . ($id ? ',id,' . $id : ''),
        'password' => 'min:6|max:40',
        'recovery_password_token' => 'max:8'
    ];
}

Route::group([
    'prefix' => USERS_BASE_URL,
    'before' => 'auth'
], function () {

    Route::get('/', function () {
        $data = users()
            ->select([
                'id', 'name', 'email', 'status'
            ])
            ->get();

        $datagrid = [
            'data' => $data,
            'use_datatable' => true,
            'columns' => [
                [
                    'key' => 'name',
                    'label' => 'Nome',
                    'attrs' => ['class' => 'text-start w-200px']
                ],
                [
                    'key' => 'email',
                    'label' => 'Email',
                    'attrs' => ['class' => 'text-start w-300px'],
                    'format' => function ($value, $row) {
                        return '<a href="' . url(USERS_BASE_URL . '/' . $row->id . '/editar') . '" title="Editar Cadastro" data-bs-toggle="tooltip">' . $value . '</a>';
                    }
                ],
                [
                    'key' => 'status',
                    'label' => 'Status',
                    'attrs' => ['class' => 'text-center w-120px'],
                    'format' => function ($value, $row) {
                        $text = (int) $value === 1 ? 'Ativo' : 'Inativo';
                        $class = (int) $value === 1 ? 'success' : 'danger';
                        return '<a href="' . url(USERS_BASE_URL . '/' . $row->id . '/toggle-status') . '" data-status="' . $value . '" class="toggle-status badge bg-' . $class . '" title="Alterar Status">' . $text . '</a>';
                    }
                ],
            ],
        ];

        return view(USERS_VIEW_PATH . '.index', compact('datagrid'));
    });

    Route::get('/{id:\d+}/email', function ($id = null) {
        $user = (array) users()->find($id);

        $user['email'] = 'um-email@um-dominio-fake.com.br';
        $user['senha'] = 'UmaSenhaFakeNaoUse';
        $user['nome']  = $user['name'];

        return view(USERS_VIEW_PATH . '.email-acesso-sistema', $user);
    });

    Route::get('/adicionar', render_page(USERS_VIEW_PATH . '.form'));

    Route::post('/adicionar', function () {
        validate(users_validation_rules());

        $item = array_only(input(), array_keys(users_validation_rules()));

        $recovery_password_token = Str::random(8);

        while (
            users()
            ->where_recovery_password_token($recovery_password_token)
            ->first()
        ) {
            $recovery_password_token = Str::random(8);
        }

        $password = gerar_senha();

        $item['password'] = Hash::make($password);
        $item['recovery_password_token'] = $recovery_password_token;
        $item['status'] = 1;

        try {
            $item['id'] = users()->insert_get_id($item);

            try {
                $mensagem = view('pages.sistema.auth.mensagem-redefinir-senha', array(
                    'nome' => get_first_name($item['name']),
                    'email' => $item['email'],
                    'senha' => $password,
                    'token' => $recovery_password_token
                ));

                add_job('sendMail', [
                    'to' => env('DEV_MODE') ? 'marcioantunes.ma@gmail.com' : $item['email'],
                    'subject' => 'Definir senha de acesso',
                    'message' => $mensagem
                ]);
            } catch (\Exception $e) {
                return response_json([
                    'item' => (object) $item,
                    'message' => 'Usuário cadastrado mas houve um erro ao enviar sua nova senha.'
                ]);
            }

            return response_json([
                'message' => 'Usuário cadastrado com sucesso',
                'item' => (object) $item
            ]);
        } catch (\Exception $e) {
            return response_json([
                'message' => 'Ocorreu um erro ao cadastrar o usuário'
            ], 500);
        }
    });

    Route::get('/{id_usuario:\d+}/editar', function ($id_usuario) {
        $item = (array) users()->find($id_usuario);

        return view(USERS_VIEW_PATH . '.form', $item);
    });

    Route::post('/{id_usuario:\d+}/editar', function ($id) {
        find_or_fail(users(), $id);

        validate(users_validation_rules($id));

        $item = array_only(input(), array_keys(users_validation_rules()));

        try {
            users()->update($item, $id);

            $item['id'] = $id;

            return response_json([
                'message' => 'Usuário atualizado com sucesso',
                'item' => (object) $item
            ]);
        } catch (\Exception $e) {
            return response_json([
                'message' => 'Ocorreu um erro ao atualizar o usuário'
            ], 500);
        }
    });

    Route::post('/{id_usuario:\d+}/toggle-status', function ($id) {
        $status = (int)input('status') === 1 ? 0 : 1;

        try {
            users()->update(compact('status'), $id);

            return response_json([
                'status' => $status,
                'message' => 'Usuário atualizado com sucesso'
            ]);
        } catch (\Exception $e) {
            return response_json([
                'message' => 'Ocorreu um erro ao atualizar o usuário'
            ], 500);
        }
    });

    Route::delete('/{id_usuario:\d+}', function ($id) {
        find_or_fail(users(), $id);

        try {
            users()->update(['deleted_at' => date('Y-m-d H:i:s')], $id);

            return response_json([
                'message' => 'Usuário excluído com sucesso'
            ]);
        } catch (\Exception $e) {
            return response_json([
                'message' => 'Ocorreu um erro ao excluir o usuário'
            ], 500);
        }
    });
});
