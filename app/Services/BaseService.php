<?php

namespace App\Services;

use App\Interfaces\BaseInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class BaseService implements BaseInterface
{
    /**
     * Create a new record.
     *
     * @param string $modelClass
     * @param array $request
     * @return Model
     */
    public function store(string $modelClass, array $request): Model
    {
        $this->validateModelClass($modelClass);
        return $modelClass::create($request);
    }

    /**
     * Bulk-insert multiple records into the specified Eloquent model.
     * Automatically sets timestamps if the table has `created_at` and `updated_at`.
     *
     * This method inserts multiple records into the database in a single query.
     * It bypasses Eloquent model events (like `creating` and `created`).
     *
     * Timestamps:
     * - If the target table contains `created_at` and `updated_at` columns, 
     *   this method will automatically set them for each record.
     * - If the table does not have these columns, timestamps will not be added.
     *
     * Notes:
     * - This method is efficient for inserting large datasets, as it reduces 
     *   the number of database queries.
     * - Since Eloquent model events are not triggered, any logic tied to 
     *   these events will not run.
     * 
     * @param string $modelClass The fully qualified class name of the model (e.g., `App\Models\User`).
     * @param array $requests An array of associative arrays, where each associative array 
     *                        represents a record to be inserted. The keys should match 
     *                        the column names of the model's associated database table.
     * @return bool True if the insert operation was successful, otherwise false.
     *
     * @throws \InvalidArgumentException If the provided class is not an Eloquent model.
     */
    public function storeMultiple(string $modelClass, array $requests): bool
    {
        $this->validateModelClass($modelClass);

        if (empty($requests)) {
            return false;
        }

        // Added this code to ensure timestamps are set
        // When using the query builder's insert(), timestamps are not set automatically like in Eloquent.
        // Manually set 'created_at' and 'updated_at' for each record.
        // Only add timestamps if the table has the columns
        $model = new $modelClass;
        $table = $model->getTable();

        // Cache schema checks per model to avoid repetitive DB hits
        static $timestampColumns = [];
        if (!isset($timestampColumns[$table])) {
            $timestampColumns[$table] = [
                'created_at' => Schema::hasColumn($table, 'created_at'),
                'updated_at' => Schema::hasColumn($table, 'updated_at'),
            ];
        }

        $hasCreatedAt = $timestampColumns[$table]['created_at'];
        $hasUpdatedAt = $timestampColumns[$table]['updated_at'];

        $now = Carbon::now();

        foreach ($requests as &$request) {
            unset($request['id']); // remove id if present

            if ($hasCreatedAt) {
                $request['created_at'] = $now;
            }

            if ($hasUpdatedAt) {
                $request['updated_at'] = $now;
            }
        }
        unset($request); // break the reference to the last item

        return $modelClass::insert($requests);
    }

    /**
     * Update a record.
     *
     * @param Model $model
     * @param array $request
     * @return Model
     */
    public function update(Model $model, array $request): Model
    {
        return tap($model)->update($request);
    }

    /**
     * Delete a record.
     *
     * @param Model $model
     * @return void
     */
    public function delete(Model $model): void
    {
        $model->delete();
    }

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
    public function deleteMultiple(string $modelClass, array $ids, ?string $columnName = 'id'): int
    {
        $this->validateModelClass($modelClass);

        if (empty($ids)) {
            return 0;
        }

        if (!Schema::hasColumn((new $modelClass)->getTable(), $columnName)) {
            throw new \InvalidArgumentException("Column '{$columnName}' does not exist on model {$modelClass}.");
        }

        // Using Eloquent's delete() method to ensure soft deletes are respected if the model uses SoftDeletes trait
        return $modelClass::whereIn($columnName, $ids)->delete();
    }

    /**
     * Ensure the class is a valid Eloquent model.
     *
     * @param string $modelClass
     * @return void
     */
    private function validateModelClass(string $modelClass): void
    {
        if (!is_subclass_of($modelClass, Model::class)) {
            throw new \InvalidArgumentException("Class must be an Eloquent model.");
        }
    }
}
