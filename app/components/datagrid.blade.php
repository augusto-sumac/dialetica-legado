@set('datatable_id', md5(rand() + time()))

<div class="datagrid {{ $datatable_id ? 'use-datatable' : '' }}" id="{{ $datatable_id }}">
    <div class="card card-fill mb-0">
        @if (!isset($filterable) || $filterable)

            <div class="card-body" style="margin: 0; padding-bottom: 0;">
                <form action="{{ isset($url) ? $url : urlCurrent() }}" method="GET" class="datagrid-form">

                    @if (isset($slot_form))
                        @include($slot_form)
                    @else
                        <div class="form-group row">
                            <div class="col">
                                <input type="text" placeholder="Filtrar... [ PRESSIONE ENTER ]"
                                    class="datagrid-filter form-control" name="search[search]" />
                            </div>
                        </div>
                    @endif

                </form>
            </div>

        @endif

        <div class="card-body" style="margin: 0; padding: 0;">
            @if (!$use_datatable)
                <div class="table-responsive">
            @endif

            <table class="table table-striped" style="margin: 0;">
                <thead>
                    <tr>
                        @foreach ($columns as $column)
                            <th id="th_col_{{ $column['key'] }}" {{ attributes(array_get($column, 'attrs', [])) }}>
                                {{ $column['label'] }}
                            </th>
                        @endforeach
                    </tr>
                </thead>

                <tbody>
                    @foreach (isset($data->results) ? $data->results : $data as $key => $row)
                        <tr>
                            @foreach ($columns as $column)
                                <td id="tr_{{ $key }}_td_col_{{ $column['key'] }}"
                                    {{ attributes(array_get($column, 'attrs', [])) }}>
                                    @if (isset($column['format']))
                                        <?php echo $column['format']($row->{$column['key']}, $row); ?>
                                    @else
                                        {{ $row->{$column['key']} }}
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>

                @if (isset($include_footer))
                    <tfoot>
                        <tr>
                            @foreach ($columns as $column)
                                <th id="tf_td_col_{{ $column['key'] }}"
                                    {{ attributes(array_get($column, 'attrs', [])) }}>
                                </th>
                            @endforeach
                        </tr>
                    </tfoot>
                @endif
            </table>

            @if (!$use_datatable)
        </div>
        @endif

        @if (!$use_datatable && isset($data->results))
            <div class="text-center">
                {{ $data->appends(array_except(input(), ['page']))->links() }}
            </div>
        @endif
    </div>
</div>
</div>

@section('js')
    @if ($use_datatable)
        <script>
            function getDatatableMergedStateData(form, data) {
                data = $.extend(true, data, form.serializeObject());
                data.page = (data.start / data.length) + 1;
                data.per_page = data.length;
                return data
            };

            $(document).ready(function() {
                var datatable = $('#{{ $datatable_id }}'),
                    table = datatable.find('table'),
                    form = datatable.find('form'),
                    sto_datagrid_filter = null,
                    sto_toggle_filters = null,
                    dt_instance = window.location.pathname.replace(/[^a-zA-Z0-9\_]/gi, '_');

                var dt_options = {{ isset($options) ? json_encode((array) $options) : '{}' }},
                    options = $.extend(true, {
                        dom: '<"table-responsive"t><p><"text-center pb-3"i>',
                        autoWidth: false,
                        pageLength: 50,
                        stateSave: true,
                        scrollY: Math.max(450, window.innerHeight - ($('table').offset().top + 210)),
                        scrollX: true,
                        stateSaveCallback: function(settings, state) {
                            state = getDatatableMergedStateData(form, state || {});
                            localStorage.setItem('DataTables_' + dt_instance, JSON.stringify(state || {}))
                        },
                        stateLoadCallback: function(settings) {
                            var state = JSON.parse(localStorage.getItem('DataTables_' + dt_instance)) || {};

                            var original = JSON.stringify(form.serializeObject().filters || {}),
                                saved = JSON.stringify(state.filters || {}),
                                toggleFilters = form.find('.toggle-filters');

                            // Restore form values
                            $.each(state.filters || {}, function(key, values) {
                                var selector = $('[name*="filters[' + key + ']"]');

                                if (selector.length) {
                                    var type = selector.first().attr('type') || selector.first()
                                        .tagName();

                                    switch (type) {
                                        case 'checkbox':
                                        case 'radio':
                                            selector
                                                .removeAttr('checked')
                                                .filter(function() {
                                                    return values.includes($(this).val() ||
                                                        'NONE');
                                                })
                                                .attr('checked', 'checked');
                                            break;

                                        case 'select':
                                            selector.val(values);
                                            selector.data("selectpicker") &&
                                                selector.data("selectpicker").refresh();
                                            break;

                                        case 'input':
                                        default:
                                            if (typeof values === 'object') {
                                                selector.val(null);
                                                $.each(values, function(k, v) {
                                                    $('[name*="filters[' + key + '][' + k +
                                                        ']"]').val(v);
                                                });
                                            } else {
                                                selector.val(values);
                                            }
                                            break;
                                    }
                                }
                            });

                            form.find(".datagrid-filter").val((state.search || {}).search || '')

                            // Show filters
                            if (saved !== original) {
                                clearTimeout(sto_toggle_filters);
                                sto_toggle_filters = setTimeout(function() {
                                    if (toggleFilters.hasClass('btn-light')) {
                                        toggleFilters.trigger('click');
                                    }
                                }, 50);
                            }

                            return state
                        },
                        language: {
                            search: "",
                            searchPlaceholder: "Filtrar",
                            emptyTable: "Nenhum registro encontrado",
                            zeroRecords: "Nenhum registro encontrado",
                            info: "Exibindo _START_ até _END_ de _TOTAL_ registros",
                            infoEmpty: "Exibindo 0 até 0 de 0 registros",
                            infoFiltered: "(filtrado de _MAX_ registros)",
                            paginate: {
                                first: "&lt;&lt;",
                                previous: "&lt;",
                                next: "&gt;",
                                last: "&gt;&gt;",
                            },
                        },
                    }, dt_options || {});

                if (options.ajax) {
                    options = $.extend(true, options, {
                        ajax: {
                            data: function(data) {
                                return getDatatableMergedStateData(form, data);
                            }
                        }
                    });
                }

                if (window.innerWidth < 768) {
                    delete options.scrollY;
                }

                table.data('dtoptions', options).dataTable(options);

                table.on('draw.dt', initBsTooltip);

                form
                    .on('submit', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        table.fnFilter(
                            datatable.find(".datagrid-filter").val()
                        );
                        return false;
                    });

                var sto_on_resize = null;

                $(window).on('resize', function() {
                    clearTimeout(sto_on_resize);
                    sto_on_resize = setTimeout(function() {
                        table.dataTable().api().columns().draw();
                    }, 100);
                });

                window.$datagrid_table = table;
            });
        </script>
    @endif

    @parent
@endsection
