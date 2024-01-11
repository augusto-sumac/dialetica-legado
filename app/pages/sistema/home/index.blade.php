@extend('layouts.sistema')

<?php
$options = [
    'title' => 'Início',
    'icon' => 'tachometer-alt',
];
?>

@section('content')
    @include('components.content-header', $options)

    <h2 class="mb-5">Artigos</h2>

    <div class="row home-cards">
        <div class="col-12 col-lg-3">
            <div class="card bg-blue-400 text-white d-flex align-items-center">
                <div class="card-body text-center pt-4 px-4 pb-0">
                    <h3>{{ $articles->submitted }}</h3>
                    <h6>Enviados</h6>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-3">
            <div class="card bg-purple-400 text-white d-flex align-items-center">
                <div class="card-body text-center pt-4 px-4 pb-0">
                    <h3>{{ $articles->approved }}</h3>
                    <h6>Aprovados / Pagamento Pendente</h6>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-3">
            <div class="card bg-orange-500 text-white d-flex align-items-center">
                <div class="card-body text-center pt-4 px-4 pb-0">
                    <h3>{{ $articles->publishing }}</h3>
                    <h6>Em Processo de Editoração</h6>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-3">
            <div class="card bg-teal-400 text-white d-flex align-items-center">
                <div class="card-body text-center pt-4 px-4 pb-0">
                    <h3>{{ $articles->published }}</h3>
                    <h6>Publicados</h6>
                </div>
            </div>
        </div>
    </div>

    <hr class="my-5">

    <h2 class="mb-5">Revisões</h2>

    <div class="row home-cards">
        <div class="col-12 col-md-3 text-blue-400">
            <h3>{{ $reviews->created ?? 0 }} solicitadas</h3>
        </div>
        <div class="col-12 col-md-3 text-orange-500">
            <h3>{{ $reviews->pending ?? 0 }} em andamento</h3>
        </div>
        <div class="col-12 col-md-3 text-teal-400">
            <h3>{{ $reviews->finished ?? 0 }} concluídas</h3>
        </div>
    </div>

    <hr class="my-5">

    <h2 class="mb-5">Autores cadastrados</h2>

    <div class="row home-cards">
        <div class="col-12 col-md-3">
            <h3>{{ $authors->total }}</h3>
            <span class="text-muted">Todos</span>
        </div>
        <div class="col-12 col-md-3">
            <h3>{{ $authors->previous_month }}</h3>
            <span class="text-muted">Mês passado</span>
        </div>
        <div class="col-12 col-md-3">
            <h3>{{ $authors->current_month }}</h3>
            <span class="text-muted">Este mês</span>
        </div>
    </div>
@endsection


@section('js')
    <script>
        $(document).ready(function() {

        });
    </script>
@endsection

@section('css')
    <style>
        .home-cards .card {
            min-height: 110px;
        }

        .home-cards h3,
        .home-cards h6 {
            margin: 0;
        }

        .home-cards h3 {
            font-size: 2.2rem;
        }

        .home-cards h6 {
            font-size: 1.2rem;
        }

    </style>
@endsection
