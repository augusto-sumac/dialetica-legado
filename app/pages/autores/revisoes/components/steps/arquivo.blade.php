<form id="article-file" action="{{ url(AUTHOR_REVIEWS_BASE_URL) }}/{{ $key }}" method="POST">
    <input type="hidden" name="id" value="{{ $id }}" />

    <div class="form-group row">
        <div class="col">
            <p>
                Chegou o momento de nos enviar o arquivo com o artigo que deseja publicar.
            </p>

            <p>
                Certifique-se que o arquivo está obedecendo as diretrizes de nossas <a
                    href="{{ url('normas-de-uso') }}" target="normas-publicacao"><strong>normas de
                        uso</strong></a> para evitar a rejeição do
                seu
                envio!
            </p>

            <ul>
                <li>Apenas formatos DOC e DOCX serão aceitos</li>
                <li>O arquivo não pode ter mais que {{ config('article_max_file_size') }}mb</li>
                <li>O arquivo não deve estar protegido com senha ou outra restrição que impeça a leitura</li>
            </ul>
        </div>
    </div>

    <div class="form-group row justify-content-center">

        <div class="col">
            <div class="text-center drop-file">
                <div>Arquivo no formato DOC ou DOCX (Máx {{ config('article_max_file_size') }}mb)</div>
                <label type="button" class="btn btn-dark mt-3 position-relative">
                    <input id="select_file" name="file" type="file" accept=".doc,.docx">
                    <input type="hidden" name="attachment"
                        value='{{ isset($attachment) ? json_encode($attachment) : '' }}'>
                    <span class="fas fa-upload"></span>
                    <span>Selecionar Arquivo</span>
                </label>
                @if ($file_name = array_get($attachment, 'name'))
                    <div class="display-file-name text-start mt-3 mb-0">
                        <strong>Arquivo Vinculado:</strong><br />
                        {{ $file_name }}
                    </div>
                @else
                    <div class="display-file-name"></div>
                @endif
            </div>
        </div>

    </div>

</form>
