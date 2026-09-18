<?php

namespace App\DTOs;

class CredentialAccessDTO extends AuditableDTO
{
    /**
     * Create a new CredentialAccessDTO instance.
     */
    public function __construct(
        public readonly int $credential_id,
        public readonly string $access_level,
        public readonly ?int $profile_id,
        public readonly ?int $user_group_id,
        ?int $id = null,
        ?int $created_by = null,
        ?int $updated_by = null
    ) {
        parent::__construct($id, $created_by, $updated_by);
    }
}
