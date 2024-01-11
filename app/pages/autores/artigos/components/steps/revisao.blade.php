<form id="article-review" action="{{ url(AUTHOR_ARTICLES_BASE_URL) }}/{{ $key }}" method="POST">
    <div id="article-review-loader" class="row align-items-center py-5">
        <div class="col-auto">
            <div class="spinner-border" role="status"></div>
        </div>
        <div class="col">
            <span>Aguarde. Carregando Revisão...</span>
        </div>
    </div>

    <div id="article-review-content"></div>
</form>
