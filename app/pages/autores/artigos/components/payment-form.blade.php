<input type="hidden" name="id" value="{{ $id }}" />
<input type="hidden" name="current_address_id" value="{{ $current_address_id }}" />
<input type="hidden" name="update_address" value="{{ $current_address_id ? 0 : 1 }}" />

<div class="form-group row">
    <div class="col">
        <h5>ENDEREÇO</h5>
    </div>
</div>

@if ($current_address_id)
    <div class="form-group">
        <span>{{ $current_address_street }}, </span>
        <span>{{ $current_address_number }}, </span>
        @if ($current_address_complement)
            <span>{{ $current_address_complement }}, </span>
        @endif
        <span>{{ $current_address_district }} - </span>
        <span>CEP {{ $current_address_zip_code }} - </span>
        <span>{{ $current_address_city }} - </span>
        <span>{{ $current_address_state }}</span>

        <div class="pt-3">
            <a href="javascript:void(0)" class="update-address">[ Atualizar Endereço ]</a>
            <a href="javascript:void(0)" style="display: none;" class="cancel-update-address">
                [ Cancelar atualização de endereço ]
            </a>
        </div>
    </div>
@endif

<div id="form-address" style="display: {{ $current_address_id ? 'none' : 'block' }};">

    @include('pages.autores.meus-dados.components.form-address')

</div>

@if (in_array($type_id, [1, 3]))
    <hr>

    <div class="form-group row">
        <div class="col">
            <h5>CUPOM DE DESCONTO</h5>
        </div>
    </div>

    <div class="form-group row">
        <div class="col-auto" style="min-width: 250px;">
            <input type="text" class="form-control form-control-lg text-center px-0" name="discount_coupon"
                placeholder="DIALETICA-A1B2C3" />
        </div>

        <div class="text-muted mt-3">
            <span class="fas fa-info-circle"></span>
            Se recebeu um cupom de desconto você pode aplicar agora.
        </div>
    </div>
@endif


<div class="payment-installments-group">
    <hr>

    <div class="form-group row">
        <div class="col">
            <h5>OPÇÃO DE PAGAMENTO</h5>
        </div>
    </div>

    <div class="form-group row">
        <div class="col-auto" style="min-width: 180px;">
            <select name="in" class="form-select">
                @foreach ($installments as $inNum => $inValue)
                    <option value="{{ $inNum }}" {{ $inNum == count($installments) ? 'selected' : '' }}>
                        <strong>{{ $inNum }}x</strong> de <strong>R$
                            {{ toMoney($inValue) }}</strong>
                    </option>
                @endforeach
            </select>
        </div>
    </div>

</div>

<div class="payment-card-group">
    <hr>

    <div class="form-group row mt-4">
        <div class="col">
            <h5>DADOS CARTÃO DE CRÉDITO</h5>
        </div>
    </div>

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
                <input type="text" class="form-control" name="na" placeholder="Nome Impresso no Cartão" />
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
                <input type="text" class="form-control" name="nu" placeholder="Número do Cartão" />
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
                <input type="text" class="form-control" name="cv" placeholder="CVC" />
            </div>
        </div>
    </div>

</div>
