@extend('layouts.autores')

@section('content')
    <div class="content-header">
        <div class="float-end">
            <button type="button" title="Atualizar dados da conta" class="btn btn-dark btn-bank-account">
                <span class="fas fa-info-circle pe-2"></span>
                <span>Minha Conta</span>
            </button>
        </div>
        <h4>
            <span class="fas fa-atlas me-3"></span>
            Minhas Comissões
        </h4>
    </div>

    <hr>

    <div class="card">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col">
                    <div class="text-darnk">Saldo Disponível</div>
                    <div class="h1 m-0 text-dark fw-bold">
                        <abbr data-bs-toggle="tooltip" title="Saldo disponível para saque" class="total_available_cashout">
                            R$ {{ toMoney($available_total) }}
                        </abbr>
                    </div>
                </div>
                <div class="col-auto cash_out_available {{ !$cash_out_available ? 'd-none' : '' }}">
                    <button type="button" class="btn btn-primary btn-lg btn-request-cashout">
                        <span class="fas fa-dollar-sign me-2"></span>
                        Solicitar Saque
                    </button>
                </div>
                <div class="col-auto">
                    <button type="button" class="btn btn-secondary btn-lg btn-reload-page-props">
                        <span class="fas fa-sync-alt"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    @include('components.datagrid', $datagrid)
@endsection

@section('css')
@endsection

@section('js')
    <script>
        var total_available_cashout = {{ $available_total }};

        function reloadDataTable() {
            $('.datagrid.use-datatable')
                .find('table[id*="DataTables_Table"]')
                .dataTable().fnFilter('');
        }

        function applyPageProps(data) {
            var fn = data.cash_out_available ? 'removeClass' : 'addClass';
            $('.cash_out_available')[fn]('d-none');

            total_available_cashout = data.available_total
            $('.total_available_cashout').html(
                'R$ ' + toMoneyBr(total_available_cashout)
            );

            reloadDataTable();

            pagePropsReloadIntervalFn();
        }

        var pagePropsReloadInterval;

        function pagePropsReloadIntervalFn() {
            clearInterval(pagePropsReloadInterval);

            pagePropsReloadInterval = setInterval(function() {
                $('.btn-reload-page-props').click();
            }, 5 * 60 * 1000);
        }

        var bank_account = {
            account_document: '{{ $account_document ?? '' }}',
            account_name: '{{ $account_name ?? '' }}',
            account_pix_type: '{{ $account_pix_type ?? '' }}',
            account_pix_key: '{{ $account_pix_key ?? '' }}',
        };

        function validateBankAccount() {
            for (let key of Object.keys(bank_account)) {
                if (!bank_account[key] || String(bank_account[key] + '').length === 0) {
                    return false
                }
            }

            return true;
        }

        function requestBankAccountConfirm(onOk = () => {}) {
            $.get('/meus-dados/minha-conta', {
                ...bank_account,
                in_modal: true
            }, (html) => {
                Swal.fire({
                    html,
                    allowEscapeKey: false,
                    allowOutsideClick: false,
                    showCancelButton: false,
                    showConfirmButton: false,
                    didOpen: function() {
                        let form = $('form#minha-conta');

                        form
                            .on('click', '.form-cancel', function() {
                                Swal.close();
                            })
                            .on('click', '.form-confirm', function(e) {
                                e.preventDefault();
                                e.stopPropagation();

                                submitForm({
                                    form,
                                    skipNotificationError: true,
                                    onSuccess(data) {
                                        if (data.bank_account) {
                                            bank_account = data.bank_account;
                                        }
                                        Swal.close();
                                        onOk && onOk();
                                    }
                                });

                                return false;
                            })
                    },
                });
            });
        }

        $(document).ready(function() {

            $(document)
                .on('click', '.btn-reload-page-props', function() {
                    $.get(baseUrl + '/minhas-comissoes/check-available')
                        .done(function(data) {
                            applyPageProps(data)
                        });
                })
                .on('click', '.btn-bank-account', () => requestBankAccountConfirm())
                .on('click', '.btn-request-cashout', function() {
                    if (!validateBankAccount()) {
                        requestBankAccountConfirm(
                            () => $('.btn-request-cashout').click()
                        );
                        return;
                    }

                    Swal.fire({
                            icon: 'question',
                            title: 'Saque de R$ ' + toMoneyBr(total_available_cashout),
                            text: `Confirma a solicitação de saque do valor total disponível?`,
                            showCancelButton: true,
                            confirmButtonText: "Sim",
                            cancelButtonText: "Não",
                            allowEscapeKey: false,
                            allowOutsideClick: false,
                        })
                        .then(function(result) {
                            if (result.isConfirmed) {
                                $.post(baseUrl + '/minhas-comissoes/saque')
                                    .done(function(data) {
                                        Toast.fire({
                                            icon: 'success',
                                            title: 'Saque solicitado com sucesso'
                                        });

                                        applyPageProps(data);
                                    })
                                    .fail(function() {
                                        Toast.fire({
                                            icon: 'error',
                                            title: 'Falha ao solicitar o saque! Tente mais tarde'
                                        });
                                    });
                            }
                        });;
                });

            pagePropsReloadIntervalFn();
        });
    </script>
@endsection
