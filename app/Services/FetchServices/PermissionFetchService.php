<?php

namespace App\Services\FetchServices;

use App\Data\CollectionResponse;
use App\Data\SupportCollectionResponse;
use App\Data\ModelResponse;
use App\Data\PaginateResponse;
use App\Helpers\Helper;
use App\Interfaces\FetchInterfaces\BaseFetchInterface;
use App\Interfaces\FetchInterfaces\PermissionFetchInterface;
use App\Models\Permission;
use App\Traits\DefaultPaginateFilterTrait;
use App\Traits\HttpErrorCodeTrait;
use Illuminate\Pagination\Paginator;

class PermissionFetchService implements PermissionFetchInterface
{
    use HttpErrorCodeTrait,
        DefaultPaginateFilterTrait;

    public function __construct(private BaseFetchInterface $fetch) {}

    /**
     * Fetch a list of permissions with optional search functionality.
     *
     * @param array $request
     * @param bool $isPaginated
     * @param class-string<\Illuminate\Http\Resources\Json\JsonResource>|null $resourceClass
     * @return PaginateResponse|CollectionResponse
     */
    public function indexPermissions(array $request = [], bool $isPaginated = false, ?string $resourceClass = null): PaginateResponse|CollectionResponse
    {
        try {
            $query = $this->fetch->indexQuery(Permission::class);

            if ($resourceClass !== null && isset($resourceClass::$relations)) {
                $query->with($resourceClass::$relations ?? []);
            }

            if (!empty($request['search'])) {
                $search = $request['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('module', 'like', "%{$search}%")
                        ->orWhere('type', 'like', "%{$search}%");
                });
            }

            if ($isPaginated) {
                $allowedFields = (new Permission())->getFillable();

                [
                    'per_page' => $per_page,
                    'sort_by' => $sort_by,
                    'sort' => $sort,
                    'current_page' => $current_page
                ] = $this->paginateFilter($request, $allowedFields);

                // Manually set the current page
                Paginator::currentPageResolver(fn() => $current_page ?? 1);

                $permissions = $query->orderBy($sort_by, $sort)->paginate($per_page);
                return PaginateResponse::success(200, Helper::SUCCESS, 'Successfully fetched!', $permissions);
            } else {

                $permissions = $query->get();
                return CollectionResponse::success(200, Helper::SUCCESS, 'Successfully fetched!', $permissions);
            }
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);

            return CollectionResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Fetch a single permission by ID.
     * Query a specific permission by its ID.
     * @param integer $permissionId
     * @param class-string<\Illuminate\Http\Resources\Json\JsonResource>|null $resourceClass
     * @return ModelResponse
     */
    public function showPermission(int $permissionId, ?string $resourceClass = null): ModelResponse
    {
        try {
            $query = $this->fetch->showQuery(Permission::class, $permissionId);

            if ($resourceClass !== null && isset($resourceClass::$relations)) {
                $query->with($resourceClass::$relations ?? []);
            }

            $permission = $query->firstOrFail();

            return ModelResponse::success(200, Helper::SUCCESS, 'Successfully fetched!', $permission, $permissionId);
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);

            return ModelResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Fetch permissions grouped by module.
     *
     * @param string|null $moduleName
     * @return SupportCollectionResponse
     */
    public function permissionsByModule(?string $moduleName = null): SupportCollectionResponse
    {
        try {
            $query = $this->fetch->indexQuery(Permission::class)
                ->join('modules', 'modules.base_name', '=', 'permissions.module')
                ->where('permissions.is_active', true)
                ->where('modules.is_active', true)
                ->orderBy('modules.base_name', 'asc')
                ->select([
                    'permissions.id as permission_id',
                    'permissions.type',
                    'permissions.is_active',
                    'modules.base_name as module',
                ]);

            if ($moduleName !== null) {
                $query->where('permissions.module', $moduleName);
            }

            $permissions = $query
                ->get()
                ->groupBy('module')
                ->map(fn($items) => $items->map(fn($item) => [
                    'permission_id' => $item->permission_id,
                    'type' => $item->type,
                    'is_active' => (bool) $item->is_active,
                ]));

            // results sample structure:
            // [
            //     'module_name' => [
            //         [
            //             'permission_id' => 1,
            //             'type' => 'view',
            //         ],
            //         [
            //             'permission_id' => 2,
            //             'type' => 'edit',
            //         ],
            //     ],
            // ]

            return SupportCollectionResponse::success(200, Helper::SUCCESS, 'Successfully fetched!', $permissions);
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);

            return SupportCollectionResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }
}
