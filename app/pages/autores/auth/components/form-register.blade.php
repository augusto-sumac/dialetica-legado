<form id="form-register" action="{{ url('cadastro') }}" method="POST" style="display: none;">

    @if (isset($id))
        <input type="hidden" name="id" value="{{ $id }}" />
        <p>Complete seu cadastro para acessar a plataforma!</p>
    @endif

    <div class="form-group row">
        <div class="col">
            <label class="form-label">Nome Completo</label>
            <input type="text" value="{{ isset($name) ? $name : '' }}" class="form-control"
                placeholder="Nome Completo" name="name" required />
        </div>
    </div>

    <div class="form-group row">
        <div class="col">
            <label class="form-label">Email</label>
            <input type="email" value="{{ isset($email) ? $email : '' }}" class="form-control"
                placeholder="nome@dominio.com" name="email" required />
        </div>
    </div>

    <div class="form-group row">
        <div class="col">
            <label class="form-label">CPF/CNPJ/ID</label>
            <input type="text" value="{{ isset($document) ? $document : '' }}" class="form-control cpf"
                placeholder="CPF/CNPJ/ID" name="document" required />
        </div>
    </div>

    <div class="form-group row">
        <div class="col">
            <label class="form-label">Celular</label>
            <input type="text" class="form-control celular" placeholder="(00) 00000-0000" name="phone" required />
        </div>
    </div>

    <div class="form-group row">
        <div class="col">
            <label class="form-label">Titulação</label>
            <select name="role" class="form-select" placeholder="Titulação" required>
                {{ select_options_author_role() }}
            </select>
        </div>
    </div>

    <div class="form-group">
        <div class="row">
            <div class="col">
                <label class="form-label">Senha</label>
            </div>
        </div>
        <div class="input-group input-group-merge">
            <input class="form-control" type="password" placeholder="Digite sua senha" name="password" required />
            <span class="input-group-text cursor-pointer">
                <span class="fas fa-eye"></span>
            </span>
        </div>
    </div>

    <div class="form-group">
        <div class="row">
            <div class="col">
                <label class="form-label">Confirme a senha</label>
            </div>
        </div>
        <div class="input-group input-group-merge">
            <input class="form-control" type="password" placeholder="Confirme a senha" name="password_confirmation"
                required />
            <span class="input-group-text cursor-pointer">
                <span class="fas fa-eye"></span>
            </span>
        </div>
    </div>

    <div class="form-group row mt-5">
        <div class="col">
            <button type="submit" class="form-confirm btn btn-primary w-100">Cadastrar</button>
        </div>
    </div>

    <div class="form-group row text-center text-white">
        <div class="col">
            <a class="show-form-login small text-muted" href="javascript:void(0)" title="Cancelar e voltar ao login">
                Cancelar e voltar ao login
            </a>
        </div>
    </div>
</form>
