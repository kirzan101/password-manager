<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfileUserGroup extends Model
{
    protected $fillable = [
        'profile_id',
        'user_group_id',
    ];

    /**
     * associate ProfileUserGroup with Profile model.
     *
     * @return BelongsTo
     */
    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    /**
     * associate ProfileUserGroup with UserGroup model.
     *
     * @return BelongsTo
     */
    public function userGroup(): BelongsTo
    {
        return $this->belongsTo(UserGroup::class);
    }
}
