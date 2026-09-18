<?php

namespace App\DTOs;

class FirstLoginChangePasswordDTO extends BaseDTO
{
    /**
     * Create a new FirstLoginChangePasswordDTO instance.
     */
    public function __construct(
        public readonly string $password,
        public readonly ?int $profile_id,
    ) {}

    /**
     * Set profile id
     *
     * @param integer $profile_id
     * @return self
     */
    public function withProfile(int $profile_id): self
    {
        $data = array_merge($this->toArray(), ['profile_id' => $profile_id]);

        return self::fromArray($data);
    }
}
