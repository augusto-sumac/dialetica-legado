<div id="scope_geral" v-scope v-cloak @vue:mounted="onMounted">
    <div class="form-group row">
        <div class="col-sm-6">
            <h3>GERAL</h3>
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-3 col-form-label">Coletânea</label>
        <div class="col-sm-6">
            <input type="text" v-model="name" name="name" placeholder="Nome/Título da Coletânea"
                :class="{ 'form-control': true, 'bg-gray-100': readOnly }" :disabled="readOnly">
        </div>
    </div>

    <div class="form-group row" v-show="!isDefaultCollection">
        <label class="col-sm-3 col-form-label">Descrição</label>
        <div class="col-sm-9">
            <textarea rows="3" v-model="description" name="description" placeholder="Descrição da Coletânea"
                :class="{ 'form-control': true, 'bg-gray-100': readOnly }" :disabled="readOnly"></textarea>
        </div>
    </div>

    <div class="form-group row" v-show="!isDefaultCollection">
        <label class="col-sm-3 col-form-label">Área</label>
        <div class="col-sm-6 col-md-4 col-lg-3">
            <select class="form-select" @change="onAreaChange" v-model="area_id" name="area_id" placeholder="Área"
                :disabled="readOnly">
                {{ select_options_areas_conhecimentos() }}
            </select>
        </div>
    </div>

    <div class="form-group row" v-show="!isDefaultCollection">
        <label class="col-sm-3 col-form-label">Sub-Área</label>
        <div class="col-sm-6 col-md-4 col-lg-3">
            <select class="form-select" @change="onSubAreaChange" v-model="subarea_id" name="subarea_id"
                placeholder="Sub-Área" :disabled="readOnly"></select>
        </div>
    </div>

    <div class="form-group row" v-show="!isDefaultCollection">
        <label class="col-sm-3 col-form-label">Especialidade</label>
        <div class="col-sm-6 col-md-4 col-lg-3">
            <select class="form-select" v-model="specialty_id" name="specialty_id" placeholder="Especialidade"
                :disabled="readOnly"></select>
        </div>
    </div>

</div>

@section('js')
    @parent

    <script type="module">
        import {
            createApp
        } from 'https://unpkg.com/petite-vue@0.4.1/dist/petite-vue.es.js?module';

        let filterable_subareas = {{ json_encode(linkable_options_subareas_conhecimentos()) }};
        let filterable_especialidades = {{ json_encode(linkable_options_articles_specialties()) }};

        let name = '{{ isset($name) ? $name : '' }}';
        let description = '{{ isset($description) ? $description : '' }}';
        let area_id = {{ isset($area_id) ? (int) $area_id : 'null' }};
        let subarea_id = {{ isset($subarea_id) ? (int) $subarea_id : 'null' }};
        let specialty_id = {{ isset($specialty_id) ? (int) $specialty_id : 'null' }};
        let status = '{{ isset($status) ? $status : 'PE' }}';
        let id = {{ isset($id) ? (int) $id : null }};

        const refreshSelect = (name, value) => {
            $('select[name="' + name + '"]').val(value || '');
            let picker = $('select[name="' + name + '"]').data("selectpicker");
            picker && picker.refresh();
        }

        createApp({
            name,
            description,
            area_id,
            subarea_id,
            specialty_id,

            get isDefaultCollection() {
                return id === 1;
            },

            get readOnly() {
                return !['PE', 'AC'].includes(status) || this.isDefaultCollection;
            },

            onAreaChange(subarea_id) {
                let _this = this;
                let selected_area = parseInt(_this.area_id);
                let items = filterable_subareas.filter((item) => selected_area === parseInt(item.area_group_value));

                let htmlOptions = items.map(function(item) {
                    let options = item.items.map(function(option) {
                        return '<option value="' + option.value + '">' + option.label + '</option>';
                    });
                    return '<optgroup label="' + item.area_group_label + '">' + options.join('') +
                        '</optgroup>';
                }).join('');

                this.subarea_id = subarea_id || null;
                this.specialty_id = null;

                $('select[name="subarea_id"]').html(htmlOptions);
                $('select[name="specialty_id"]').empty();

                refreshSelect('subarea_id', subarea_id);
                refreshSelect('specialty_id');
            },

            onSubAreaChange(specialty_id) {
                let _this = this;
                let selected_area = parseInt(_this.area_id);
                let selected_sub_area = parseInt(_this.subarea_id);

                var items = filterable_especialidades
                    .filter((item) => selected_area === parseInt(item.area_group_value))
                    .filter((item) => selected_sub_area === parseInt(item.subarea_group_value));

                var htmlOptions = items.map(function(item) {
                    var options = item.items.map(function(option) {
                        return '<option value="' + option.value + '">' + option.label + '</option>';
                    });
                    return '<optgroup label="' + item.subarea_group_label + '">' + options.join('') +
                        '</optgroup>';
                });

                this.specialty_id = specialty_id || null;

                $('select[name="specialty_id"]').html(htmlOptions);

                refreshSelect('specialty_id', specialty_id);
            },

            onMounted() {
                $('select[name="area_id"]').val(area_id);
                this.onAreaChange(subarea_id);
                this.onSubAreaChange(specialty_id);
            }
        }).mount('#scope_geral')
    </script>
@endsection
