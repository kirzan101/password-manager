<?php

namespace App\DTOs;

class RolePermissionDTO extends BaseDTO
{
    /**
     * Create a new RolePermissionDTO instance.
     */
    public function __construct(
        public readonly int $role_id,
        public readonly int $permission_id,
        ?int $id = null
    ) {
        parent::__construct($id);
    }
}
