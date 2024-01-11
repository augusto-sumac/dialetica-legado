@extend('layouts.sistema')

<?php
$options = [
    'title' => 'Coletâneas',
    'icon' => 'level-up-alt fa-rotate-90',
    'url' => ARTICLES_COLLECTIONS_BASE_URL,
];
?>
@section('content')
    @include('components.content-header', $options)

    @include('components.form-header', ['title' => 'Coletâneas'])

    <form id="form-coletaneas" {{ attr_data_id($id) }} action="{{ urlCurrent() }}" method="POST" v-scope v-cloak>

        @include('pages.sistema.coletaneas.components.form-geral')

        <div class="card tabs">
            <div class="card-body">

                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Coletânea</label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control {{ $id === 1 ? 'bg-gray-100' : '' }}" name="name"
                            placeholder="Nome/Título da Coletânea" value="{{ isset($name) ? $name : '' }}"
                            {{ $id === 1 ? 'readonly' : '' }}>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Descrição</label>
                    <div class="col-sm-9">
                        <textarea rows="3" class="form-control {{ $id === 1 ? 'bg-gray-100' : '' }}" name="description"
                            placeholder="Descrição da Coletânea" {{ $id === 1 ? 'readonly' : '' }}>{{ isset($description) ? $description : '' }}</textarea>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Área</label>
                    <div class="col-sm-6 col-md-4 col-lg-3">
                        <select class="form-select {{ $id === 1 ? 'bg-gray-100' : '' }}" name="area_id" placeholder="Área"
                            {{ $id === 1 ? 'disabled readonly' : '' }}>
                            {{ select_options_areas_conhecimentos(isset($area_id) ? $area_id : null) }}
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Sub-Área</label>
                    <div class="col-sm-6 col-md-4 col-lg-3">
                        <select class="form-select {{ $id === 1 ? 'bg-gray-100' : '' }}" name="subarea_id"
                            placeholder="Sub-Área" data-value="{{ isset($subarea_id) ? $subarea_id : null }}"
                            {{ $id === 1 ? 'disabled readonly' : '' }}></select>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Especialidade</label>
                    <div class="col-sm-6 col-md-4 col-lg-3">
                        <select class="form-select {{ $id === 1 ? 'bg-gray-100' : '' }}" name="specialty_id"
                            placeholder="Especialidade" data-value="{{ isset($specialty_id) ? $specialty_id : null }}"
                            {{ $id === 1 ? 'disabled readonly' : '' }}></select>
                    </div>
                </div>

                <div class="form-group row" v-if="collection.author_id">
                    <label class="col-sm-3 col-form-label">Organizadores</label>
                    <div class="col-sm-6 col-md-4 col-lg-3">
                        <div v-for="(item, k) in collection.authors" :key="k">
                            <a :href="authorUrl(item)" target="author">
                                <span class="fas fa-external-link-alt me-2"></span>
                                ${ item.name }}
                            </a>
                        </div>
                    </div>
                </div>

            </div>

            <div class="card-body pt-0" v-if="collection.author_id">

                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Status</label>
                    <div class="col-sm-6 col-md-4 col-lg-3">
                        <span class="py-2 px-3 text-uppercase rounded border"
                            :class="`border-${collection.status_badge.color} text-${collection.status_badge.color}`">
                            ${collection.status_badge.label}}
                        </span>
                    </div>
                </div>


                <div class="form-group row mt-0 mb-0" v-if="validStatusActions">
                    <label class="col-sm-3 col-form-label">&nbsp;</label>
                    <div class="col-sm-9">

                        <button type="button" class="btn-approve-collection btn btn-success btn-lg me-3" v-if="isPending"
                            @click="approve">
                            <span class="fas fa-thumbs-up me-2"></span> Aprovar
                        </button>

                        <button type="button" class="btn-reject-collection btn btn-danger" v-if="isPending"
                            @click="reject">
                            <span class="fas fa-thumbs-down me-2"></span> Rejeitar
                        </button>

                        <button type="button" class="btn-publich-collection btn btn-success btn-lg me-3"
                            v-if="isPubishable" @click="publish">
                            <span class="fas fa-thumbs-up me-2"></span> Publicar Coletânea
                        </button>

                        <button type="button" class="btn-finish-collection btn btn-dark btn-lg me-3" v-if="isFinishable"
                            @click="finish">
                            <span class="fas fa-thumbs-up me-2"></span> Finalizar Coletânea
                        </button>

                    </div>
                </div>

            </div>
        </div>

        <div class="card tabs">
            <div class="card-body">

                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">ISBN - Livro Físico</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" name="isbn" placeholder="ISBN - Livro Físico"
                            value="{{ isset($isbn) ? $isbn : '' }}">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">ISBN - E-Book</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" name="isbn_e_book" placeholder="ISBN - E-Book"
                            value="{{ isset($isbn_e_book) ? $isbn_e_book : '' }}">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">DOI</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" name="doi" placeholder="DOI da Coletânea"
                            value="{{ isset($doi) ? $doi : '' }}">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Link Livro</label>
                    <div class="col-sm-7">
                        <input type="text" class="form-control" name="book_url" placeholder="Link dol ivro"
                            value="{{ isset($book_url) ? $book_url : '' }}">
                    </div>
                </div>

            </div>

            @include('components.form-card-buttons', ['url' => $options['url'], 'onModalForm' => true])

        </div>

    </form>

    @if (isset($articles))
        {{ $articles }}
    @endif
