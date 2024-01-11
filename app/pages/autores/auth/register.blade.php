@extend('layouts.auth', ['app_class' => 'app-authors'])

@section('content')
    <div class="row">
        @include('pages.autores.auth.components.form-register')
    </div>
@endsection

@section('js')
    <script>
        (function() {
            $(document).ready(function() {
                // $('[name="fone"]').mask("(00) 00000-0009");
                $('[name="fone"]').mask("####################");

                var form_register = $('#form-register');

                form_register.show();

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

                $('.show-form-login')
                    .on('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();

                        window.location.href = baseUrl + '/auth/login';

                        return false;
                    });
            });
        })();
    </script>
@endsection
