<?php

namespace App\Policies;

use App\Helpers\Helper;
use App\Models\User;
use App\Models\Role;

class RolePolicy extends BasePolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Role $role): bool
    {
        return $this->canDo($user, Helper::ACTION_TYPE_VIEW, $role);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->canDo($user, Helper::ACTION_TYPE_CREATE, Role::class);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Role $role): bool
    {
        return $this->canDo($user, Helper::ACTION_TYPE_UPDATE, $role);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Role $role): bool
    {
        return $this->canDo($user, Helper::ACTION_TYPE_DELETE, $role);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Role $role): bool
    {
        return $this->canDo($user, Helper::ACTION_TYPE_RESTORE, $role);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Role $role): bool
    {
        return $this->canDo($user, Helper::ACTION_TYPE_DELETE, $role);
    }
}
