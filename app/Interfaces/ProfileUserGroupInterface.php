<?php

namespace App\Interfaces;

use App\Data\ModelResponse;
use App\DTOs\ProfileUserGroupDTO;

interface ProfileUserGroupInterface
{
    /**
     * Store a new profile user group in the database.
     *
     * @param  ProfileUserGroupDTO  $profileUserGroupDTO
     * @return ModelResponse
     */
    public function storeProfileUserGroup(ProfileUserGroupDTO $profileUserGroupDTO): ModelResponse;

    /**
     * Update an existing profile user group in the database.
     *
     * @param  ProfileUserGroupDTO  $profileUserGroupDTO
     * @param  int    $profileUserGroupId
     * @return ModelResponse
     */
    public function updateProfileUserGroup(ProfileUserGroupDTO $profileUserGroupDTO, int $profileUserGroupId): ModelResponse;

    /**
     * Update an existing profile user group in the database using profile id.
     * 
     * @param ProfileUserGroupDTO $profileUserGroupDTO
     * @param int $profileId
     * @return ModelResponse
     */
    public function updateProfileUserGroupWithProfileId(ProfileUserGroupDTO $profileUserGroupDTO, int $profileId): ModelResponse;

    /**
     * Delete the given profile user group in the database.
     *
     * @param  int  $profileUserGroupId
     * @return ModelResponse
     */
    public function deleteProfileUserGroup(int $profileUserGroupId): ModelResponse;

    /**
     * Delete the given profile user group in the database with profile id.
     * 
     * @param int $profileId
     * @return ModelResponse
     */
    public function deleteProfileUserGroupWithProfileId(int $profileId): ModelResponse;
}
