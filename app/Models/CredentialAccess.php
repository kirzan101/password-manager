<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class CredentialAccess extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'credential_id',
        'profile_id',
        'user_group_id',
        'access_level',
        'created_by',
        'updated_by',
    ];

    /**
     * Get the credential that owns the access.
     */
    public function credential(): BelongsTo
    {
        return $this->belongsTo(Credential::class);
    }

    /**
     * Get the profile that owns the access.
     */
    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    /**
     * Get the user group that owns the access.
     */
    public function userGroup(): BelongsTo
    {
        return $this->belongsTo(UserGroup::class);
    }

    /**
     * Get the profile who created the access.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'created_by');
    }

    /**
     * Get the profile who last updated the access.
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'updated_by');
    }
}
