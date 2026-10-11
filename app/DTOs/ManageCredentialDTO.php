<?php

namespace App\DTOs;

class ManageCredentialDTO extends AuditableDTO
{
    /**
     * Create a new ManageCredentialDTO instance.
     */
    public function __construct(
        public readonly int $owner_profile_id,
        public readonly string $name,
        public readonly string $username,
        public readonly string $secret,
        public readonly bool $is_favorite = false,
        public readonly ?string $category = null,
        public readonly ?array $properties = null,
        public readonly ?array $access = null,
        ?int $id = null,
        ?int $created_by = null,
        ?int $updated_by = null
    ) {
        parent::__construct($id, $created_by, $updated_by);
    }

    /**
     * Return a new instance with the specified category.
     */
    public function withCategory(?string $category): self
    {
        $data = array_merge($this->toArray(), [
            'category' => $category,
        ]);

        return self::fromArray($data);
    }

    /**
     * Return a new instance with the specified properties.
     */
    public function withProperties(?array $properties): self
    {
        $data = array_merge($this->toArray(), [
            'properties' => $properties,
        ]);

        return self::fromArray($data);
    }

    /**
     * Return a new instance with the specified access.
     */
    public function withAccess(?array $access): self
    {
        $data = array_merge($this->toArray(), [
            'access' => $access,
        ]);

        return self::fromArray($data);
    }
}
