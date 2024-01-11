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
                <br /> - ID e Titulo da Obra
                <br /> - Nome do Autor
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
    <div class="col-xs-12 col-md-3 mb-3 mb-md-0">
        <label class="col-form-label col-form-label-sm mt-0">
            Status
            <a href="javascript:void(0)" class="clear-select-filter">[ limpar ]</a>
        </label>
        <div class="col-12">
            <select class="form-control-sm form-select-sm" name="filters[status][]" placeholder="Status"
                data-multiple="true" data-size="sm">
                {{ select_options_article_status(null, 3, fn($i) => $i['value'] !== 0) }}
            </select>
        </div>
    </div>
    <div class="col-xs-12 col-md-3 mb-3 mb-md-0">
        <label class="col-form-label col-form-label-sm mt-0">
            Status Pagto
            <a href="javascript:void(0)" class="clear-select-filter">[ limpar ]</a>
        </label>
        <div class="col-12">
            <select class="form-control-sm form-select-sm" name="filters[status_pagamento][]"
                placeholder="Status Pagamento" data-multiple="true" data-size="sm">
                {{ select_options_article_payment_status(null) }}
            </select>
        </div>
    </div>
    <div class="col">
        <label class="col-form-label col-form-label-sm mt-0">
            Data Envio/Conclusão
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
    <div class="col-auto">
        <button type="submit" class="btn btn-primary btn-sm" title="Aplicar filtros">
            <span class="fas fa-check"></span>
        </button>
    </div>
</div>

@section('js')
    @parent
    <script>
        // TESTE
        $(document).ready(function() {
            function stopEvent(e) {
                e.preventDefault();
                e.stopPropagation();
            };

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
                                baseUrl + '/revisoes/download-report?file=' + data.file
                            );
                        });
                    }, 250);

                    return false;
                });
        });
    </script>
@endsection
