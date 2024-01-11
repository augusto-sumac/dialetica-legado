<div class="card-body pt-0">

    <div class="form-group mt-0 mb-0">
        <div class="col-sm-6 offset-sm-3">
            <button type="button" class="form-confirm btn btn-primary btn-lg">
                Confirmar
            </button>

            @if (isset($url))
                <a class="btn btn-default ml-4 form-cancel" href="{{ url($url) }}">
                    Cancelar
                </a>
            @endif

            @if ((!isset($onModalForm) || !$onModalForm) && isset($url))
                @include('components.form-add-new', ['url' => url($url . '/adicionar')])
            @endif
        </div>
    </div>

</div>
