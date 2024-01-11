<div class="overflow-hidden {{ isset($box_class) ? $box_class : '' }}" id="scope_articles_list" v-scope v-cloak
    @vue:mounted="mounted" v-if="enableModule">
    <div class="card-body">
        <div class="row">
            <div class="col">
                <h3 class="m-0">ARTIGOS</h3>
                <div class="mb-0">
                    <small class="text-mutted">É necessário haver ao menos ${ store.paid_articles_limit }} artigos
                        aprovados para
                        publicar a Coletânea</small>
                </div>
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-secondary btn-load-articles" @click="load">
                    <span class="fas fa-sync"></span>
                </button>

                <button type="button" class="btn btn-success ms-2" v-if="allowPublish" @click="publish">
                    <span class="fas fa-check me-2"></span>
                    Publicar
                </button>

            </div>
        </div>
    </div>
    <div class="card-body m-0 pt-0" v-if="!hasArticles">
        <div class="alert alert-danger m-0">
            Ainda não há artigos publicados!
        </div>
    </div>
    <div class="datagrid" v-else>
        <div class="table-responsive">
            <table class="table">
                <tbody>
                    <tr v-for="(article, k) in store.articles" :key="k">
                        <td class="w-60px text-center pe-0" v-html="article.status_icon"></td>
                        <td>
                            <div class="text-uppercase">
                                ${ article.title }}
                            </div>
                            <div class="mt-2">
                                <div class="row">
                                    <div class="col">
                                        <button type="button"
                                            class="btn btn-sm btn-outline-secondary btn-show-article-detail"
                                            @click="detail(article)">
                                            <span class="fas fa-eye me-2"></span> Detalhes
                                        </button>

                                        <button type="button"
                                            class="btn btn-sm btn-outline-primary btn-dial-approve-article"
                                            v-if="article.allow_dial_approve" @click="dial_approve($event, article)">
                                            <span class="fas fa-thumbs-up me-2"></span> Aprovar Artigo
                                        </button>

                                        <button type="button"
                                            class="btn btn-sm btn-outline-success btn-approve-article"
                                            v-if="article.allow_approve" @click="approve($event, article)">
                                            <span class="fas fa-thumbs-up me-2"></span> Aprovar Artigo
                                        </button>

                                    </div>
                                    <div class="col-auto">
                                        <button type="button"
                                            class="btn btn-sm btn-outline-danger btn-reject-article ms-5"
                                            v-if="article.allow_approve" @click="reject($event, article)">
                                            <span class="fas fa-thumbs-down me-2"></span> RejeitarArtigo
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="w-140px text-end">
                            <div v-if="store.isAdminView && article.is_paid">
                                <a :href="`/articles/${article.id}/invoice`" class="btn btn-light btn-sm"
                                    target="invoice-pdf" v-if="article.service_invoice_id">
                                    <span class="fas fa-download"></span> PDF NFS-e
                                </a>
                                <button type="button" class="btn btn-sm btn-dark btn-gerar-nf"
                                    :disabled="article.await_invoice" @click="generateInvoice($event, article)" v-else>
                                    <span class="spinner-border spinner-border-sm me-2"
                                        v-if="article.await_invoice"></span>
                                    Gerar NFS-e
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

