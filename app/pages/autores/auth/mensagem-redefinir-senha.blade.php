<html>

<head>
    <meta http-equiv="Content-Type" content="text/html">
    <meta charset="utf-8">
</head>

<body style="background-color: #66191E; font-family: Arial; font-size: 16px;">
    <center>
        <div style="max-width: 800px; width:100%; background-color: #fff; color: #333;">
            <div style="background-color:#086c7e;" align="center">
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents(ROOT_PATH . 'public/img/logo.png')) }}"
                    style="padding:20px; width: 100px; border-radius: 5px;" />

            </div>
            <div class="arial" id="form" style="padding:40px; border: 1px solid #CCC;" align="justify">
                <p>
                    Olá {{ $nome }}, tudo bom?
                </p>
                <br>
                <p>
                    Uma nova senha foi gerada para você!
                </p>
                <br>
                <p>
                    Acesse
                    <a href="{{ url('/auth/login') }}"
                        style="font-weight: bold; text-decoration: none; color: #66191E;">Login -
                        {{ env('APP_NAME', 'Dialética') }}</a>
                    para acessar o sistema.
                </p>
                <p>
                    Use o email: <br><strong>{{ $email }}</strong>
                </p>
                <p>
                    Use a senha: <br><strong>{{ $senha }}</strong>
                </p>
                @if (isset($token))
                    <br>
                    <br>
                    <p>
                        Se não quiser usar a senha definida pelo sistema
                        <a href="{{ url('/auth/redefinir-senha?token=' . $token) }}"
                            style="font-weight: bold; text-decoration: none; color: #66191E;">
                            clique aqui
                        </a>
                        para definir sua nova senha de acesso
                    </p>
                @endif
            </div>
            <div style="background-color: #66191E; height: 40px; padding-top: 20px; color: #fff; font-size: 14px;"
                align="center">
                {{ config('app_name', 'PRF') }} &copy; {{ date('Y') }} - Todos os Direitos Reservados
            </div>
        </div>
    </center>
</body>

</html>
