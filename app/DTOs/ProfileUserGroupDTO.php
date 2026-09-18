<?php

namespace App\DTOs;

class ProfileUserGroupDTO extends BaseDTO
{
    /**
     * Create a new ProfileUserGroupDTO instance.
     */
    public function __construct(
        public readonly ?int $profile_id = null,
        public readonly ?int $user_group_id = null,
        ?int $id = null,
    ) {
        parent::__construct($id);
    }
}
