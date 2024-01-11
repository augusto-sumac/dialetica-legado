<div id="scope_organizadores" class="{{ isset($box_class) ? $box_class : '' }}" v-scope v-cloak
    v-if="!store.isDefaultCollection" @vue:mounted="onMounted">
    <div class="form-group row mb-0">
        <div class="col-sm-6">
            <h3>ORGANIZADORES</h3>
        </div>
    </div>

    <div class="form-group row mb-0" v-show="store.hasAuthors">
        <div class="col">
            <table class="table table-borderless table-hover">
                <thead v-if="store.isAdminView">
                    <tr>
                        <th class="bg-transparent border-0 py-2 px-2 text-center w-40px"></th>
                        <th class="bg-transparent border-0 py-2 px-3 text-center w-110px">Principal?
                        </th>
                        <th class="bg-transparent border-0 py-2 px-3">Organizador</th>
                        <th class="bg-transparent border-0 py-2 px-3 w-120px text-end">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(author, k) in store.authors" :key="author.id">
                        <td class="py-2 px-2 border-0 text-center w-40px">
                            <button type="button" class="btn btn-sm btn-outline-danger" @click="remove(author)"
                                v-if="!isCurrentLoggedAuthor(author.id)">
                                <span class="fas fa-trash"></span>
                            </button>
                            <input type="hidden" name="author_ids[]" :value="author.id" />
                        </td>
                        <td class="py-2 px-3 border-0 text-center w-110px" v-if="store.isAdminView">
                            <input class="form-check-input" type="radio" v-model="store.author_id" name="author_id"
                                :value="author.id" />
                        </td>
                        <td class="py-2 px-3 border-0 text-uppercase">
                            <div v-if="isCurrentLoggedAuthor(author.id) && !store.id">
                                <div>
                                    <strong>${author.name}}</strong>
                                </div>


                                <div class="mt-3" v-if="!store.isAdminView && !store.id">
                                    <input type="hidden" name="author_id" :value="store.logged_author.id" />

                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Titulação</label>
                                        <div class="col-sm-5">
                                            <select v-model="store.logged_author.role" name="author_role"
                                                class="form-select" placeholder="Titulação" required>
                                                {{ select_options_author_role() }}
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Lattes/ORCID Url</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" name="author_curriculum_url"
                                                placeholder="Lattes/ORCID Url"
                                                v-model="store.logged_author.curriculum_url">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Resumo do Currículo</label>
                                        <div class="col-sm-9">
                                            <textarea rows="4" class="form-control" v-model="store.logged_author.curriculum" name="author_curriculum"
                                                placeholder="Resumo do Currículo"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <a v-else href="javascript:void(0)" @click="authorInfo(author)"
                                title="Dados do Organizador">
                                ${author.name}}
                            </a>
                        </td>
                        <td class="py-2 px-3 border-0 text-uppercase w-120px text-end">
                            <span v-if="author.accepted_at" class="badge bg-success">ACEITO</span>
                            <span v-else class="badge bg-secondary">PENDENTE</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="form-group row">
        <div class="col">
            <button type="button" class="btn btn-sm btn-outline-dark" @click="find">
                <span class="fas fa-plus"></span> Adicionar Coorganizador
            </button>
        </div>
    </div>

</div>

@section('js')
    @parent

    <script type="module">
        import {
            createApp
        } from 'https://unpkg.com/petite-vue@0.4.1/dist/petite-vue.es.js?module';

        const store = window.{{ $app_store_id }};

        createApp({
            store,

            isCurrentLoggedAuthor(authorId) {
                return this.store.loggedAuthorId === parseInt(authorId);
            },

            add(author) {
                if (this.store.authors.find(_ => _.id === author.id)) {
                    return;
                }

                if (!this.store.author_id) {
                    this.store.author_id = author.id;
                }

                this.store.authors.push(author);

                if (this.store.id) {
                    $.put(`/collections/${this.store.id}/organizer/${author.id}`)
                }
            },

            remove(author) {
                let _this = this;

                const onOk = () => {
                    let _authors = [..._this.store.authors.filter(i => i.id !== author.id)];

                    if (author.id === _this.store.author_id) {
                        _this.store.author_id = (_authors[0] && _authors[0].id) || null;
                    }

                    _this.store.authors = _authors;

                    if (_this.store.id && author.created_at) {
                        $.delete(`/collections/${_this.store.id}/organizer/${author.id}`)
                    }
                }

                dialogConfirm('Confirma a remoção do organizador?', onOk);
            },

            authorInfo(organizer) {
                $.get(`/collections/${this.store.id||0}/organizer/${organizer.id}/info`).then(data => {
                    Swal.fire({
                        html: `<div class="text-start">
                            <h2 class="pt-2">DADOS DO ORGANIZADOR</h2>
                            <hr />
                            <div class="form-group">
                                <strong>Nome</strong>
                                <div>${data.name}</div>
                            </div>
                            <div class="form-group">
                                <strong>CPF/CNPJ/ID</strong>
                                <div>${data.document ?? 'Não Informado'}</div>
                            </div>
                            <div class="form-group">
                                <strong>Email</strong>
                                <div>${data.email ?? 'Não Informado'}</div>
                            </div>
                            <div class="form-group">
                                <strong>Titulação</strong>
                                <div>${data.role ?? 'Não Informado'}</div>
                            </div>
                            <div class="form-group">
                                <strong>Lattes/ORCID Url</strong>
                                <div>${data.curriculum_url ?? 'Não Informado'}</div>
                            </div>
                            <div class="form-group">
                                <strong>Resumo do Currículo</strong>
                                <div>${data.curriculum ?? 'Não Informado'}</div>
                            </div>
                        </div>`
                    });
                });
            },

            find() {
                findAuthorByDocumentDialog(this.add, store)
            },

            onMounted() {
                setTimeout(() => {
                    let el = $('[name="author_role"]');
                    if (el.length) {
                        el.val(this.store.logged_author.role).data("selectpicker").refresh();
                    }
                }, 250);
            }
        }).mount('#scope_organizadores')
    </script>
@endsection
