<div class="card overflow-hidden" id="articles-table" v-scope v-cloak>
    <div class="card-body">
        <div class="row">
            <div class="col">
                <div class="card-title">
                    <strong>Artigos</strong>
                </div>
                <div class="card-subtitle">
                    <small>É necessário haver ao menos {{ $paid_articles_limit }} artigos aprovados para
                        publicar a Coletânea</small>
                </div>
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-secondary btn-refresh-collection-articles" @click="reload">
                    <span class="fas fa-sync"></span>
                </button>

                <button type="button" class="btn btn-success btn-publish-collection ms-2" v-if="allowPublish"
                    @click="publish">
                    <span class="fas fa-check me-2"></span>
                    Publicar
                </button>

            </div>
        </div>
    </div>
    <div class="text-danger py-3 px-4" v-if="!hasArticles">
        Ainda não há artigos publicados!
    </div>
    <div class="datagrid" v-else>
        <div class="table-responsive">
            <table class="table">
                <tbody>
                    <tr v-for="(article, k) in articles" :key="k">
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
                                            @click="detail($event, article)">
                                            <span class="fas fa-eye me-2"></span> Detalhes
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
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script type="module">
    import {
        createApp
    } from 'https://unpkg.com/petite-vue@0.4.1/dist/petite-vue.es.js?module';

    var reload_interval_timer = null;

    createApp({
        articles: {{ json_encode($articles) }},
        paid_articles: {{ $paid_articles }},
        paid_articles_limit: {{ $paid_articles_limit }},
        collection: {{ json_encode($collection) }},

        get hasArticles() {
            return this.articles && this.articles.length;
        },

        get allowPublish() {
            return this.paid_articles >= this.paid_articles_limit && this.collection.status === 'AC';
        },

        reload() {
            var _this = this;
            var btn = $('.btn-refresh-collection-articles');
            showButtonLoading(btn);

            $.ajax({
                    url: '/collections/' + this.collection.id + '/articles',
                    method: 'GET',
                    global: false
                }).done(function(data) {
                    _this.articles = data.articles;
                    _this.paid_articles = data.paid_articles;
                    _this.paid_articles_limit = data.paid_articles_limit;
                })
                .always(function() {
                    hideButtonLoading(btn);

                    _this.reloadInterval();
                });
        },

        detail($event, item) {
            $.ajax({
                    url: '/artigos/' + item.id + '/' + item.collection_id,
                    method: 'GET',
                    global: false
                })
                .done(function(html) {
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
                })
                .always(function() {});
        },

        approve($event, article) {
            var _this = this;
            var btn = $('.btn-approve-article');
            var url = 'collections/' + article.collection_id + '/aprove/' + article.id
            showButtonLoading(btn);

            dialogConfirm('Confirma a aprovação do artigo?', function() {
                $.ajax({
                        url,
                        method: 'PUT',
                        global: false
                    })
                    .done(function() {
                        _this.reload();
                    })
                    .always(function() {
                        hideButtonLoading(btn);
                    });
            });
        },

        reject($event, article) {
            var _this = this;
            var btn = $('.btn-reject-article');
            var url = 'collections/' + article.collection_id + '/reject/' + article.id
            showButtonLoading(btn);

            dialogConfirm('Confirma a rejeição do artigo?', function() {
                $.ajax({
                        url,
                        method: 'PUT',
                        global: false
                    })
                    .done(function() {
                        _this.reload();
                    })
                    .always(function() {
                        hideButtonLoading(btn);
                    });
            });
        },

        publish($event) {
            var _this = this;
            dialogConfirm('Confirma a publicação da Coletânea? Isso não podrá ser desfeito!',
                function() {
                    $.put('/collections/' + _this.collection.id + '/publish')
                        .done(
                            function(data) {
                                $('.status-wrapper .badge').attr('class', 'badge bg-' + data
                                    .status_badge.color).html(data.status_badge.label);
                                $('.btn-refresh-collection-articles').click();
                                _this.collection.status = data.status;
                            });
                });
        },

        reloadInterval() {
            var _this = this;
            clearInterval(reload_interval_timer);

            reload_interval_timer = setInterval(function() {
                _this.reload();
            }, 10 * 1 * 1000)
        },

        mounted() {
            this.reloadInterval();
        }
    }).mount('#articles-table')
</script>
