<form id="form-login" action="{{ url('auth/login') }}" method="POST">
    <div class="form-group row">
        <div class="col">
            <p class="text-muted text-center mb-2">
                Informe seu e-mail e senha para entrar.
            </p>
        </div>
    </div>

    <div class="form-group row">
        <div class="col">
            <label class="form-label">E-mail</label>
            <input type="email" class="form-control" placeholder="nome@dominio.com" name="email" required />
        </div>
    </div>

    <div class="form-group">
        <div class="row">
            <div class="col">
                <label class="form-label">Senha</label>
            </div>
            <div class="col-auto">
                <a class="form-text small text-muted show-form-reset" href="#" title="Esqueci a senha">
                    Esqueceu a senha?
                </a>
            </div>
        </div>
        <div class="input-group input-group-merge">
            <input class="form-control" type="password" placeholder="Digite sua senha" name="senha" required />
            <span class="input-group-text cursor-pointer">
                <span class="fas fa-eye"></span>
            </span>
        </div>
    </div>

    <div class="form-group row mt-5">
        <div class="col">
            <button type="submit" class="btn btn-primary w-100">Entrar</button>
        </div>
    </div>

    <div class="form-group row">
        <div class="col">
            <p class="text-muted text-center mb-3">
                OU
            </p>

            <p class="text-muted text-center mb-2">
                Cadastre-se como um autor e submeta artigos e materiais para publicação.
            </p>
        </div>
    </div>

    <div class="form-group row">
        <div class="col">
            <button type="button" class="btn btn-secondary w-100 show-form-register">Cadastre-se</button>
        </div>
    </div>

</form>
