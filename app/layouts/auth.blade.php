<!DOCTYPE html>
<html lang="pt-br" class="{{ isset($app_class) ? $app_class : '' }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="EDITORA DIALÉTICA" />
    <meta name="author" content="Phixies" />

    <title>EDITORA DIALÉTICA - LOGIN</title>

    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Noto+Sans:400,700,400italic">
    <link rel="stylesheet" type="text/css"
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css"
        integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css"
        href="https://cdn.datatables.net/v/bs5/dt-1.11.3/fc-4.0.1/fh-3.2.0/r-2.2.9/sc-2.0.5/sl-1.3.3/datatables.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.2.1/dist/sweetalert2.min.css"
        integrity="sha256-bu+Q0/9Hd3s1aqqSfpxp4Ce2ZjPhyyQlx0eU7n/JHvU=" crossorigin="anonymous">
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta2/dist/css/bootstrap-select.min.css"
        integrity="sha256-UqiEyrW1sB5d6ZDzcWXKfYCR4MKVYMEdXNjJde84cjc=" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tagin@2.0.2/dist/tagin.css"
        integrity="sha256-+gcWeMFGmGX2AUEgmReKC62pfr/iky8GCcIJZLXXrIo=" crossorigin="anonymous">

    <link rel="stylesheet" href="{{ url('public/vendor/fontawesome/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ url('public/vendor/fontawesome/css/brands.min.css') }}">
    <link rel="stylesheet" href="{{ url('public/vendor/fontawesome/css/solid.min.css') }}">

    <link rel="stylesheet" href="{{ url('public/css/theme.css?' . APP_VERSION) }}">
    <link rel="stylesheet" href="{{ url('public/css/auth.css?' . APP_VERSION) }}">
    <link rel="stylesheet" href="{{ url('public/css/colors.css?' . APP_VERSION) }}">
    <link rel="stylesheet" href="{{ url('public/css/app-utils.css?' . APP_VERSION) }}">

    @yield('css')
</head>

<body>
    <div id="app" class="d-flex align-items-center position-relative">

        @if (env('DEV_MODE'))
            <div class="alert alert-danger text-center m-0 fixed-top" style="border-radius: 0;">
                ATENÇÃO - VERSÃO DE DESENVOLVIMENTO
            </div>
        @endif

        <div class="container-fluid py-5">
            <div class="row justify-content-center">
                <div class="col text-center">
                    <img class="app-logo my-5" src="{{ url('public/img/logo.svg') }}" alt="EDITORA DIALÉTICA" />
                </div>
            </div>

            {{ showFlashAlerts() }}

            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"
        integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/jquery.validate.min.js"
        crossorigin="anonymous">
    </script>
    <script type="text/javascript"
        src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/additional-methods.min.js" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-mask-plugin@1.14.16/dist/jquery.mask.min.js"
        integrity="sha256-Kg2zTcFO9LXOc7IwcBx1YeUBJmekycsnTsq2RuFHSZU=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-maskmoney@3.0.2/dist/jquery.maskMoney.min.js"
        integrity="sha256-U0YLVHo5+B3q9VEC4BJqRngDIRFCjrhAIZooLdqVOcs=" crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous">
    </script>
    <script type="text/javascript"
        src="https://cdn.datatables.net/v/bs5/dt-1.11.3/fc-4.0.1/fh-3.2.0/r-2.2.9/sc-2.0.5/sl-1.3.3/datatables.min.js">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.2.1/dist/sweetalert2.min.js"
        integrity="sha256-OVxeY1nP2DXp15LcHll2UDTcwaqvHlJ3xj1CjVLqvsY=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/handlebars@4.7.7/dist/handlebars.min.js"
        integrity="sha256-ZSnrWNaPzGe8v25yP0S6YaMaDLMTDHC+4mHTw0xydEk=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta2/dist/js/bootstrap-select.min.js"
        integrity="sha256-KK/CsQKh6Rb0LsRn4Z8Jcs4h7rRquelIb4EjQm6ige4=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta2/dist/js/i18n/defaults-pt_BR.min.js"
        integrity="sha256-PCUnT1xSuwnYhR3Hp1+vRimrmjjekfpAmKjq7ZhTfw8=" crossorigin="anonymous"></script>

    <script>
        var baseUrl = "{{ rtrim(url('/'), '/') }}",
            publicUrl = "{{ rtrim(url('public'), '/') }}";
    </script>
    <script src="{{ url('public/js/jquery.methodOverride.js?' . APP_VERSION) }}"></script>
    <script src="{{ url('public/js/jquery.serializejson.min.js?' . APP_VERSION) }}"></script>
    <script src="{{ url('public/js/shared.js?' . APP_VERSION) }}"></script>
    <script src="{{ url('public/js/auth.js?' . APP_VERSION) }}"></script>

    @yield('js')

</body>

</html>
