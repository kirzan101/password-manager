<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Profile extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'avatar',
        'first_name',
        'middle_name',
        'last_name',
        'nickname',
        'position',
        'type',
        'contact_numbers',
        'user_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'contact_numbers' => 'array', // Store contact numbers as an array
    ];

    /**
     * Get the user that owns the profile.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

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
     * Get the profile user group associated with the profile.
     *
     * @return HasOne
     */
    public function profileUserGroup(): HasOne
    {
        return $this->hasOne(ProfileUserGroup::class);
    }

    /**
     * Get the profile roles associated with the profile.
     *
     * @return HasMany
     */
    public function profileRoles(): HasMany
    {
        return $this->hasMany(ProfileRole::class);
    }

    /**
     * Get the full name of the profile, with the middle name shortened to its initials.
     *
     * Example:
     *  - middle_name: "San Jose" → "S.J."
     *
     * @return string
     */
    public function getFullName(): string
    {
        $middleInitials = '';
        if (!empty($this->middle_name)) {
            $words = preg_split('/\s+/', trim($this->middle_name));
            $initials = array_map(fn($word) => strtoupper($word[0]), $words);
            $middleInitials = implode('.', $initials) . '.';

            return trim(sprintf(
                '%s %s %s',
                $this->first_name,
                $middleInitials,
                $this->last_name
            ));
        }

        return trim(sprintf(
            '%s %s',
            $this->first_name,
            $this->last_name
        ));
    }

    /**
     * Return the person's name in different formats based on the given option.
     *
     * Options:
     *   1: "First Last"                      (e.g., Lalatina Dustiness)
     *   2: "Last, First"                     (e.g., Dustiness, Ford)
     *   3: "Last, First Middle"              (e.g., Dustiness, Lalatina Ford)
     *   4: "Last, First M.I."                (e.g., Dustiness, Lalatina F.)
     *   Other: Defaults to getFullName()
     *
     * @param  int|null  $format  The desired format style (1–4).
     * @return string  The formatted name.
     */
    public function getName(?int $format = 1): string
    {
        $middleInitials = '';
        if (!empty($this->middle_name)) {
            $words = preg_split('/\s+/', trim($this->middle_name));
            $initials = array_map(fn($word) => strtoupper($word[0]), $words);
            $middleInitials = implode('.', $initials) . '.';
        }

        return match ($format) {
            1 => trim(sprintf('%s %s', $this->first_name, $this->last_name)),
            2 => trim(sprintf('%s, %s', $this->last_name, $this->first_name)),
            3 => trim(sprintf(
                '%s, %s %s',
                $this->last_name,
                $this->first_name,
                $this->middle_name ?? ''
            )),
            4 => trim(sprintf(
                '%s, %s %s',
                $this->last_name,
                $this->first_name,
                $middleInitials
            )),
            default => $this->getFullName(),
        };
    }

    /**
     * Get the initials of the profile's first and last name.
     *
     * @return string
     */
    public function getInitials(): string
    {
        $initials = strtoupper($this->first_name[0] ?? '') . strtoupper($this->last_name[0] ?? '');
        return $initials ?: 'NA';
    }
}
