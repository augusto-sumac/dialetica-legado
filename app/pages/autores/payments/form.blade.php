@extend(APP_AUTHOR_LAYOUT)

@section('content')
    @set('options', ['title' => 'Pagamento -> ' . $article_type->name, 'icon' => 'credit-card'])
    @include('components.content-header', $options)

    <form id="article-payment" action="{{ urlCurrent() }}" method="POST">

        <div class="card">

            <div class="card-header">
                <div>
                    <span class="fas fa-map-marker me-3"></span> ENDEREÇO
                </div>
            </div>

            <div class="card-body">

                <input type="hidden" name="id" value="{{ $article->id }}" />
                <input type="hidden" name="address_id" value="{{ $address->id }}" />
                <input type="hidden" name="update_address" value="{{ $address->id ? 0 : 1 }}" />

                @if ($address->id)
                    <div class="form-group mb-0">
                        <span>{{ $address->street }}, </span>
                        <span>{{ $address->number }}, </span>
                        @if ($address->complement)
                            <span>{{ $address->complement }}, </span>
                        @endif
                        <span>{{ $address->district }} - </span>
                        <span>CEP {{ $address->zip_code }} - </span>
                        <span>{{ $address->city }} - </span>
                        <span>{{ $address->state }}</span>

                        <div class="pt-3">
                            <a href="javascript:void(0)" class="update-address">[ Atualizar Endereço ]</a>
                            <a href="javascript:void(0)" style="display: none;" class="cancel-update-address">
                                [ Cancelar atualização de endereço ]
                            </a>
                        </div>
                    </div>
                @endif

                <div id="form-address" class="mt-4" style="display: {{ $address->id ? 'none' : 'block' }};">

                    @include('partials.form-address', compact('address'))

                    {{--
                    <div class="form-group mt-0 mb-0">
                        <div class="col-sm-6 offset-sm-3">
                            <button type="button" class="confirm-update-address btn btn-primary btn-lg">
                                Confirmar
                            </button>

                            <a class="btn btn-default ml-4 cancel-update-address">
                                Cancelar
                            </a>
                        </div>
                    </div>
                    --}}

                </div>

            </div>

        </div>

        @if (in_array($article->type_id, [1, 3]))
            <div class="card">

                <div class="card-header">
                    <div>
                        <span class="fas fa-gift me-3"></span> CUPOM DE DESCONTO
                    </div>
                </div>

                <div class="card-body">

                    <div class="form-group mb-0 row">
                        <div class="col-auto" style="min-width: 350px;">
                            <input type="text" class="form-control form-control-lg text-center px-0 py-3"
                                name="discount_coupon" placeholder="DIALETICA-A1B2C3" />
                        </div>

                        <div class="text-muted mt-3">
                            <span class="fas fa-info-circle"></span>
                            Se recebeu um cupom de desconto você pode aplicar agora.
                        </div>
                    </div>

                </div>

            </div>
        @endif

        <div class="card payment-card-group">

            <input type="hidden" name="payment_type" value="P">

            <div class="card-header">
                <ul class="nav nav-pills nav-fill payment-type-select">
                    <li class="nav-item">
                        <a href="javascript:void(0)" class="nav-link active" data-type="pix">
                            <i class="fas fa-qrcode mr-2"></i>
                            Pix
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="javascript:void(0)" class="nav-link" data-type="credit">
                            <i class="fas fa-credit-card mr-2"></i>
                            Cartão de Crédito
                        </a>
                    </li>
                    {{--
                        <li class="nav-item">
                        <a href="javascript:void(0)" class="nav-link" data-type="billet">
                            <i class="fas fa-barcode mr-2"></i>
                            Boleto
                        </a>
                    </li> 
                    --}}
                </ul>
            </div>

            <div class="card-body paymen-type-form ">

                <div id="payment_type_pix">
                    <div class="form-group mb-0">
                        <div class="text-center d-flex align-content-center justify-content-center mb-4">
                            <span class="fas fa-qrcode rounded border-1 p-2 shadow"
                                style="width: 250px; heigth: 250px; font-size: 250px"></span>
                        </div>
                        <div>
                            <h1 class="m-0 text-muted">
                                R$ <span class="article-amount">{{ toMoney($article->amount) }}</span>
                            </h1>
                        </div>
                        <div class="text-muted mt-3">
                            <span class="fas fa-info-circle me-2"></span>
                            Ao clicar em <strong>Confirmar</strong> iremos gerar um <strong>QrCode</strong> para que você
                            efetue o pagamento.
                        </div>
                        <div class="text-muted mt-3">
                            <span class="fas fa-info-circle me-2"></span>
                            Somente após a confirmação do pagamento o Artigo ou a Revisão será processado(a)!
                        </div>
                    </div>
                </div>

                <div id="payment_type_credit" style="display: none">

                    <div class="payment-form">
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">
                                Bandeira
                            </label>
                            <div class="col-sm-6 col-md-6 col-lg-4">
                                <select class="form-control" name="br" placeholder="Bandeira do Cartão">
                                    <option value="master">Mastercard</option>
                                    <option value="visa">Visa</option>
                                    <option value="elo">Elo</option>
                                    <option value="amex">American Express</option>
                                    <option value="diners">Diners</option>
                                    <option value="hipercard">Hipercad</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">
                                Nome Titular
                            </label>
                            <div class="col-sm-6 col-md-6 col-lg-4">
                                <input type="text" class="form-control" name="na"
                                    placeholder="Nome Impresso no Cartão" />
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">CPF/CNPJ/ID Titular</label>
                            <div class="col-sm-6 col-md-6 col-lg-4">
                                <input type="text" class="form-control" name="do"
                                    placeholder="CPF/CNPJ/ID do titular do Cartão" />
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Número</label>
                            <div class="col-sm-6 col-md-6 col-lg-4">
                                <input type="text" class="form-control" name="nu"
                                    placeholder="Número do Cartão" />
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Validade</label>
                            <div class="col-sm-6 col-md-3 col-lg-2">
                                <input type="text" class="form-control" name="ex" placeholder="MM/AA" />
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">CVV</label>
                            <div class="col-sm-6 col-md-3 col-lg-2">
                                <input type="text" class="form-control" name="cv" placeholder="CVV" />
                            </div>
                        </div>

                        <div class="form-group row payment-installments-group">
                            <label class="col-sm-3 col-form-label">Parcelas</label>
                            <div class="col-sm-6 col-md-4 col-lg-3">
                                <select name="in" class="form-select">
                                    @foreach ($installments as $inNum => $inValue)
                                        <option value="{{ $inNum }}"
                                            {{ $inNum == count($installments) ? 'selected' : '' }}>
                                            <strong>{{ $inNum }}x</strong> de <strong>R$
                                                {{ toMoney($inValue) }}</strong>
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>
                    <div class="mt-5">
                        <h1 class="m-0 text-muted">
                            R$ <span class="article-amount">{{ toMoney($article->amount) }}</span>
                        </h1>
                    </div>
                    <div class="text-muted mt-3">
                        <span class="fas fa-info-circle me-2"></span>
                        Somente após a confirmação do pagamento o Artigo ou a Revisão será processado(a)!
                    </div>

                </div>

                {{--
                    <div id="payment_type_billet" style="display: none">
                    <div class="form-group mb-0">
                        <div class="text-center d-flex align-content-center justify-content-center mb-4">
                            <span class="fas fa-barcode rounded border-1 p-2 shadow"
                                style="width: 250px; heigth: 100px; font-size: 100px"></span>
                        </div>
                        <div>
                            <h1 class="m-0 text-muted">
                                R$ <span class="article-amount">{{ toMoney($article->amount) }}</span>
                            </h1>
                        </div>
                        <div class="text-muted mt-3">
                            <span class="fas fa-info-circle me-2"></span>
                            Ao clicar em <strong>Confirmar</strong> iremos gerar um <strong>Boleto</strong> para que você
                            efetue o pagamento.
                        </div>
                        <div class="text-muted mt-3">
                            <span class="fas fa-info-circle me-2"></span>
                            Somente após a confirmação do pagamento o Artigo ou a Revisão será processado(a)!
                        </div>
                    </div>
                </div> 
                --}}

            </div>

        </div>

        <div class="card">

            <div class="card-body">

                <div class="form-group mt-0 mb-0">
                    <div class="col-12 text-center">
                        <button type="button" class="form-confirm btn btn-primary btn-lg">
                            Confirmar Pagamento
                        </button>

                        <a class="btn btn-default ml-4 form-cancel" href="{{ $back_route }}">
                            Cancelar e retornar
                        </a>
                    </div>
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
        let article = {{ json_encode($article) }};
        let address = {{ json_encode($address) }};
        let back_route = '{{ $back_route }}';

        let paymentTypeOptions = {
            pix: 'P',
            credit: 'C',
            billet: 'B',
        }

        function displayDiscountMessage(amount, percent) {
            let target = $('[name = "discount_coupon"]').closest('.form-group');
            target.find('discount-info').remove();

            if (!amount || parseFloat(amount) === 0) return;

            let display_amount = parseInt(percent) === 100 ? '100%' : `R$ ${toMoneyBr(amount||0)}`;

            target.append(
                `<div class="discount-info mt-5 mb-3">
                    <span class="alert alert-success">
                    Você recebeu <strong>${display_amount}</strong> de desconto!
                    </span>
                </div>`
            );
        }

        $(document).ready(function() {
            $('[name="do"]').mask('##############################');
            $('[name="nu"]').mask('#### #### #### 9999 999');
            $('[name="ex"]').mask('##/#9');
            $('[name="cv"]').mask('#######9');

            displayDiscountMessage(
                article.discount_amount,
                Math.round(
                    (1 - (
                        parseFloat(article.amount) /
                        parseFloat(article.gross_amount)
                    )) * 100
                )
            );

            var form = $('form#article-payment');

            form
                .on('click', '.form-confirm', function(e) {
                    e.preventDefault();
                    e.stopPropagation()

                    submitForm({
                        form: form,
                        onSuccess: function(data) {
                            const onOkClick = () => window.location.href = back_route;

                            let options = {
                                icon: 'success',
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                allowEnterKey: false,
                                confirmButtonText: "Ok, fechar janela",
                                customClass: {
                                    htmlContainer: 'p-0 m-0'
                                }
                            }

                            let amount = data?.payment?.Amount / 100;

                            switch (data?.payment?.Type) {
                                case 'Pix':
                                    let imgSrc = data?.payment?.QrCodeBase64Image;

                                    Swal.fire({
                                            ...options,
                                            title: 'Pix Gerado',
                                            html: `
                                            <div class="p-4 pb-0">
                                                <p class="mb-4">Utilize o QrCode para realizar o pagamento.</p>
                                                <p class="text-center mb-4">
                                                    <span class="border p-1 rounded shadow d-inline-block">
                                                        <img src="data:image/jpeg;base64,${imgSrc}" style="width: 250px; height: 250px" class="d-inline-block" />    
                                                    </span>    
                                                </p>
                                                <h2 class="mb-4">Valor R$ ${toMoneyBr(amount)}</h2>                                             
                                            </div>
                                            <div class="text-start row g-0 align-items-center border-top border-bottom p-4">
                                                <div class="col-auto">
                                                    <span class="fas fa-info-circle fa-2x me-4 text-warning"></span>
                                                </div>
                                                <div class="col">
                                                    A confirmação pode levar até duas horas após a realização do pagamento!
                                                </div>
                                            </div>`,
                                        })
                                        .then(onOkClick);
                                    break;

                                case 'Boleto':
                                    let url = data?.payment?.Url;
                                    let barcode = data?.payment?.BarCodeNumber;

                                    Swal.fire({
                                            ...options,
                                            title: 'Boleto Gerado',
                                            html: `
                                            <div class="p-4 pb-0">
                                                <p class="mb-4">Clique no botão abaixo baixar o boleto de pagamento.</p>
                                                <p class="mb-4">
                                                    <a href="${url}" target="_blank" title="Link boleto" class="btn btn-dark px-4">Clique para baixar</a>
                                                </p>
                                                <p class="mb-4">Ou utilize o a linha digitável abaixo</p>
                                                <div class="input-group mb-4">
                                                    <input type="text" class="form-control" value="${barcode}">
                                                    <button class="btn btn-outline-secondary" type="button" 
                                                        data-bs-toggle="tooltip"
                                                        title="Copiar para área de transferência"
                                                        data-copy-to-clipboard="${barcode}"
                                                        data-alert="true">
                                                        <span class="fas fa-clipboard"></span>
                                                    </button>
                                                </div>
                                                <h2 class="mb-4">Valor R$ ${toMoneyBr(amount)}</h2>                                                
                                            </div>
                                            <div class="text-start row g-0 align-items-center border-top border-bottom p-4">
                                                <div class="col-auto">
                                                    <span class="fas fa-info-circle fa-2x me-4 text-warning"></span>
                                                </div>
                                                <div class="col">
                                                    A confirmação pode levar até dois dias úteis após a realização do pagamento!
                                                </div>
                                            </div>`
                                        })
                                        .then(onOkClick);
                                    break;

                                case 'CreditCard':
                                    Swal.fire({
                                        ...options,
                                        title: 'Sucesso',
                                        html: `
                                        <div class="p-4 pb-0">
                                            <p class="mb-4">
                                                Pagamento realizado com sucesso.
                                            </p>
                                            <p class="mb-4">
                                                Em breve você receberá um email com mais detalhes
                                            </p>
                                            <h2 class="mb-4">Valor R$ ${toMoneyBr(amount)}</h2>                                                
                                        </div>`
                                    }).then(onOkClick);
                                    break;

                                case 'Discount':
                                    Swal.fire({
                                        ...options,
                                        title: 'Sucesso',
                                        html: `
                                        <div class="p-4 pb-0">
                                            <p class="mb-4">
                                                Você utilizou um cupom de 100% de desconto!
                                            </p>
                                            <p class="mb-4">
                                                Em breve você receberá um email com mais detalhes
                                            </p>
                                            <h2 class="mb-4">Valor R$ ${toMoneyBr(amount)}</h2>                                                
                                        </div>`
                                    }).then(onOkClick);
                                    break;

                                default:
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Oops!',
                                        html: `
                                        <div class="p-4 pb-0">
                                            <p class="mb-4">
                                                Houve algum erro no processamento
                                            </p>
                                            <p class="mb-4">
                                                Em breve você receberá um email com mais detalhes
                                            </p>                                             
                                        </div>`
                                    }).then(onOkClick);
                                    break;
                            }

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
                .on('click', '.payment-type-select a', function() {
                    let el = $(this);
                    let type = el.data('type');

                    $('[name="payment_type"]').val(paymentTypeOptions[type]);

                    $('.payment-type-select a').removeClass('active');
                    el.addClass('active');

                    $('.paymen-type-form > div').hide();
                    $('.paymen-type-form > div#payment_type_' + type).show();
                })
                .on('click', '.update-address', function() {
                    $(this).hide();
                    $('.cancel-update-address').show();
                    $('[name="update_address"]').val(1);
                    $('#form-address').show();
                })
                .on('click', '.cancel-update-address', function() {
                    $('.cancel-update-address').hide();
                    $('.update-address').show();
                    $('[name="update_address"]').val(0);
                    $('#form-address').hide();
                })
                .on('change', '[name="discount_coupon"]', function() {
                    var input = $(this);
                    var token = input.val();

                    input.closest('.form-group').find('.discount-info').remove();

                    let url = `${baseUrl}/coupons/${token}/validate/${article.id}`;

                    $.get(url)
                        .done(function(data) {
                            console.log(data);

                            form.find('[name="in"]').empty().html(data.installments).data('selectpicker')
                                .refresh();

                            if (data.valid) {
                                displayDiscountMessage(data.discount_amount, data.discount_percent);

                                if (data.discount_percent === 100) {
                                    $('.payment-installments-group, .payment-card-group').hide();
                                } else {
                                    $('.payment-installments-group, .payment-card-group').show();
                                }

                                $('.article-amount').html(toMoneyBr(data.article_amount));

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
                                            title: data.message ? data.message :
                                                'O cupom informado não é válido'
                                        });
                                    }, 100);
                                }

                                $('[name="discount_coupon"]').val('').focus();

                                $('.payment-installments-group, .payment-card-group').show();

                                $('.article-amount').html(toMoneyBr(article.amount));

                                displayDiscountMessage(0, 0);
                            }
                        })
                        .fail(function() {
                            $('.payment-installments-group, .payment-card-group').show();

                            Toast.fire({
                                icon: 'error',
                                title: 'O cupom informado não é válido'
                            });

                            $('[name="discount_coupon"]').val('').focus();

                            $('.article-amount').html(toMoneyBr(article.amount));

                            displayDiscountMessage(0, 0);
                        });
                });
        });
    </script>
@endsection
