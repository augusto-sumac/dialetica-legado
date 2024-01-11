@extend('layouts.sistema')

<?php
$options = [
    'title' => 'Coletâneas',
    'icon' => 'level-up-alt fa-rotate-90',
    'url' => ARTICLES_COLLECTIONS_BASE_URL,
];
?>
@section('content')
    @include('components.content-header', $options)

    @include('components.form-header', ['title' => 'Coletâneas'])

    <form id="form-coletaneas" {{ attr_data_id($id) }} action="{{ urlCurrent() }}" method="POST" class="card tabs">

        @if ($id > 1)
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs">
                    <li class="nav-item">
                        <a class="nav-link active" href="#cadastro_geral">CADASTRO</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#lista_artigos">GERENCIAR ARTIGOS</a>
                    </li>
                </ul>
            </div>
        @endif

        <div id="tab_cadastro_geral" class="card-body">

            <div class="arrow-steps">
                <div class="step"><span>Ag. Aprovação</span></div>
                <div class="step success available"><span>Aprovar</span></div>
                <div class="step danger available"><span>Rejeitar</span></div>
            </div>

            <div class="arrow-steps">
                <div class="step"><span>Ag. Publicação</span></div>
                <div class="step primary available"><span>Produção</span></div>
                <div class="step dark available"><span>Publicar</span></div>
            </div>

            <div class="row row-steps gx-0" style="padding-right: 14px;">
                <div class="col">
                    <button type="button" class="btn btn-default btn-arrow-right">
                        Ag. Aprovação
                    </button>
                </div>
                <div class="col">
                    <button type="button" class="btn btn-success btn-arrow-right">
                        Aprovar
                    </button>
                </div>
                <div class="col">
                    <button type="button" class="btn btn-danger btn-arrow-right">
                        Rejeitar
                    </button>
                </div>
            </div>

            <div class="row row-steps gx-0" style="padding-right: 14px;">
                <div class="col">
                    <button type="button" class="btn btn-default btn-arrow-right">
                        Ag. Publicação
                    </button>
                </div>
                <div class="col">
                    <button type="button" class="btn btn-primary btn-arrow-right">
                        Produção
                    </button>
                </div>
                <div class="col">
                    <button type="button" class="btn btn-dark btn-arrow-right">
                        Publicar
                    </button>
                </div>
            </div>

            @include('pages.collections.components.form-geral')

            @include('pages.collections.components.form-registro')

            @if ($id !== 1)
                @include('pages.collections.components.form-organizadores')
            @endif

            <div class="form-group row mb-0">
                <div class="col-sm-3"></div>
                <div class="col-sm-6">
                    <button type="button" class="form-confirm btn btn-primary btn-lg">
                        Confirmar
                    </button>

                    <a class="btn btn-default ml-4 form-cancel" href="{{ url($options['url']) }}">
                        Cancelar
                    </a>
                </div>
            </div>

        </div>

        @if ($id !== 1)
            <div id="tab_lista_artigos">
                @include('pages.collections.components.articles-list')
            </div>
        @endif

    </form>
@endsection

@section('css')
    <style>
        #form-coletaneas #tab_cadastro_geral>div+div:not(.form-group) {
            padding-top: 1.5rem;
            margin-top: 1.5rem;
            border-top: 1px solid #eee;
        }

        .row-steps .btn {
            width: 100%;
        }

        .row-steps .btn.btn-default {
            background-color: #e9ecef;
            border-color: #e9ecef;
            cursor: not-allowed;
        }

        .arrow-steps {
            display: flex;
            flex-wrap: wrap;
            width: 100%;
        }

        /* Breadcrups CSS */

        .arrow-steps .step {
            flex-shrink: 0;
            width: 100%;
            max-width: 100%;
            flex: 1 0 0%;
            font-size: 12px;
            text-align: center;
            color: #666;
            cursor: default;
            padding: 10px 10px 10px 20px;
            position: relative;
            background-color: #e9ecef;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            transition: background-color 0.2s ease;
            cursor: not-allowed;
            line-height: 18px;
        }

        .arrow-steps .step+.step {
            margin-left: 2px;
        }

        .arrow-steps .step:after,
        .arrow-steps .step:before {
            content: " ";
            position: absolute;
            top: 0;
            right: -17px;
            width: 0;
            height: 0;
            border-top: 20px solid transparent;
            border-bottom: 18px solid transparent;
            border-left: 18px solid #e9ecef;
            z-index: 2;
            transition: border-color 0.2s ease;
        }

        .arrow-steps .step:before {
            right: auto;
            left: 0;
            border-left: 17px solid #fff;
            z-index: 0;
        }

        .arrow-steps .step:first-child:before {
            border: none;
        }

        .arrow-steps .step:last-child:after {
            border: none;
        }

        .arrow-steps .step:first-child {
            border-top-left-radius: 4px;
            border-bottom-left-radius: 4px;
        }

        .arrow-steps .step:last-child {
            border-top-right-radius: 4px;
            border-bottom-right-radius: 4px;
        }

        .arrow-steps .step.current {
            color: #fff;
            background-color: #23468c;
        }

        .arrow-steps .step.current:after {
            border-left: 17px solid #23468c;
        }



        .arrow-steps .step.success {
            color: #fff;
            background-color: #00d97e;
        }

        .arrow-steps .step.success:after {
            border-left: 17px solid #00d97e;
        }

        .arrow-steps .step.danger {
            color: #fff;
            background-color: #e63757;
        }

        .arrow-steps .step.danger:after {
            border-left: 17px solid #e63757;
        }

        .arrow-steps .step.available:hover {
            cursor: pointer;
            font-weight: bold;
        }
    </style>
