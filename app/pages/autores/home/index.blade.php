@extend('layouts.autores')

<?php
$options = [
    'title' => 'Início',
    'icon' => 'tachometer-alt',
];
?>
@section('content')
    @include('components.content-header', $options)

    <h2 class="mb-5">Meus artigos</h2>

    <div class="row home-cards">
        <div class="col-12 col-lg-3">
            <div class="card bg-blue-400 text-white d-flex align-items-center">
                <div class="card-body text-center pt-4 px-4 pb-0">
                    <span class="h3 font-weight-bold text-contador">{{ $artices_submitted }}</span>
                    <h6 class="m-0 text-dashboard font-weight-bold">Enviados</h6>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-3">
            <div class="card bg-purple-400 text-white d-flex align-items-center">
                <div class="card-body text-center pt-4 px-4 pb-0">
                    <span class="h3 font-weight-bold text-contador">{{ $artices_approved }}</span>
                    <h6 class="m-0 text-dashboard font-weight-bold">Aprovados / Pagamento Pendente</h6>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-3">
            <div class="card bg-orange-500 text-white d-flex align-items-center">
                <div class="card-body text-center pt-4 px-4 pb-0">
                    <span class="h3 font-weight-bold text-contador">{{ $artices_publishing }}</span>
                    <h6 class="m-0 text-dashboard font-weight-bold">Em Processo de Editoração</h6>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-3">
            <div class="card bg-teal-400 text-white d-flex align-items-center">
                <div class="card-body text-center pt-4 px-4 pb-0">
                    <span class="h3 font-weight-bold text-contador">{{ $artices_published }}</span>
                    <h6 class="m-0 text-dashboard font-weight-bold">Publicados</h6>
                </div>
            </div>
        </div>
    </div>

    <hr class="my-5">

    <h2 class="mb-5">Minhas revisões</h2>

    <div class="row">
        <div class="col-md-3 text-revisoes" style="color: #2F80ED;">
            <strong>{{ $reviews_created ?? 0 }} </strong> solicitadas
        </div>
        <div class="col-md-3 text-revisoes" style="color: #F8931F;">
            <strong>{{ $reviews_pending ?? 0 }} </strong> em andamento
        </div>
        <div class="col-md-3 text-revisoes" style="color: #00BC92;">
            <strong>{{ $reviews_finished ?? 0 }} </strong> concluídas
        </div>
    </div>

    <hr class="my-5">

    <h2 class="mb-5">Serviços disponíveis</h2>

    <div class="row">

        <div class="col">
            <a href="{{ url('artigos/adicionar') }}"
                class="btn btn-lg d-block border bg-gray-200 fs-1 fw-bold p-5 text-gray-600">
                + Publicar novo artigo
            </a>
        </div>
        <div class="col">
            <a href="{{ url('revisoes/adicionar') }}"
                class="btn btn-lg d-block border bg-gray-200 fs-1 fw-bold p-5 text-gray-600">
                + Solicitar nova revisão
            </a>
        </div>

    </div>

    <div class="row d-none">
        <div class="col-12 col-md-6">
            <div class="card bg-danger text-white cards-servicos">
                <div class="card-body p-3 alinhar-card">
                    <strong style="font-size: 2.5rem; color: #4F4F4F; font-weight: bold;">+ Publicar novo artigo</strong>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="card bg-danger text-white cards-servicos">
                <div class="card-body p-3 alinhar-card">
                    <strong style="font-size: 2.5rem; color: #4F4F4F; font-weight: bold;">+ Solicitar nova revisão</strong>
                </div>
            </div>
        </div>
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

    </style>
@endsection
