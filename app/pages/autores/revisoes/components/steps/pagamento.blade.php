<form id="article-payment" action="{{ url(AUTHOR_REVIEWS_BASE_URL) }}/{{ $key }}" method="POST">
    <div id="article-payment-loader" class="row align-items-center py-5">
        <div class="col-auto">
            <div class="spinner-border" role="status"></div>
        </div>
        <div class="col">
            <span>Aguarde. Carregando Pagamento...</span>
        </div>
    </div>

    <div id="article-payment-content"></div>
</form>
