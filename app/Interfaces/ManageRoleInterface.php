<?php

namespace App\Interfaces;

use App\Data\ModelResponse;
use App\DTOs\ManageRoleDTO;

interface ManageRoleInterface
{
    /**
     * Store a new role in the database.
     *
     * @param  ManageRoleDTO $manageRoleDTO
     * @return ModelResponse
     */
    public function storeRole(ManageRoleDTO $manageRoleDTO): ModelResponse;

    /**
     * Update an existing role in the database.
     *
     * @param  ManageRoleDTO $manageRoleDTO
     * @param  int    $roleId
     * @return ModelResponse
     */
    public function updateRole(ManageRoleDTO $manageRoleDTO, int $roleId): ModelResponse;

    /**
     * Delete the given role in the database.
     *
     * @param  int  $roleId
     * @return ModelResponse
     */
    public function deleteRole(int $roleId): ModelResponse;
}
