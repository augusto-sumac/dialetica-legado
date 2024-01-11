<?php

// [Middleware] Remove os dados da sessão
Route::filter('author-clear', function () {
    unset($_SESSION['author']);
    unset($_SESSION['last_author_activity']);
});

function redirect_to_author_login()
{
    if (isAjax()) {
        return response_json(['redirect' => 'auth/login'], 401);
    }

    return redirect('/auth/login');
}

Route::filter('author-auth', function () {
    if (!preg_match('/\/sistema|\/cadastro|\/auth|public-coletanea/i', uri_path())) {
        if (!isset($_SESSION['author']) || empty($_SESSION['author'])) {
            return redirect_to_author_login();
        }

        $last_author_activity = isset($_SESSION['last_author_activity']) ? $_SESSION['last_author_activity'] : 0;

        // Sessão expira em 12 horas
        if ($last_author_activity && $last_author_activity < (time() - (12 * 60 * 60))) {
            return redirect_to_author_login();
        }

        $_SESSION['last_author_activity'] = time();
    }
});

Route::get('/auth', fn () => redirect_to_author_login());
Route::get('/auth/logout', fn () => redirect_to_author_login());

function process_register($auth = true)
{
    $rules = [
        'name' => 'required|max:200',
        'document' => 'required',
        // 'document' => 'required|cpf',
        'phone' => 'required|max:10',
        // 'phone' => 'required|match:/\([0-9]{2}\) [0-9]{5}-[0-9]{4}/',
        'role' => 'required|max:50',
        'email' => 'required|email|max:200',
    ];

    if ($auth) {
        $rules['password'] = 'required|min:6|max:40|confirmed';
    }

    validate($rules);

    $exists = authors()->where_email(input('email'))->first();

    if ($exists && (int)$exists->id !== (int)input('id')) {
        return response_json([
            'message' => 'Existem campos inválidos',
            'errors' => ['email' => 'Já existe um registro com o email informado!']
        ], 422);
    }

    $user = array_only(input(), ['name', 'document', 'phone', 'role', 'email', 'password']);

    if (!$auth) {
        $user['password'] = null;
    }

    $user['password'] = Hash::make($user['password']);
    $user['type'] = 'author';

    try {
        if (input('id')) {
            $user['recovery_password_token'] = null;
            authors()->update($user, input('id'));
            $user['id'] = input('id');
        } else {
            $user['id'] = authors()->insert_get_id($user);
        }
    } catch (\Exception $e) {
        return response_json(array(
            'message' => 'Falha ao efetuar o cadastro',
        ), 500);
    }

    try {
        $message = [
            'id' => 'd-6a3c7b756efd46fc838eea7b6b63ef9c',
            'vars' => [
                'first_name' => get_first_name($user['name']),
            ]
        ];

        add_job('sendMail', [
            'to' => env('DEV_MODE') ? 'marcioantunes.ma@gmail.com' : $user['email'],
            'subject' => 'Boas-vindas à Editora Dialética',
            'message' => $message
        ]);
    } catch (\Exception $e) {
        ///
    }

    if ($auth) {
        $user['curriculum'] = $user['curriculum'] ?? '';
        $user['curriculum_url'] = $user['curriculum_url'] ?? '';
        $_SESSION['author'] = (object)array_only((array)$user, ['id', 'name', 'email', 'role', 'curriculum', 'curriculum_url']);
        $_SESSION['last_author_activity'] = time();
    }

    $data = [
        'item' => ['id' => $user['id']]
    ];

    if (isset($_SESSION['COLLECTION_URL'])) {
        $data['redirect'] = $_SESSION['COLLECTION_URL'];
    }

    return response_json_success('Cadastro realizado com sucesso. Você será direcionado ao seu painel de gestão!', $data);
}

Route::group(
    [
        'prefix' => 'cadastro',
        'before' => 'author-clear'
    ],
    function () {
        Route::get('', render_page('autores.auth.register'));
        Route::post('', fn () => process_register());
    }
);

