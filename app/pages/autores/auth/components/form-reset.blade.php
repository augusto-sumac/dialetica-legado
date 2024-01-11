<form id="form-reset" action="{{ url('auth/esqueci-minha-senha') }}" method="POST" style="display: none;">
    <div class="form-group row">
        <div class="col">
            <h1 class="display-4 text-center mb-3">Recuperar Acesso</h1>
            <p class="text-muted text-center mb-2">
                Informe seu e-mail para obter um link de redefinição de senha.
            </p>
        </div>
    </div>

    <div class="form-group row">
        <div class="col">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" placeholder="nome@dominio.com" name="email" required />
        </div>
    </div>

    <div class="form-group row">
        <div class="col">
            <button type="submit" class="btn btn-primary w-100">Recupearar Acesso</button>
        </div>
    </div>

    <div class="form-group row text-center text-white">
        <div class="col">
            <a class="show-form-login small text-muted" href="#" title="Lembrei! Fazer login">
                Lembrei! Voltar ao login
            </a>
        </div>
    </div>
</form>
