@if (isset($affiliate_coupon) && $affiliate_coupon)
    @include('pages.autores.meus-dados.components.card-affiliate-coupon')
@endif

<form id="minha-conta" action="/meus-dados/minha-conta" class=" mb-0 {{ isset($in_modal) ? 'text-start' : 'card tabs' }}"
    method="POST">
    <div class="{{ isset($in_modal) ? 'p-0 pt-3' : 'card-body' }}">

        <div class="form-group row">
            <p>
                <span class="fas fa-info-circle me-2"></span> Os dados da conta serão utilizados para transferir o
                saldo que recebeu pela publicação de artigos usando seu cupom de descontos.
            </p>
        </div>

        <div class="form-group row">
            <div class="col-sm-6">
                <h3>FAVORECIDO</h3>
            </div>
        </div>

        <div class="form-group row">
            <label class="{{ isset($in_modal) ? 'col-12' : 'col-sm-3' }} col-form-label">CPF/CNPJ/ID</label>
            <div class="{{ isset($in_modal) ? 'col-12' : 'col-sm-6 col-md-4 col-xl-3' }}">
                <input type="text" class="form-control" name="account_document" placeholder="CPF/CNPJ/ID Favorecido"
                    value="{{ isset($account_document) ? mask($account_document, 'cpf_cnpj') : '' }}">
            </div>
        </div>

        <div class="form-group row">
            <label class="{{ isset($in_modal) ? 'col-12' : 'col-sm-3' }} col-form-label">Nome/Razão Social</label>
            <div class="{{ isset($in_modal) ? 'col-12' : 'col-sm-6' }}">
                <input type="text" class="form-control" name="account_name"
                    placeholder="Nome/Razão Social Favorecido" value="{{ isset($account_name) ? $account_name : '' }}">
            </div>
        </div>

        <div class="form-group row pt-4">
            <div class="col-sm-6">
                <h3>DADOS PARA PIX</h3>
            </div>
        </div>

        <div class="form-group row">
            <label class="{{ isset($in_modal) ? 'col-12' : 'col-sm-3' }} col-form-label">Tipo Chave Pix</label>
            <div class="{{ isset($in_modal) ? 'col-12' : 'col-sm-4 col-lg-3 col-xl-2' }}">
                <select class="form-select" name="account_pix_type" placeholder="Tipo chave">
                    {{ select_options_tipos_chave_pix(isset($account_pix_type) ? $account_pix_type : null) }}
                </select>
            </div>
        </div>

        <div class="form-group row">
            <label class="{{ isset($in_modal) ? 'col-12' : 'col-sm-3' }} col-form-label">Chave Pix</label>
            <div class="{{ isset($in_modal) ? 'col-12' : 'col-sm-6 col-lg-5 col-xl-4' }}">
                <input type="text" class="form-control" name="account_pix_key" placeholder="Chave Pix"
                    value="{{ isset($account_pix_key) ? $account_pix_key : '' }}">
            </div>
        </div>

    </div>

    @if (!isset($in_modal))
        <div class="card-body pt-0">
            <div class="form-group mt-0 mb-0">
                <div class="col-sm-6 offset-sm-3">
                    <button type="button" class="form-confirm btn btn-primary btn-lg">
                        Alterar Conta
                    </button>
                </div>
            </div>
        </div>
    @else
        <div class="row align-items-center pt-4">
            <div class="col">
                <button type="button" class="form-confirm btn btn-primary">
                    Confirmar
                </button>
            </div>
            <div class="col text-end">
                <button type="button" class="form-cancel btn btn-secondary">
                    Cancelar
                </button>
            </div>
        </div>
    @endif

</form>
