<?php

namespace App\Interfaces\FetchInterfaces;

use Illuminate\Database\Query\Builder;

interface BaseDbFetchInterface
{
    /**
     * Fetch a list of records from the given table.
     *
     * @param string $tableName
     * @return \Illuminate\Database\Query\Builder
     */
    public function indexQuery(string $tableName): Builder;

    /**
     * Return a query builder filtered by a column (default: id) for further chaining.
     *
     * @param string $tableName
     * @param int|string $id
     * @param string|null $columnName
     * @return \Illuminate\Database\Query\Builder
     */
    public function showQuery(string $tableName, int|string $id, string $columnName = 'id'): Builder;
}
