<?php

namespace App\Interfaces;

use App\Data\ModelResponse;
use App\DTOs\UserGroupDTO;

interface UserGroupInterface
{
    /**
     * Store a new user group in the database.
     *
     * @param UserGroupDTO $userGroupDTO
     * @return ModelResponse
     */
    public function storeUserGroup(UserGroupDTO $userGroupDTO): ModelResponse;

    /**
     * update an existing user group in the database.
     *
     * @param UserGroupDTO $userGroupDTO
     * @param integer $userGroupId
     * @return ModelResponse
     */
    public function updateUserGroup(UserGroupDTO $userGroupDTO, int $userGroupId): ModelResponse;

    /**
     * delete a user group from the database.
     *
     * @param integer $userGroupId
     * @return ModelResponse
     */
    public function deleteUserGroup(int $userGroupId): ModelResponse;
}
