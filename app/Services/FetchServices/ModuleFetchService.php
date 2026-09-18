<?php

namespace App\Services\FetchServices;

use App\Data\PaginateResponse;
use App\Data\CollectionResponse;
use App\Data\ModelResponse;
use App\Helpers\Helper;
use App\Models\Module;
use App\Interfaces\FetchInterfaces\ModuleFetchInterface;
use App\Traits\HttpErrorCodeTrait;
use App\Traits\DefaultPaginateFilterTrait;
use App\Interfaces\CurrentUserInterface;
use App\Interfaces\BaseInterface;
use App\Interfaces\FetchInterfaces\BaseFetchInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;

class ModuleFetchService extends BaseFetchService implements ModuleFetchInterface
{
    use HttpErrorCodeTrait,
        DefaultPaginateFilterTrait;

    /**
     * Fetch a list of module with optional search functionality.
     *
     * @param array $request
     * @param bool $isPaginated
     * @param class-string<\Illuminate\Http\Resources\Json\JsonResource>|null $resourceClass
     * @return PaginateResponse|CollectionResponse A response containing the list of modules, either paginated or as a collection.
     */
    public function indexModules(array $request = [], bool $isPaginated = false, ?string $resourceClass = null): PaginateResponse|CollectionResponse
    {
        try {
            $query = $this->indexQuery(Module::class);

            if ($resourceClass !== null && isset($resourceClass::$relations)) {
                $query->with($resourceClass::$relations ?? []);
            }

            if (isset($request['is_active'])) {
                $isActive = (bool) $request['is_active'];
                $query->where('is_active', $isActive);
            }

            //Uncomment this code if query has a seach functionality
            //Search filter
            if (isset($request['search']) && !empty($request['search'])) {
                $search = $request['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('icon', 'like', "%{$search}%")
                        ->orWhere('route', 'like', "%{$search}%")
                        ->orWhere('category', 'like', "%{$search}%");
                });
            }

            if ($isPaginated) {
                $allowedFields = (new Module())->getFillable();

                [
                    'per_page' => $per_page,
                    'sort_by' => $sort_by,
                    'sort' => $sort,
                    'current_page' => $current_page
                ] = $this->paginateFilter($request, $allowedFields);

                // Manually set the current page
                Paginator::currentPageResolver(fn() => $current_page ?? 1);

                $modules = $query->orderBy($sort_by, $sort)->paginate($per_page);
                return PaginateResponse::success(200, Helper::SUCCESS, 'Successfully fetched!', $modules);
            } else {
                $modules = $query->get();
                return CollectionResponse::success(200, Helper::SUCCESS, 'Successfully fetched!', $modules);
            }
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);

            return CollectionResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Fetch a single module by ID.
     *
     * @param integer $moduleId
     * @param class-string<\Illuminate\Http\Resources\Json\JsonResource>|null $resourceClass
     * @return ModelResponse
     */
    public function showModule(int $moduleId, ?string $resourceClass = null): ModelResponse
    {
        try {
            $query = $this->showQuery(Module::class, $moduleId);

            if ($resourceClass !== null && isset($resourceClass::$relations)) {
                $query->with($resourceClass::$relations ?? []);
            }

            $model = $query->firstOrFail();

            return ModelResponse::success(200, Helper::SUCCESS, 'Successfully fetched!', $model, $moduleId);
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);

            return ModelResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }
}
