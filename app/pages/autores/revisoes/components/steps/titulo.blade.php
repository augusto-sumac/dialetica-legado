<form id="article-title" action="{{ url(AUTHOR_REVIEWS_BASE_URL) }}/{{ $key }}" method="POST">
    <input type="hidden" name="id" value="{{ $id }}" />

    <div class="form-group row">
        <label class="col-form-label pt-0">Título do Trabalho</label>
        <div class="col-12">
            <input type="text" class="form-control" name="title" placeholder="Título"
                value="{{ isset($title) ? $title : '' }}">
        </div>
    </div>

    <div class="row">
        <div class="col">
            <p>Quando você utiliza o serviço "Solicitar Revisão", seu texto é enviado diretamente para um dos nossos
                revisores especializados. Ele irá examinar cuidadosamente seu trabalho e fornecerá conselhos valiosos
                sobre como aprimorá-lo. Eles se concentrarão na forma (gramática, pontuação, estilo), garantindo que seu
                texto esteja claro, conciso e envolvente.
            <p>
            <p>Lembre-se, este é um serviço separado do "Publicar Artigo". A revisão é um passo opcional que visa
                aprimorar a qualidade do seu texto antes da submissão para publicação. Se você já está satisfeito com o
                seu trabalho e deseja submetê-lo diretamente para avaliação pelo nosso Conselho Editorial, você pode
                utilizar a opção "Publicar Artigo".</p>
        </div>
    </div>

    <div class="form-group row">
        <div class="col">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" role="switch" id="input_accept_publication_rules"
                    name="accept_publication_rules" value="1"
                    {{ isset($accept_publication_rules) && $accept_publication_rules ? ' checked' : '' }} />
                <label class="form-check-label" for="input_accept_publication_rules">
                    Confirmo que estou de acordo com as
                    <a href="{{ url('normas') }}" target="normas-publicacao">
                        <strong>normas de uso</strong>
                    </a>
                    da Editora Dialética.
                </label>
            </div>
        </div>
    </div>



</form>
