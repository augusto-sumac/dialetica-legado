@extend('layouts.autores')

<?php
$options = [
    'title' => 'Meus Dados',
    'icon' => 'user-circle',
];
?>
@section('content')
    @include('components.content-header', $options)

    <form id="meus-dados" action="{{ urlCurrent() }}" class="card tabs" method="POST">
        <div class="card-body">

            <div class="form-group row">
                <p>
                    <span class="fas fa-info-circle me-2"></span>
                    Mantenha seus dados sempre atualizados.
                </p>
            </div>

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
                    <input type="text" class="form-control" name="document" placeholder="CPF/CNPJ/ID"
                        value="{{ isset($document) ? $document : '' }}">
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-3 col-form-label">Celular</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control phone" name="phone" placeholder="Celular"
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

        <div class="card-body pt-0">

            <div class="form-group mt-0 mb-0">
                <div class="col-sm-6 offset-sm-3">
                    <button type="button" class="form-confirm btn btn-primary btn-lg">
                        Salvar
                    </button>
                </div>
            </div>

        </div>

    </form>

    <form id="minha-senha" action="{{ urlCurrent() }}/alterar-senha" class="card tabs" method="POST">
        <div class="card-body">

            <div class="form-group row">
                <p>
                    <span class="fas fa-info-circle me-2"></span> Se desejar alterar a sua senha use o formulário abaixo.
                </p>
            </div>

            <div class="form-group row">
                <label class="col-sm-3 col-form-label">Senha</label>
                <div class="col-sm-3">
                    <div class="input-group input-group-merge">
                        <input type="password" class="form-control" name="password" placeholder="Senha">
                        <span class="input-group-text cursor-pointer">
                            <span class="fas fa-eye"></span>
                        </span>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-3 col-form-label">Confirmar a Senha</label>
                <div class="col-sm-3">
                    <div class="input-group input-group-merge">
                        <input type="password" class="form-control" name="password_confirmation"
                            placeholder="Confirmar a Senha">
                        <span class="input-group-text cursor-pointer">
                            <span class="fas fa-eye"></span>
                        </span>
                    </div>
                </div>
            </div>

        </div>

        <div class="card-body pt-0">

            <div class="form-group mt-0 mb-0">
                <div class="col-sm-6 offset-sm-3">
                    <button type="button" class="form-confirm btn btn-primary btn-lg">
                        Alterar Senha
                    </button>
                </div>
            </div>

        </div>

    </form>

    <form id="address" action="{{ urlCurrent() }}/address" class="card tabs {{ $has_affiliate_coupon ? '' : 'mb-0' }}"
        method="POST">
        <div class="card-body">

            <div class="form-group row">
                <p>
                    <span class="fas fa-info-circle me-2"></span> Mantenha seu endereço atualizado. Ele é necessário para
                    que possamos emitir suas notas fiscais de serviço.
                </p>
            </div>

            @include('partials.form-address', compact('address'))

        </div>

        <div class="card-body pt-0">

            <div class="form-group mt-0 mb-0">
                <div class="col-sm-6 offset-sm-3">
                    <button type="button" class="form-confirm btn btn-primary btn-lg">
                        Salvar Endereço
                    </button>
                </div>
            </div>

        </div>

    </form>

    @if ($has_affiliate_coupon)
        @include('pages.autores.meus-dados.components.form-account')
    @endif
@endsection

@section('js')
    @include('pages.autores.meus-dados.components.form-js')

    <script>
        $(document).ready(function() {
            // $('[name="phone"]').mask("(00) 00000-0009");
            $('[name="phone"]').mask("####################");

            var form = $('form#meus-dados')

            form
                .on('click', '.form-confirm', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    submitForm({
                        form: form,
                        onSuccess: function() {

                        }
                    });

                    return false;
                });

            var form_pass = $('form#minha-senha');

            form_pass
                .on('click', '.form-confirm', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    submitForm({
                        form: form_pass
                    });

                    return false;
                });

            var form_address = $('form#address');

            form_address
                .on('click', '.form-confirm', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    submitForm({
                        form: form_address,
                        onSuccess: function(data) {
                            form_address.find('[name="id"]').val(data.item.id);
                        }
                    });

                    return false;
                });

            var form_account = $('form#minha-conta');

            form_account
                .on('click', '.form-confirm', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    submitForm({
                        form: form_account,
                        onSuccess: function(data) {}
                    });

                    return false;
                });
        });
    </script>
@endsection
