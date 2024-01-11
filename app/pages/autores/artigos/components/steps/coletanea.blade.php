<form id="article-collection" action="{{ url(AUTHOR_ARTICLES_BASE_URL) }}/{{ $key }}" method="POST">
    <input type="hidden" name="id" value="{{ $id }}" />
    <input type="hidden" name="collection_id" value="{{ $collection->id }}" />
    <input type="hidden" name="area_id" value="{{ $collection->area_id }}" />
    <input type="hidden" name="subarea_id" value="{{ $collection->subarea_id }}" />
    <input type="hidden" name="specialty_id" value="{{ $collection->specialty_id }}" />

    <div class="form-group row">
        <div class="col">
            <p>
                Você usou o link da Coletânea <strong>{{ $collection->name }}</strong>. Caso você não deseje publicar
                nesta coletânea e
                prefira que a editora selecione outra coletânea para o seu artigo, clique <a href="javascript:void(0)"
                    class="btn-reset-article-collection">aqui</a>.
            </p>

            <p>
                Ao prosseguir no processo de publicação, você concorda em publicar na coletânea
                <strong>{{ $collection->name }}</strong>...
            </p>

            <p>
                Caso você não deseje publicar nesta coletânea e prefira que a editora selecione outra coletânea para o
                seu artigo, clique <a href="javascript:void(0)" class="btn-reset-article-collection">aqui</a>.
            </p>
        </div>
    </div>
</form>
