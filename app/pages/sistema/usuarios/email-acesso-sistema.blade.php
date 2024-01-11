<html>

<head>
    <meta http-equiv="Content-Type" content="text/html">
    <meta charset="utf-8">
</head>

<body style="background-color: #f4f4f4; font-family: Arial; font-size: 16px;">
    <center>
        <div style="max-width: 800px; width:100%; background-color: #fff; color: #333;">
            <div style="background-color:#086c7e;" align="center">
                <img src="{{ url('public/img/logo-verde.png') }}"
                    style="padding:20px; width: 100px; border-radius: 5px;" />

            </div>
            <div class="arial" id="form" style="padding:40px; border: 1px solid #CCC;" align="justify">
                <p>
                    Olá {{ $nome }}, tudo bom?
                </p>
                <p>
                    A <strong>{{ config('app_name') }}</strong> cadastrou você como usuário do sistema.
                </p>
                <br>
                <p>
                    Acesse
                    <a href="{{ url('/sistema/auth/login') }}"
                        style="font-weight: bold; text-decoration: none; color: #086c7e;">Login -
                        {{ config('app_name') }}</a>
                    para conhecer o sistema.
                </p>
                <p>
                    Use o email: <br><strong>{{ $email }}</strong>
                </p>
                <p>
                    Use a senha: <br><strong>{{ $senha }}</strong>
                </p>
                <br>

                <div style="font-size: 12px;">
                    Email gerado em {{ date('d/m/Y H:i:s') }}
                </div>
            </div>
            <div style="background-color: #086c7e; height: 40px; padding-top: 20px; color: #fff; font-size: 14px;"
                align="center">
                {{ config('app_name') }} &copy; {{ date('Y') }} - Todos os Direitos Reservados
            </div>
        </div>
    </center>
</body>

</html>
