@extend('layouts.sistema')

@section('content')
    <div class="content-header">
        <div class="float-end">
            <a href="javascript:void(0)" title="Novo cadastro" class="btn btn-sm btn-primary" onclick="$appCoupon.add()">
                <span class="fas fa-plus"></span>
                <span>Adicionar</span>
            </a>
        </div>
        <h4>
            <span class="fas fa-address-card me-3"></span>
            Afiliados / Cupons
        </h4>
    </div>

    <hr>

    @include('components.datagrid', $datagrid)

    @include('pages.sistema.afiliados.cupons.components.modal-form')
@endsection

@section('css')
    <style>
        .modal-withdraw-detail {
            padding: 0 0 5px;
        }

        .modal-withdraw-detail .swal2-html-container {
            margin: 0;
            padding: 0;
            text-align: inherit;
        }

        .modal-withdraw-detail .swal2-title {
            display: none;
        }

        .modal-withdraw-detail .alert-status {
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0;
            margin: 0;
        }

        .modal-withdraw-detail .card-body .form-group:last-child {
            margin: 0;
        }

        #paymentModalForm .alert-status {
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0;
            margin: 0;
        }

        #paymentModalForm .form-group:last-child {
            margin: 0;
        }
    </style>
@endsection

@section('js')
    <script></script>
@endsection