@endsection

@section('js')
    <script>
        $(document).ready(function() {

            var form = $('form#form-coletaneas')

            form
                .on('click', '.form-confirm', function(e) {
                    e.preventDefault();
                    e.stopPropagation()

                    submitForm({
                        form: form,
                        onSuccess: function(data) {
                            form
                                .attr('action', baseUrl + '/coletaneas/' + data.item.id + '/editar')
                                .data('id', data.item.id);

                            $(".form-header").text('#' + (data.item.id + '').padStart(5, '0'));

                            $('.add-new').show();
                        }
                    });

                    return false;
                });

            $(document)
                .on('click', '.btn-approve-collection, .btn-reject-collection', function() {
                    var action = $(this).hasClass('btn-reject-collection') ? 'rejeitar' : 'aprovar';
                    var div = $(this).parent();
                    $.put(baseUrl + '/coletaneas/' + $(this).data('id') + '/situation/' + action)
                        .done(function(data) {
                            console.log(data)
                            div.html(
                                `<span class="py-2 px-3 text-uppercase rounded border border-${data.status.color} text-${data.status.color}">${data.status.label}</span>`
                            )
                        });
                });
        });
    </script>

    <script type="module">
        /*import {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                            createApp
                                                                                                                                                                                                                                                                                                                                                                                                                                                                        } from 'https://unpkg.com/petite-vue@0.4.1/dist/petite-vue.es.js?module';

                                                                                                                                                                                                                                                                                                                                                                                                                                                                        let collection = {{ json_encode(isset($collection) ? $collection : [], false) }};
                                                                                                                                                                                                                                                                                                                                                                                                                                                                        let baseURL = '{{ url('sistema') }}';

                                                                                                                                                                                                                                                                                                                                                                                                                                                                        createApp({
                                                                                                                                                                                                                                                                                                                                                                                                                                                                            collection,

                                                                                                                                                                                                                                                                                                                                                                                                                                                                            baseURL,

                                                                                                                                                                                                                                                                                                                                                                                                                                                                            get validStatusActions() {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                return ['PE', 'RP', 'WP'].includes(this.collection.status);
                                                                                                                                                                                                                                                                                                                                                                                                                                                                            },

                                                                                                                                                                                                                                                                                                                                                                                                                                                                            get isPending() {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                return this.collection.status === 'PE';
                                                                                                                                                                                                                                                                                                                                                                                                                                                                            },

                                                                                                                                                                                                                                                                                                                                                                                                                                                                            get isPubishable() {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                return this.collection.status === 'RP';
                                                                                                                                                                                                                                                                                                                                                                                                                                                                            },

                                                                                                                                                                                                                                                                                                                                                                                                                                                                            get isFinishable() {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                return this.collection.status === 'WP';
                                                                                                                                                                                                                                                                                                                                                                                                                                                                            },

                                                                                                                                                                                                                                                                                                                                                                                                                                                                            authorUrl(item) {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                return this.baseURL + '/autores/' + item.id;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                            },

                                                                                                                                                                                                                                                                                                                                                                                                                                                                            approve() {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                this.changeStatus('AP')
                                                                                                                                                                                                                                                                                                                                                                                                                                                                            },

                                                                                                                                                                                                                                                                                                                                                                                                                                                                            reject() {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                this.changeStatus('RE')
                                                                                                                                                                                                                                                                                                                                                                                                                                                                            },

                                                                                                                                                                                                                                                                                                                                                                                                                                                                            publish() {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                this.changeStatus('WP')
                                                                                                                                                                                                                                                                                                                                                                                                                                                                            },

                                                                                                                                                                                                                                                                                                                                                                                                                                                                            finish() {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                this.changeStatus('PU')
                                                                                                                                                                                                                                                                                                                                                                                                                                                                            },

                                                                                                                                                                                                                                                                                                                                                                                                                                                                            changeStatus(status) {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                let _this = this;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                $.put(_this.baseURL + '/coletaneas/' + _this.collection.id + '/change-status', {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        status
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    })
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    .done(function(data) {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        console.log(data);
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        _this.collection.status = data.status;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        _this.collection.status_badge = data.status_badge;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    })
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    .fail(function() {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        // ...
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    });
                                                                                                                                                                                                                                                                                                                                                                                                                                                                            },

                                                                                                                                                                                                                                                                                                                                                                                                                                                                            mounted() {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                console.log(this);
                                                                                                                                                                                                                                                                                                                                                                                                                                                                            }
                                                                                                                                                                                                                                                                                                                                                                                                                                                                        }).mount('#form-coletaneas')*/
    </script>
@endsection
