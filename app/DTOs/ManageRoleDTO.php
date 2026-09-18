<?php

namespace App\DTOs;

class ManageRoleDTO extends BaseDTO
{
    /**
     * Create a new ManageRolePermissionDTO instance.
     */
    public function __construct(
        public readonly string $name,
        public readonly ?string $description = null,
        public readonly ?int $user_group_id = null,
        public readonly bool $is_active = true,
        public readonly ?array $permissionIds = [],
    ) {}

    /**
     * Create a new ManageRolePermissionDTO instance with updated permission IDs.
     *
     * @param array|null $permissionIds
     * @return self
     */
    public function withPermissionIds(?array $permissionIds): self
    {
        $data = array_merge($this->toArray(), ['permissionIds' => $permissionIds]);

        return self::fromArray($data);
    }
}
