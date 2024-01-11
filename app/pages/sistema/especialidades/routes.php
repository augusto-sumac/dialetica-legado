<?php

function articles_specialties_validation_rules($id = null)
{
    return [
        'name' => 'required|max:100|unique:' . TB_ARTICLES_SPECIALTIES . ($id ? ',id,' . $id : ''),
        'subarea_id' => 'required|exists:' . TB_ARTICLES_SUBAREAS . ',id',
        'area_id' => 'required|exists:' . TB_ARTICLES_AREAS . ',id',
    ];
}

Route::group([
    'prefix' => ARTICLES_SPECIALTIES_BASE_URL,
    'before' => 'auth'
], function () {
    Route::get('', function () {
        $datagrid = make_datatable_options([
            'data' => [],
            'use_datatable' => true,
            'options' => [
                'order' => [[1, 'desc']],
                'processing' =>  true,
                'serverSide' =>  true,
                'deferRender' => true,
                'ajax' => [
                    'url' => url(ARTICLES_SPECIALTIES_BASE_URL),
                    'type' => 'POST'
                ],
                'pageLength' => 50
            ],
            'columns' => [
                [
                    'key' => 'id',
                    'label' => '#ID',
                    'attrs' => ['class' => 'text-start w-120px'],
                    'format' => function ($value) {
                        $url = url(ARTICLES_SPECIALTIES_BASE_URL . '/' . $value . '/editar');
                        return '<a href="' . $url . '" title="Editar Cadastro">' . str_pad_id($value) . '</a>';
                    }
                ],
                [
                    'key' => 'nome_area',
                    'label' => 'Área',
                    'attrs' => ['class' => 'w-200px']
                ],
                [
                    'key' => 'nome_subarea',
                    'label' => 'Subárea',
                    'attrs' => ['class' => 'w-200px']
                ],
                [
                    'key' => 'name',
                    'label' => 'Especialidade',
                    'attrs' => ['class' => 'w-200px']
                ],
                [
                    'key' => 'created_at',
                    'label' => 'Dt Cadastro',
                    'attrs' => ['class' => 'text-end'],
                    'format' => fn ($value) => datetimeFromMySql($value)
                ],
                [
                    'key' => 'status',
                    'label' => 'Status',
                    'attrs' => ['class' => 'text-end w-120px'],
                    'format' => function ($value, $row) {
                        $text = (int) $value === 1 ? 'Ativo' : 'Inativo';
                        $class = (int) $value === 1 ? 'success' : 'danger';
                        return '<a href="' . url(ARTICLES_SPECIALTIES_BASE_URL . '/' . $row->id . '/toggle-status') . '" data-status="' . $value . '" class="toggle-status badge bg-' . $class . '" title="Alterar Status">' . $text . '</a>';
                    }
                ],
            ]
        ]);

        return view(ARTICLES_SPECIALTIES_VIEW_PATH . '.index', compact('datagrid'));
    });
    Route::post('', function () {
        $rows = articles_specialties()
            ->join(TB_ARTICLES_AREAS, TB_ARTICLES_AREAS . '.id', '=', TB_ARTICLES_SPECIALTIES . '.area_id')
            ->join(TB_ARTICLES_SUBAREAS, TB_ARTICLES_SUBAREAS . '.id', '=', TB_ARTICLES_SPECIALTIES . '.subarea_id')
            ->select([
                TB_ARTICLES_SPECIALTIES . '.*',
                DB::raw("coalesce(" . TB_ARTICLES_AREAS . ".name, 'Não Relacionada') as nome_area"),
                DB::raw("coalesce(" . TB_ARTICLES_SUBAREAS . ".name, 'Não Relacionada') as nome_subarea"),
            ]);

        $total = clone $rows;

        // Sort
        $cols = input('columns', []);
        foreach (input('order', []) as $order) {
            $col = array_get($cols, $order['column'] . '.data');
            if ($col) {
                $rows->order_by(DB::raw($col . ' ' . strtolower($order['dir'])));
            }
        }

        // Search
        if ($search = input('search.value', input('search.search'))) {
            $rows->where(function ($q) use ($search) {
                $q->or_like(TB_ARTICLES_SPECIALTIES . '.name', $search);
                $q->or_like(TB_ARTICLES_AREAS . '.name', $search);
                $q->or_like(TB_ARTICLES_SUBAREAS . '.name', $search);
            });
        }

        // Data
        $rows = $rows->paginate(input('per_page', 15));

        return response_json([
            'draw' => input('draw', 1),
            'recordsTotal' => $total->count(),
            'recordsFiltered' => $rows->total,
            'data' => array_map(function ($row) {
                $row_status = $row->status;

                $status_text = (int) $row_status === 1 ? 'Ativo' : 'Inativo';
                $status_class = (int) $row_status === 1 ? 'success' : 'danger';
                $row->status = '<a href="' . url(ARTICLES_SPECIALTIES_BASE_URL . '/' . $row->id . '/toggle-status') . '" data-status="' . $row_status . '" class="toggle-status badge bg-' . $status_class . '" title="Alterar Status">' . $status_text . '</a>';

                $row->id = '<a href="' . url(ARTICLES_SPECIALTIES_BASE_URL . '/' . $row->id . '/editar') . '" title="Editar Cadastro">' . str_pad_id($row->id) . '</a>';

                $row->created_at = datetimeFromMySql($row->created_at);

                return $row;
            }, $rows->results)
        ]);
    });

    Route::get('/adicionar', render_page(ARTICLES_SPECIALTIES_VIEW_PATH . '.form'));

    Route::post('/adicionar', function () {
        validate(articles_specialties_validation_rules());

        $item = array_only(input(), array_keys(articles_specialties_validation_rules()));

        try {
            $item['id'] = articles_specialties()->insert_get_id($item);

            return response_json_create_success([
                'item' => (object) $item
            ]);
        } catch (\Exception $e) {
            return response_json_create_fail();
        }
    });

    Route::get('/{id}/editar', function ($id) {
        $item = (array) articles_specialties()->find($id);

        return view(ARTICLES_SPECIALTIES_VIEW_PATH . '.form', $item);
    });

    Route::post('/{id}/editar', function ($id) {
        find_or_fail(articles_specialties(), $id);

        validate(articles_specialties_validation_rules($id));

        $item = array_only(input(), array_keys(articles_specialties_validation_rules()));

        try {
            articles_specialties()->update($item, $id);
            $item['id'] = $id;

            return response_json_update_success([
                'item' => (object) $item
            ]);
        } catch (\Exception $e) {
            return response_json_update_fail();
        }
    });

    Route::post('/{id:\d+}/toggle-status', function ($id) {
        $status = (int)input('status') === 1 ? 0 : 1;

        try {
            articles_specialties()->update(compact('status'), $id);

            return response_json_update_success([
                'status' => $status,
            ]);
        } catch (\Exception $e) {
            return response_json_update_fail();
        }
    });
});
