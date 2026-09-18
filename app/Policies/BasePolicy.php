<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Traits\ReturnModulePermissionTrait;

abstract class BasePolicy
{
    use ReturnModulePermissionTrait;

    /**
     * Cache permissions per request.
     */
    protected array $permissionCache = [];

    /**
     * Get allowed permissions for a user's profile on a given model.
     */
    protected function getCan(User $user, Model $model): array
    {
        $profileId = $user->profile?->id;

        if (!$profileId) {
            return [];
        }

        $key = $user->id . '-' . $model->getTable();

        if (!isset($this->permissionCache[$key])) {
            $this->permissionCache[$key] = $this->returnPermissions($model, $profileId);
        }

        return $this->permissionCache[$key];
    }

    /**
     * Build permission string (e.g. "view-users").
     */
    protected function getConcatenatedPermission(string $action, Model $model): string
    {
        return Str::kebab("{$action}-{$model->getTable()}");
    }

    /**
     * Central permission checker.
     */
    protected function canDo(User $user, string $action, string|Model $model): bool
    {
        if (is_string($model)) {
            $model = new $model;
        }

        if ($user->is_admin) {
            return true;
        }

        return in_array(
            $this->getConcatenatedPermission($action, $model),
            $this->getCan($user, $model)
        );
    }
}
