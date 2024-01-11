@extend('layouts.sistema')

<?php
$options = [
    'title' => 'Configurações',
    'icon' => 'cogs',
];
?>

@section('content')
    @set('options', $options)
    @include('components.content-header', $options)

    <div class="card tabs">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs">
                <li class="nav-item">
                    <a class="nav-link active" href="#system">SISTEMA</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#costs">CUSTO SERVIÇOS</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#affiliates">AFILIADOS/CUPONS</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#collections">COLETÂNEAS</a>
                </li>
            </ul>
        </div>

        <form action="{{ url(SETTINGS_BASE_URL . '/system') }}" method="POST" id="tab_system" class="card-body">

            <div class="mb-5">
                <label class="form-label">Sistema em manutenção?</label>
                <div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="maintenance_mode" id="maintenance_mode_true"
                            value="1" {{ $maintenance_mode === 1 ? 'checked' : '' }} />
                        <label class="form-check-label" for="maintenance_mode_true">Sim</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="maintenance_mode" id="maintenance_mode_false"
                            value="0" {{ $maintenance_mode === 0 ? 'checked' : '' }} />
                        <label class="form-check-label" for="maintenance_mode_false">Não</label>
                    </div>
                </div>

                <div class="form-text mt-2">Ao selecionar "Sim" os autores não conseguirão acesso a plataforma</div>
            </div>

            <div>
                <button type="button" class="form-confirm btn btn-primary btn-lg">
                    <span class="fas fa-save me-3"></span> Salvar
                </button>
            </div>

        </form>


        <form action="{{ url(SETTINGS_BASE_URL . '/articles-types') }}" method="POST" id="tab_costs" class="card-body">

            <div class="mb-5">
                <label class="form-label">Publicar Artigo</label>
                <input type="text" class="form-control w-150px money-br" name="article_price[1]"
                    value="{{ toMoney(array_get($article_types, '1', 0)) }}">
                <div class="form-text">Valor de custo para o autor publicar um artigo</div>
            </div>

            <div class="mb-5">
                <label class="form-label">Solicitar Revisão</label>
                <input type="text" class="form-control w-150px money-br" name="article_price[3]"
                    value="{{ toMoney(array_get($article_types, '3', 0)) }}">
                <div class="form-text">Valor de custo por palavra para revisão de artigo</div>
            </div>

            <div>
                <button type="button" class="form-confirm btn btn-primary btn-lg">
                    <span class="fas fa-save me-3"></span> Salvar
                </button>
            </div>

        </form>

        <form action="{{ url(SETTINGS_BASE_URL . '/affiliates') }}" method="POST" id="tab_affiliates" class="card-body">

            <div class="mb-5">
                <label class="form-label">Valor mínimo para saque</label>
                <input type="text" class="form-control w-150px money-br" name="minimum_withdrawal_amount"
                    value="{{ toMoney($minimum_withdrawal_amount) }}">
                <div class="form-text">Valor mínimo para solicitar saque</div>
            </div>

            <div class="mb-5">
                <label class="form-label">% Desc Cupons</label>
                <input type="text" class="form-control w-150px money-br" name="coupon_discount_percent"
                    value="{{ toMoney($coupon_discount_percent) }}">
                <div class="form-text">Percentual de desconto aplicado aos artigos</div>
            </div>

            <div class="mb-5">
                <label class="form-label">% Comissão Cupons</label>
                <input type="text" class="form-control w-150px money-br" name="coupon_affiliate_percent"
                    value="{{ toMoney($coupon_affiliate_percent) }}">
                <div class="form-text">Percentual de comissão concedido aos organizadores</div>
            </div>

            <div>
                <button type="button" class="form-confirm btn btn-primary btn-lg">
                    <span class="fas fa-save me-3"></span> Salvar
                </button>
            </div>

        </form>

        <form action="{{ url(SETTINGS_BASE_URL . '/collections') }}" method="POST" id="tab_collections"
            class="card-body">
            <div class="mb-5">
                <label class="form-label">Prazo para envio de artigos</label>
                <input type="text" class="form-control w-150px quantidade" name="collection_days_limit"
                    value="{{ $collection_days_limit }}">
                <div class="form-text">Número de dias corridos desde o cadastro da coletânea na qual ela fica disponível
                    para envio de artigos</div>
            </div>

            <div class="mb-5">
                <label class="form-label">Artigos para publicar coletânea</label>
                <input type="text" class="form-control w-150px quantidade" name="minimum_articles_in_collection"
                    value="{{ $minimum_articles_in_collection }}">
                <div class="form-text">Quantidade mínima de artigos pagos/válidos para que seja possível publicar uma
                    coletânea</div>
            </div>

            <div class="mb-5">
                <label class="form-label">Prazo aprovação coletânea</label>
                <input type="text" class="form-control w-150px quantidade" name="days_approve_collection"
                    value="{{ $days_approve_collection }}">
                <div class="form-text">Quantidade de dias corridos para que os ADMs aprovem uma coletânea, após esse prazo
                    ela é automaticamente aprovada</div>
            </div>

            <div class="mb-5">
                <label class="form-label">Prazo aprovação de artigos</label>
                <input type="text" class="form-control w-150px quantidade" name="days_approve_article"
                    value="{{ $days_approve_article }}">
                <div class="form-text">Quantidade de dias corridos para que os Organizadores aprovem os artigos envidaos
                    para a coletânea, após esse prazo são automaticamente aprovados</div>
            </div>

            <div>
                <button type="button" class="form-confirm btn btn-primary btn-lg">
                    <span class="fas fa-save me-3"></span> Salvar
                </button>
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

                    submitForm({
                        form: $(this).closest('form'),
                        onSuccess: function() {}
                    });

                    return false;
                })
        });
    </script>
@endsection
