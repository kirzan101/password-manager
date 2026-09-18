<?php

namespace App\Policies;

use App\Helpers\Helper;
use App\Models\User;
use App\Models\Permission;

class PermissionPolicy extends BasePolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Permission $permission): bool
    {
        return $this->canDo($user, Helper::ACTION_TYPE_VIEW, $permission);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->canDo($user, Helper::ACTION_TYPE_CREATE, Permission::class);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Permission $permission): bool
    {
        return $this->canDo($user, Helper::ACTION_TYPE_UPDATE, $permission);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Permission $permission): bool
    {
        return $this->canDo($user, Helper::ACTION_TYPE_DELETE, $permission);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Permission $permission): bool
    {
        return $this->canDo($user, Helper::ACTION_TYPE_RESTORE, $permission);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Permission $permission): bool
    {
        return $this->canDo($user, Helper::ACTION_TYPE_DELETE, $permission);
    }
}