<div class="address-form">
    <input type="hidden" name="address_id" value="{{ $address->id }}" />

    <div class="form-group row">
        <label class="col-sm-3 col-form-label">
            CEP
        </label>
        <div class="col-sm-6 col-md-4 col-xl-3">
            <div class="position-relative">
                <input type="text" class="form-control" name="zip_code" value="{{ $address->zip_code }}">
                <span class="position-absolute top-50 end-0 translate-middle">
                    <span class="spinner-border spinner-border-sm text-secondary" style="display: none;"></span>
                </span>
            </div>
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-3 col-form-label">Rua/Logradouro</label>
        <div class="col-sm-6 col-md-4 col-xl-3">
            <input type="text" class="form-control" name="street" placeholder="Rua/Logradouro"
                value="{{ $address->street }}">
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-3 col-form-label">Número</label>
        <div class="col-sm-6 col-md-4 col-xl-3">
            <input type="text" class="form-control" name="number" placeholder="Número"
                value="{{ $address->number }}">
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-3 col-form-label">Complemento</label>
        <div class="col-sm-6 col-md-4 col-xl-3">
            <input type="text" class="form-control" name="complement" placeholder="Complemento"
                value="{{ $address->complement }}">
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-3 col-form-label">Bairro</label>
        <div class="col-sm-6 col-md-4 col-xl-3">
            <input type="text" class="form-control" name="district" placeholder="Bairro"
                value="{{ $address->district }}">
        </div>
    </div>

    <div class="form-group row">
        <input type="hidden" name="city_ibge_id" value="{{ $address->city_ibge_id }}" />
        <label class="col-sm-3 col-form-label">Cidade</label>
        <div class="col-sm-6 col-md-4 col-xl-3">
            <input type="text" class="form-control" name="city" placeholder="Cidade" value="{{ $address->city }}">
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-3 col-form-label">UF</label>
        <div class="col-sm-6 col-md-4 col-xl-3">
            <select name="state" class="form-control">
                {{ select_options_estados_brasil($address->state) }}
            </select>
        </div>
    </div>
</div>
