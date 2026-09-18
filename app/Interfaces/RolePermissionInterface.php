<?php

namespace App\Interfaces;

use App\Data\CollectionResponse;
use App\Data\ModelResponse;
use App\DTOs\RolePermissionDTO;

interface RolePermissionInterface
{
    /**
     * Store a new role permission in the database.
     *
     * @param  RolePermissionDTO $rolePermissionDTO
     * @return ModelResponse
     */
    public function storeRolePermission(RolePermissionDTO $rolePermissionDTO): ModelResponse;

    /**
     * Update an existing role permission in the database.
     *
     * @param  RolePermissionDTO $rolePermissionDTO
     * @param  int    $rolePermissionId
     * @return ModelResponse
     */
    public function updateRolePermission(RolePermissionDTO $rolePermissionDTO, int $rolePermissionId): ModelResponse;

    /**
     * Delete the given role permission in the database.
     *
     * @param  int  $rolePermissionId
     * @return ModelResponse
     */
    public function deleteRolePermission(int $rolePermissionId): ModelResponse;


    /**
     * Store multiple role permissions in the database.
     *
     * This method is used to assign multiple permissions to a role. It takes an array of
     * permission IDs and a role ID, and creates a new role_permission record for each
     * permission ID with the given role ID. A permission is considered assigned to the role
     * simply by the existence of its role_permission record (no `is_active` flag involved).
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
    public function storeMultipleRolePermissions(array $permissionIds, int $roleId): CollectionResponse;

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
    public function updateMultipleRolePermissions(array $permissionIds, int $roleId): CollectionResponse;

    /**
     * Delete the role permissions associated with the given role ID in the database.
     *
     * @param int $roleId
     * @return ModelResponse
     */
    public function deleteRolePermissionByRoleId(int $roleId): ModelResponse;
}
