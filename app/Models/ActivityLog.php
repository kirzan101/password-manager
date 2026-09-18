<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'module',
        'description',
        'status',
        'type',
        'properties',
        'old_properties',
        'processed_by',
    ];

    protected $casts = [
        'properties' => 'array', // Store properties as an array
        'old_properties' => 'array', // Store old_properties as an array
    ];

    /**
     * Get the profile that processed this activity log.
     *
     * @return BelongsTo
     */
    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'processed_by');
    }
}
