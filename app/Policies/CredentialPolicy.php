<?php

namespace App\Policies;

use App\Helpers\Helper;
use App\Models\User;
use App\Models\Credential;

class CredentialPolicy extends BasePolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Credential $credential): bool
    {
        return $this->canDo($user, Helper::ACTION_TYPE_VIEW, $credential);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->canDo($user, Helper::ACTION_TYPE_CREATE, Credential::class);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Credential $credential): bool
    {
        return $this->canDo($user, Helper::ACTION_TYPE_UPDATE, $credential);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Credential $credential): bool
    {
        return $this->canDo($user, Helper::ACTION_TYPE_DELETE, $credential);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Credential $credential): bool
    {
        return $this->canDo($user, Helper::ACTION_TYPE_RESTORE, $credential);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Credential $credential): bool
    {
        return $this->canDo($user, Helper::ACTION_TYPE_DELETE, $credential);
    }
}