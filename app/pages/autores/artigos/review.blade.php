<input type="hidden" name="id" value="{{ $id }}" />

<div class="form-group">
    <strong>Título</strong>
    <div>
        {{ isset($title) ? $title : 'Não Informado' }}
    </div>
</div>

<div class="form-group">
    <strong>Resumo</strong>
    <div>
        {{ isset($resume) ? $resume : 'Não Informado' }}
    </div>
</div>


<div class="form-group">
    <strong>Data Submissão</strong>
    <div>
        {{ isset($created_at) ? dateTimeFromMysql($created_at) : 'Não Informado' }}
    </div>
</div>

<div class="form-group">
    <strong>Coletânea</strong>
    <div>
        {{ isset($collection_name) ? $collection_name : 'Não Informado' }}
    </div>
</div>

<div class="form-group">
    <strong>Área</strong>
    <div>
        {{ isset($area_name) ? $area_name : 'Não Informado' }}
    </div>
</div>

<div class="form-group">
    <strong>Subárea</strong>
    <div>
        {{ isset($subarea_name) ? $subarea_name : 'Não Informado' }}
    </div>
</div>

<div class="form-group">
    <strong>Especialidade</strong>
    <div>
        {{ isset($specialty_name) ? $specialty_name : 'Não Informado' }}
    </div>
</div>

<div class="form-group">
    <strong>Palavras-chave</strong>
    <div>
        {{ isset($tags) ? str_replace(',', ', ', $tags) : 'Não Informado' }}
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

@if (isset($author))
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
@endif

@if (isset($co_authors))

    <hr>

    <div class="form-group">
        <strong>CO-AUTORES</strong>

        @foreach ($co_authors as $author)
            <div class="mt-4">
                <strong class="text-muted">{{ $author->name }}</strong>
                <br>
                {{ $author->role }} <br>
                {{ $author->email }}
            </div>
        @endforeach
    </div>

@endif
