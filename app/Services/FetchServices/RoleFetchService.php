<?php

namespace App\Services\FetchServices;

use App\Data\PaginateResponse;
use App\Data\CollectionResponse;
use App\Data\ModelResponse;
use App\Helpers\Helper;
use App\Models\Role;
use App\Interfaces\FetchInterfaces\RoleFetchInterface;
use App\Traits\HttpErrorCodeTrait;
use App\Traits\DefaultPaginateFilterTrait;
use App\Interfaces\CurrentUserInterface;
use App\Interfaces\BaseInterface;
use App\Interfaces\FetchInterfaces\BaseFetchInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;

class RoleFetchService extends BaseFetchService implements RoleFetchInterface
{
    use HttpErrorCodeTrait,
        DefaultPaginateFilterTrait;

    /**
     * Fetch a list of role with optional search functionality.
     *
     * @param array $request
     * @param bool $isPaginated
     * @param class-string<\Illuminate\Http\Resources\Json\JsonResource>|null $resourceClass
     * @return PaginateResponse|CollectionResponse A response containing the list of roles, either paginated or as a collection.
     */
    public function indexRoles(array $request = [], bool $isPaginated = false, ?string $resourceClass = null): PaginateResponse|CollectionResponse
    {
        try {
            $query = $this->indexQuery(Role::class);

            if ($resourceClass !== null && isset($resourceClass::$relations)) {
                $query->with($resourceClass::$relations ?? []);
            }

            // is_active filter
            if (isset($request['is_active']) && !empty($request['is_active'])) {
                $isActive = filter_var($request['is_active'], FILTER_VALIDATE_BOOLEAN);
                $query->where('is_active', $isActive);
            }

            //Search filter
            if (isset($request['search']) && !empty($request['search'])) {
                $search = $request['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            }

            if ($isPaginated) {
                $allowedFields = (new Role())->getFillable();

                [
                    'per_page' => $per_page,
                    'sort_by' => $sort_by,
                    'sort' => $sort,
                    'current_page' => $current_page
                ] = $this->paginateFilter($request, $allowedFields);

                // Manually set the current page
                Paginator::currentPageResolver(fn() => $current_page ?? 1);

                $roles = $query->orderBy($sort_by, $sort)->paginate($per_page);
                return PaginateResponse::success(200, Helper::SUCCESS, 'Successfully fetched!', $roles);
            } else {
                $roles = $query->get();
                return CollectionResponse::success(200, Helper::SUCCESS, 'Successfully fetched!', $roles);
            }
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);

            return CollectionResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Fetch a single role by ID.
     *
     * @param integer $roleId
     * @param class-string<\Illuminate\Http\Resources\Json\JsonResource>|null $resourceClass
     * @return ModelResponse
     */
    public function showRole(int $roleId, ?string $resourceClass = null): ModelResponse
    {
        try {
            $query = $this->showQuery(Role::class, $roleId);

            if ($resourceClass !== null && isset($resourceClass::$relations)) {
                $query->with($resourceClass::$relations ?? []);
            }

            $model = $query->firstOrFail();

            return ModelResponse::success(200, Helper::SUCCESS, 'Successfully fetched!', $model, $roleId);
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);

            return ModelResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }
}
