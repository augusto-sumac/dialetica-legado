@extend('layouts.autores')

<?php
$options = [
    'title' => 'Minhas Revisões',
    'icon' => 'spell-check',
    'route_add' => AUTHOR_REVIEWS_BASE_URL . '/adicionar',
];
?>
@section('content')
    @include('components.content-header', $options)

    <div class="card">
        <div class="row">
            <div class="col-12 col-lg-7 col-xl-8 border-end remove-border-end-on-mobile">
                <div class="card-body pb-0">
                    <div class="form-group">
                        <div class="row align-items-center">
                            <div class="col status-wrapper">
                                <h4 class="m-0">
                                    #ID {{ str_pad_id($id) }} {{ $status_badge }}
                                </h4>
                            </div>
                            <div class="col w-80px text-end">
                                <a href="{{ url(AUTHOR_REVIEWS_BASE_URL) }}" class="btn btn-sm btn-secondary">Voltar</a>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <strong>Título do Trabalho</strong>
                        <div>
                            {{ $title }}
                        </div>
                    </div>

                    <div class="form-group">
                        <strong>Data Envio</strong>
                        <div>
                            {{ datetimeFromMySql($created_at) }}
                        </div>
                    </div>

                    <div class="form-group">
                        <strong>Data Prev. Entrega</strong>
                        <div>
                            {{ substr(datetimeFromMySql($due_date), 0, 10) }}
                        </div>
                    </div>

                </div>

                @if ($status === 70)
                    <hr>

                    <div class="card-body py-0">
                        <h2>Detalhes da revisão</h2>
                        <div class="form-group">
                            <strong>Arquivo Prova</strong>
                            <div>
                                <a href="{{ url('/download-article-attachment?path=' . array_get($proof_attachment, 'path')) }}"
                                    target="_blank">
                                    Baixe aqui ({{ array_get($proof_attachment, 'name', 'Não Informado') }})
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body py-0">
                        <div class="form-group">
                            <strong>Arquivo Final</strong>
                            <div>
                                <a href="{{ url('/download-article-attachment?path=' . array_get($final_attachment, 'path')) }}"
                                    target="_blank">
                                    Baixe aqui ({{ array_get($final_attachment, 'name', 'Não Informado') }})
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body py-0 pb-lg-3">
                        <div class="form-group m-0">
                            <strong>Observação do revisor</strong>
                            <div>
                                {{ $review_comment ? $review_comment : 'Não Informado' }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-12 col-lg-5 col-xl-4 ps-lg-0">
                <hr class="d-xs-block d-lg-none">

                <div class="card-body py-0 pt-lg-4">
                    <div class="form-group">
                        <strong>AUTOR</strong>
                        <div class="mt-4">
                            <strong class="text-muted">{{ $author->name }}</strong>
                            <br>
                            {{ $author->role }}
                            <br>
                            {{ $author->email }}
                        </div>
                    </div>
                </div>

                @if ($status >= 30)
                    <hr>

                    <div class="card-body pt-0 pb-0">
                        <div class="form-group">
                            <strong>PAGAMENTO</strong> {{ $payment_status_badge }}

                            <div class="mt-3 fw-bold fs-1">
                                R$ {{ toMoney($amount) }}
                            </div>

                            @if (!$payment_status)
                                <div class="mt-3">
                                    <a class="btn btn-success"
                                        href="{{ url(AUTHOR_PAYMENTS_BASE_URL . '/' . $id . '?complete-form=true') }}">
                                        Pagar Agora <span class="ms-1 fas fa-arrow-right"></span>
                                    </a>
                                </div>
                            @endif


                        </div>

                    </div>

                    {{ renderPaymentInfo($id, $payment_status) }}
                @endif
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {});
    </script>
@endsection

@section('css')
    <style>
        @media(max-width: 992px) {
            .remove-border-end-on-mobile {
                border-right: 0 none !important;
            }
        }
    </style>
@endsection
