<?php

namespace App\Interfaces\FetchInterfaces;

use App\Data\CollectionResponse;
use App\Data\ModelResponse;
use App\Data\PaginateResponse;

interface UserFetchInterface
{
    /**
     * Fetch a list of users.
     *
     * @param array $request Optional parameters for filtering or pagination.
     * @param bool $isPaginated Whether to paginate the results.
     * @param class-string<\Illuminate\Http\Resources\Json\JsonResource>|null $resourceClass The resource class to transform the results.
     * @return PaginateResponse|CollectionResponse The response containing the list of users, either paginated or as a collection.
     */
    public function indexUsers(array $request = [], bool $isPaginated = false, ?string $resourceClass = null): PaginateResponse|CollectionResponse;

    /**
     * Fetch a specific user by their ID.
     *
     * @param integer $userId The ID of the user to fetch.
     * @param class-string<\Illuminate\Http\Resources\Json\JsonResource>|null $resourceClass The resource class to transform the result.
     * @return ModelResponse The response containing the user data.
     */
    public function showUser(int $userId, ?string $resourceClass = null): ModelResponse;
}
