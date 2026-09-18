<?php

namespace App\Interfaces;

use App\Data\ModelResponse;
use App\DTOs\RoleDTO;

interface RoleInterface
{
    /**
     * Store a new role in the database.
     *
     * @param  RoleDTO $roleDTO
     * @return ModelResponse
     */
    public function storeRole(RoleDTO $roleDTO): ModelResponse;

    /**
     * Update an existing role in the database.
     *
     * @param  RoleDTO $roleDTO
     * @param  int    $roleId
     * @return ModelResponse
     */
    public function updateRole(RoleDTO $roleDTO, int $roleId): ModelResponse;

    /**
     * Delete the given role in the database.
     *
     * @param  int  $roleId
     * @return ModelResponse
     */
    public function deleteRole(int $roleId): ModelResponse;
}
