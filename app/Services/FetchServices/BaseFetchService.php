<?php

namespace App\Services\FetchServices;

use App\Interfaces\FetchInterfaces\BaseFetchInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class BaseFetchService implements BaseFetchInterface
{
    /**
     * Fetch a list of records from the given model.
     *
     * @param string $modelClass
     * @return Builder
     */
    public function indexQuery(string $modelClass): Builder
    {
        $this->validateModelClass($modelClass);

        return $modelClass::query();
    }

    /**
     * Return a query builder filtered by a column (default: id) for further chaining.
     *
     * @param string $modelClass
     * @param int|string $id
     * @param string|null $columnName
     * @return Builder
     */
    public function showQuery(string $modelClass, int|string $id, string $columnName = 'id'): Builder
    {
        $this->validateModelClass($modelClass);

        return $modelClass::query()->where($columnName, $id);
    }

    ##start methods
    /**
     * Validate that the provided class is a subclass of Eloquent Model.
     *
     * @param string $modelClass
     * @throws \InvalidArgumentException
     */
    private function validateModelClass(string $modelClass): void
    {
        if (!is_subclass_of($modelClass, Model::class)) {
            throw new \InvalidArgumentException("Class must be an Eloquent model.");
        }
    }
    ##end methods
}
