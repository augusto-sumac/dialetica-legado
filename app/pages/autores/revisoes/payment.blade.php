@extend('layouts.autores')

<?php
$options = [
    'title' => 'Minhas Revisões -> Pagamento',
    'icon' => 'credit-card',
];
?>
@section('content')
    @include('components.content-header', $options)

    <form id="article-payment" action="{{ urlCurrent() }}" method="POST" class="card tabs">

        <div class="card-body">

            <div class="form-group row">
                <div class="col">
                    <label>Título do Trabalho</label>
                    <h3>{{ $title }}</h3>
                </div>
            </div>

            <hr>

            @include('pages.autores.artigos.components.payment-form')

        </div>

        <div class="card-body pt-0">

            <div class="form-group mt-0 mb-0">
                <div class="col-sm-6 offset-sm-3">
                    <button type="button" class="form-confirm btn btn-primary btn-lg">
                        Confirmar Pagamento
                    </button>

                    <a class="btn btn-default ml-4 form-cancel" href="{{ url(AUTHOR_REVIEWS_BASE_URL . '/' . $id) }}">
                        Cancelar e retornar
                    </a>
                </div>
            </div>

        </div>

    </form>
@endsection

@section('css')
    <style>
        @media(min-width: 768px) {
            .payment-form .col-form-label {
                max-width: 150px;
            }
        }
    </style>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            $('[name="do"]').mask('##############################');
            $('[name="nu"]').mask('#### #### #### 9999 999');
            $('[name="ex"]').mask('##/#9');
            $('[name="cv"]').mask('#######9');

            var form = $('form#article-payment');

            form
                .on('click', '.form-confirm', function(e) {
                    e.preventDefault();
                    e.stopPropagation()

                    submitForm({
                        form: form,
                        onSuccess: function(data) {
                            Swal.fire({
                                    icon: 'success',
                                    title: 'Sucesso',
                                    text: 'Pagamento realizado com sucesso. Em breve você receberá um email com mais detalhes!'
                                })
                                .then(function() {
                                    var url = baseUrl + '/revisoes/' + {{ $id }};
                                    window.location.href = url;
                                });
                        },
                        onError: function(data) {
                            if (data.errors) return;

                            Swal.fire({
                                icon: 'error',
                                title: 'Ocorrer um erro!',
                                html: [
                                    '<p>Falha ao realizar o pagamento.</p>',
                                    '<p>Retorno do autorizador: <br/><strong class="text-danger">' +
                                    data.error_message + '</strong></p>',
                                    '<p>Tente novamente!</p>'
                                ].join('')
                            });
                        }
                    });

                    return false;
                });

            $(document)
                .on('click', '.update-address', function() {
                    $(this).hide();
                    $('.cancel-update-address').show();
                    $('[name="update_address"]').val(1);
                    $('#form-address').show();
                })
                .on('click', '.cancel-update-address', function() {
                    $(this).hide();
                    $('.update-address').show();
                    $('[name="update_address"]').val(0);
                    $('#form-address').hide();
                })
                .on('change', '[name="discount_coupon"]', function() {
                    var input = $(this);
                    var token = input.val();

                    input.closest('.form-group').find('.discount-info').remove();

                    $.get(baseUrl + '/revisoes/{{ $id }}/cupom/' + token)
                        .done(function(data) {
                            form.find('[name="in"]').empty().html(data.installments).data('selectpicker')
                                .refresh();

                            if (data.valid) {
                                let discount_amount = data.discount_percent === 100 ? '100%' : 'R$ ' +
                                    toMoneyBr(data.discount_amount)

                                input.closest('.form-group').append(
                                    ['<div class="discount-info mt-4">',
                                        '<span class="alert alert-success">',
                                        'Você recebeu <strong>',
                                        discount_amount,
                                        '</strong> de desconto!',
                                        '</span>',
                                        '</div>'
                                    ].join('')
                                )

                                if (data.discount_percent === 100) {
                                    $('.payment-installments-group, .payment-card-group').hide();
                                } else {
                                    $('.payment-installments-group, .payment-card-group').show();
                                }

                                setTimeout(function() {
                                    Toast.fire({
                                        icon: 'success',
                                        title: 'Cupom aplicado com sucesso'
                                    });
                                }, 100)
                            } else {
                                if (token.length) {
                                    setTimeout(function() {
                                        Toast.fire({
                                            icon: 'error',
                                            title: data.message ||
                                                'O cupom informado não é válido'
                                        });
                                    }, 100);
                                }

                                $('[name="discount_coupon"]').val('').focus();

                                $('.payment-installments-group, .payment-card-group').show();
                            }
                        })
                        .fail(function() {
                            $('.payment-installments-group, .payment-card-group').show();

                            Toast.fire({
                                icon: 'error',
                                title: 'O cupom informado não é válido'
                            });

                            $('[name="discount_coupon"]').val('').focus();
                        });
                });
        });
    </script>
@endsection
