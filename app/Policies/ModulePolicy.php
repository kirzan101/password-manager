<?php

namespace App\Policies;

use App\Helpers\Helper;
use App\Models\User;
use App\Models\Module;

class ModulePolicy extends BasePolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Module $module): bool
    {
        return $this->canDo($user, Helper::ACTION_TYPE_VIEW, $module);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->canDo($user, Helper::ACTION_TYPE_CREATE, Module::class);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Module $module): bool
    {
        return $this->canDo($user, Helper::ACTION_TYPE_UPDATE, $module);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Module $module): bool
    {
        return $this->canDo($user, Helper::ACTION_TYPE_DELETE, $module);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Module $module): bool
    {
        return $this->canDo($user, Helper::ACTION_TYPE_RESTORE, $module);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Module $module): bool
    {
        return $this->canDo($user, Helper::ACTION_TYPE_DELETE, $module);
    }
}
