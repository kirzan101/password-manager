<?php

namespace App\DTOs;

class CredentialDTO extends AuditableDTO
{
    /**
     * Create a new CredentialDTO instance.
     */
    public function __construct(
        public readonly int $owner_profile_id,
        public readonly string $name,
        public readonly string $username,
        public readonly string $secret,
        public readonly bool $is_favorite = false,
        public readonly ?string $category = null,
        public readonly ?array $properties = null,
        ?int $id = null,
        ?int $created_by = null,
        ?int $updated_by = null
    ) {
        parent::__construct($id, $created_by, $updated_by);
    }
}