@section('js')
    @parent
    <script type="module">
        import {
            createApp
        } from 'https://unpkg.com/petite-vue@0.4.1/dist/petite-vue.es.js?module';

        let reload_interval_timer = null;

        const store = window.{{ $app_store_id }};

        createApp({
            store,

            get enableModule() {
                return this.store.id > 1 && !['PE', 'RE', 'FL'].includes(this.store.status);
            },

            get hasArticles() {
                return this.store.articles && this.store.articles.length;
            },

            get allowPublish() {
                return this.store.allow_publish && !this.store.isAdminView;
            },

            load() {
                if (!this.enableModule) return;

                let _this = this;
                let btn = $('.btn-load-articles');
                showButtonLoading(btn);

                $.ajax({
                        url: `/collections/${this.store.id}/articles`,
                        method: 'GET',
                        global: false
                    }).done((data) => {
                        _this.store.articles = data.articles;
                        _this.store.allow_publish = data.allow_publish;
                        _this.store.paid_articles = data.paid_articles;
                        _this.store.paid_articles_limit = data.paid_articles_limit;
                    })
                    .always(() => {
                        hideButtonLoading(btn);
                        _this.reloadInterval();
                    });
            },

            detail(item) {
                $.ajax({
                        url: `/artigos/${item.id}/${this.store.id}`,
                        method: 'GET',
                        global: false
                    })
                    .done(showModalDetail);
            },

            dial_approve($event, article) {
                let _this = this;
                let btn = $($event.target).closest('button');
                let url = `/sistema/artigos/${article.id}/change-status`;

                dialogConfirm('Confirma a aprovação do artigo [Liberar para Pagamento]?', function() {
                    showButtonLoading(btn);

                    $.ajax({
                            url,
                            method: 'PUT',
                            data: {
                                status: 30
                            },
                            global: false
                        })
                        .always(() => {
                            _this.load()
                            hideButtonLoading(btn)
                        });
                });
            },

            approve($event, article) {
                let _this = this;
                let btn = $($event.target).closest('button');
                let url = `/collections/${this.store.id}/approve/${article.id}`;
                dialogConfirm('Confirma a aprovação do artigo?', function() {
                    showButtonLoading(btn);

                    $.ajax({
                            url,
                            method: 'PUT',
                            global: false
                        })
                        .done(() => _this.load())
                        .always(() => hideButtonLoading(btn));
                });
            },

            reject($event, article) {
                let _this = this;
                let btn = $($event.target).closest('button');
                let url = `/collections/${this.store.id}/reject/${article.id}`;

                dialogConfirm('Confirma a rejeição do artigo?', function() {
                    showButtonLoading(btn);

                    $.ajax({
                            url,
                            method: 'PUT',
                            global: false
                        })
                        .done(() => _this.load())
                        .always(() => hideButtonLoading(btn));
                });
            },

            publish() {
                let _this = this;
                const onOk = () => $.put(`/collections/${_this.store.id}/publish`)
                    .done((data) => {
                        updateStatusBadge(data);
                        _this.load()
                    });

                dialogConfirm('Confirma a publicação da Coletânea? Isso não podrá ser desfeito!', onOk);
            },

            reloadInterval() {
                clearInterval(reload_interval_timer);
                reload_interval_timer = setInterval(() => this.load(), 60 * 1000)
            },

            generateInvoice($event, article) {
                let _this = this;
                let btn = $($event.target).closest('button');
                dialogConfirm('Confirma a geração da NFS-e para esta obra?', function() {
                    showButtonLoading(btn);

                    $.post(`/sistema/artigos/${article.id}/invoice`)
                        .done(function(data) {
                            Toast.fire({
                                icon: 'success',
                                text: 'Emissão da NFS-e iniciada com sucesso'
                            });

                            _this.store.articles = _this.store.articles.map(item => {
                                if (item.id === article.id) {
                                    item.await_invoice = true;
                                }
                                return item;
                            });
                        })
                        .fail(function(response) {
                            Toast.fire({
                                icon: 'error',
                                text: response?.responseJSON?.message ||
                                    'Falha ao gerar NFS-e! Tente novamente'
                            });
                        })
                        .always(() => hideButtonLoading(btn));;
                });
            },

            mounted() {
                this.load();
            }
        }).mount('#scope_articles_list');

        const showModalDetail = (html) => {
            setTimeout(function() {
                Swal.fire({
                    title: 'Detalhes do artigo',
                    html,
                    width: '90%',
                    padding: '0',
                    showCloseButton: true,
                    showConfirmButton: false,
                    showCancelButton: false,
                    customClass: {
                        container: 'modal-show-article-detail'
                    }
                });
            }, 50)
        };

        const updateStatusBadge = (data) => {
            let {
                color,
                label
            } = data.status_badge || {};

            let el = $('.status-wrapper .badge');

            color && el.attr('class', 'badge bg-' + color).html(label);
        }
    </script>
@endsection
