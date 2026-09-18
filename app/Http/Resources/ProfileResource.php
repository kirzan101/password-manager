<?php

namespace App\Http\Resources;

use App\Traits\ReturnDatetimeFormat;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    use ReturnDatetimeFormat;

    /**
     * Define relationships this resource may need.
     */
    public static array $relations = [
        'createdBy',
        'updatedBy',
        'user',
        'profileUserGroup',
        'profileRoles',
        'profileRoles.role'
    ];

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'full_name' => $this->getFullName(),
            'initials' => $this->getInitials(),
            'name' => $this->getName(1),
            // 'avatar' => $this->avatar ? \Illuminate\Support\Facades\Storage::url($this->avatar) : null,
            'avatar' => $this->avatar ? route('uploads.show', ['path' => $this->avatar]) : null,
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'nickname' => $this->nickname,
            'type' => $this->type,
            'position' => $this->position,
            'contact_numbers' => $this->contact_numbers,
            'user_id' => $this->user_id,
            'username' => $this->user->username,
            'email' => $this->user->email,
            'is_admin' => (bool) $this->user->is_admin,
            'is_first_login' => (bool) $this->user->is_first_login,
            'status' => $this->user->status,
            'user_group_id' => $this->profileUserGroup->user_group_id ?? null,
            'user_group_name' => $this->profileUserGroup->userGroup->name ?? null,
            'role_ids' => $this->getRoleIds(),
            'role_names' => $this->getRoleNames(),
            'last_login_at' => $this->returnShortDateTime($this->user->last_login_at),
            'created_at' => $this->returnShortDateTime($this->created_at),
            'updated_at' => $this->returnShortDateTime($this->updated_at),
            'createdBy' => $this->created_by ? $this->createdBy->getFullName() : null,
            'updatedBy' => $this->updated_by ? $this->updatedBy->getFullName() : null,
        ];
    }

    /**
     * Get the profile role_ids
     *
     * @return array
     */
    protected function getRoleIds(): array
    {
        return $this->profileRoles->pluck('role_id')->toArray();
    }

    /**
     * Get the profile role names
     *
     * @return array
     */
    protected function getRoleNames(): array
    {
        return $this->profileRoles->pluck('role.name')->toArray();
    }
}
