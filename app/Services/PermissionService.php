<?php

namespace App\Services;

use App\Data\ModelResponse;
use App\DTOs\PermissionDTO;
use App\Interfaces\BaseInterface;
use App\Interfaces\CurrentUserInterface;
use App\Interfaces\FetchInterfaces\BaseFetchInterface;
use App\Traits\HttpErrorCodeTrait;
use App\Traits\ReturnModelCollectionTrait;
use App\Traits\ReturnModelTrait;
use App\Interfaces\ModuleNameResolverInterface;
use App\Interfaces\PermissionInterface;
use App\Models\Permission;
use App\Models\Role;
use App\Models\RolePermission;
use App\Traits\CheckIfColumnExistsTrait;
use App\Traits\DetectsSoftDeletesTrait;
use Illuminate\Support\Facades\DB;

class PermissionService implements PermissionInterface
{
    use HttpErrorCodeTrait,
        ReturnModelCollectionTrait,
        ReturnModelTrait,
        DetectsSoftDeletesTrait,
        CheckIfColumnExistsTrait;

    public function __construct(
        private BaseInterface $base,
        private BaseFetchInterface $fetch,
        private ModuleNameResolverInterface $moduleResolver,
        private CurrentUserInterface $currentUser
    ) {}

    /**
     * Store a new permission in the database.
     *
     * @param PermissionDTO $permissionDTO
     * @return ModelResponse
     */
    public function storePermission(PermissionDTO $permissionDTO): ModelResponse
    {
        try {
            return DB::transaction(function () use ($permissionDTO) {

                $permissionData = $permissionDTO->toArray();
                $permission = $this->base->store(Permission::class, $permissionData);

                // automatically add the permission to the admin role
                $this->addPermissionToAdminRole($permission);

                return ModelResponse::success(201, 'success', 'Permission created successfully!', $permission, $permission->id);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return ModelResponse::error($code, 'error', $th->getMessage());
        }
    }

    /**
     * Update an existing permission in the database.
     *
     * @param PermissionDTO $permissionDTO
     * @param int $permissionId
     * @return ModelResponse
     */
    public function updatePermission(PermissionDTO $permissionDTO, int $permissionId): ModelResponse
    {
        try {
            return DB::transaction(function () use ($permissionDTO, $permissionId) {
                $permission = $this->fetch->showQuery(Permission::class, $permissionId)->firstOrFail();

                $permissionData = PermissionDTO::fromModel($permission, $permissionDTO->toArray())->toArray();
                $permission = $this->base->update($permission, $permissionData);

                return ModelResponse::success(200, 'success', 'Permission updated successfully!', $permission, $permissionId);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return ModelResponse::error($code, 'error', $th->getMessage());
        }
    }

    /**
     * Delete a permission from the database.
     *
     * @param int $permissionId
     * @return ModelResponse
     */
    public function deletePermission(int $permissionId): ModelResponse
    {
        try {
            return DB::transaction(function () use ($permissionId) {
                $permission = $this->fetch->showQuery(Permission::class, $permissionId)->firstOrFail();

                if ($this->modelUsesSoftDeletes($permission)) {
                    if ($this->modelHasColumn($permission, 'updated_by')) {
                        // record who deleted the activity log
                        $this->base->update($permission, [
                            'updated_by' => $this->currentUser->getProfileId(),
                        ]);
                    }
                }

                // automatically remove the permission from the admin role
                $this->removePermissionFromAdminRole($permission);

                $this->base->delete($permission);

                return ModelResponse::success(204, 'success', 'Permission deleted successfully!', null, $permissionId);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return ModelResponse::error($code, 'error', $th->getMessage());
        }
    }

    /**
     * Add a permission to the admin role.
     *
     * @param Permission $permission
     * @return void
     */
    private function addPermissionToAdminRole(Permission $permission)
    {
        $adminRoleId = $this->fetch->showQuery(Role::class, 1)->pluck('id')->first();

        $this->base->store(RolePermission::class, [
            'role_id' => $adminRoleId,
            'permission_id' => $permission->id,
        ]);
    }

    /**
     * Remove a permission from the admin role.
     *
     * @param Permission $permission
     * @return void
     */
    private function removePermissionFromAdminRole(Permission $permission)
    {
        $adminRoleId = $this->fetch->showQuery(Role::class, 1)->pluck('id')->first();

        $rolePermission = $this->fetch->indexQuery(RolePermission::class)
            ->where('role_id', $adminRoleId)
            ->where('permission_id', $permission->id)
            ->firstOrFail();

        $this->base->delete($rolePermission);
    }
}
