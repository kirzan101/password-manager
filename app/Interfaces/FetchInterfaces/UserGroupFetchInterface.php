<?php

namespace App\Interfaces\FetchInterfaces;

use App\Data\CollectionResponse;
use App\Data\ModelResponse;
use App\Data\PaginateResponse;

interface UserGroupFetchInterface
{
    /**
     * Fetch a list of user groups.
     *
     * @param array $request Optional parameters for filtering or pagination.
     * @param bool $isPaginated Whether to paginate the results.
     * @param class-string<\Illuminate\Http\Resources\Json\JsonResource>|null $resourceClass The resource class to transform the results.
     * @return PaginateResponse|CollectionResponse The response containing the list of user groups, either paginated or as a collection.
     */
    public function indexUserGroups(array $request = [], bool $isPaginated = false, ?string $resourceClass = null): PaginateResponse|CollectionResponse;

    /**
     * Fetch a specific user group by their ID.
     *
     * @param integer $userGroupId The ID of the user group to fetch.
     * @param class-string<\Illuminate\Http\Resources\Json\JsonResource>|null $resourceClass The resource class to transform the result.
     * @return ModelResponse The response containing the user group data.
     */
    public function showUserGroup(int $userGroupId, ?string $resourceClass = null): ModelResponse;
}