@endsection

<?php
$filterable_subareas = linkable_options_subareas_conhecimentos();
$filterable_especialidades = linkable_options_articles_specialties();
?>

@section('js')
    <script>
        var filterable_subareas = {{ json_encode($filterable_subareas) }};
        var filterable_especialidades = {{ json_encode($filterable_especialidades) }};

        function make_subareas_options() {
            var selected_area = [$('select[name="area_id"]').val(), $('select[name="area_id"]').data('value')].map(_ =>
                parseInt(_));
            var items = filterable_subareas.filter(function(item) {
                return selected_area.includes(parseInt(item.area_group_value));
            });

            var html = items.map(function(item) {
                var options = item.items.map(function(option) {
                    return '<option value="' + option.value + '">' + option.label + '</option>';
                });
                return '<optgroup label="' + item.area_group_label + '">' + options.join('') + '</optgroup>';
            });

            $('select[name="subarea_id"]').html(
                html.join('')
            ).val('').selectpicker('refresh');

            $('select[name="subarea_id"]').val(
                $('select[name="subarea_id"]').data('value')
            ).data("selectpicker").refresh();
        }

        function make_especialidades_options() {
            var selected_area = [$('select[name="area_id"]').val(), $('select[name="area_id"]').data('value')].map(_ =>
                parseInt(_));
            var selected_sub_area = [$('select[name="subarea_id"]').val(), $('select[name="subarea_id"]').data('value')]
                .map(_ => parseInt(_));
            var items = filterable_especialidades
                .filter(function(item) {
                    return selected_area.includes(parseInt(item.area_group_value));
                })
                .filter(function(item) {
                    return selected_sub_area.includes(parseInt(item.subarea_group_value));
                });

            var html = items.map(function(item) {
                var options = item.items.map(function(option) {
                    return '<option value="' + option.value + '">' + option.label + '</option>';
                });
                return '<optgroup label="' + item.subarea_group_label + '">' + options.join('') + '</optgroup>';
            });

            $('select[name="specialty_id"]').html(
                html.join('')
            ).val('').selectpicker('refresh');

            $('select[name="specialty_id"]').val(
                $('select[name="specialty_id"]').data('value')
            ).data("selectpicker").refresh();
        }

        $(document).ready(function() {
            make_subareas_options();
            make_especialidades_options();

            var form = $('form#form-coletaneas')

            form
                .on('click', '.form-confirm', function(e) {
                    e.preventDefault();
                    e.stopPropagation()

                    submitForm({
                        form: form,
                        onSuccess: function(data) {
                            form
                                .attr('action', baseUrl + '/coletaneas/' + data.item.id + '/editar')
                                .data('id', data.item.id);

                            $(".form-header").text('#' + (data.item.id + '').padStart(5, '0'));

                            $('.add-new').show();
                        }
                    });

                    return false;
                });

            $(document)
                .on('change', 'select[name="area_id"]', function() {
                    make_subareas_options();
                    make_especialidades_options();
                })
                .on('change', 'select[name="subarea_id"]', function() {
                    make_especialidades_options();
                })
                .on('click', '.btn-approve-collection, .btn-reject-collection', function() {
                    var action = $(this).hasClass('btn-reject-collection') ? 'rejeitar' : 'aprovar';
                    var div = $(this).parent();
                    $.put(baseUrl + '/coletaneas/' + $(this).data('id') + '/situation/' + action)
                        .done(function(data) {
                            console.log(data)
                            div.html(
                                `<span class="py-2 px-3 text-uppercase rounded border border-${data.status.color} text-${data.status.color}">${data.status.label}</span>`
                            )
                        });
                });
        });
    </script>

    <script type="module">
        import {
            createApp
        } from 'https://unpkg.com/petite-vue@0.4.1/dist/petite-vue.es.js?module';

        let collection = {{ json_encode(isset($collection) ? $collection : [], false) }};
        let baseURL = '{{ url('sistema') }}';

        createApp({
            collection,

            baseURL,

            get validStatusActions() {
                return ['PE', 'RP', 'WP'].includes(this.collection.status);
            },

            get isPending() {
                return this.collection.status === 'PE';
            },

            get isPubishable() {
                return this.collection.status === 'RP';
            },

            get isFinishable() {
                return this.collection.status === 'WP';
            },

            authorUrl(item) {
                return this.baseURL + '/autores/' + item.id;
            },

            approve() {
                this.changeStatus('AP')
            },

            reject() {
                this.changeStatus('RE')
            },

            publish() {
                this.changeStatus('WP')
            },

            finish() {
                this.changeStatus('PU')
            },

            changeStatus(status) {
                let _this = this;
                $.put(_this.baseURL + '/coletaneas/' + _this.collection.id + '/change-status', {
                        status
                    })
                    .done(function(data) {
                        console.log(data);
                        _this.collection.status = data.status;
                        _this.collection.status_badge = data.status_badge;
                    })
                    .fail(function() {
                        // ...
                    });
            },

            mounted() {
                console.log(this);
            }
        }).mount('#form-coletaneas')
    </script>
@endsection
