<input type="hidden" name="id" value="{{ $id }}" />

<div class="form-group">
    <strong>Título</strong>
    <div>
        {{ isset($title) ? $title : 'Não Informado' }}
    </div>
</div>

<hr>

<div class="form-group">
    <strong>AUTOR</strong>
    <div class="mt-4">
        <strong class="text-muted">{{ $author->name }}</strong>
        <br>
        {{ $author->role }} <br>
        {{ $author->email }}
    </div>
</div>

<hr>

<div class="form-group">
    <strong>Data Submissão</strong>
    <div>
        {{ isset($created_at) ? dateTimeFromMysql($created_at) : 'Não Informado' }}
    </div>
</div>

<hr>

<div class="form-group">
    <strong>Arquivo</strong>
    <div>
        <a href="{{ url('/download-article-attachment?path=' . array_get($attachment, 'path')) }}" target="_blank">
            Baixe aqui ({{ array_get($attachment, 'name', 'Não Informado') }})
        </a>
    </div>
</div>

<hr>

<div class="form-group">
    <strong>Contagem de palavras</strong>
    <div>
        {{ isset($words_count) ? $words_count : 'Não Informado' }}
    </div>
</div>

<hr>

<div class="form-group">
    <strong>Valor do serviço</strong>
    <div>
        {{ isset($amount) ? 'R$ ' . toMoney($amount) : 'Não Informado' }} (Em até 4x sem juros)
    </div>
</div>
