<form id="article-title" action="{{ url(AUTHOR_ARTICLES_BASE_URL) }}/{{ $key }}" method="POST">
    <input type="hidden" name="id" value="{{ $id }}" />

    <div class="form-group row">
        <label class="col-form-label pt-0">Título do Artigo</label>
        <div class="col-12">
            <input type="text" class="form-control" name="title" placeholder="Título"
                value="{{ isset($title) ? $title : '' }}">
        </div>
    </div>

    <div class="form-group row">
        <label class="col-form-label pt-0">Resumo</label>
        <div class="col-12">
            <textarea rows="10" class="form-control" name="resume" placeholder="Resumo">{{ isset($resume) ? $resume : '' }}</textarea>
        </div>
    </div>

    <div class="form-group tags-wrapper row">
        <label class="col-form-label pt-0">Palavras-chave</label>
        @foreach ($tags as $tKey => $tag)
            <div class="form-group">
                <div class="col-4">
                    <input type="text" class="form-control" name="tags[{{ $tKey }}]"
                        placeholder="Palavra-chave {{ $tKey + 1 }}" value="{{ isset($tag) ? $tag : '' }}">
                </div>
            </div>
        @endforeach
    </div>

    <div class="form-group row">
        <div class="col">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" role="switch" id="input_accept_contract"
                    name="accept_contract" value="1"
                    {{ isset($accept_contract) && $accept_contract ? ' checked' : '' }}>
                <label class="form-check-label" for="input_accept_contract">
                    Declaro que li e estou de acordo com o
                    <a href="https://editoradialetica.com/contrato-submissao-de-capitulos" target="_blank">
                        <strong>contrato de publicação</strong>
                    </a>
                    da Editora Dialética.
                </label>
            </div>
        </div>
    </div>

    <div class="form-group row">
        <div class="col">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" role="switch" id="input_accept_publication_rules"
                    name="accept_publication_rules" value="1"
                    {{ isset($accept_publication_rules) && $accept_publication_rules ? ' checked' : '' }}>
                <label class="form-check-label" for="input_accept_publication_rules">
                    Confirmo que o meu artigo esta formatado de acordo com as
                    <a href="{{ url('normas') }}" target="normas-publicacao">
                        <strong>normas de uso</strong>
                    </a>
                    da Editora Dialética.
                </label>
            </div>
        </div>
    </div>

</form>
