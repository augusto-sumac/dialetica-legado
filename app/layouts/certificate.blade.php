<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="CERTIFICADO" />
    <meta name="author" content="Phixies" />

    <title>CERTIFICADO</title>

    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Noto+Sans:400,700,400italic">
    <link rel="stylesheet" type="text/css"
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css"
        integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.2.1/dist/sweetalert2.min.css"
        integrity="sha256-bu+Q0/9Hd3s1aqqSfpxp4Ce2ZjPhyyQlx0eU7n/JHvU=" crossorigin="anonymous">

    <link rel="stylesheet" href="{{ url('public/vendor/fontawesome/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ url('public/vendor/fontawesome/css/brands.min.css') }}">
    <link rel="stylesheet" href="{{ url('public/vendor/fontawesome/css/solid.min.css') }}">

    <link rel="stylesheet" href="{{ url('public/css/theme.css?' . APP_VERSION) }}">
    <link rel="stylesheet" href="{{ url('public/css/app.css?' . APP_VERSION) }}">
    <link rel="stylesheet" href="{{ url('public/css/colors.css?' . APP_VERSION) }}">
    <link rel="stylesheet" href="{{ url('public/css/app-utils.css?' . APP_VERSION) }}">

    <?php
    $months = [
        1 => 'Janeiro',
        2 => 'Fevereiro',
        3 => 'Março',
        4 => 'Abril',
        5 => 'Maio',
        6 => 'Junho',
        7 => 'Julho',
        8 => 'Agosto',
        9 => 'Setembro',
        10 => 'Outubro',
        11 => 'Novembro',
        12 => 'Dezembro',
    ];
    ?>

    <style>
        .app {
            position: relative;
        }

        .row {
            height: 100%;
            min-height: 100%;
        }

        .wrapper {}

        .certificate {
            width: 29.7cm;
            height: 21cm;
            margin: 0 auto;

            background: #fff url('{{ url('public/img/bg-certificado.jpeg?time') }}');
            background-size: cover;

            box-shadow: 0 0 3rem rgb(18 38 63 / 18%);
        }

        .certificate.no-shadow {
            box-shadow: none;
        }

        .certificate p {
            padding: 20% 35% 20% 8%;
            font-size: 85%;
            text-align: justify;
        }

        .btn-download {
            position: fixed;
            bottom: 20px;
            left: 50%;
            margin-left: -35px;
            width: 70px;
            line-height: 70px;
            padding: 0;
            text-align: center;
            border-radius: 70px;
        }

        .loading {
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.05);
            display: none;
        }

    </style>
</head>

<body>
    <div id="app" class="app">

        <div class="row align-items-center">

            <div class="col justify-content-center">

                <div class="wrapper">

                    <div class="certificate">

                        <p>
                            A quem possa interessar,
                            <br>
                            <br>
                            Pelo presente certificado, atesto para os devidos fins que o artigo intitulado
                            <strong>{{ $title }}</strong>, de
                            autoria de {{ $authors_names }} foi aprovado para publicação como capítulo de livro
                            pela Editora Dialética e será publicado em até 60 (sessenta) dias, contados a partir da data
                            de
                            hoje.
                            <br>
                            <br>
                            São Paulo, {{ $day }} de {{ array_get($months, (int) $month) }} de
                            {{ $year }}.
                        </p>

                    </div>

                </div>

            </div>

        </div>

        <button class="btn btn-lg btn-dark btn-download">
            <span class="fas fa-download"></span>
        </button>

        <div class="loading align-content-center justify-content-center align-items-center">
            <div class="spinner-border" role="status"></div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"
        integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/jquery.validate.min.js"
        crossorigin="anonymous">
    </script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.2.1/dist/sweetalert2.min.js"
        integrity="sha256-OVxeY1nP2DXp15LcHll2UDTcwaqvHlJ3xj1CjVLqvsY=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"
        integrity="sha256-mMzxeqEMILsTAXYmGPzJtqs6Tn8mtgcdZNC0EVTfOHU=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"
        integrity="sha256-6H5VB5QyLldKH9oMFUmjxw2uWpPZETQXpCkBaDjquMs=" crossorigin="anonymous"></script>

    <script>
        var appVersion = {{ APP_VERSION }};
    </script>

    <script>
        $(document).ready(function() {
            var ratio = 0.6625;

            /* function updateCertificateSize() {
                var width = $('.certificate').width();
                $('.certificate').height(Math.ceil(width * ratio));
            }

            $(window).on('resize', function() {
                updateCertificateSize();
            });

            updateCertificateSize();*/

            function adjustFontSize() {
                var letters = $('.certificate p').text().replace(/\s|\n/gi, '').split('').length;

                var fontSize = '16px';

                if (letters > 600) {
                    fontSize = '15px';
                }

                if (letters > 800) {
                    fontSize = '14px';
                }

                if (letters > 1000) {
                    fontSize = '13px';
                }

                $('.certificate p').css('font-size', fontSize);
            }

            adjustFontSize();

            $('.btn-download').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                $('.loading').show().addClass('d-flex');

                var pdf = new jspdf.jsPDF({
                    orientation: 'l',
                    unit: 'px',
                    format: 'a4',
                    compress: false,
                });

                pdf.deletePage(1);

                var page = document.querySelector('.certificate');

                page.classList.add('no-shadow');

                html2canvas(page).then(function(canvasPage) {
                    page.classList.remove('no-shadow');
                    pdf.addPage('a4', 'l');
                    let imgData = canvasPage.toDataURL('image/jpeg', 1);
                    pdf.addImage(imgData, 'jpeg', 0, 0, 632, 447);
                    pdf.save(`DIALÉTICA-CERTIFICADO-ARTIGO-PUBLICADO`);

                    $('.loading').hide().removeClass('d-flex');
                });

                setTimeout(function() {
                    $('.loading').hide().removeClass('d-flex');
                }, 5000);

                return false;
            });
        });
    </script>

</body>

</html>
