<script id="div-author-line" type="text/x-handlebars-template"><div class="co-author">
        <div class="row align-items-center">
            <div class="col">
                <div class="form-group row mt-3">
                    <label class="col-auto w-120px col-form-label">Nome</label>
                    <div class="col col-md-8 col-lg-6 col-xl-4">
                        <input type="text" class="form-control" name="authors[${key}}][name]" placeholder="Nome" value="${name}}" data-key="${key}}" data-name="name">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-auto w-120px col-form-label">Email</label>
                    <div class="col col-md-8 col-lg-6 col-xl-6">
                        <input type="email" class="form-control" name="authors[${key}}][email]" placeholder="Email" value="${email}}" data-key="${key}}" data-name="email">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-auto w-120px col-form-label">Titulação</label>
                    <div class="col col-md-8 col-lg-6 col-xl-3">
                        <select name="authors[${key}}][role]" class="form-select" placeholder="Titulação" data-key="${key}}" data-name="role">
                            $${ role_options }}}
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-auto w-120px col-form-label">Lattes/ORCID Url</label>
                    <div class="col">
                        <input type="text" class="form-control" name="authors[${key}}][curriculum_url]" placeholder="Lattes/ORCID Url"
                            value="${curriculum_url}}" data-key="${key}}" data-name="curriculum_url">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-auto w-120px col-form-label">Resumo Currículo</label>
                    <div class="col">
                        <textarea rows="3" class="form-control" name="authors[${key}}][curriculum]"
                            placeholder="Resumo do Currículo" data-key="${key}}" data-name="curriculum">${curriculum}}</textarea>
                    </div>
                </div>
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-danger btn-sm remove-author" data-key="${key}}">
                    <span class="fas fa-times"></span>
                </button>
            </div>
        </div>
    </div></script>

<form id="article-authors" action="{{ url(AUTHOR_ARTICLES_BASE_URL) }}/{{ $key }}" method="POST">
    <input type="hidden" name="id" value="{{ $id }}" />

    <div class="form-group row">
        <div class="col">
            <strong>AUTOR</strong>
        </div>
    </div>

    <div class="form-group row mt-3">
        <label class="col-auto w-120px col-form-label">Nome</label>
        <div class="col col-md-8 col-lg-6 col-xl-4">
            <input class="form-control bg-gray-200 text-gray-800" value="{{ logged_author()->name }}" disabled>
        </div>
    </div>

    <div class="form-group row">
        <label class="col-auto w-120px col-form-label">Email</label>
        <div class="col col-md-8 col-lg-6 col-xl-6">
            <input class="form-control bg-gray-200 text-gray-800" value="{{ logged_author()->email }}" disabled>
        </div>
    </div>

    <div class="form-group row">
        <label class="col-auto w-120px col-form-label">Titulação</label>
        <div class="col col-md-8 col-lg-6 col-xl-3">
            <select name="role" class="form-select" placeholder="Titulação" required>
                {{ select_options_author_role(logged_author()->role) }}
            </select>
        </div>
    </div>

    <div class="form-group row">
        <label class="col-auto w-120px col-form-label">Lattes/ORCID Url</label>
        <div class="col">
            <input type="text" class="form-control" name="curriculum_url" placeholder="Lattes/ORCID Url"
                value="{{ logged_author()->curriculum_url }}">
        </div>
    </div>

    <div class="form-group row">
        <label class="col-auto w-120px col-form-label">Resumo Currículo</label>
        <div class="col">
            <textarea rows="3" class="form-control" name="curriculum" placeholder="Resumo do Currículo">{{ logged_author()->curriculum }}</textarea>
        </div>
    </div>

    <hr>

    <div class="form-group row">
        <div class="col">
            <strong>CO-AUTORES</strong>
        </div>
    </div>

    <div class="co-authors"></div>

    <div class="form-group">
        <a href="#" class="add-author">+ Adicionar Co-Autor</a>
    </div>

</form>
