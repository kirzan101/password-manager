<?php

namespace App\Interfaces\FetchInterfaces;

use App\Data\CollectionResponse;
use App\Data\ModelResponse;
use App\Data\PaginateResponse;

interface ActivityLogFetchInterface
{
    /**
     * Fetch a list of activity logs.
     *
     * @param array $request Optional parameters for filtering or pagination.
     * @param bool $isPaginated Whether to paginate the results.
     * @param class-string<\Illuminate\Http\Resources\Json\JsonResource>|null $resource The resource class to transform the results.
     * @return PaginateResponse|CollectionResponse
     */
    public function indexActivityLogs(array $request = [], bool $isPaginated = false, ?string $resourceClass = null): PaginateResponse|CollectionResponse;

    /**
     * Fetch a specific activity log by their ID.
     *
     * @param integer $activityLogId
     * @param class-string<\Illuminate\Http\Resources\Json\JsonResource>|null $resourceClass
     * @return ModelResponse
     */
    public function showActivityLog(int $activityLogId, ?string $resourceClass = null): ModelResponse;
}
