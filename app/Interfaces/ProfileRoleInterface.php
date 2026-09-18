<?php

namespace App\Interfaces;

use App\Data\ModelResponse;
use App\Data\StandardResponse;
use App\DTOs\ProfileRoleDTO;

interface ProfileRoleInterface
{
    /**
     * Store a new profile role in the database.
     *
     * @param  ProfileRoleDTO $profileRoleDTO
     * @return ModelResponse
     */
    public function storeProfileRole(ProfileRoleDTO $profileRoleDTO): ModelResponse;

    /**
     * Update an existing profile role in the database.
     *
     * @param  ProfileRoleDTO $profileRoleDTO
     * @param  int    $profileRoleId
     * @return ModelResponse
     */
    public function updateProfileRole(ProfileRoleDTO $profileRoleDTO, int $profileRoleId): ModelResponse;

    /**
     * Delete the given profile role in the database.
     *
     * @param  int  $profileRoleId
     * @return ModelResponse
     */
    public function deleteProfileRole(int $profileRoleId): ModelResponse;

    /**
     * Store multiple profile roles for a given profile in the database.
     *
     * @param int $profileId
     * @param array $roleIds
     * @return StandardResponse
     */
    public function storeMultipleProfileRoles(int $profileId, array $roleIds): StandardResponse;

    /**
     * Update multiple profile roles for a given profile in the database.
     *
     * @param int $profileId
     * @param array $roleIds
     * @return StandardResponse
     */
    public function updateMultipleProfileRoles(int $profileId, array $roleIds): StandardResponse;

    /**
     * Delete profile roles associated with the given role id in the database.
     *
     * @param int $roleId
     * @return StandardResponse
     */
    public function deleteProfileRolesByRoleId(int $roleId): StandardResponse;

    /**
     * Delete the given profile role in the database by profile ID.
     *
     * @param integer $profileId
     * @return StandardResponse
     */
    public function removeProfileRolesByProfileId(int $profileId): StandardResponse;
}
