@extend('layouts.auth', ['app_class' => 'app-authors'])

@section('content')
    <div class="row">
        <form id="form-reset" action="{{ url('auth/redefinir-senha?token=' . input('token')) }}" class="form-horizontal"
            method="post">
            <div class="form-group row">
                <div class="col">
                    <h1 class="display-4 text-center mb-3">Redefinir Senha</h1>
                    <p class="text-muted text-center mb-2">
                        Digite sua nova senha de acesso.
                    </p>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col">
                        <label class="form-label">Senha</label>
                    </div>
                </div>
                <div class="input-group input-group-merge">
                    <input class="form-control" type="password" placeholder="Digite sua senha" name="senha" required />
                    <span class="input-group-text cursor-pointer">
                        <span class="fas fa-eye"></span>
                    </span>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col">
                        <label class="form-label">Confirme a nova senha</label>
                    </div>
                </div>
                <div class="input-group input-group-merge">
                    <input class="form-control" type="password" placeholder="Confirme a nova senha"
                        name="senha_confirmation" required />
                    <span class="input-group-text cursor-pointer">
                        <span class="fas fa-eye"></span>
                    </span>
                </div>
            </div>

            <div class="form-group row mt-5">
                <div class="col">
                    <button type="submit" class="btn btn-primary w-100">Enviar</button>
                </div>
            </div>

            <div class="form-group row text-center text-white">
                <div class="col">
                    <a class="show-form-login small text-muted" href="{{ url('auth/login') }}" title="Voltar ao login<">
                        Voltar ao login</a>
                </div>
            </div>
        </form>
    </div>
@endsection


@section('js')
    <script>
        (function() {
            $(document).ready(function() {
                var form = $('#form-reset');

                form
                    .on('submit', function(e) {
                        e.preventDefault();
                        e.stopPropagation();

                        var data = {
                            senha: $(this).find('[name="senha"]').val(),
                            senha_confirmation: $(this).find('[name="senha_confirmation"]').val(),
                        };

                        if (data.senha.length === 0 || data.senha_confirmation.length === 0) {
                            Toast.fire({
                                icon: 'error',
                                title: 'Atenção',
                                text: 'Informe a sua nova senha'
                            });

                            return false;
                        }

                        showFormSubmitLoading(form);

                        $.post(form.attr('action'), data)
                            .done(data => {
                                Toast.fire({
                                    icon: 'success',
                                    title: 'Sua senha foi redefinada com sucesso... Redirecionando para a tela de Login'
                                });

                                setTimeout(() => {
                                    window.location.href = '/';
                                }, 3000);
                            })
                            .fail(function(response) {
                                response = response.responseJSON || {};

                                Toast.fire({
                                    icon: 'error',
                                    title: response.message ||
                                        'A senha e a confirmação devm ser iguais!'
                                });

                                hideFormSubmitLoading(form);
                            });

                        return false;
                    });

            });
        })();
    </script>
@endsection
