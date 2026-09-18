<?php

namespace App\DTOs;

class AccountDTO extends BaseDTO
{
    public function __construct(
        public readonly UserDTO $user,
        public readonly ProfileDTO $profile,
        public readonly ?int $user_group_id = null,
        public readonly array $role_ids = [],
    ) {}
}
