    <input type="hidden" name="address_id" value="{{ isset($address_id) ? $address_id : null }}" />

    <div class="address-form">
        <div class="form-group row">
            <label class="col-sm-3 col-form-label">
                CEP
            </label>
            <div class="col-sm-6 col-md-4 col-xl-3">
                <div class="position-relative">
                    <input type="text" class="form-control" name="zip_code"
                        value="{{ isset($address_zip_code) ? mask($address_zip_code, 'cep') : '' }}">
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
                    value="{{ isset($address_street) ? $address_street : '' }}">
            </div>
        </div>

        <div class="form-group row">
            <label class="col-sm-3 col-form-label">Número</label>
            <div class="col-sm-6 col-md-4 col-xl-3">
                <input type="text" class="form-control" name="number" placeholder="Número"
                    value="{{ isset($address_number) ? $address_number : '' }}">
            </div>
        </div>

        <div class="form-group row">
            <label class="col-sm-3 col-form-label">Complemento</label>
            <div class="col-sm-6 col-md-4 col-xl-3">
                <input type="text" class="form-control" name="complement" placeholder="Complemento"
                    value="{{ isset($address_complement) ? $address_complement : '' }}">
            </div>
        </div>

        <div class="form-group row">
            <label class="col-sm-3 col-form-label">Bairro</label>
            <div class="col-sm-6 col-md-4 col-xl-3">
                <input type="text" class="form-control" name="district" placeholder="Bairro"
                    value="{{ isset($address_district) ? $address_district : '' }}">
            </div>
        </div>

        <div class="form-group row">
            <input type="hidden" name="city_ibge_id"
                value="{{ isset($address_city_ibge_id) ? $address_city_ibge_id : '' }}" />
            <label class="col-sm-3 col-form-label">Cidade</label>
            <div class="col-sm-6 col-md-4 col-xl-3">
                <input type="text" class="form-control" name="city" placeholder="Cidade"
                    value="{{ isset($address_city) ? $address_city : '' }}">
            </div>
        </div>

        <div class="form-group row">
            <label class="col-sm-3 col-form-label">UF</label>
            <div class="col-sm-6 col-md-4 col-xl-3">
                <select name="state" class="form-control">
                    {{ select_options_estados_brasil(isset($address_state) ? $address_state : '') }}
                </select>
            </div>
        </div>
    </div>
