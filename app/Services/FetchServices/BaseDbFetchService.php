<?php

namespace App\Services\FetchServices;

use App\Interfaces\FetchInterfaces\BaseDbFetchInterface;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class BaseDbFetchService implements BaseDbFetchInterface
{
    /**
     * Fetch a list of records from the given table.
     *
     * @param string $tableName
     * @return Builder
     */
    public function indexQuery(string $tableName): Builder
    {
        $this->validateTableName($tableName);

        return DB::table($tableName);
    }

    /**
     * Return a query builder filtered by a column (default: id) for further chaining.
     *
     * @param string $tableName
     * @param int|string $id
     * @param string|null $columnName
     * @return Builder
     */
    public function showQuery(string $tableName, int|string $id, string $columnName = 'id'): Builder
    {
        $this->validateTableName($tableName);

        return DB::table($tableName)->where($columnName, $id);
    }

    ##start methods

    /**
     * Get a list of all table names in the current database.
     *
     * @return array
     */
    private function getAllTableNames(): array
    {
        // Cache the table names for 10 minutes to avoid frequent database queries
        return Cache::remember('db_table_names', now()->addMinutes(10), function () {
            $schemaManager = DB::connection()->getDoctrineSchemaManager();
            // Register enum as string (Laravel 10+ compatibility)
            $schemaManager->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');

            return $schemaManager->listTableNames();
        });
    }

    /**
     * Validate that the provided table name exists in the database.
     *
     * @param string $tableName
     * @throws \InvalidArgumentException
     */
    private function validateTableName(string $tableName): void
    {
        $allowedTables = $this->getAllTableNames();

        if (!in_array($tableName, $allowedTables, true)) {
            throw new \InvalidArgumentException("Table '{$tableName}' does not exist or is not allowed.");
        }
    }
    ##end methods
}
