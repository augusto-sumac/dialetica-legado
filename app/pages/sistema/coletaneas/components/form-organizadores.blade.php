<div id="scope_organizadores" v-scope v-cloak>
    <div class="form-group row mb-0">
        <div class="col-sm-6">
            <h3 :class="{ 'mb-0': loggedAuthorId }">ORGANIZADORES</h3>
            <div class="mb-3" v-if="loggedAuthorId">
                <small class="text-mutted">
                    Você já é um dos organizadores
                </small>
            </div>
        </div>
    </div>

    <div class="form-group row mb-0" v-show="hasAuthors">
        <div class="col">
            <table class="table table-borderless table-hover">
                <thead v-if="isAdminView">
                    <tr>
                        <th class="bg-transparent border-0 py-2 px-2 text-center w-40px"></th>
                        <th class="bg-transparent border-0 py-2 px-3 text-center w-110px">Principal?
                        </th>
                        <th class="bg-transparent border-0 py-2 px-3">Organizador</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(author, k) in authors" :key="author.id"
                        v-show="!isCurrentLoggedAuthor(author.id)">
                        <td class="py-2 px-2 border-0 text-center w-40px">
                            <button type="button" class="btn btn-sm btn-outline-danger" @click="remove(author)"
                                v-if="!isCurrentLoggedAuthor(author.id)">
                                <span class="fas fa-trash"></span>
                            </button>
                            <input type="hidden" name="author_ids[]" :value="author.id" />
                        </td>
                        <td class="py-2 px-3 border-0 text-center w-110px" v-if="isAdminView">
                            <input class="form-check-input" type="radio" v-model="author_id" name="author_id"
                                :value="author.id" />
                        </td>
                        <td class="py-2 px-3 border-0 text-uppercase">
                            ${author.name}}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="form-group row">
        <div class="col">
            <button type="button" class="btn btn-sm btn-outline-dark" @click="find">
                <span class="fas fa-plus"></span> Adicionar
            </button>
        </div>
    </div>

</div>

<?php
$is_admin_view = preg_match('/\/sistema/i', urlCurrent());
$logged_author_id = !$is_admin_view ? (int) logged_author()->id : null;
if ((!isset($authors) || empty($authors)) && $logged_author_id) {
    $authors = ['id' => logged_author()->id, 'name' => logged_author()->name];
}
?>

@section('js')
    @parent

    <script type="module">
        import {
            createApp
        } from 'https://unpkg.com/petite-vue@0.4.1/dist/petite-vue.es.js?module';

        let authors = {{ isset($authors) ? json_encode($authors, true) : '[]' }};
        let author_id = {{ isset($author_id) ? $author_id : 'null' }};
        let is_admin_view = {{ $is_admin_view ? 'true' : 'false' }};
        let logged_author_id = {{ $logged_author_id ?? 'null' }};

        createApp({
            author_id,

            authors,

            get hasAuthors() {
                return this.authors.filter(i => parseInt(i.id) !== this.loggedAuthorId).length > 0
            },

            get loggedAuthorId() {
                return logged_author_id;
            },

            get isAdminView() {
                return is_admin_view;
            },

            isCurrentLoggedAuthor(authorId) {
                return this.loggedAuthorId === parseInt(authorId);
            },

            add(author) {
                if (this.authors.find(_ => _.id === author.id)) {
                    return;
                }

                if (!this.author_id) {
                    this.author_id = author.id;
                }

                this.authors.push(author)
            },

            remove(author) {
                let _this = this;
                dialogConfirm('Confirma a remoção do organizador?', () => {
                    let _authors = [..._this.authors.filter(i => i.id !== author.id)];

                    if (author.id === _this.author_id) {
                        _this.author_id = (_authors[0] && _authors[0].id) || null;
                    }

                    _this.authors = _authors;
                });
            },

            find() {
                findAuthorByDocumentDialog(this.add)
            },
        }).mount('#scope_organizadores')
    </script>
@endsection
