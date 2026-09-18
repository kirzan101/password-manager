<?php

namespace App\Interfaces\FetchInterfaces;

use App\Data\PaginateResponse;
use App\Data\CollectionResponse;
use App\Data\ModelResponse;

interface RoleFetchInterface
{
    /**
     * Fetch a list of role with optional search functionality.
     *
     * @param array $request Optional parameters for filtering or pagination.
     * @param bool $isPaginated Whether to paginate the results.
     * @param class-string<\Illuminate\Http\Resources\Json\JsonResource>|null $resourceClass The resource class to transform the results.
     * @return PaginateResponse|CollectionResponse A response containing the list of roles, either paginated or as a collection.
     */
    public function indexRoles(array $request = [], bool $isPaginated = false, ?string $resourceClass = null): PaginateResponse|CollectionResponse;

    /**
     * Fetch a single role by ID.
     *
     * @param integer $roleId
     * @param class-string<\Illuminate\Http\Resources\Json\JsonResource>|null $resourceClass
     * @return ModelResponse A model response containing the role.
     */
    public function showRole(int $roleId, ?string $resourceClass = null): ModelResponse;
}
