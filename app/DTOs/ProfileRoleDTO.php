<?php

namespace App\DTOs;

class ProfileRoleDTO extends BaseDTO
{
    /**
     * Create a new ProfileRoleDTO instance.
     */
    public function __construct(
        public readonly int $profile_id,
        public readonly int $role_id,
        ?int $id = null
    ) {
        parent::__construct($id);
    }
}
