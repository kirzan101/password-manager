<?php

namespace App\Interfaces\FetchInterfaces;

use App\Data\CollectionResponse;
use App\Data\ModelResponse;
use App\Data\PaginateResponse;

interface ProfileFetchInterface
{
    /**
     * Fetch a list of profiles.
     *
     * @param array $request Optional parameters for filtering or pagination.
     * @param bool $isPaginated Whether to paginate the results.
     * @param class-string<\Illuminate\Http\Resources\Json\JsonResource>|null $resourceClass The resource class to transform the results.
     * @return PaginateResponse|CollectionResponse The response containing the list of profiles, either paginated or as a collection.
     */
    public function indexProfiles(array $request = [], bool $isPaginated = false, ?string $resourceClass = null): PaginateResponse|CollectionResponse;

    /**
     * Fetch a specific profile by their ID.
     *
     * @param integer $profileId The ID of the profile to fetch.
     * @param class-string<\Illuminate\Http\Resources\Json\JsonResource>|null $resourceClass The resource class to transform the result.
     * @return ModelResponse The response containing the profile data.
     */
    public function showProfile(int $profileId, ?string $resourceClass = null): ModelResponse;
}
