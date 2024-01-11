<form id="article-area" action="{{ url(AUTHOR_ARTICLES_BASE_URL) }}/{{ $key }}" method="POST">
    <input type="hidden" name="id" value="{{ $id }}" />

    <div class="form-group row">
        <label class="col-form-label pt-0">Área</label>
        <div class="col-12 col-md-8 col-lg-6 col-xl-4">
            <select class="form-select" name="area_id" placeholder="Área"
                data-value="{{ isset($area_id) ? $area_id : null }}">
                {{ select_options_areas_conhecimentos(isset($area_id) ? $area_id : null) }}
            </select>
        </div>
    </div>

    <div class="form-group row">
        <label class="col-form-label pt-0">Sub-Área</label>
        <div class="col-12 col-md-8 col-lg-6 col-xl-4">
            <select class="form-select" name="subarea_id" placeholder="Sub-Área"
                data-value="{{ isset($subarea_id) ? $subarea_id : null }}"></select>
        </div>
    </div>

    <div class="form-group row">
        <label class="col-form-label pt-0">Especialidade</label>
        <div class="col-12 col-md-8 col-lg-6 col-xl-4">
            <select class="form-select" name="specialty_id" placeholder="Especialidade"
                data-value="{{ isset($specialty_id) ? $specialty_id : null }}"></select>
        </div>
    </div>
</form>
