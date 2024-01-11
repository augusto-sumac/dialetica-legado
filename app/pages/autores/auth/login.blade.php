@extend('layouts.auth', ['app_class' => 'app-authors'])

@section('content')
    <div class="row">
        @include('pages.autores.auth.components.form-login')
        @include('pages.autores.auth.components.form-reset')
        @include('pages.autores.auth.components.form-register')
    </div>
@endsection

@section('js')
    <script>
        (function() {
            $(document).ready(function() {
                // $('[name="fone"]').mask("(00) 00000-0009");
                $('[name="fone"]').mask("####################");

                var form_login = $('#form-login'),
                    form_reset = $('#form-reset'),
                    form_register = $('#form-register');

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

                                window.location.href = data.redirect ? data.redirect : '/';
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

                form_register
                    .on('click', '.form-confirm', function(e) {
                        e.preventDefault();
                        e.stopPropagation();

                        submitForm({
                            form: form_register,
                            onSuccess() {
                                window.location.href = '/';
                            }
                        });

                        return false;
                    });

                $('.show-form-reset')
                    .on('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();

                        form_login.hide();
                        form_reset.show();
                        form_register.hide();

                        return false;
                    });

                $('.show-form-login')
                    .on('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();

                        form_login.show();
                        form_reset.hide();
                        form_register.hide();

                        return false;
                    });

                $('.show-form-register')
                    .on('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();

                        form_login.hide();
                        form_reset.hide();
                        form_register.show();

                        return false;
                    });
            });
        })();
    </script>
@endsection
