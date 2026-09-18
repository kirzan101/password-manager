<?php

namespace App\DTOs;

class RoleDTO extends BaseDTO
{
    /**
     * Create a new RoleDTO instance.
     */
    public function __construct(
        public readonly string $name,
        public readonly ?string $description = null,
        public readonly ?int $user_group_id = null,
        public readonly bool $is_active = true,
        ?int $id = null
    ) {
        parent::__construct($id);
    }
}
