<?php

namespace App\Models;

use App\Traits\ReturnModulePermissionTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class RolePermission extends Model
{
    use ReturnModulePermissionTrait;

    /**
     * Boot method to clear cache when permission is saved or deleted.
     *
     * @return void
     */
    protected static function booted()
    {
        static::saved(fn($rp) => $rp->clearPermissionCache());
        static::deleted(fn($rp) => $rp->clearPermissionCache());
    }

    /**
     * Clear the permission cache for the current permission.
     *
     * This method clears the cache for all profiles that have this permission.
     */
    public function clearPermissionCache()
    {
        $this->loadMissing('permission', 'role.profileRoles.profile');

        $module = $this->permission?->module;
        if (!$module) {
            return;
        }

        foreach ($this->role->profileRoles as $profileRole) {
            $profile = $profileRole->profile;
            if ($profile) {
                Cache::forget($this->getPermissionCacheKey($profile->id, $module));
            }
        }
    }

    protected $fillable = [
        'role_id',
        'permission_id',
    ];

    /**
     * associate role permission to role
     *
     * @return BelongsTo
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * associate role permission to permission
     *
     * @return BelongsTo
     */
    public function permission(): BelongsTo
    {
        return $this->belongsTo(Permission::class);
    }
}
