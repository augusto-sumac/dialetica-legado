<?php

namespace App\Models;

use Model, Model_Query;

class BaseModel extends Model
{
    /**
     * Get a new fluent query builder instance for the model.
     *
     * @return Model_Query
     */
    protected function _query()
    {
        $model = new Model_Query($this);

        if ($model->model && $model->model->soft_delete) {
            $model->where($this->table() . '.deleted', '=', 0);
        }
        return $model;
    }
}
