<?php

namespace App\Interfaces\FetchInterfaces;

use Illuminate\Database\Eloquent\Builder;

interface BaseFetchInterface
{
    /**
     * Fetch a list of records from the given model.
     *
     * @param string $modelClass
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function indexQuery(string $modelClass): Builder;

    /**
     * Return a query builder filtered by a column (default: id) for further chaining.
     *
     * @param string $modelClass
     * @param int|string $id
     * @param string|null $columnName
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function showQuery(string $modelClass, int|string $id, string $columnName = 'id'): Builder;
}
