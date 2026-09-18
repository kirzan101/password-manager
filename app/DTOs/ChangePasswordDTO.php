<?php

namespace App\DTOs;

class ChangePasswordDTO extends BaseDTO
{
    /**
     * Create a new ChangePasswordDTO instance.
     */
    public function __construct(
        public readonly int $profile_id,
        public readonly string $current_password,
        public readonly string $new_password,
    ) {}
}
