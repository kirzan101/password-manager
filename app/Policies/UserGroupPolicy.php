<?php

namespace App\Policies;

use App\Helpers\Helper;
use App\Models\User;
use App\Models\UserGroup;

class UserGroupPolicy extends BasePolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, UserGroup $userGroup): bool
    {
        return $this->canDo($user, Helper::ACTION_TYPE_VIEW, $userGroup);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->canDo($user, Helper::ACTION_TYPE_CREATE, UserGroup::class);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, UserGroup $userGroup): bool
    {
        return $this->canDo($user, Helper::ACTION_TYPE_UPDATE, $userGroup);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, UserGroup $userGroup): bool
    {
        return $this->canDo($user, Helper::ACTION_TYPE_DELETE, $userGroup);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, UserGroup $userGroup): bool
    {
        return $this->canDo($user, Helper::ACTION_TYPE_RESTORE, $userGroup);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, UserGroup $userGroup): bool
    {
        return $this->canDo($user, Helper::ACTION_TYPE_DELETE, $userGroup);
    }
}
