<?php

namespace App\Interfaces\FetchInterfaces;

use App\Data\PaginateResponse;
use App\Data\CollectionResponse;
use App\Data\ModelResponse;

interface ModuleFetchInterface
{
    /**
     * Fetch a list of module with optional search functionality.
     *
     * @param array $request Optional parameters for filtering or pagination.
     * @param bool $isPaginated Whether to paginate the results.
     * @param class-string<\Illuminate\Http\Resources\Json\JsonResource>|null $resourceClass The resource class to transform the results.
     * @return PaginateResponse|CollectionResponse A response containing the list of modules, either paginated or as a collection.
     */
    public function indexModules(array $request = [], bool $isPaginated = false, ?string $resourceClass = null): PaginateResponse|CollectionResponse;

    /**
     * Fetch a single module by ID.
     *
     * @param integer $moduleId
     * @param class-string<\Illuminate\Http\Resources\Json\JsonResource>|null $resourceClass
     * @return ModelResponse A model response containing the module.
     */
    public function showModule(int $moduleId, ?string $resourceClass = null): ModelResponse;
}
