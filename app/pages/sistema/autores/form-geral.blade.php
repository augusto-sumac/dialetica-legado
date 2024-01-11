<div class="card-body">

    <div class="form-group row">
        <label class="col-sm-3 col-form-label">Nome</label>
        <div class="col-sm-6">
            <input type="text" class="form-control" name="name" placeholder="Nome"
                value="{{ isset($name) ? $name : '' }}">
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-3 col-form-label">Email</label>
        <div class="col-sm-6">
            <input type="email" class="form-control" name="email" placeholder="Email"
                value="{{ isset($email) ? $email : '' }}">
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-3 col-form-label">CPF/CNPJ/ID</label>
        <div class="col-sm-4">
            <input type="text" class="form-control cpf" name="document" placeholder="CPF/CNPJ/ID"
                value="{{ isset($document) ? $document : '' }}">
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-3 col-form-label">Celular</label>
        <div class="col-sm-4">
            <input type="text" class="form-control phone celular" name="phone" placeholder="Celular"
                value="{{ isset($phone) ? $phone : '' }}">
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-3 col-form-label">Titulação</label>
        <div class="col-sm-5">
            <select name="role" class="form-select" placeholder="Titulação" required>
                {{ select_options_author_role(isset($role) ? $role : null) }}
            </select>
        </div>
    </div>

    <div class="form-group row pt-5">
        <p>
            <span class="fas fa-info-circle me-2"></span> Seu mini currículo será utilizado na publicação de
            artigos.
        </p>
    </div>

    <div class="form-group row">
        <label class="col-sm-3 col-form-label">Lattes/ORCID Url</label>
        <div class="col-sm-9">
            <input type="text" class="form-control" name="curriculum_url" placeholder="Lattes/ORCID Url"
                value="{{ isset($curriculum_url) ? $curriculum_url : '' }}">
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-3 col-form-label">Resumo do Currículo</label>
        <div class="col-sm-9">
            <textarea rows="4" class="form-control" name="curriculum" placeholder="Resumo do Currículo">{{ isset($curriculum) ? $curriculum : '' }}</textarea>
        </div>
    </div>

</div>
