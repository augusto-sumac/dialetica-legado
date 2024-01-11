<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="PLATAFORMA EM MANUTENÇÃO" />
    <meta name="author" content="Phixies" />

    <title>PLATAFORMA EM MANUTENÇÃO</title>

    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Noto+Sans:400,700,400italic">
    <link rel="stylesheet" type="text/css"
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css"
        integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <style>
        html,
        body,
        #app {
            background-color: #7C1A26;
            color: #fff;
            text-align: center;
            width: 100%;
            height: 100%;
        }

        .logo {
            width: 80%;
            max-width: 400px;
            margin-bottom: 5rem;
        }

        h1 {
            font-size: 2rem;
            margin-bottom: 2rem;
        }

        p {
            font-size: 1rem;
            margin-bottom: 2rem;
        }

    </style>
</head>

<body>
    <div id="app" class="app">

        <div class="d-flex w-100 h-100 align-items-center justify-content-center">

            <div>
                <img src="{{ url('public/img/logo.svg') }}" class="logo" alt="{{ env('APP_NAME') }}">

                <h1>ESTAMOS ATUALIZANDO A PLATAFORMA</h1>

                <p>A plataforma de serviços de Dialética está sendo atualizada neste momento.</p>

                <p>Por favor, volte mais tarde para acessar sua conta.</p>
            </div>

        </div>

    </div>

</body>

</html>
