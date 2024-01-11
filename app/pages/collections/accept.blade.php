@extend('layouts.autores')

<?php
$options = [
    'title' => 'Convites Coletâneas',
    'icon' => 'check',
];
?>
@section('content')
    @include('components.content-header', $options)

    <div v-scope v-cloak id="scope">
        <div v-if="isEmpty" class="alert alert-info m-0">
            <span class="fas fa-check"></span> Não há convites pendentes
        </div>
        <div v-else>
            <div class="card" id="meus-dados">
                <div class="card-body">
                    <h2>
                        Antes de aceitar o(s) convite(s) é necessário preencher seus dados abaixo!
                    </h2>

                    <hr class="my-4">

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Titulação</label>
                        <div class="col-sm-5">
                            <select v-model="role" class="form-select" :class="{ 'is-invalid': !validRole }"
                                placeholder="Titulação" required>
                                {{ select_options_author_role(logged_author()->role ?? '') }}
                            </select>
                        </div>
                    </div>

                    <div class="form-group row pt-5">
                        <p>
                            <span class="fas fa-info-circle me-2"></span> Seu mini currículo será utilizado na publicação de
                            artigos.
                        </p>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Lattes/ORCID Url</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" :class="{ 'is-invalid': !validUrl }"
                                v-model="curriculum_url" placeholder="http://seu-curriculo.com.br">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Resumo do Currículo</label>
                        <div class="col-sm-9">
                            <textarea rows="4" class="form-control" :class="{ 'is-invalid': !validCurriculum }" v-model="curriculum"
                                placeholder="Resumo do Currículo"></textarea>
                            <!-- <small class="text-muted">Mínimo 1 caracteres</small> -->
                        </div>
                    </div>

                    <div class="form-group row mb-0">
                        <div class="col-sm-9 offset-sm-3">
                            <button class="btn btn-primary" @click="save" :disabled="!isValidUserData">
                                <span class="fas fa-check"></span> Salvar Meu Dados
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4" v-for="item in collections">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h2 class="text-uppercase">${item.name}}</h2>
                            <div>${item.description}}</div>

                            <div class="row mt-3">
                                <div class="col-auto">
                                    <small>Área: <br />${item.area_name}}</small>
                                </div>
                                <div class="col-auto">
                                    <small>Subárea: <br />${item.subarea_name}}</small>
                                </div>
                                <div class="col-auto">
                                    <small>Especialidade: <br />${item.specialty_name}}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-success" title="Aceitar Convite" @click="accept(item)"
                                :disabled="disableAcceptInvite">
                                <span class="fas fa-check"></span> Aceitar Convite
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('css')
@endsection
@section('js')
    <script type="module">
        import {
            createApp
        } from 'https://unpkg.com/petite-vue@0.4.1/dist/petite-vue.es.js?module';

        let collections = {{ json_encode($collections) }};
        let role = '{{ logged_author()->role ?? '' }}';
        let curriculum = '{{ logged_author()->curriculum ?? '' }}';
        let curriculum_url = '{{ logged_author()->curriculum_url ?? '' }}';
        let user = {{ json_encode(logged_author()) }};
        let roles = {{ json_encode(array_map(fn($i) => trim($i['value']), author_role(null))) }};

        function isValidURL(str) {
            let pattern = new RegExp('^(https?:\\/\\/)?'+ // protocol
                '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|'+ // domain name
                '((\\d{1,3}\\.){3}\\d{1,3}))'+ // OR ip (v4) address
                '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*'+ // port and path
                '(\\?[;&a-z\\d%_.~+=-]*)?'+ // query string
                '(\\#[-a-z\\d_]*)?$','i'); // fragment locator
            return !!pattern.test(str);
        }

        createApp({
            collections,

            role,
            curriculum,
            curriculum_url,

            original: {
                role,
                curriculum,
                curriculum_url,
            },

            get isEmpty() {
                return this.collections.length === 0
            },

            get validRole() {
                return roles.includes(this.role)
            },

            get validUrl() {
                return isValidURL(this.curriculum_url)
            },

            get validCurriculum() {
                return this.curriculum.length >= 1;
            },

            get isValidUserData() {
                return this.validRole && this.validUrl && this.validCurriculum
            },

            get disableAcceptInvite() {
                return !isValidURL(this.original.curriculum_url) || !roles.includes(this.original.role) || this.original.curriculum.length < 1
            },

            accept(item) {
                $.put(`${baseUrl}/collections/${item.id}/accept`);

                this.collections = this.collections.filter(c => c.id !== item.id);
            },

            save() {
                var form = $('#meus-dados');

                let _this = this;

                let {role, curriculum, curriculum_url} = this;

                submitForm({
                    form: form,
                    url: `${baseUrl}/collections/organizer-data`,
                    method: 'POST',
                    data: {role, curriculum, curriculum_url},
                    onSuccess() {
                        _this.original = {role, curriculum, curriculum_url}
                    }
                });
            }
        }).mount('#scope');
    </script>
@endsection
