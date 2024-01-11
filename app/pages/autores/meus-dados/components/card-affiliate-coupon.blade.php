@if ($affiliate_coupon)
    <div class="card">
        <div class="card-body">
            <div class="row mb-3">
                <p>
                    <span class="fas fa-info-circle me-2"></span>
                    Este é o seu cupom de <strong>afiliado</strong>. Com ele, o seu público recebe um
                    <strong>desconto</strong> de {{ $affiliate_coupon->coupon_discount_percent }}% ao submeter um
                    artigo
                    e você recebe uma <strong>comissão</strong> de {{ $affiliate_coupon->coupon_affiliate_percent }}%
                    toda vez que um autor usá-lo para publicar um artigo, seja em suas coletâneas ou em quaisquer
                    outras. O cupom pode ser aplicado em qualquer submissão. Por isso, envie este cupom para todo e
                    qualquer acadêmico do seu círculo de relacionamento, independentemente dessa pessoa ser ou não da
                    sua área de atuação.
                </p>
            </div>

            <div class="row align-items-center">
                <div class="col">
                    <span class="bg-dark text-white btn m-0 h1 px-5 user-select-all" style="cursor: default;">
                        {{ $affiliate_coupon->token }}
                    </span>
                    <button class="btn btn-outline-secondary ms-2 h1 m-0 px-4" type="button" data-bs-toggle="tooltip"
                        title="Copiar para área de transferência"
                        data-copy-to-clipboard="{{ $affiliate_coupon->token }}">
                        <span class="fas fa-clipboard"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif
