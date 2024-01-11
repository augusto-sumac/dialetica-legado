<div id="scope_geral" v-scope v-cloak @vue:mounted="mounted">
    <div class="form-group row">
        <div class="col-sm-6">
            <h3>GERAL</h3>
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-3 col-form-label">Coletânea</label>
        <div class="col-sm-6">
            <input type="text" v-model="store.name" name="name" placeholder="Nome/Título da Coletânea"
                :class="{ 'form-control': true, 'bg-gray-100': store.readOnly }" :disabled="store.readOnly">
        </div>
    </div>

    <div class="form-group row" v-show="store.collection_url">
        <label class="col-sm-3 col-form-label">Url Coletânea</label>
        <div class="col-sm-9">
            <div class="input-group">
                <input type="text" class="form-control bg-gray-200" :value="store.collection_url" readonly disabled>
                <button class="btn btn-secondary" type="button" title="Copiar para área de transferência"
                    @click="copyCollectionUrl">
                    <span class="fas fa-clipboard"></span>
                </button>
            </div>
        </div>
    </div>

    <div class="form-group row" v-show="!store.isDefaultCollection">
        <label class="col-sm-3 col-form-label">Descrição</label>
        <div class="col-sm-9">
            <textarea rows="3" v-model="store.description" name="description" placeholder="Descrição da Coletânea"
                :class="{ 'form-control': true, 'bg-gray-100': store.readOnly }" :disabled="store.readOnly"></textarea>
            <div class="position-relative">
                <span class="badge position-absolute top-0 end-0 translate-middle-y me-2"
                    :class="desc_color">${desc_count}}/${desc_limit}}</span>
            </div>
        </div>
    </div>

    <div class="form-group row" v-show="store.isAdminView">
        <label class="col-sm-3 col-form-label">Imagem Capa</label>
        <div class="col-sm-9">
            <input type="hidden" name="cover_image" v-model="store.cover_image">
            <div class="position-relative collection-cover w-200px">
                <div :style="store.cover_image_backgroud" class="collection-cover-thumbnail img-thumbnail"></div>
                <div class="collection-cover-buttons position-absolute top-50 start-50 translate-middle">
                    <button type="button" class="btn btn-success" title="Download" v-if="store.is_valid_cover_image"
                        @click="store.downloadCoverImage">
                        <span class="fas fa-download"></span>
                    </button>
                    <button type="button" class="btn btn-dark" title="Ampliar" @click="store.zoomCoverImage">
                        <span class="fas fa-expand-arrows-alt"></span>
                    </button>
                    <label for="select_cover_image" class="btn btn-primary position-relative" title="Selecionar Imagem">
                        <span class="fas fa-upload"></span>
                        <input type="file" id="select_cover_image"
                            style="position: absolute; top: 0; left: -10000px;" @change="previewCoverImage"
                            accept="image/png, image/gif, image/jpeg" />
                    </label>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group row" v-show="!store.isDefaultCollection">
        <label class="col-sm-3 col-form-label">Área</label>
        <div class="col-sm-6 col-md-4 col-lg-3">
            <select class="form-select" @change="onAreaChange()" v-model.number="store.area_id" name="area_id"
                placeholder="Área" :disabled="store.readOnly">
                {{ select_options_areas_conhecimentos() }}
            </select>
        </div>
    </div>

    <div class="form-group row" v-show="!store.isDefaultCollection">
        <label class="col-sm-3 col-form-label">Sub-Área</label>
        <div class="col-sm-6 col-md-4 col-lg-3">
            <select class="form-select" @change="onSubAreaChange()" v-model.number="store.subarea_id" name="subarea_id"
                placeholder="Sub-Área" :disabled="store.readOnly"></select>
        </div>
    </div>

    <div class="form-group row" v-show="!store.isDefaultCollection">
        <label class="col-sm-3 col-form-label">Especialidade</label>
        <div class="col-sm-6 col-md-4 col-lg-3">
            <select class="form-select" v-model.number="store.specialty_id" name="specialty_id"
                placeholder="Especialidade" :disabled="store.readOnly"></select>
        </div>
    </div>

</div>

@section('js')
    @parent

    <script type="module">
        import {
            createApp
        } from 'https://unpkg.com/petite-vue@0.4.1/dist/petite-vue.es.js?module';

        const refreshSelect = (name, value) => {
            $('select[name="' + name + '"]').val(value || '');
            let picker = $('select[name="' + name + '"]').data("selectpicker");
            picker && picker.refresh();
        }

        const makeSelectOptions = (items, group) => {
            return items.map(function(item, k) {
                let options = item.items.map(({
                        value,
                        label,
                        style
                    }) => `<option value="${value}" style="${style?style:''}">${label}</option>`)
                    .join('');

                return `<optgroup id="opt_group_${group}_${k}" label="${item[group]}">${options}</optgroup>`;
            }).join('');
        }

        const store = window.{{ $app_store_id }};

        createApp({
            store,

            desc_limit: 2000,

            get desc_count() {
                return (this.store.description+'').length;
            },

            get desc_color() {
                return this.desc_count >= this.desc_limit ? 'bg-danger' : 'bg-info';
            },

            onAreaChange(subarea_id) {
                let selected_area = parseInt(this.store.area_id);
                let items = filterable_subareas.filter((item) => selected_area === parseInt(item.area_group_value));

                this.store.subarea_id = subarea_id || null;
                $('select[name="subarea_id"]').empty().html(
                    makeSelectOptions(items, 'area_group_label')
                );
                refreshSelect('subarea_id', subarea_id);

                if (!subarea_id) {
                    this.store.specialty_id = null;
                    $('select[name="specialty_id"]').empty();
                    refreshSelect('specialty_id');
                }
            },

            onSubAreaChange(specialty_id) {
                let selected_area = parseInt(this.store.area_id);
                let selected_sub_area = parseInt(this.store.subarea_id);

                var items = filterable_especialidades
                    .filter((item) => selected_area === parseInt(item.area_group_value))
                    .filter((item) => selected_sub_area === parseInt(item.subarea_group_value));

                items = items.map(item => {
                    let first_item = JSON.parse(JSON.stringify(item.items[0]));
                    first_item.label = 'Não se aplica';
                    first_item.value = 1;
                    first_item.style = 'color: red; font-weigth: bold;';
                    item.items.unshift(first_item)
                    return item;
                });

                this.store.specialty_id = specialty_id || null;
                $('select[name="specialty_id"]').html(
                    makeSelectOptions(items, 'subarea_group_label')
                );
                refreshSelect('specialty_id', specialty_id);
            },

            copyCollectionUrl() {
                try {
                    navigator.clipboard.writeText(store.collection_url);
                } catch (e) {}

                Toast.fire({
                    icon: 'success',
                    title: 'URL copiada para sua área de transferência'
                });
            },

            previewCoverImage(event) {
                if(event?.target?.files.length > 0){
                    let _this = this;
                    let fileReader = new FileReader();
                    fileReader.readAsDataURL(event.target.files[0]);            
                    fileReader.onload = (event) => {
                        _this.store.cover_image = event.target.result;
                        _this.uploadCoverImage(event.target.result);
                    }
                }
            },

            uploadCoverImage(base64) {
                $.put('/collections/cover-image', {
                    cover_image: this.store.cover_image,
                    id: this.store.id || null
                })
                .done(({cover_image}) => {
                    this.store.cover_image = cover_image
                });
            },

            mounted() {
                $('select[name="area_id"]').val(this.store.area_id);
                this.onAreaChange(this.store.subarea_id);
                this.onSubAreaChange(this.store.specialty_id);
            },
        }).mount('#scope_geral');

        let filterable_subareas = {{ json_encode(linkable_options_subareas_conhecimentos()) }};
        let filterable_especialidades = {{ json_encode(linkable_options_articles_specialties()) }};
    </script>
@endsection
