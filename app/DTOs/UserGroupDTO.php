<?php

namespace App\DTOs;

class UserGroupDTO extends AuditableDTO
{
    /*
    * Create a new UserGroupDTO instance.
    */
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $code = null,
        public readonly ?string $description = null,
        ?int $id = null,
        ?int $created_by = null,
        ?int $updated_by = null,
    ) {
        parent::__construct($id, $created_by, $updated_by);
    }
}
