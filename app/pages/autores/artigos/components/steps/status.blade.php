<form id="article-status" action="{{ url(AUTHOR_ARTICLES_BASE_URL) }}/{{ $key }}" method="POST">
    <input type="hidden" name="id" value="{{ $id }}" />

    <p>
        <span class="fas fa-check fa-fw status-icon bg-success text-white me-1"></span>
        Pagamento Realizado com sucesso!
    </p>

    <p>
        Deu tudo certo, agora basta aguardar nossos e-mails de confirmação.
    </p>

    <hr>

    <p>
        Seu artigo segue agora para análise do nosso Conselho Editorial.
    </p>

    <p>
        Não se preocupe, contamos com um corpo de profissionais dedicados à avaliação dos trabalhos e em cerca de uma
        semana você receberá um e-mail com a decisão editorial.
    </p>

    <p>
        Por isso, fique atento a caixa de entra e spam do seu e-mail cadastrado.
    </p>

    <p>
        Você também pode acompanhar o status de todos os seus artigos na seção "minhas submissões".
    </p>

    <hr>

    <a href="{{ url(AUTHOR_ARTICLES_BASE_URL) }}" class="btn btn-sm btn-secondary">
        OK, ver minhas submissões
    </a>

</form>
