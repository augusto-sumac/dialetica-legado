@extend('layouts.autores')

<?php
$options = [
    'title' => 'Minhas Coletâneas',
    'icon' => 'sitemap',
];

$collection_url = url('/coletanea/' . $token);
?>
@section('content')
    @include('components.content-header', $options)

    <div class="card" v-scope v-cloak id="global_scope">
        <div class="row">
            <div class="col-12 col-lg-7 col-xl-8 border-end remove-border-end-on-mobile">
                <div class="card-body pb-0">
                    <div class="form-group">
                        <div class="row align-items-center">
                            <div class="col status-wrapper">
                                <h4 class="m-0">
                                    #ID {{ str_pad_id($id) }}
                                    <span class="badge bg-{{ $status_badge['color'] }}">
                                        {{ $status_badge['label'] }}
                                    </span>
                                </h4>
                            </div>
                            <div class="col w-80px text-end">
                                <a href="{{ url(AUTHOR_COLLECTIONS_BASE_URL) }}" class="btn btn-sm btn-secondary">Voltar</a>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">

                        @if ($cover_image)
                            <div class="col-auto">
                                <div class="position-relative collection-cover w-150px">
                                    <div :style="store.cover_image_backgroud"
                                        class="collection-cover-thumbnail img-thumbnail authors"></div>
                                    <div
                                        class="collection-cover-buttons position-absolute top-50 start-50 translate-middle">
                                        <button type="button" class="btn btn-success" title="Download"
                                            v-if="store.is_valid_cover_image" @click="store.downloadCoverImage">
                                            <span class="fas fa-download"></span>
                                        </button>
                                        <button type="button" class="btn btn-dark" title="Ampliar"
                                            @click="store.zoomCoverImage">
                                            <span class="fas fa-expand-arrows-alt"></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="col">
                            <div class="form-group">
                                <strong>Coletânea</strong>
                                <div>
                                    {{ $name }}
                                </div>
                            </div>

                            <div class="form-group">
                                <strong>Descrição</strong>
                                <div>
                                    {{ $description }}
                                </div>
                            </div>
                        </div>
                    </div>

                    @if (isset($collection_url) && $collection_url && !in_array($status, ['PE', 'FL']))
                        <div class="form-group">
                            <strong>URL direta da coletânea</strong>
                            <div>
                                <button class="btn btn-sm btn-outline-secondary me-2" type="button"
                                    data-bs-toggle="tooltip" title="Copiar para área de transferência"
                                    onclick="copyCollectionUrl()">
                                    <span class="fas fa-clipboard"></span>
                                </button>
                                <span class="user-select-all">
                                    <a href="{{ $collection_url }}" target="_blank">{{ $collection_url }}</a>
                                </span>
                            </div>
                            <div>
                                <small>Envie a url abaixo para que as pessoas possam publicar diretamente da sua
                                    coletânea</small>
                            </div>
                        </div>
                    @endif


                </div>
            </div>

            <div class="col-12 col-lg-5 col-xl-4 ps-lg-0">
                <hr class="d-xs-block d-lg-none">
                <div class="card-body pb-0 pt-xs-0 pt-lg-4">
                    <div class="form-group">
                        <strong>Data Cadastro</strong>
                        <div>
                            {{ date('d/m/Y', strtotime($created_at)) }}
                        </div>
                    </div>

                    @if (!in_array($status, ['PE']))
                        <div class="form-group">
                            <strong>Data Limite Envio Artigos</strong>
                            <div>
                                {{ date('d/m/Y', strtotime($expires_at)) }}
                            </div>
                        </div>
                    @else
                        <div class="form-group">
                            <strong>Data Limite Análise</strong>
                            <div>
                                {{ date('d/m/Y', strtotime('+7 days', strtotime($created_at))) }}
                            </div>
                        </div>
                    @endif

                    <div class="form-group">
                        <strong>Área</strong>
                        <div>
                            {{ $nome_area }}
                        </div>
                    </div>

                    <div class="form-group">
                        <strong>Sub-Área</strong>
                        <div>
                            {{ $nome_sub_area }}
                        </div>
                    </div>

                    <div class="form-group">
                        <strong>Especialidade</strong>
                        <div>
                            {{ $nome_especialidade }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($status === 'FL')
        <div class="card">
            <div class="card-body">
                <h1 class="text-red-500">Coletânea não publicada</h1>

                <p>Infelizmente sua coletânea não atingiu a quantidade mínima de artigos para ser publicada.</p>

                <p>Caso deseje "reinicar" sua coletânea clique no botão abaixo.</p>

                <p>
                    <button type="button" class="btn btn-success btn-lg py-3 px-4 restart-collection">
                        <span class="fas fa-sync-alt me-2"></span>
                        Reiniciar Coletânea
                    </button>
                </p>
            </div>
        </div>
    @endif

    @if ($has_affiliate_coupon && !in_array($status, ['PE', 'FL']))
        @include('pages.autores.meus-dados.components.card-affiliate-coupon')
    @endif

    @include('pages.collections.components.form-organizadores', ['box_class' => 'card card-body'])

    @include('pages.collections.components.articles-list', ['box_class' => 'card'])
@endsection

@section('css')
    <style>
        #scope_organizadores .form-group:last-child {
            margin-bottom: 0;
        }

        @media(max-width: 992px) {
            .remove-border-end-on-mobile {
                border-right: 0 none !important;
            }
        }
    </style>
@endsection

@section('js')
    <script>
        $(document)
            .ready(() => {
                $(document)
                    .on('click', '.restart-collection', (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        dialogConfirm('Confirma reiniciar a Coletânea?', () => {
                            $.post('/minhas-coletaneas/{{ $id }}/reiniciar')
                                .done(() => {
                                    Swal
                                        .fire({
                                            icon: 'success',
                                            title: 'Fantástico',
                                            text: 'Sua coletânea foi reiniciada com sucesso!'
                                        })
                                        .then(() => {
                                            window.location.reload();
                                        });
                                });
                        });
                        return false;
                    });
            });

        function copyCollectionUrl() {
            try {
                navigator.clipboard.writeText('{{ $collection_url }}')
            } catch (e) {}

            Toast.fire({
                icon: 'success',
                title: 'URL copiada para sua área de transferência'
            });
        }
    </script>

    @include('pages.collections.components.form-js-module')

    <script type="module">
        import {
            createApp
        } from 'https://unpkg.com/petite-vue@0.4.1/dist/petite-vue.es.js?module';

        const store = window.{{ $app_store_id }};

        createApp({
            store,
        }).mount('#global_scope');
    </script>
@endsection
