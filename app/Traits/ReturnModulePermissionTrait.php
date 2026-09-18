<?php

namespace App\Traits;

use App\Models\Profile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

trait ReturnModulePermissionTrait
{
    /**
     * Get permission types for a given profile and module.
     *
     * This method retrieves permissions assigned to the specified profile
     * through its roles. It filters permissions by:
     * - Module name (derived from the given model's table)
     * - Roles that have the permission assigned (role_permission record exists) and active permissions
     *
     * Results are cached for 60 minutes using a profile + module-based key.
     *
     * @param \Illuminate\Database\Eloquent\Model $model The model used to determine the module (via table name).
     * @param int $currentProfileId The profile ID to evaluate.
     *
     * @return array List of unique permission types (e.g. ['view-profiles', 'update-profiles', 'delete-profiles']).
     *               Returns an empty array if the profile is not found or has no permissions.
     */
    public function returnPermissions(Model $model, int $currentProfileId): array
    {
        // Use the profile + module name as a unique cache key
        $moduleName = $model->getTable();
        // $version = Cache::get("permissions.version.$currentProfileId", 1);
        $version = Cache::get("permissions.version.global", 1);

        $cacheKey = "access.profile.$currentProfileId.module.$moduleName.v$version";

        return Cache::remember($cacheKey, now()->addMinutes(60), function () use ($model, $currentProfileId, $moduleName) {
            // Load the profile with its associated roles and permissions
            $profile = Profile::with(['profileRoles.role.rolePermissions.permission'])
                ->find($currentProfileId);

            if (!$profile?->profileRoles) {
                return [];
            }

            $permissionLists = $profile->profileRoles
                ->map(fn($pr) => $pr->role)
                ->filter()
                ->map(fn($role) => $role->rolePermissions)
                ->flatten()
                ->map(fn($rp) => $rp->permission)
                ->filter()
                ->filter(fn($perm) => $perm->module === $moduleName && $perm->is_active)
                ->pluck('type')
                ->unique()
                ->toArray();

            // add model name to the permission (e.g. 'view-profiles', 'update-profiles')
            return array_map(fn($perm) => "{$perm}-{$moduleName}", $permissionLists);
        });
    }

    /**
     * Get custom permissions for a given profile and module.
     *
     * This method is similar to returnPermissions but allows specifying the module name directly,
     * rather than deriving it from a model. It retrieves permissions assigned to the specified profile
     * through its roles, considering only permissions assigned to the role (role_permission record exists) and active permissions.
     *
     * Results are cached for 60 minutes using a profile + module-based key.
     *
     * @param string $module The name of the module for which permissions should be checked.
     * @param int $currentProfileId The profile ID to evaluate.
     *
     * @return array List of unique permission types (e.g. ['view-user_management', 'update-user_management', 'delete-user_management']).
     *               Returns an empty array if the profile is not found or has no permissions.
     */
    public function returnCustomPermissions(string $module, int $currentProfileId): array
    {
        // Ensure the module name is in snake_case format (e.g. 'user_management' instead of 'UserManagement')
        $module = Str::snake(Str::plural(Str::lower($module))); // Convert to plural snake_case (e.g. 'user_management')

        // $version = Cache::get("permissions.version.$currentProfileId", 1);
        $version = Cache::get("permissions.version.global", 1);
        $cacheKey = "access.profile.$currentProfileId.module.$module.v$version";

        return Cache::remember($cacheKey, now()->addMinutes(60), function () use ($module, $currentProfileId) {
            // Load the profile with its associated roles and permissions
            $profile = Profile::with(['profileRoles.role.rolePermissions.permission'])
                ->find($currentProfileId);

            if (!$profile?->profileRoles) {
                return [];
            }

            $permissionLists = $profile->profileRoles
                ->map(fn($pr) => $pr->role)
                ->filter()
                ->map(fn($role) => $role->rolePermissions)
                ->flatten()
                ->map(fn($rp) => $rp->permission)
                ->filter()
                ->filter(fn($perm) => $perm->module === $module && $perm->is_active)
                ->pluck('type')
                ->unique()
                ->toArray();

            // add model name to the permission (e.g. 'view-user_management', 'update-user_management')
            return array_map(fn($perm) => "{$perm}-{$module}", $permissionLists);
        });
    }

    /**
     * Returns the cache key for permissions based on profile ID and module name.
     *
     * This method is used to generate a unique cache key for storing and retrieving permissions.
     *
     * @param int $profileId The ID of the profile for which permissions should be checked.
     * @param string $module The name of the module for which permissions should be checked.
     *
     * @return string The cache key for the specified profile and module.
     */
    protected function getPermissionCacheKey(int $profileId, string $module): string
    {
        // $version = Cache::get("permissions.version.$profileId", 1);
        $version = Cache::get("permissions.version.global", 1);
        return "access.profile.$profileId.module.$module.v$version";
    }
}
