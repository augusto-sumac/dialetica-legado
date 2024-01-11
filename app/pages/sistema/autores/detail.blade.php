@extend('layouts.sistema')

<?php
$options = ['title' => 'Autores', 'icon' => 'user-graduate'];
?>
@section('content')
    @include('components.content-header', $options)

    @include('components.form-header', ['title' => 'Autores'])

    <div class="card tabs">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs">
                <li class="nav-item">
                    <a class="nav-link active" href="#geral">GERAL</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#address">ENDEREÇO</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#password">ALTERAR SENHA</a>
                </li>
            </ul>
        </div>

        <div id="tab_geral">

            <form action="{{ url(urlCurrent()) }}" method="POST">

                @include(AUTHORS_VIEW_PATH . '.form-geral')

                @if ($affiliate_coupon)
                    <div class="card-body pt-0">

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Cupon Afiliado</label>
                            <div class="col-auto align-self-center">
                                <div class="alert alert-dark h1 m-0 affiliate_coupon_token">
                                    {{ $affiliate_coupon->token }}
                                </div>
                            </div>
                            <div class="col-auto align-self-center">
                                <button type="button" class="btn btn-outline-dark btn-edit-affiliate-coupon-token"
                                    data-bs-toggle="tooltip" title="Alterar ID do cupom">
                                    <span class="fas fa-edit"></span>
                                </button>
                            </div>
                        </div>

                    </div>
                @endif

                <div class="card-body pt-0">

                    <div class="form-group mt-0 mb-0">
                        <div class="col-sm-6 offset-sm-3">
                            <button type="button" class="form-confirm btn btn-primary btn-lg">
                                Salvar Cadastro
                            </button>
                        </div>
                    </div>

                </div>

            </form>

        </div>

        <form action="{{ urlCurrent() }}/address" method="POST" id="tab_address" style="display: none;">
            <div class="card-body">

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

        <form action="{{ urlCurrent() }}/password" method="POST" id="tab_password" style="display: none;">
            <div class="card-body">

                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Senha</label>
                    <div class="col-sm-5">
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
                    <div class="col-sm-5">
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

    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            $('form')
                .on('click', '.form-confirm', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    var form = $(this).closest('form');

                    submitForm({
                        form,
                        onSuccess: function() {}
                    });

                    return false;
                })
                .on('click', '.btn-edit-affiliate-coupon-token', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    Swal.fire({
                            title: 'Alterar Cupom',
                            text: 'Não deve haver espação em branco e não pode haver mais que 30 caracteres!',
                            input: 'text',
                            inputAttributes: {
                                autocapitalize: 'off',
                                placeholder: 'DIALETIA-A1b2C3'
                            },
                            showCancelButton: true,
                            cancelButtonText: 'Cancelar',
                            confirmButtonText: 'Ok',
                            showLoaderOnConfirm: true,
                            preConfirm: function(token) {
                                return $.post('{{ url(urlCurrent()) }}/update-affiliate-coupon', {
                                        token
                                    })
                                    .fail(function(error) {
                                        if (error.responseJSON && error.responseJSON.message) {
                                            Swal.showValidationMessage(error.responseJSON.message);
                                        } else {
                                            Swal.showValidationMessage(
                                                'Não é possível utilizar o cupom informado! Tente outro!'
                                            );
                                        }
                                    });
                            },
                            allowOutsideClick: false
                        })
                        .then(function(result) {
                            if (result.isConfirmed) {
                                Toast.fire({
                                    icon: 'success',
                                    title: 'Cupom atualizado com sucesso!'
                                });

                                $('.affiliate_coupon_token').html(
                                    result.value.token
                                );
                            }
                        });

                    return false;
                });
        });
    </script>
@endsection
