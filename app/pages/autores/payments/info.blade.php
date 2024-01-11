@if ($isPix)
    <div class="p-4 pb-0 border-top">
        <p class="mb-4">Utilize o QrCode para realizar o pagamento.</p>
        <p class="text-center mb-4">
            <span class="border p-1 rounded shadow d-inline-block">
                <img src="data:image/jpeg;base64,{{ $qrCode }}" style="width: 200px; height: 200px"
                    class="d-inline-block" />
            </span>
        </p>
        @if ($paymentStatus)
            <p>
                <a class="btn btn-success" href="{{ url(AUTHOR_PAYMENTS_BASE_URL . '/' . $articleId) }}">
                    Gerar Novo QrCode <span class="ms-1 fas fa-arrow-right"></span>
                </a>
            </p>
        @endif
    </div>
    <div class="text-start row g-0 align-items-center border-top border-bottom p-4">
        <div class="col-auto">
            <span class="fas fa-info-circle fa-2x me-4 text-warning"></span>
        </div>
        <div class="col">
            A confirmação pode levar até duas horas após a realização do pagamento!
        </div>
    </div>
@elseif ($isBillet)
    <div class="p-4 pb-0 border-top">
        <p class="mb-4">Clique no botão abaixo baixar o boleto de pagamento.</p>
        <p class="mb-4">
            <a href="{{ $url }}" target="_blank" title="Link boleto" class="btn btn-dark px-4">
                Clique para baixar
            </a>
        </p>
        <p class="mb-4">Ou utilize o a linha digitável abaixo</p>
        <div class="input-group mb-4">
            <input type="text" class="form-control" value="{{ $barcode }}">
            <button class="btn btn-outline-secondary" type="button" data-bs-toggle="tooltip"
                title="Copiar para área de transferência" data-copy-to-clipboard="{{ $barcode }}"
                data-alert="true">
                <span class="fas fa-clipboard"></span>
            </button>
        </div>
        @if ($paymentStatus)
            <p>
                <a class="btn btn-success" href="{{ url(AUTHOR_PAYMENTS_BASE_URL . '/' . $articleId) }}">
                    Gerar Novo Boleto <span class="ms-1 fas fa-arrow-right"></span>
                </a>
            </p>
        @endif
    </div>
    <div class="text-start row g-0 align-items-center border-top border-bottom p-4">
        <div class="col-auto">
            <span class="fas fa-info-circle fa-2x me-4 text-warning"></span>
        </div>
        <div class="col">
            A confirmação pode levar até dois dias úteis após a realização do pagamento!
        </div>
    </div>
@endif
