@extend('layouts.sistema')

<?php
$options = [
    'title' => 'Especialidades',
    'icon' => 'level-up-alt fa-rotate-90',
];
?>
@section('content')
    @include('components.content-header', $options)

    @include('components.form-header', ['title' => 'Especialidades'])

    <form id="sub-areas-conhecimento" {{ attr_data_id($id) }} action="{{ urlCurrent() }}" class="card tabs"
        method="POST">
        <div class="card-body">

            <div class="form-group row">
                <label class="col-sm-3 col-form-label">Área</label>
                <div class="col-sm-4">
                    <select class="form-select" name="area_id" placeholder="Área">
                        {{ select_options_areas_conhecimentos(isset($area_id) ? $area_id : null) }}
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-3 col-form-label">Subárea</label>
                <div class="col-sm-4">
                    <select class="form-select" name="subarea_id" placeholder="Subárea"
                        data-value="{{ isset($subarea_id) ? $subarea_id : null }}">
                        {{ select_options_subareas_conhecimentos(isset($subarea_id) ? $subarea_id : null) }}
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-3 col-form-label">Especialidade</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" name="name" placeholder="Especialidade"
                        value="{{ isset($name) ? $name : '' }}">
                </div>
            </div>

        </div>

        <?php
        $url = ARTICLES_SPECIALTIES_BASE_URL;
        ?>

        @include('components.form-card-buttons', ['url' => $url])

    </form>
@endsection

<?php
$filterable_subareas = linkable_options_subareas_conhecimentos();
?>

@section('js')
    <script>
        var filterable_subareas = {{ json_encode($filterable_subareas) }};

        function make_subareas_options(selected) {
            var selected_area = [$('select[name="area"]').val()].map(_ => parseInt(_));
            var items = filterable_subareas.filter(function(item) {
                return selected_area.includes(parseInt(item.area_group_value));
            });

            var html = items.map(function(item) {
                var options = item.items.map(function(option) {
                    return '<option value="' + option.value + '">' + option.label + '</option>';
                });
                return '<optgroup label="' + item.area_group_label + '">' + options.join('') + '</optgroup>';
            });

            $('select[name="subarea"]').html(
                html.join('')
            ).val(selected).selectpicker('refresh');
        }

        $(document).ready(function() {
            make_subareas_options({{ isset($subarea) ? $subarea : '' }});

            $(document)
                .on('change', 'select[name="area"]', function() {
                    make_subareas_options('');
                });

            var form = $('form#sub-areas-conhecimento');

            form
                .on('click', '.form-confirm', function(e) {
                    e.preventDefault();
                    e.stopPropagation()

                    submitForm({
                        form: form,
                        onSuccess: function(data) {
                            form
                                .attr('action', baseUrl + '/especialidades/' + data.item.id +
                                    '/editar')
                                .data('id', data.item.id);

                            $(".form-header").text('#' + (data.item.id + '').padStart(5, '0'));

                            $('.add-new').show();
                        }
                    });

                    return false;
                })
        });
    </script>
@endsection
