<?php

namespace App\DTOs;

class BasicProfileDTO extends BaseDTO
{
    /**
     * Create a new BasicProfileDTO instance.
     */
    public function __construct(
        public readonly string $email,
        public readonly int $profile_id,
        public readonly int $user_id,
        public readonly ?string $nickname = null,
        public readonly ?string $position = null,
        public readonly ?array $contact_numbers = null
    ) {}
}
