<?php

namespace App\Interfaces;

use App\Data\ModelResponse;
use App\DTOs\PermissionDTO;

interface PermissionInterface
{
    /**
     * Store a new permission in the database.
     *
     * @param PermissionDTO $permissionDTO
     * @return ModelResponse
     */
    public function storePermission(PermissionDTO $permissionDTO): ModelResponse;

    /**
     * update an existing permission in the database.
     *
     * @param PermissionDTO $permissionDTO
     * @param integer $permissionId
     * @return ModelResponse
     */
    public function updatePermission(PermissionDTO $permissionDTO, int $permissionId): ModelResponse;

    /**
     * delete a permission from the database.
     *
     * @param integer $permissionId
     * @return ModelResponse
     */
    public function deletePermission(int $permissionId): ModelResponse;
}
