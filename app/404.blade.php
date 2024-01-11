@layout('layouts.bootstrap')

@section('body')

    <body class="d-flex align-items-center bg-auth border-top border-top-2 border-primary">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-md-5 col-xl-4 my-5">

                    <div class="text-center">

                        <h6 class="text-uppercase text-muted mb-5">
                            erro 404
                        </h6>

                        <h1 class="display-4 mb-5">
                            Página não encontrada
                        </h1>

                        <a href="{{ url('/') }}" class="btn btn-lg btn-primary">
                            Retorne ao Dashboard
                        </a>

                    </div>

                </div>
            </div>
        </div>
    </body>

@endsection
