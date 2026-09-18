<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserGroup extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'created_by',
        'updated_by',
    ];

    /**
     * Get the profile that created this profile.
     *
     * @return BelongsTo
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'created_by');
    }

    /**
     * Get the profile that last updated this profile.
     *
     * @return BelongsTo
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'updated_by');
    }

    /**
     * Get the profile user groups associated with the user group.
     *
     * @return HasMany
     */
    public function profileUserGroups(): HasMany
    {
        return $this->hasMany(ProfileUserGroup::class, 'user_group_id');
    }
}
