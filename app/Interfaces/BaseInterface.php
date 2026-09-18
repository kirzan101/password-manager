<?php

namespace App\Interfaces;

use Illuminate\Database\Eloquent\Model;

interface BaseInterface
{
    /**
     * Create a new record.
     *
     * @param string $modelClass
     * @param array $request
     * @return mixed
     */
    public function store(string $modelClass, array $request);

    /**
     * Create multiple records in the specified Eloquent model.
     *
     * @param string $modelClass
     * @param array $requests
     * @return int
     */
    public function storeMultiple(string $modelClass, array $requests): bool;

    /**
     * Update a record.
     *
     * @param mixed $model
     * @param array $request
     * @return mixed
     */
    public function update(Model $model, array $request);

    /**
     * Delete a record.
     *
     * @param mixed $model
     * @return void
     */
    public function delete(Model $model): void;
    /**
     * Delete multiple records.
     * 
     * Returns rows deleted count. If the model uses soft deletes, it will return the count of records soft-deleted.
     *
     * @param string $modelClass
     * @param array $ids
     * @param string|null $columnName
     * @return int The number of rows deleted.
     */
    public function deleteMultiple(string $modelClass, array $ids, ?string $columnName = 'id'): int;
}
