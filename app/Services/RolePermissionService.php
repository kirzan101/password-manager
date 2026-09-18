<?php

namespace App\Services;

use App\Data\CollectionResponse;
use App\Data\ModelResponse;
use App\DTOs\RolePermissionDTO;
use App\Helpers\Helper;
use App\Interfaces\RolePermissionInterface;
use App\Traits\HttpErrorCodeTrait;
use App\Traits\ReturnModelCollectionTrait;
use App\Traits\ReturnModelTrait;
use App\Interfaces\CurrentUserInterface;
use App\Interfaces\BaseInterface;
use App\Interfaces\FetchInterfaces\BaseFetchInterface;
use App\Traits\CheckIfColumnExistsTrait;
use App\Traits\DetectsSoftDeletesTrait;
use App\Traits\EnsureDataTrait;
use App\Traits\EnsureSuccessTrait;
use Illuminate\Support\Facades\DB;
use App\Models\RolePermission;

class RolePermissionService implements RolePermissionInterface
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
        private CurrentUserInterface $currentUser
    ) {}

    /**
     * Store a new role permission in the database.
     *
     * @param RolePermissionDTO $rolePermissionDTO
     * @return ModelResponse
     */
    public function storeRolePermission(RolePermissionDTO $rolePermissionDTO): ModelResponse
    {
        try {
            return DB::transaction(function () use ($rolePermissionDTO) {

                $rolePermissionData = $rolePermissionDTO->toArray();
                $rolePermission = $this->base->store(RolePermission::class, $rolePermissionData);

                return ModelResponse::success(201, Helper::SUCCESS, 'Role permission created successfully!', $rolePermission, $rolePermission->id);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return ModelResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Update an existing role permission in the database.
     *
     * @param RolePermissionDTO $rolePermissionDTO
     * @param int $rolePermissionId
     * @return ModelResponse
     */
    public function updateRolePermission(RolePermissionDTO $rolePermissionDTO, int $rolePermissionId): ModelResponse
    {
        try {
            return DB::transaction(function () use ($rolePermissionDTO, $rolePermissionId) {
                $rolePermission = $this->fetch->showQuery(RolePermission::class, $rolePermissionId)->firstOrFail();

                $rolePermissionDTO = RolePermissionDTO::fromModel($rolePermission, $rolePermissionDTO->toArray());

                $rolePermissionData = $rolePermissionDTO->toArray();
                $rolePermission = $this->base->update($rolePermission, $rolePermissionData);

                return ModelResponse::success(200, Helper::SUCCESS, 'Role permission updated successfully!', $rolePermission, $rolePermissionId);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return ModelResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Delete the given role permission in the database.
     *
     * @param int $rolePermissionId
     * @return ModelResponse
     */
    public function deleteRolePermission(int $rolePermissionId): ModelResponse
    {
        try {
            return DB::transaction(function () use ($rolePermissionId) {
                $rolePermission = $this->fetch->showQuery(RolePermission::class, $rolePermissionId)->firstOrFail();

                $this->base->delete($rolePermission);

                return ModelResponse::success(204, Helper::SUCCESS, 'Role permission deleted successfully!', null, $rolePermissionId);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return ModelResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Store multiple role permissions in the database.
     *
     * This method is used to assign multiple permissions to a role. It takes an array of
     * permission IDs and a role ID, and creates a new role_permission record for each
     * permission ID with the given role ID. A permission is considered assigned to the role
     * simply by the existence of its role_permission record.
     *
     * Process Overview:
     * - Build a role_permission row for every provided permission ID.
     * - Store the role permissions in the database using the base interface's storeMultiple method.
     * - Return a CollectionResponse with the created role permissions.
     *
     * @param array $permissionIds
     * @param int $roleId
     * @return CollectionResponse
     */
    public function storeMultipleRolePermissions(array $permissionIds, int $roleId): CollectionResponse
    {
        try {
            return DB::transaction(function () use ($permissionIds, $roleId) {
                $permissionIds = array_unique($permissionIds);

                $rolePermissionsData = array_map(fn($permissionId) => [
                    'role_id' => $roleId,
                    'permission_id' => $permissionId,
                ], $permissionIds);

                if (!empty($rolePermissionsData)) {
                    $this->base->storeMultiple(RolePermission::class, $rolePermissionsData);
                }

                $rolePermissions = $this->fetch->indexQuery(RolePermission::class)
                    ->where('role_id', $roleId)
                    ->get();

                return CollectionResponse::success(201, Helper::SUCCESS, 'Role permissions created successfully!', $rolePermissions);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return CollectionResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Update multiple role permissions in the database.
     *
     * This method synchronizes the permissions assigned to a role with the given array of
     * permission IDs. Permissions no longer present in the array are removed (their
     * role_permission record is deleted), and permissions newly present in the array are
     * added (a new role_permission record is created). Existing, unchanged assignments are
     * left untouched.
     *
     * Process Overview:
     * - Fetch the permission IDs currently assigned to the given role ID.
     * - Determine which permission IDs need to be added and which need to be removed.
     * - Delete the role_permission records for removed permissions.
     * - Create role_permission records for newly added permissions.
     * - Return a CollectionResponse with the role's current role permissions.
     *
     * @param array $permissionIds
     * @param int $roleId
     * @return CollectionResponse
     */
    public function updateMultipleRolePermissions(array $permissionIds, int $roleId): CollectionResponse
    {
        try {
            return DB::transaction(function () use ($permissionIds, $roleId) {
                $permissionIds = array_unique($permissionIds);

                $existingPermissionIds = $this->fetch->indexQuery(RolePermission::class)
                    ->where('role_id', $roleId)
                    ->pluck('permission_id')
                    ->toArray();

                $permissionIdsToAdd = array_diff($permissionIds, $existingPermissionIds);
                $permissionIdsToRemove = array_diff($existingPermissionIds, $permissionIds);

                // Remove permissions that are no longer assigned to the role
                if (!empty($permissionIdsToRemove)) {
                    $this->fetch->indexQuery(RolePermission::class)
                        ->where('role_id', $roleId)
                        ->whereIn('permission_id', $permissionIdsToRemove)
                        ->delete();
                }

                // Create role_permission records for the newly assigned permissions
                if (!empty($permissionIdsToAdd)) {
                    $rolePermissionsData = array_map(fn($permissionId) => [
                        'role_id' => $roleId,
                        'permission_id' => $permissionId,
                    ], array_values($permissionIdsToAdd));

                    $this->base->storeMultiple(RolePermission::class, $rolePermissionsData);
                }

                $rolePermissions = $this->fetch->indexQuery(RolePermission::class)
                    ->where('role_id', $roleId)
                    ->get();

                return CollectionResponse::success(200, Helper::SUCCESS, 'Role permissions updated successfully!', $rolePermissions);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return CollectionResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Delete role permissions by role ID in the database.
     *
     * @param int $roleId
     * @return ModelResponse
     */
    public function deleteRolePermissionByRoleId(int $roleId): ModelResponse
    {
        try {
            return DB::transaction(function () use ($roleId) {

                $deleteResponse = $this->fetch->indexQuery(RolePermission::class)
                    ->where('role_id', $roleId)
                    ->delete();

                if ($deleteResponse === false) {
                    throw new \Exception('Failed to delete role permissions for the role.');
                }

                return ModelResponse::success(204, Helper::SUCCESS, 'Role permissions deleted successfully!', null, $roleId);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return ModelResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }
}
