<?php

// [Middleware] Remove os dados da sessão
Route::filter('clear', function () {
    unset($_SESSION['user']);
    unset($_SESSION['last_activity']);
});

function redirect_to_login()
{
    if (isAjax()) {
        return response_json(['redirect' => 'sistema/auth/login'], 401);
    }

    return redirect('/sistema/auth/login');
}

Route::filter('auth', function () {
    if (preg_match('/\/sistema/i', uri_path()) && !preg_match('/\/auth/i', uri_path())) {
        if (!isset($_SESSION['user']) || empty($_SESSION['user'])) {
            return redirect_to_login();
        }

        $last_activity = isset($_SESSION['last_activity']) ? $_SESSION['last_activity'] : 0;

        // Sessão expira em 12 horas
        if ($last_activity && $last_activity < (time() - (12 * 60 * 60))) {
            return redirect_to_login();
        }

        $_SESSION['last_activity'] = time();
    }
});

Route::get('/sistema/auth', fn () => redirect_to_login());
Route::get('/sistema/auth/logout', fn () => redirect_to_login());

Route::group([
    'prefix' => 'sistema/auth',
    'before' => 'clear'
], function () {
    Route::get('login', render_page('sistema.auth.login'));

    Route::post('login', function () {
        $email = input('email');
        $senha = input('senha');

        try {
            $user = users()->where_status(1)->where_email($email)->first();

            if (!$user) {
                return response_json(array(
                    'message' => 'Email ou senha inválidos'
                ), 422);
            }
        } catch (\Exception $e) {
            return response_json(array(
                'message' => 'Email ou senha inválidos'
            ), 422);
        }

        if (!env('DEV_MODE') && !Hash::check($senha, $user->password)) {
            return response_json(array(
                'message' => 'Email ou senha inválidos'
            ), 422);
        }

        $_SESSION['user'] = (object)array_only((array)$user, ['id', 'name', 'email']);

        return response_json(array(
            'nome' => $user->name,
            'redirect' => url('/sistema')
        ));
    });

    Route::get('esqueci-minha-senha', render_page('sistema.auth.index'));
    Route::post('esqueci-minha-senha', function () {
        $email = input('email');

        $user = users()->where_status(1)->where_email($email)->first();

        if (!$user) {
            return response_json(array(
                'message' => 'Email inválido'
            ), 422);
        }

        $recovery_password_token = Str::random(8);

        while (
            users()->where_recovery_password_token($recovery_password_token)->first()
        ) {
            $recovery_password_token = Str::random(8);
        }

        $senha = gerar_senha();

        users()
            ->update([
                'password' => Hash::make($senha),
                'recovery_password_token' => $recovery_password_token
            ], $user->id);

        try {
            $mensagem = view('pages.sistema.auth.mensagem-redefinir-senha', array(
                'nome' => get_first_name($user->name),
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
                'redirect' => url('sistema/auth/login')
            ));
        } catch (\Exception $e) {
            return response_json(array(
                'message' => 'Houve um erro ao enviar sua nova senha. Por favor tente mais tarde',
            ), 500);
        }
    });

    Route::get('redefinir-senha', render_page('sistema.auth.redefinir-senha'));
    Route::post('redefinir-senha', function () {
        $token = input('token');
        $senha = input('senha');
        $senha_confirmation = input('senha_confirmation');

        $user = users()->where_recovery_password_token($token)->first();

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

        users()->update([
            'password' => Hash::make($senha),
            'recovery_password_token' => null
        ], $user->id);

        return response_json(array(
            'message' => 'Senha definida com sucesso',
            'redirect' => url('sistema/auth/login')
        ));
    });
});
