<div class="card overflow-hidden">
    <div class="card-body">
        <div class="row">
            <div class="col">
                <div class="card-title">
                    <strong>Artigos</strong>
                </div>
                <div class="card-subtitle">
                    <small>É necessário haver ao menos 4 artigos aprovados para publicar a Coletânea</small>
                </div>
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-secondary btn-refresh-collection-articles"
                    data-url="{{ url('minhas-coletaneas/' . $collection->id . '/artigos') }}">
                    <span class="fas fa-sync"></span>
                </button>

                @if ($paid_articles >= $paid_articles_limit && $collection->status === 'AP')
                    <button type="button" class="btn btn-success btn-publish-collection ms-2">
                        <span class="fas fa-check me-2"></span>
                        Publicar
                    </button>
                @endif
            </div>
        </div>
    </div>
    @if (empty($articles))
        <div class="text-danger py-3 px-4">
            Ainda não há artigos publicados!
        </div>
    @else
        <div class="datagrid">
            <div class="table-responsive">
                <table class="table">
                    <tbody>
                        @foreach ($articles as $article)
                            <tr>
                                <td class="w-60px text-center pe-0">
                                    {{ $article->status_icon }}
                                </td>
                                <td>
                                    <div class="text-uppercase">
                                        {{ $article->title }}
                                    </div>
                                    <div class="mt-2">
                                        <div class="row">
                                            <div class="col">
                                                <button type="button"
                                                    data-collection-id="{{ $article->collection_id }}"
                                                    data-id="{{ $article->id }}"
                                                    class="btn btn-sm btn-outline-secondary btn-show-article-detail">
                                                    <span class="fas fa-eye me-2"></span> Detalhes
                                                </button>
                                                @if ($article->allow_approve)
                                                    <button type="button"
                                                        data-collection-id="{{ $article->collection_id }}"
                                                        data-id="{{ $article->id }}"
                                                        class="btn btn-sm btn-outline-success btn-approve-article">
                                                        <span class="fas fa-thumbs-up me-2"></span> Aprovar Artigo
                                                    </button>
                                                @endif
                                            </div>
                                            <div class="col-auto">
                                                @if ($article->allow_approve)
                                                    <button type="button"
                                                        data-collection-id="{{ $article->collection_id }}"
                                                        data-id="{{ $article->id }}"
                                                        class="btn btn-sm btn-outline-danger btn-reject-article ms-5">
                                                        <span class="fas fa-thumbs-down me-2"></span> Rejeitar
                                                        Artigo
                                                    </button>
                                                @endif
                                            </div>
                                        </div>

                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
