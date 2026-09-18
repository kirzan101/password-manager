<?php

namespace App\Interfaces;

use App\Data\ModelResponse;
use App\DTOs\AccountDTO;
use App\DTOs\ChangePasswordDTO;
use App\DTOs\FirstLoginChangePasswordDTO;
use App\DTOs\UserDTO;
use App\DTOs\BasicProfileDTO;
use Illuminate\Http\UploadedFile;

interface ManageAccountInterface
{
    /**
     * Register a new user with profile.
     *
     * @param AccountDTO $accountDTO
     * @return ModelResponse
     * @throws \Throwable
     */
    public function register(AccountDTO $accountDTO): ModelResponse;

    /**
     * Update the authenticated user's profile.
     *
     * @param AccountDTO $accountDTO
     * @param int $profileId
     * @return ModelResponse
     */
    public function updateUserProfile(AccountDTO $accountDTO, int $profileId): ModelResponse;

    /**
     * Update the basic profile information for a user.
     *
     * @param BasicProfileDTO $basicProfileDTO
     * @return ModelResponse
     */
    public function updateBasicProfile(BasicProfileDTO $basicProfileDTO): ModelResponse;

    /**
     * Change the password for the authenticated user's profile.
     *
     * @param ChangePasswordDTO $changePasswordDTO
     * @return ModelResponse
     */
    public function changeUserProfilePassword(ChangePasswordDTO $changePasswordDTO): ModelResponse;

    /**
     * Reset user password
     *
     * @param integer $userId
     * @return ModelResponse
     */
    public function resetPassword(int $userId): ModelResponse;

    /**
     * Set user active status
     *
     * @param integer $userId
     * @return ModelResponse
     */
    public function setUserActiveStatus(int $userId): ModelResponse;

    /**
     * Update first login password
     *
     * @param FirstLoginChangePasswordDTO $firstLoginChangePasswordDTO
     * @return ModelResponse
     */
    public function firstLoginChangePassword(FirstLoginChangePasswordDTO $firstLoginChangePasswordDTO): ModelResponse;

    /**
     * Change profile avatar
     *
     * @param integer $profileId
     * @param UploadedFile $file
     * @return ModelResponse
     */
    public function changeProfileAvatar(int $profileId, UploadedFile $file): ModelResponse;

    /**
     * Remove profile avatar
     *
     * @param integer $profileId
     * @return ModelResponse
     */
    public function removeProfileAvatar(int $profileId): ModelResponse;
}
