<?php

namespace App\DTOs;

use App\Models\Profile;

class ProfileDTO extends AuditableDTO
{
    /**
     * Create a new ProfileDTO instance.
     */
    public function __construct(
        public readonly ?string $avatar = null,
        public readonly ?string $first_name = null,
        public readonly ?string $middle_name = null,
        public readonly ?string $last_name = null,
        public readonly ?string $nickname = null,
        public readonly ?string $type = null,
        public readonly ?string $position = null,
        public readonly array $contact_numbers = [],
        public readonly ?int $user_id = null,
        ?int $id = null,
        ?int $created_by = null,
        ?int $updated_by = null,
    ) {
        parent::__construct($id, $created_by, $updated_by);
    }

    /**
     * Create a ProfileDTO with explicit user info.
     */
    public function withUser(int $userId): self
    {
        $data = array_merge($this->toArray(), [
            'user_id' => $userId,
        ]);

        return self::fromArray($data);
    }
}
