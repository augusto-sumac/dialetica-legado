<?php

function articles_subareas_validation_rules($id = null)
{
    return [
        'area_id' => 'required|exists:' . TB_ARTICLES_AREAS . ',id',
        'name' => 'required|max:100|unique:' . TB_ARTICLES_SUBAREAS . ($id ? ',id,' . $id : ''),
    ];
}

Route::group([
    'prefix' => ARTICLES_SUBAREAS_BASE_URL,
    'before' => 'auth'
], function () {
    Route::get('', function () {
        $data = articles_subareas()
            ->join(TB_ARTICLES_AREAS, TB_ARTICLES_AREAS . '.id', '=', TB_ARTICLES_SUBAREAS . '.area_id')
            ->get([
                TB_ARTICLES_SUBAREAS . '.*',
                DB::raw("coalesce(" . TB_ARTICLES_AREAS . ".name, 'Não Relacionada') as nome_area"),
            ]);

        $datagrid = [
            'data' => $data,
            'use_datatable' => true,
            'columns' => [
                [
                    'key' => 'id',
                    'label' => '#ID',
                    'attrs' => ['class' => 'text-start w-120px'],
                    'format' => function ($value) {
                        $url = url(ARTICLES_SUBAREAS_BASE_URL . '/' . $value . '/editar');
                        return '<a href="' . $url . '" title="Editar Cadastro">' . str_pad_id($value) . '</a>';
                    }
                ],
                [
                    'key' => 'nome_area',
                    'label' => 'Área',
                    'attrs' => ['class' => 'w-200px']
                ],
                [
                    'key' => 'name',
                    'label' => 'Subárea',
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
                        return '<a href="' . url(ARTICLES_SUBAREAS_BASE_URL . '/' . $row->id . '/toggle-status') . '" data-status="' . $value . '" class="toggle-status badge bg-' . $class . '" title="Alterar Status">' . $text . '</a>';
                    }
                ],
            ]
        ];

        return view(ARTICLES_SUBAREAS_VIEW_PATH . '.index', compact('datagrid'));
    });

    Route::get('/adicionar', render_page(ARTICLES_SUBAREAS_VIEW_PATH . '.form'));

    Route::post('/adicionar', function () {
        validate(articles_subareas_validation_rules());

        $item = array_only(input(), array_keys(articles_subareas_validation_rules()));

        try {
            $item['id'] = articles_subareas()->insert_get_id($item);

            return response_json_create_success([
                'item' => (object) $item
            ]);
        } catch (\Exception $e) {
            return response_json_create_fail();
        }
    });

    Route::get('/{id}/editar', function ($id) {
        $item = (array) articles_subareas()->find($id);

        return view(ARTICLES_SUBAREAS_VIEW_PATH . '.form', $item);
    });

    Route::post('/{id}/editar', function ($id) {
        find_or_fail(articles_subareas(), $id);

        validate(articles_subareas_validation_rules($id));

        $item = array_only(input(), array_keys(articles_subareas_validation_rules()));

        try {
            articles_subareas()->update($item, $id);
            $item['id'] = $id;

            return response_json_update_success([
                'item' => (object) $item
            ]);
        } catch (\Exception $e) {
            return response_json_update_fail();
        }
    });

    Route::post('/{id:\d+}/toggle-status', function ($id) {
        find_or_fail(articles_subareas(), $id);

        $status = (int)input('status') === 1 ? 0 : 1;

        try {
            articles_subareas()->update(compact('status'), $id);

            return response_json_update_success([
                'status' => $status
            ]);
        } catch (\Exception $e) {
            return response_json_update_fail();
        }
    });
});
