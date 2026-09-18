<?php

namespace App\Services\FetchServices;

use App\Data\CollectionResponse;
use App\Data\ModelResponse;
use App\Data\PaginateResponse;
use App\Helpers\Helper;
use App\Interfaces\FetchInterfaces\ActivityLogFetchInterface;
use App\Interfaces\FetchInterfaces\BaseFetchInterface;
use App\Models\ActivityLog;
use App\Traits\DefaultPaginateFilterTrait;
use App\Traits\HttpErrorCodeTrait;
use Illuminate\Pagination\Paginator;

class ActivityLogFetchService implements ActivityLogFetchInterface
{
    use HttpErrorCodeTrait,
        DefaultPaginateFilterTrait;

    public function __construct(private BaseFetchInterface $fetch) {}

    /**
     * Fetch a list of activity logs with optional search functionality.
     *
     * @param array $request
     * @param bool $isPaginated
     * @param class-string<\Illuminate\Http\Resources\Json\JsonResource>|null $resourceClass
     * @return PaginateResponse|CollectionResponse
     */
    public function indexActivityLogs(array $request = [], bool $isPaginated = false, ?string $resourceClass = null): PaginateResponse|CollectionResponse
    {
        try {
            $query = $this->fetch->indexQuery(ActivityLog::class);

            if ($resourceClass !== null && isset($resourceClass::$relations)) {
                $query->with($resourceClass::$relations ?? []);
            }

            if (!empty($request['status'])) {
                $status = $request['status'];
                $query->where('status', $status);
            }

            if (!empty($request['module'])) {
                $module = $request['module'];
                $query->where('module', $module);
            }


            if (!empty($request['type'])) {
                $type = $request['type'];
                $query->where('type', $type);
            }

            if (!empty($request['processed_by'])) {
                $processed_by = $request['processed_by'];
                $query->where('processed_by', $processed_by);
            }

            // process date range filter, will use created_at field for filtering
            if (!empty($request['start_date']) && !empty($request['end_date'])) {
                $start_date = $request['start_date'];
                $end_date = $request['end_date'];
                $query->whereBetween('created_at', [$start_date, $end_date]);
            }

            if (isset($request['search']) && !empty($request['search'])) {
                $search = $request['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('description', 'like', "%{$search}%");
                });
            }

            if ($isPaginated) {
                $allowedFields = (new ActivityLog())->getFillable();

                [
                    'per_page' => $per_page,
                    'sort_by' => $sort_by,
                    'sort' => $sort,
                    'current_page' => $current_page
                ] = $this->paginateFilter($request, $allowedFields);

                // Manually set the current page
                Paginator::currentPageResolver(fn() => $current_page ?? 1);

                $activityLogs = $query->orderBy($sort_by, $sort)->paginate($per_page);
                return PaginateResponse::success(200, Helper::SUCCESS, 'Successfully fetched!', $activityLogs);
            } else {

                $activityLogs = $query->get();
                return CollectionResponse::success(200, Helper::SUCCESS, 'Successfully fetched!', $activityLogs);
            }
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);

            return CollectionResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Fetch a single activity log by ID.
     *
     * @param integer $id
     * @param class-string<\Illuminate\Http\Resources\Json\JsonResource>|null $resourceClass
     * @return ModelResponse
     */
    public function showActivityLog(int $id, ?string $resourceClass = null): ModelResponse
    {
        try {
            $query = $this->fetch->showQuery(ActivityLog::class, $id);

            if ($resourceClass !== null && isset($resourceClass::$relations)) {
                $query->with($resourceClass::$relations ?? []);
            }

            $activityLog = $query->firstOrFail();

            return ModelResponse::success(200, Helper::SUCCESS, 'Successfully fetched!', $activityLog, $id);
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);

            return ModelResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }
}
