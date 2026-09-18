<?php

namespace App\Services;

use App\Data\ModelResponse;
use App\DTOs\ManageRoleDTO;
use App\DTOs\RoleDTO;
use App\Helpers\Helper;
use App\Interfaces\ManageRoleInterface;
use App\Traits\HttpErrorCodeTrait;
use App\Traits\ReturnModelCollectionTrait;
use App\Traits\ReturnModelTrait;
use App\Interfaces\CurrentUserInterface;
use App\Interfaces\BaseInterface;
use App\Interfaces\FetchInterfaces\BaseFetchInterface;
use App\Interfaces\ProfileRoleInterface;
use App\Interfaces\RoleInterface;
use App\Interfaces\RolePermissionInterface;
use App\Traits\CheckIfColumnExistsTrait;
use App\Traits\DetectsSoftDeletesTrait;
use App\Traits\EnsureDataTrait;
use App\Traits\EnsureSuccessTrait;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ManageRoleService implements ManageRoleInterface
{
    use HttpErrorCodeTrait,
        ReturnModelCollectionTrait,
        ReturnModelTrait,
        DetectsSoftDeletesTrait,
        CheckIfColumnExistsTrait,
        EnsureSuccessTrait,
        EnsureDataTrait;

    public function __construct(
        private BaseInterface $base,
        private BaseFetchInterface $fetch,
        private CurrentUserInterface $currentUser,
        private RoleInterface $role,
        private RolePermissionInterface $rolePermission,
        private ProfileRoleInterface $profileRole
    ) {}

    /**
     * Store a new role in the database.
     *
     * @param ManageRoleDTO $manageRoleDTO
     * @return ModelResponse
     */
    public function storeRole(ManageRoleDTO $manageRoleDTO): ModelResponse
    {
        try {
            return DB::transaction(function () use ($manageRoleDTO) {
                // Create the role
                $roleDTO = RoleDTO::fromArray([
                    'name' => $manageRoleDTO->name,
                    'description' => $manageRoleDTO->description,
                ]);
                $roleResponse = $this->role->storeRole($roleDTO);
                $this->ensureSuccess($roleResponse->toArray(), 'Failed to create role.');

                $role = $roleResponse->data;
                $this->ensureModel($role, 'Failed to create role.');

                // Assign permissions to the role
                $rolePermissionResponse = $this->rolePermission->storeMultipleRolePermissions($manageRoleDTO->permissionIds, $role->id);
                $this->ensureSuccess($rolePermissionResponse->toArray(), 'Failed to assign permissions to role.');

                return ModelResponse::success(201, Helper::SUCCESS, 'Role created successfully!', $role, $role->id);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return ModelResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Update an existing role in the database.
     *
     * @param ManageRoleDTO $manageRoleDTO
     * @param int $roleId
     * @return ModelResponse
     */
    public function updateRole(ManageRoleDTO $manageRoleDTO, int $roleId): ModelResponse
    {
        try {
            return DB::transaction(function () use ($manageRoleDTO, $roleId) {

                //Update the role
                $roleDTO = RoleDTO::fromArray([
                    'name' => $manageRoleDTO->name,
                    'description' => $manageRoleDTO->description,
                    'is_active' => $manageRoleDTO->is_active ?? true,
                ]);
                $roleResponse = $this->role->updateRole($roleDTO, $roleId);
                $this->ensureSuccess($roleResponse->toArray(), 'Failed to update role.');

                $role = $roleResponse->data;
                $this->ensureModel($role, 'Failed to update role.');

                // if role is deactivated, delete the profile roles associated with the role
                if (!$manageRoleDTO->is_active) {
                    $deleteProfileRolesResponse = $this->profileRole->deleteProfileRolesByRoleId($roleId);
                    $this->ensureSuccess($deleteProfileRolesResponse->toArray(), 'Failed to delete profile roles associated with the role.');
                } else { // if role is activated, update the role permissions associated with the role

                    // Then update the selected permissions to active
                    $rolePermissionResponse = $this->rolePermission->updateMultipleRolePermissions($manageRoleDTO->permissionIds, $roleId);
                    $this->ensureSuccess($rolePermissionResponse->toArray(), 'Failed to sync permissions to role.');

                    // update the permissions version for the profile to invalidate the cache
                    $this->refreshGlobalPermissionsVersion();
                }
                return ModelResponse::success(200, Helper::SUCCESS, 'Role updated successfully!', $role, $roleId);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return ModelResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Delete the given role in the database.
     *
     * @param int $roleId
     * @return ModelResponse
     */
    public function deleteRole(int $roleId): ModelResponse
    {
        try {
            return DB::transaction(function () use ($roleId) {
                // Delete the permissions associated with the role
                $rolePermissionResponse = $this->rolePermission->deleteRolePermissionByRoleId($roleId);
                $this->ensureSuccess($rolePermissionResponse->toArray(), 'Failed to delete role permissions.');

                // Delete the role itself
                $roleResponse = $this->role->deleteRole($roleId);
                $this->ensureSuccess($roleResponse->toArray(), 'Failed to delete role.');

                return ModelResponse::success(204, Helper::SUCCESS, 'Role & permissions deleted successfully!', null, $roleId);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return ModelResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Refresh the global permissions version.
     *
     * This method increments the global permissions version in the cache. It is used to invalidate
     * cached module access data for all users when permissions are updated.
     */
    protected function refreshGlobalPermissionsVersion(): void
    {
        // if no cache exists for the global permissions version, initialize it to 1
        if (!Cache::has("permissions.version.global")) {
            Cache::add("permissions.version.global", 1, now()->addYears(10));
        }

        Cache::increment("permissions.version.global");
    }
}
