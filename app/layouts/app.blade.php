<!DOCTYPE html>
<html lang="pt-br" class="{{ isset($app_class) ? $app_class : '' }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="{{ $app_title }}" />
    <meta name="author" content="Phixies" />

    <title>{{ $app_title }}</title>

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
    <link rel="stylesheet" href="{{ url('public/css/app.css?' . APP_VERSION) }}">
    <link rel="stylesheet" href="{{ url('public/css/colors.css?' . APP_VERSION) }}">
    <link rel="stylesheet" href="{{ url('public/css/app-utils.css?' . APP_VERSION) }}">

    @yield('css')
</head>

<body>
    <div id="app" class="app">

        <aside class="position-fixed fixed-start h-100 d-flex flex-column flex-shrink-0 shadow sidebar">
            <div class="d-block">
                <a href="{{ url($menu_items[0]->url) }}"
                    class="d-flex justify-content-md-centers align-items-center text-decoration-none app-logo"
                    title="{{ env('APP_NAME') }}" data-bs-toggle="tooltips" data-bs-placement="right">
                    <img src="{{ url('public/img/logo.svg') }}" class="logotipo" alt="{{ env('APP_NAME') }}">
                </a>
            </div>

            <div class="user-data">
                <div class="user-name">{{ $user->name }}
                </div>
                <div class="user-email">{{ $user->email }}</div>
            </div>

            <div class="mb-auto main-menu">
                <ul class="nav nav-pills flex-column">
                    @foreach ($menu_items as $item)
                        @if (isset($item->url))
                            <li class="nav-item">
                                <a href="{{ url($item->url) }}"
                                    class="nav-link {{ addActiveClass($item->url, preg_match('/(artigos|revisoes|minhas-coletaneas)\/adicionar/i', urlCurrent()) ? true : $item->exact) }}"
                                    title="{{ $item->text }}" data-bs-toggle="tooltips" data-bs-placement="right">
                                    <span class="{{ $item->icon }} fa-fw"></span>
                                    <span>{{ $item->text }}</span>
                                </a>
                            </li>
                        @else
                            <li class="nav-item separator">
                                <span class="nav-link disabled">
                                    {{ $item->text }}
                                </span>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </div>

            <div class="footer-menu">
                <a href="javascript:void(0)" class="logout" data-bs-toggle="tooltips" data-bs-placement="right"
                    title="Sair">
                    <span class="fas fa-sign-out-alt"></span>
                    <span>Sair</span>
                </a>
            </div>

        </aside>

        <nav class="navbar">
            <div class="row align-items-center">
                <div class="col">
                    <a href="{{ url($menu_items[0]->url) }}" class="text-decoration-none"
                        title="{{ env('APP_NAME') }}" data-bs-toggle="tooltip" data-bs-placement="right">
                        <img src="{{ url('public/img/logo.png') }}" class="logotipo" alt="{{ env('APP_NAME') }}">
                    </a>
                </div>
                <div class="col-auto">
                    <a href="javascript:void(0)" class="sidebar-toggle">
                        <span class="fas fa-bars"></span>
                    </a>
                </div>
            </div>
        </nav>

        <main class="app-main flex-grow-1 position-relative">

            @if (env('DEV_MODE'))
                <div class="alert alert-danger text-center m-0 fixed-start dev-mode-alert" style="border-radius: 0;">
                    ATENÇÃO - VERSÃO DE DESENVOLVIMENTO
                </div>
            @endif

            <div class="d-flex h-100 flex-column main-content-wrapper">
                <div class="container-fluid main-content">
                    {{ showFlashAlerts() }}
                    @yield('content')
                </div>
                <div class="mt-auto text-center">
                    <div class="footer-content">
                        <div>
                            © {{ date('Y') }} <strong>{{ env('APP_NAME') }}</strong> Produzido por <a
                                href="http://phixies.com" class="text-gray-600" target="_blank">Phixies</a>
                        </div>
                    </div>
                </div>
        </main>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"
        integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/jquery.validate.min.js"
        crossorigin="anonymous"></script>
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.6.2/dist/chart.min.js"
        integrity="sha256-D2tkh/3EROq+XuDEmgxOLW1oNxf0rLNlOwsPIUX+co4=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/tagin@2.0.2/dist/tagin.min.js"
        integrity="sha256-ccAl1/1cAujfOiJB72+Wl+e92ainZGhhbUJm05c6974=" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/dayjs@1.10.7/dayjs.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1.10.7/locale/pt-br.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1.10.7/plugin/updateLocale.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1.10.7/plugin/advancedFormat.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1.10.7/plugin/calendar.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1.10.7/plugin/customParseFormat.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1.10.7/plugin/localeData.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1.10.7/plugin/localizedFormat.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1.10.7/plugin/isToday.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1.10.7/plugin/isYesterday.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1.10.7/plugin/minMax.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1.10.7/plugin/duration.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1.10.7/plugin/weekOfYear.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1.10.7/plugin/isSameOrAfter.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1.10.7/plugin/isSameOrBefore.js"></script>

    <script>
        var baseUrl = "{{ $base_url }}",
            publicUrl = "{{ rtrim(url('public'), '/') }}",
            appVersion = {{ APP_VERSION }};
    </script>
    <script src="{{ url('public/js/jquery.methodOverride.js?' . APP_VERSION) }}"></script>
    <script src="{{ url('public/js/jquery.serializejson.min.js?' . APP_VERSION) }}"></script>
    <script src="{{ url('public/js/charts.js?' . APP_VERSION) }}"></script>
    <script src="{{ url('public/js/dayjs.js?' . APP_VERSION) }}"></script>
    <script src="{{ url('public/js/shared.js?' . APP_VERSION) }}"></script>
    <script src="{{ url('public/js/app.js?' . APP_VERSION) }}"></script>

    <input type="hidden" name="current_user_email" value="{{ $user->email }}">

    <script>
        $(document).ready(function() {
            var currentVersion = parseInt(localStorage.getItem('app_version'));

            if (currentVersion !== appVersion) {
                Object.keys(localStorage).filter(function(k) {
                        return k.includes('DataTables')
                    })
                    .map(function(k) {
                        localStorage.removeItem(k);
                    });
            }

            localStorage.setItem('app_version', appVersion);
        });

        const USER = {{ json_encode(logged_author()) }};
    </script>

    @yield('js')

    @yield('modals')

</body>

</html>
