<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Credential extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'owner_profile_id',
        'name',
        'username',
        'secret',
        'category',
        'properties',
        'is_favorite',
        'notes',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_favorite' => 'boolean',
        'properties' => 'array',
    ];

    /**
     * Get the owner profile that owns the credential.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ownerProfile(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'owner_profile_id');
    }

    /**
     * Get the profile who created the credential.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'created_by');
    }

    /**
     * Get the profile who last updated the credential.
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'updated_by');
    }

    /**
     * Get the profile who deleted the credential.
     */
    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'deleted_by');
    }
}
