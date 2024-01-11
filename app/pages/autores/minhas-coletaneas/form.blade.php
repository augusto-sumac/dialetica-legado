@extend('layouts.autores')

<?php
$options = [
    'title' => 'Minhas Coletâneas / Nova',
    'icon' => 'sitemap',
    'url' => AUTHOR_COLLECTIONS_BASE_URL,
];
?>
@section('content')
    @include('components.content-header', $options)

    @include('components.form-header', ['title' => 'Nova Coletânea'])

    <form id="form-coletaneas" {{ attr_data_id($id) }} action="{{ urlCurrent() }}" class="card tabs" method="POST">

        <div id="tab_cadastro_geral" class="card-body">

            @include('pages.collections.components.form-geral')

            @include('pages.collections.components.form-organizadores')

            <div>
                <div class="form-group row">
                    <div class="col">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch"
                                id="input_accept_publication_rules_articles" name="accept_publication_rules[articles_qty]"
                                value="1"
                                {{ isset($accept_publication_rules['articles_qty']) && $accept_publication_rules['articles_qty'] ? ' checked' : '' }}>
                            <label class="form-check-label" for="input_accept_publication_rules_articles">
                                Estou ciente de que a publicação da coletânea está condicionada a existência de, no mínimo,
                                {{ $minimum_articles_in_collection }} artigos submetidos e efetivamente pagos.
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch"
                                id="input_accept_publication_rules_responsibility"
                                name="accept_publication_rules[responsibility]" value="1"
                                {{ isset($accept_publication_rules['responsibility']) && $accept_publication_rules['responsibility'] ? ' checked' : '' }}>
                            <label class="form-check-label" for="input_accept_publication_rules_responsibility">
                                Estou ciente de que é de minha exclusiva responsabilidade, como organizador da coletânea,
                                fazer com que autores de artigos sintam-se incentivados a submeter artigos diretamente para
                                a coletânea.
                            </label>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="card-body pt-0">

            <div class="form-group mt-0 mb-0">
                <div class="col-sm-6 offset-sm-3">
                    <button type="button" class="form-confirm btn btn-primary btn-lg">
                        Criar Coletânea
                    </button>

                    <a class="btn btn-default ml-4 form-cancel" href="{{ url(AUTHOR_COLLECTIONS_BASE_URL) }}">
                        Cancelar
                    </a>
                </div>
            </div>

        </div>

    </form>

    <div class="card">
        <div class="card-body">
            <h1>
                <span class="fas fa-info-circle text-green-500 me-2"></span> Olha que bacana!
            </h1>

            <hr>

            <p>
                Ao criar uma coletânea você e os autores por você selecionados se tornam <strong>organizadores</strong>.
            </p>
            <p>
                Cada organizador recebe um <strong>cupom</strong> único. Este cupom concede desconto de
                <strong>{{ $coupon_discount_percent }}%</strong>
                na publicação de
                artigos para os autores que aplicarem o cupom.
            </p>
            <p>
                Além do desconto oferecido para aqueles que aplicarem o cupom, o organizador cujo cupom foi aplicado recebe
                uma comissão de <strong>{{ $coupon_affiliate_percent }}%</strong>.
            </p>
            <p>
                Sempre que acumular <strong>R$ {{ $minimum_withdrawal_amount }}</strong> em comissão, o organizador pode
                solicitar o saque. Após
                análise do <strong>gestor</strong> de contas o valor será depositado
                na conta do organizador.
            </p>
        </div>
    </div>
@endsection

@section('css')
    <style>
        #form-coletaneas #tab_cadastro_geral>div+div:not(.form-group) {
            padding-top: 1.5rem;
            margin-top: 1.5rem;
            border-top: 1px solid #eee;
        }
    </style>
@endsection

@section('js')
    <script>
        $(document).ready(function() {

            var form = $('form#form-coletaneas')

            form
                .on('click', '.form-confirm', function(e) {
                    e.preventDefault();
                    e.stopPropagation()

                    submitForm({
                        form: form,
                        onSuccess: function(data) {
                            window.location.href = baseUrl + '/minhas-coletaneas/' + data.item.id +
                                '/detail'
                        }
                    });

                    return false;
                });
        });
    </script>

    @include('pages.collections.components.form-js-module')
@endsection
