<div class="form-group row">
    <div class="col">
        <div class="position-relative">
            <span class="position-absolute top-50 end-0" style="margin-top: -13px;">
                <a href="#" class="text-muted p-2 rounded-circle bg-light me-2" style="font-size: 10px;"
                    onclick="$('.help-search').toggle(); $(this).toggleClass('bg-orange-300');">
                    <span class="fas fa-fw fa-question"></span>
                </a>
            </span>
            <input type="text" placeholder="Buscar... [ PRESSIONE ENTER ]" class="datagrid-filter form-control"
                name="search[search]" />
        </div>
        <div class="help-search" style="display: none;">
            <small class="form-text m-0">
                A pesquisa é realizada nos campos:
                <br /> - ID e Nome da Coletânea
            </small>
        </div>
    </div>
    <div class="col-auto">
        <button type="button" class="btn btn-light me-2 toggle-filters">
            <span class="fas fa-filter"></span>
        </button>
        <button type="button" class="btn btn-dark me-2 excel-export" title="Exportar para Excel">
            <span class="fas fa-file-excel"></span>
        </button>
        <button type="submit" class="btn btn-secondary">
            <span class="fas fa-sync-alt"></span>
        </button>
    </div>
</div>

<div class="form-group row align-items-end filter-group" style="display: none;">
    <div class="col mb-3 mb-md-0">
        <label class="col-form-label col-form-label-sm mt-0">
            Status
            <a href="javascript:void(0)" class="clear-select-filter">[ limpar ]</a>
        </label>
        <div class="col-12">
            <select class="form-control-sm form-select-sm" name="filters[status][]" placeholder="Status"
                data-multiple="true" data-size="sm">
                {{ select_options_articles_collections_status(null) }}
                <option value="99">99 - Vencidas Sem Artigos</option>
            </select>
        </div>
    </div>
    <div class="col mb-3 mb-md-0">
        <label class="col-form-label col-form-label-sm mt-0">
            Origem
            <a href="javascript:void(0)" class="clear-select-filter">[ limpar ]</a>
        </label>
        <div class="col-12">
            <select class="form-control-sm form-select-sm" name="filters[origin]" placeholder="Origem"
                data-multiple="false" data-size="sm">
                {{ make_select_options([''   => 'Selecione...', 'A' => 'Admin', 'P' => 'Público']) }}
            </select>
        </div>
    </div>
    <div class="col">
        <label class="col-form-label col-form-label-sm mt-0">
            Data Cadastro
            <a href="javascript:void(0)" class="clear-input-filter">[ limpar ]</a>
        </label>
        <div class="col-12">
            <div class="input-group input-group-sm">
                <input type="text" class="form-control campo-data" name="filters[created_at][start]" />
                <span class="input-group-text bg-light">até</span>
                <input type="text" class="form-control campo-data" name="filters[created_at][end]" />
            </div>
        </div>
    </div>
    <div class="col">
        <label class="col-form-label col-form-label-sm mt-0">
            Data Limite
            <a href="javascript:void(0)" class="clear-input-filter">[ limpar ]</a>
        </label>
        <div class="col-12">
            <div class="input-group input-group-sm">
                <input type="text" class="form-control campo-data" name="filters[expires_at][start]" />
                <span class="input-group-text bg-light">até</span>
                <input type="text" class="form-control campo-data" name="filters[expires_at][end]" />
            </div>
        </div>
    </div>
    <div class="col-auto">
        <button type="submit" class="btn btn-primary btn-sm" title="Aplicar filtros">
            <span class="fas fa-check"></span>
        </button>
    </div>
</div>

<?php
$filterable_subareas = linkable_options_subareas_conhecimentos();
$filterable_especialidades = linkable_options_articles_specialties();
?>

@section('js')
    @parent
    <script>
        var filterable_subareas = {{ json_encode($filterable_subareas) }};
        var filterable_especialidades = {{ json_encode($filterable_especialidades) }};

        function make_subareas_options() {
            var selected_area = [$('select[name="filters[area]"]').val()].map(_ => parseInt(_));
            var items = filterable_subareas.filter(function(item) {
                return selected_area.includes(parseInt(item.area_group_value));
            });

            var html = items.map(function(item) {
                var options = item.items.map(function(option) {
                    return '<option value="' + option.value + '">' + option.label + '</option>';
                });
                return '<optgroup label="' + item.area_group_label + '">' + options.join('') + '</optgroup>';
            });

            $('select[name="filters[sub_area]"]').html(
                html.join('')
            ).val('').selectpicker('refresh');
        }

        function make_especialidades_options() {
            var selected_area = [$('select[name="filters[area]"]').val()].map(_ => parseInt(_));
            var selected_sub_area = [$('select[name="filters[sub_area]"]').val()].map(_ => parseInt(_));
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

            $('select[name="filters[especialidade]"]').html(
                html.join('')
            ).val('').selectpicker('refresh');
        }

        // TESTE
        $(document).ready(function() {
            function stopEvent(e) {
                e.preventDefault();
                e.stopPropagation();
            };

            make_subareas_options();
            make_especialidades_options();

            $(document)
                .on('click', '.toogle-checked-status', function(e) {
                    stopEvent(e)
                    var a = $(this),
                        active = a.hasClass('active'),
                        t = active ? '[ nenhum ]' : '[ todos ]';
                    a.toggleClass('active').text(t);
                    a.closest('.form-group').find('[type="checkbox"]').prop('checked', active);
                    return false;
                })
                .on('click', '.clear-select-filter, .clear-input-filter', function(e) {
                    stopEvent(e)

                    var cl = $(this).closest('div');
                    if ($(this).hasClass('clear-select-filter')) {
                        cl.find('select').selectpicker('val', '');
                        var name = cl.find('select').attr('name');
                        if (name === 'filters[area]') {
                            make_subareas_options();
                            make_especialidades_options();
                        }
                        if (name === 'filters[sub_area]') {
                            make_especialidades_options();
                        }
                        return false;
                    }

                    cl.find('input').val('');

                    return false;
                })
                .on('click', '.toggle-filters', function(e) {
                    stopEvent(e);

                    $(this).toggleClass('btn-light');
                    $(this).toggleClass('btn-info');
                    $('.filter-group').toggle();

                    return false;
                })
                .on('change', 'select[name="filters[area]"]', function() {
                    make_subareas_options();
                    make_especialidades_options();
                })
                .on('change', 'select[name="filters[sub_area]"]', function() {
                    make_especialidades_options();
                })
                .on('click', '.excel-export', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    var datatable = $(this).closest('.datagrid.use-datatable');
                    var table = datatable.find('table[id*="DataTables_Table"]');

                    table.dataTable().fnFilter(
                        datatable.find(".datagrid-filter").val()
                    );

                    setTimeout(function() {
                        var url = table.dataTable().api().ajax.url();
                        var data = table.dataTable().api().ajax.params();

                        data['export'] = 'excel';

                        $.post({
                            url,
                            data
                        }).done(function(data) {
                            window.open(
                                baseUrl + '/artigos/download-report?file=' + data.file
                            );
                        });
                    }, 250);

                    return false;
                });
        });
    </script>
@endsection
