<?php

namespace App\Policies;

use App\Helpers\Helper;
use App\Models\User;

class UserPolicy extends BasePolicy
{
    /**
     * View user.
     */
    public function view(User $user, User $profile): bool
    {
        return $this->canDo($user, Helper::ACTION_TYPE_VIEW, $profile);
    }

    /**
     * Create user.
     */
    public function create(User $user): bool
    {
        return $this->canDo($user, Helper::ACTION_TYPE_CREATE, User::class);
    }

    /**
     * Update user.
     */
    public function update(User $user, User $profile): bool
    {
        return $this->canDo($user, Helper::ACTION_TYPE_UPDATE, $profile);
    }

    /**
     * Delete user.
     */
    public function delete(User $user, User $profile): bool
    {
        return $this->canDo($user, Helper::ACTION_TYPE_DELETE, $profile);
    }

    /**
     * Restore user.
     */
    public function restore(User $user, User $profile): bool
    {
        return $this->canDo($user, Helper::ACTION_TYPE_RESTORE, $profile);
    }

    /**
     * Force delete user.
     */
    public function forceDelete(User $user, User $profile): bool
    {
        return $this->canDo($user, Helper::ACTION_TYPE_DELETE, $profile);
    }

    /**
     * Reset user password.
     */
    public function reset(User $user, User $profile): bool
    {
        return $this->canDo($user, Helper::ACTION_TYPE_RESET, $profile);
    }

    /**
     * Set user account status.
     */
    public function setStatus(User $user, User $profile): bool
    {
        return $this->canDo($user, Helper::ACTION_TYPE_SET_STATUS, $profile);
    }

    /**
     * Set user avatar.
     */
    public function setAvatar(User $user, User $profile): bool
    {
        return $this->canDo($user, Helper::ACTION_TYPE_SET_AVATAR, $profile);
    }
}
