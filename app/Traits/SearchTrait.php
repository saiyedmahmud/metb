<?php

namespace App\Traits;

use Illuminate\Contracts\Database\Eloquent\Builder;

trait SearchTrait
{
    public function searchQry(Builder $query, array $columns, $operator, $key): Builder
    {
        return $query->where(function ($query) use ($columns, $operator, $key) {
            foreach ($columns as $column) {
                $query->orWhere($column, $operator, "%$key%");
            }
        });
    }
}
