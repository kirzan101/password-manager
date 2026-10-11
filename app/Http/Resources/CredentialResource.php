<?php

namespace App\Http\Resources;

use App\Traits\ReturnDatetimeFormat;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CredentialResource extends JsonResource
{
    use ReturnDatetimeFormat;

    public static array $relations = [
        'ownerProfile',
        'createdBy',
        'updatedBy',
        'deletedBy',
        'credentialAccesses',
        'credentialAccesses.profile.profileUserGroup'

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
            'name' => $this->name,
            'username' => $this->username,
            'category' => $this->category,
            'properties' => $this->properties,
            'is_favorite' => $this->is_favorite,
            'notes' => $this->notes,
            'ownerName' => $this->ownerProfileName(),
            'createdByName' => $this->createdByName(),
            'updatedByName' => $this->updatedByName(),
            'deletedByName' => $this->deletedByName(),
            'created_at' => $this->returnShortDateTime($this->created_at),
            'updated_at' => $this->returnShortDateTime($this->updated_at),
            'deleted_at' => $this->returnShortDateTime($this->deleted_at),
            'current_user_access_level' => $this->currentUserAccessLevel(),
        ];
    }

    /**
     * Get the full name of the owner profile.
     *
     * @return string|null
     */
    private function ownerProfileName(): ?string
    {
        return $this->ownerProfile ? $this->ownerProfile->getFullName() : null;
    }

    /**
     * Get the full name of the user who created the credential.
     *
     * @return string|null
     */
    private function createdByName(): ?string
    {
        return $this->createdBy ? $this->createdBy->getFullName() : null;
    }

    /**
     * Get the full name of the user who last updated the credential.
     *
     * @return string|null
     */
    private function updatedByName(): ?string
    {
        return $this->updatedBy ? $this->updatedBy->getFullName() : null;
    }

    /**
     * Get the full name of the user who deleted the credential.
     *
     * @return string|null
     */
    private function deletedByName(): ?string
    {
        return $this->deletedBy ? $this->deletedBy->getFullName() : null;
    }

    /**
     * Get the highest access level of the current user for this credential.
     *
     * @return string|null
     */
    private function currentUserAccessLevel(): ?string
    {
        $profile = \Illuminate\Support\Facades\Auth::user()?->profile;

        if (!$profile) {
            return null;
        }

        $userGroupId = $profile->profileUserGroup?->user_group_id;

        if (!$userGroupId) {
            return null;
        }

        $credentialAccesses = $this->credentialAccesses()
            ->where('profile_id', $profile->id)
            ->where('user_group_id', $userGroupId)
            ->pluck('access_level');

        if ($credentialAccesses->contains(
            \App\Helpers\Helper::CREDENTIAL_ACCESS_LEVEL_MODERATOR
        )) {
            return \App\Helpers\Helper::CREDENTIAL_ACCESS_LEVEL_MODERATOR;
        }

        if ($credentialAccesses->contains(
            \App\Helpers\Helper::CREDENTIAL_ACCESS_LEVEL_EDITOR
        )) {
            return \App\Helpers\Helper::CREDENTIAL_ACCESS_LEVEL_EDITOR;
        }

        if ($credentialAccesses->contains(
            \App\Helpers\Helper::CREDENTIAL_ACCESS_LEVEL_VIEWER
        )) {
            return \App\Helpers\Helper::CREDENTIAL_ACCESS_LEVEL_VIEWER;
        }

        return null;
    }
}
