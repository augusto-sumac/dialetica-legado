<?php

function articles_areas_validation_rules($id = null)
{
    return [
        'name' => 'required|max:100|unique:' . TB_ARTICLES_AREAS . ($id ? ',id,' . $id : '')
    ];
}

Route::group([
    'prefix' => ARTICLES_AREAS_BASE_URL,
    'before' => 'auth'
], function () {
    Route::get('', function () {
        $data = articles_areas()->get();

        $datagrid = [
            'data' => $data,
            'use_datatable' => true,
            'columns' => [
                [
                    'key' => 'id',
                    'label' => '#ID',
                    'attrs' => ['class' => 'text-start w-120px'],
                    'format' => function ($value) {
                        $url = url(ARTICLES_AREAS_BASE_URL . '/' . $value . '/editar');
                        return '<a href="' . $url . '" title="Editar Cadastro">' . str_pad_id($value) . '</a>';
                    }
                ],
                [
                    'key' => 'name',
                    'label' => 'Área',
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
                        return '<a href="' . url(ARTICLES_AREAS_BASE_URL . '/' . $row->id . '/toggle-status') . '" data-status="' . $value . '" class="toggle-status badge bg-' . $class . '" title="Alterar Status">' . $text . '</a>';
                    }
                ],
            ]
        ];

        return view(ARTICLES_AREAS_VIEW_PATH . '.index', compact('datagrid'));
    });

    Route::get('/adicionar', render_page(ARTICLES_AREAS_VIEW_PATH . '.form'));

    Route::post('/adicionar', function () {
        validate(articles_areas_validation_rules());

        $item = array_only(input(), array_keys(articles_areas_validation_rules()));

        try {
            $item['id'] = articles_areas()->insert_get_id($item);

            return response_json_create_success([
                'item' => (object) $item
            ]);
        } catch (\Exception $e) {
            return response_json_create_fail();
        }
    });

    Route::get('/{id}/editar', function ($id) {
        $item = (array) articles_areas()->find($id);

        return view(ARTICLES_AREAS_VIEW_PATH . '.form', $item);
    });

    Route::post('/{id}/editar', function ($id) {
        find_or_fail(articles_areas(), $id);

        validate(articles_areas_validation_rules($id));

        $item = array_only(input(), array_keys(articles_areas_validation_rules()));

        try {
            $item['id'] = articles_areas()->update($item, $id);

            return response_json_update_success([
                'item' => (object) $item
            ]);
        } catch (\Exception $e) {
            return response_json_update_fail();
        }
    });

    Route::post('/{id:\d+}/toggle-status', function ($id) {
        find_or_fail(articles_areas(), $id);

        $status = (int)input('status') === 1 ? 0 : 1;

        try {
            articles_areas()->update(compact('status'), $id);

            return response_json([
                'status' => $status,
                'message' => 'Registro atualizado com sucesso'
            ]);
        } catch (\Exception $e) {
            return response_json([
                'message' => 'Ocorreu um erro ao atualizar o registro'
            ], 500);
        }
    });
});
