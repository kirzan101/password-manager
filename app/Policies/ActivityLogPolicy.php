<?php

namespace App\Policies;

use App\Helpers\Helper;
use App\Models\User;
use App\Models\ActivityLog;

class ActivityLogPolicy extends BasePolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ActivityLog $activityLog): bool
    {
        return $this->canDo($user, Helper::ACTION_TYPE_VIEW, $activityLog);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->canDo($user, Helper::ACTION_TYPE_CREATE, ActivityLog::class);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ActivityLog $activityLog): bool
    {
        return $this->canDo($user, Helper::ACTION_TYPE_UPDATE, $activityLog);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ActivityLog $activityLog): bool
    {
        return $this->canDo($user, Helper::ACTION_TYPE_DELETE, $activityLog);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ActivityLog $activityLog): bool
    {
        return $this->canDo($user, Helper::ACTION_TYPE_RESTORE, $activityLog);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ActivityLog $activityLog): bool
    {
        return $this->canDo($user, Helper::ACTION_TYPE_DELETE, $activityLog);
    }
}