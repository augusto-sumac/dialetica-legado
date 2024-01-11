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

        <div class="card-header" id="scope_manage_tabs" v-scope v-cloak v-if="enableTabs">
            <ul class="nav nav-tabs card-header-tabs">
                <li class="nav-item">
                    <a class="nav-link active" href="#cadastro_geral">CADASTRO</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#lista_artigos">GERENCIAR ARTIGOS</a>
                </li>
            </ul>
        </div>

        @include('pages.collections.components.status-menu')

        <div id="scope_expires_at" class="card-body border-bottom" v-scope v-cloak @vue:mounted="onMounted">
            <div class="form-group row mb-0">
                <label class="col-sm-3 col-form-label">Data Limite</label>
                <div class="col-sm-9">
                    <div class="input-group mb-3 w-180px">
                        <input type="text" v-model="store.expires_at" id="name_expires_at" placeholder="Data Limite"
                            class="form-control" :class="{ 'border-warning': store.isExpired }" @focus="$el.select()"
                            @change="onChangeExpiredAt">
                        <button class="btn btn-dark" type="button" @click="updateExpiresAt" :disabled="is_loading"
                            :class="{ 'border-warning': store.isExpired, 'border-dark': !store.isExpired }">
                            <span v-if="is_loading" class="fas fa-spinner fa-spin"></span>
                            <span v-else class="fas fa-check"></span>
                        </button>
                    </div>

                    <div>
                        <small>Data limite para envio de artigos</small>
                    </div>

                    <div class="mt-5" v-if="store.isExpired">
                        <p>A coletânea não atingiu a quantidade mínima de artigos para ser publicada.</p>

                        <button type="button" class="btn btn-danger" @click="finishWithFail" :disabled="is_saving">
                            <span v-if="is_saving" class="fas fa-spinner fa-spin"></span>
                            <span v-else class="fas fa-thumbs-down me-2"></span>
                            Encerrar Coletânea
                        </button>
                        <div>
                            <small>Encerrar a Coletânea por insuficiencia de artigos publicados.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if (isset($created_by))
        <div class="card-body border-bottom">
            <div class="form-group row mb-0">
                <label class="col-sm-3 col-form-label">Origem</label>
                <div class="col-sm-9">
                    {{ $created_by }}
                </div>
            </div>
        </div>
        @endif

        <div id="tab_cadastro_geral" class="card-body">
            @include('pages.collections.components.form-geral')

            @include('pages.collections.components.form-registro')

            @include('pages.collections.components.form-organizadores')

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

        <div id="tab_lista_artigos">
            @include('pages.collections.components.articles-list')
        </div>

    </form>
@endsection

@section('css')
    <style>
        #form-coletaneas #tab_cadastro_geral>div+div:not(.form-group) {
            padding-top: 1.5rem;
            margin-top: 1.5rem;
            border-top: 1px solid #eee;
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

                            window.{{ $app_store_id }}.id = data.item.id;
                            if (data.item.status) {
                                window.{{ $app_store_id }}.status = data.item.status;
                            }
                        }
                    });

                    return false;
                });
        });
    </script>

    @include('pages.collections.components.form-js-module')

    <script type="module">
        import {
            createApp
        } from 'https://unpkg.com/petite-vue@0.4.1/dist/petite-vue.es.js?module';

        const store = window.{{ $app_store_id }};

        createApp({
            store,

            get enableTabs() {
                return this.store.id && this.store.id > 1;
            }
        }).mount('#scope_manage_tabs');

        createApp({
            store,

            is_loading: false,
            is_saving: false,

            onChangeExpiredAt(e) {
                this.store.expires_at = e.target.value;
            },

            updateExpiresAt() {
                this.is_loading = true;
                let expires_at = (this.store.expires_at+'').split('/').reverse().join('-');
                if (dayjs(expires_at, 'YYYY-MM-DD').isValid()) {
                    this.store.original_expires_at = this.store.expires_at
                    return $.put(`/collections/${this.store.id}/expires/${expires_at}`).always(() => this.is_loading = false);
                }

                Swal.fire({
                    icon: 'warning',
                    title: 'Ooops!',
                    text: 'A data informada não é válida!'
                });
            },

            finishWithFail() {
                dialogConfirm('Confirma o encerramento da Coletânea', () => {
                    this.is_saving = true;
                    $.put(`/collections/${this.store.id}/change-status`, {status: 'FL'})
                    .done(() => window.location.reload())
                    .always(() => this.is_saving = false);
                });
            },

            onMounted() {
                $('[id="name_expires_at"]').mask("00/00/0000", { placeholder: "__/__/____" });
            }
        }).mount('#scope_expires_at');
    </script>
@endsection
