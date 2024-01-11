<?php

namespace App\Models;

use App\Models\BaseModel;

class Author extends BaseModel
{
    public static $table = 'users';

    /**
     * Get a new fluent query builder instance for the model.
     *
     * @return Model_Query
     */
    protected function _query()
    {
        return parent::_query()->where(parent::table() . '.type', 'author');
    }
}