Route::group([
    'prefix' => 'auth',
    'before' => 'author-clear'
], function () {
    Route::get('login', render_page('autores.auth.login'));

    Route::post('login', function () {
        $email = input('email');
        $senha = input('senha');

        try {
            $user = authors()->where_email($email)->first();

            if (!$user) {
                return response_json(array(
                    'message' => 'Email ou senha inválidos'
                ), 422);
            }
        } catch (\Exception $e) {
            return response_json(array(
                'message' => 'Email ou senha inválidos',
            ), 422);
        }

        if (!env('DEV_MODE') && !Hash::check($senha, $user->password)) {
            return response_json(array(
                'message' => 'Email ou senha inválidos'
            ), 422);
        }

        $_SESSION['author'] = (object)array_only((array)$user, ['id', 'name', 'email', 'role', 'curriculum', 'curriculum_url']);
        $_SESSION['last_author_activity'] = time();

        return response_json(array(
            'name' => $user->name,
            'redirect' => isset($_SESSION['COLLECTION_URL']) ? $_SESSION['COLLECTION_URL'] : url('/')
        ));
    });

    // funcao que cadastra novos autores
    Route::post('cadastro', fn () => process_register());

    Route::get('esqueci-minha-senha', render_page('autores.auth.index'));
    Route::post('esqueci-minha-senha', function () {
        $email = input('email');

        $user = authors()->where_email($email)->first();

        if (!$user) {
            return response_json(array(
                'message' => 'Email inválido'
            ), 422);
        }

        $recovery_password_token = Str::random(8);

        while (
            authors()->where_recovery_password_token($recovery_password_token)->first()
        ) {
            $recovery_password_token = Str::random(8);
        }

        $senha = gerar_senha();

        authors()
            ->update([
                'password' => Hash::make($senha),
                'recovery_password_token' => $recovery_password_token
            ], $user->id);

        try {
            $mensagem = view('pages.autores.auth.mensagem-redefinir-senha', array(
                'nome' => $user->name,
                'email' => $user->email,
                'senha' => $senha,
                'token' => $recovery_password_token
            ));

            add_job('sendMail', [
                'to' => env('DEV_MODE') ? 'marcioantunes.ma@gmail.com' : $user->email,
                'subject' => 'Recuperação de Senha',
                'message' => $mensagem
            ]);

            return response_json(array(
                'message' => 'Olá ' . $user->name . '! Enviamos um email com instruções!',
                'redirect' => url('auth/login')
            ));
        } catch (\Exception $e) {
            return response_json(array(
                'message' => 'Houve um erro ao enviar sua nova senha. Por favor tente mais tarde',
            ), 500);
        }
    });

    Route::get('redefinir-senha', render_page('autores.auth.redefinir-senha'));
    Route::post('redefinir-senha', function () {
        $token = input('token');
        $senha = input('senha');
        $senha_confirmation = input('senha_confirmation');

        $user = authors()->where_recovery_password_token($token)->first();

        if (!$user) {
            return response_json(array(
                'message' => 'Token inválido ou expirado'
            ), 422);
        }

        if ($senha !== $senha_confirmation) {
            return response_json(array(
                'message' => 'As senhas devem ser iguais!'
            ), 422);
        }

        authors()->update([
            'password' => Hash::make($senha),
            'recovery_password_token' => null
        ], $user->id);

        $_SESSION['author'] = (object)array_only((array)$user, ['id', 'name', 'email', 'role']);
        $_SESSION['last_author_activity'] = time();

        return response_json(array(
            'message' => 'Senha definida com sucesso',
            'redirect' => url('/')
        ));
    });

    Route::get('/register/{token}', function ($token) {
        $user = authors()->where_recovery_password_token($token)->first();
        if (!$user) {
            return redirect('/auth/login');
        }

        return view('pages.autores.auth.register', (array)$user);
    });
});
