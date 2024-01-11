@extend('layouts.auth')

@section('content')
    <div class="row">
        <form id="form-login" action="{{ url('sistema/auth/login') }}" method="POST">
            <div class="form-group row">
                <div class="col">
                    <h1 class="display-4 text-center mb-3">Login</h1>
                    <p class="text-muted text-center mb-2">
                        Informe seu e-mail e senha para entrar no sistema.
                    </p>
                </div>
            </div>

            <div class="form-group row">
                <div class="col">
                    <label class="form-label">Email</label>
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
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </div>
            </div>

        </form>

        <form id="form-reset" action="{{ url('sistema/auth/esqueci-minha-senha') }}" method="POST" style="display: none;">
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
                    <button type="submit" class="btn btn-primary w-100">Recuperar Acesso</button>
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
    </div>
@endsection

@section('js')
    <script>
        (function() {
            $(document).ready(function() {
                var form_login = $('#form-login'),
                    form_reset = $('#form-reset');

                form_login
                    .on('submit', function(e) {
                        e.preventDefault();
                        e.stopPropagation();

                        var data = {
                            email: $(this).find('[name="email"]').val(),
                            senha: $(this).find('[name="senha"]').val(),
                        };

                        if (data.email.length === 0 || data.senha.length === 0) {
                            Toast.fire({
                                icon: 'error',
                                title: 'Atenção',
                                text: 'Informe o seu email e a sua senha'
                            });

                            return false;
                        }

                        showFormSubmitLoading(form_login);

                        $.post(form_login.attr('action'), data)
                            .done(data => {
                                var hora = new Date().getHours();
                                var saudacao = 'Bom dia';
                                if (hora > 11 && hora < 19) saudacao = 'Boa tarde';
                                if (hora < 6) saudacao = 'Boa noite';

                                Toast.fire({
                                    icon: 'success',
                                    title: saudacao + ', ' + data.name + ', tudo bem com você?',
                                    html: 'Seu login foi realizado com sucesso. <br />Vamos lhe redirecionar para o Dashboard'
                                });

                                window.location.href = '/sistema';
                            })
                            .fail(function(response) {
                                response = response.responseJSON || {};

                                Toast.fire({
                                    icon: 'error',
                                    title: response.message || 'Email ou senha inválidos'
                                });

                                hideFormSubmitLoading(form_login);
                            });

                        return false;
                    });

                form_reset
                    .on('submit', function(e) {
                        e.preventDefault();
                        e.stopPropagation();

                        var data = {
                            email: $(this).find('[name="email"]').val()
                        };

                        if (data.email.length === 0) {
                            Toast.fire({
                                icon: 'error',
                                title: 'Atenção',
                                text: 'Informe o seu email'
                            });

                            return false;
                        }

                        showFormSubmitLoading(form_reset);

                        $.post(form_reset.attr('action'), data)
                            .done(data => {
                                Toast.fire({
                                    icon: 'success',
                                    title: data.message || 'Enviamos um email com instruções'
                                });

                                form_login.show();
                                form_reset.hide();
                            })
                            .fail(function(response) {
                                response = response.responseJSON || {};
                                Toast.fire({
                                    icon: 'error',
                                    title: response.message || 'Email inválido'
                                });
                            })
                            .always(function() {
                                hideFormSubmitLoading(form_reset);
                            });

                        return false;
                    });

                $('.show-form-reset')
                    .on('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();

                        form_login.hide();
                        form_reset.show();

                        return false;
                    });

                $('.show-form-login')
                    .on('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();

                        form_login.show();
                        form_reset.hide();

                        return false;
                    });
            });
        })();
    </script>
@endsection
