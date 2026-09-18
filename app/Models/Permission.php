<?php

namespace App\Models;

use App\Traits\ReturnModulePermissionTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class Permission extends Model
{
    use ReturnModulePermissionTrait;

    /**
     * Boot method to clear cache when permission is saved or deleted.
     *
     * @return void
     */
    protected static function booted()
    {
        static::saved(fn($perm) => $perm->clearPermissionCache());
        static::deleted(fn($perm) => $perm->clearPermissionCache());
    }

    /**
     * Clear the permission cache for the current permission.
     *
     * This method clears the cache for all profiles that have this permission.
     */
    public function clearPermissionCache()
    {
        $module = $this->module;

        // Clear cache for all profiles that have this permission
        $profileIds = $this->rolePermissions()->with('role.profileRoles.profile')
            ->get()
            ->pluck('role.profileRoles.*.profile.id')
            ->flatten()
            ->unique();

        foreach ($profileIds as $profileId) {
            Cache::forget($this->getPermissionCacheKey($profileId, $module));
        }

        // Clear the permission fetch list cache
        Cache::forget('permission_fetch_list');
    }

    protected $fillable = [
        'module',
        'type',
        'is_active',
    ];

    /**
     * Get the role permissions associated with the permission.
     */
    public function rolePermissions(): HasMany
    {
        return $this->hasMany(RolePermission::class, 'permission_id');
    }
}
