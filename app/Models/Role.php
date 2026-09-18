<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Cache;

class Role extends Model
{
    /**
     * The "booted" method of the model.
     *
     * This method is called when the model is booted and is used to register model event listeners.
     * In this case, it clears the roles list cache whenever a role is saved or deleted.
     */
    protected static function booted()
    {
        static::saved(fn($role) => $role->clearRoleListCache());
        static::deleted(fn($role) => $role->clearRoleListCache());
    }

    /**
     * Clear the cached list of roles.
     *
     * This method removes the "roles_list" cache entry. It is called whenever a role is saved or deleted.
     */
    public function clearRoleListCache()
    {
        Cache::forget("roles_list");
    }

    protected $fillable = [
        'name',
        'description',
        'user_group_id',
        'is_active',
    ];

    /**
     * Get the user group that owns the role.
     */
    public function userGroup(): BelongsTo
    {
        return $this->belongsTo(UserGroup::class);
    }

    /**
     * Get the permissions associated with the role.
     */
    public function rolePermissions(): HasMany
    {
        return $this->hasMany(RolePermission::class);
    }

    /**
     * Get the profile roles associated with the role.
     */
    public function profileRoles(): HasMany
    {
        return $this->hasMany(ProfileRole::class);
    }
}
