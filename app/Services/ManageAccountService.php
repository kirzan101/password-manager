<?php

namespace App\Services;

use App\Data\ModelResponse;
use App\DTOs\AccountDTO;
use App\DTOs\BasicProfileDTO;
use App\DTOs\ChangePasswordDTO;
use App\DTOs\FirstLoginChangePasswordDTO;
use App\DTOs\ProfileDTO;
use App\DTOs\ProfileUserGroupDTO;
use App\DTOs\UserDTO;
use App\Helpers\Helper;
use App\Interfaces\BaseInterface;
use App\Traits\HttpErrorCodeTrait;
use App\Traits\ReturnModelTrait;
use App\Interfaces\CurrentUserInterface;
use App\Interfaces\FetchInterfaces\BaseFetchInterface;
use App\Interfaces\ManageAccountInterface;
use App\Interfaces\ManageRoleInterface;
use App\Interfaces\ProfileInterface;
use App\Interfaces\ProfileRoleInterface;
use App\Interfaces\ProfileUserGroupInterface;
use App\Interfaces\UserInterface;
use App\Models\Profile;
use App\Models\User;
use App\Traits\EnsureDataTrait;
use App\Traits\EnsureSuccessTrait;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class ManageAccountService implements ManageAccountInterface
{
    use HttpErrorCodeTrait,
        ReturnModelTrait,
        EnsureSuccessTrait,
        EnsureDataTrait;

    public function __construct(
        private BaseFetchInterface $fetch,
        private BaseInterface $base,
        private UserInterface $user,
        private ProfileInterface $profile,
        private ProfileUserGroupInterface $profileUserGroup,
        private ManageRoleInterface $manageRole,
        private ProfileRoleInterface $profileRole,
        private CurrentUserInterface $currentUser
    ) {}

    /**
     * Register a new user with profile.
     *
     * @param AccountDTO $accountDTO
     * @return ModelResponse
     * @throws \Throwable
     */
    public function register(AccountDTO $accountDTO): ModelResponse
    {
        try {
            return DB::transaction(function () use ($accountDTO) {
                // set to null if this function use in registration page.
                $currentProfileId = $this->currentUser->getProfileId() ?? null;


                // Create user
                $userDto = $accountDTO->user;
                $userResult = $this->user->storeUser($userDto);

                // Ensure user creation was successful
                $this->ensureSuccess($userResult->toArray(), 'User creation failed!');

                $userId = $userResult->lastId ?? null;

                // Create profile
                $profileDTO = $accountDTO->profile->withUser($userId);
                if ($currentProfileId) {
                    $profileDTO = $profileDTO->withDefaultAudit($currentProfileId);
                }

                $profileResult = $this->profile->storeProfile($profileDTO);

                // Ensure profile creation was successful
                $this->ensureSuccess($profileResult->toArray(), 'Profile creation failed!');

                // Get the profile data
                $profile = $profileResult->data;
                $this->ensureModel($profile, 'Profile creation failed!');

                // create profile user group
                if (!empty($accountDTO->user_group_id)) {
                    $profileUserGroupDto = ProfileUserGroupDTO::fromArray([
                        'profile_id' => $profile->id,
                        'user_group_id' => $accountDTO->user_group_id
                    ]);
                    $profileUserGroupResult = $this->profileUserGroup->storeProfileUserGroup($profileUserGroupDto);

                    // Ensure profile user group creation was successful
                    $this->ensureSuccess($profileUserGroupResult->toArray(), 'Profile user group creation failed!');
                }

                // assign roles to the profile
                if (is_array($accountDTO->role_ids) && !empty($accountDTO->role_ids)) {
                    // Assign roles to the profile
                    $manageRoleResult = $this->profileRole->storeMultipleProfileRoles($profile->id, $accountDTO->role_ids);
                    $this->ensureSuccess($manageRoleResult->toArray(), 'Profile roles assignment failed!');
                }

                return ModelResponse::success(201, Helper::SUCCESS, 'Profile registration successfully!', $profile, $profile->id);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return ModelResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Update an existing user profile.
     *
     * @param AccountDTO $accountDTO
     * @param int $profileId
     * @return ModelResponse
     */
    public function updateUserProfile(AccountDTO $accountDTO, int $profileId): ModelResponse
    {
        try {
            return DB::transaction(function () use ($accountDTO, $profileId) {

                // Get the profile by profile ID
                $profile = $this->fetch
                    ->showQuery(Profile::class, $profileId)
                    ->with('user')
                    ->firstOrFail();

                // Update profile
                $profileData = $accountDTO->profile;

                $profileResult = $this->profile->updateProfile($profileData, $profileId);

                // Ensure profile update was successful
                $this->ensureSuccess($profileResult->toArray(), 'Profile update failed!');

                // Get user associated with the profile
                $user = $profile->user;

                if (!$user) {
                    throw new RuntimeException('User associated with the profile not found.');
                }

                // Update user
                $userDTO = $accountDTO->user;

                // Check if user is first login
                if (!$user->is_first_login) {
                    // do not update the first_login field if the user is not on their first login
                    $userDTO = $userDTO->isAlreadyLoggedIn($user->toArray()); // user here is the existing user model, we convert it to array to pass to the DTO
                }

                $userResult = $this->user->updateUser($userDTO, $user->id);

                // Ensure user update was successful
                $this->ensureSuccess($userResult->toArray(), 'User update failed!');

                // Update user group if provided
                if (!empty($accountDTO->user_group_id)) {
                    $profileUserGroupDto = ProfileUserGroupDTO::fromArray([
                        'profile_id' => $profile->id,
                        'user_group_id' => $accountDTO->user_group_id
                    ]);
                    $profileUserGroupResult = $this->profileUserGroup->updateProfileUserGroupWithProfileId($profileUserGroupDto, $profileId);

                    // Ensure profile user group update was successful
                    $this->ensureSuccess($profileUserGroupResult->toArray(), 'Profile user group update failed!');
                }

                // Update profile roles
                if (is_array($accountDTO->role_ids) && !empty($accountDTO->role_ids)) {
                    $manageRoleResult = $this->profileRole->updateMultipleProfileRoles($profile->id, $accountDTO->role_ids);
                    $this->ensureSuccess($manageRoleResult->toArray(), 'Profile roles update failed!');
                } else {
                    // If no role IDs are provided, remove all roles associated with the profile
                    $removeRolesResult = $this->profileRole->removeProfileRolesByProfileId($profile->id);
                    $this->ensureSuccess($removeRolesResult->toArray(), 'Failed to remove profile roles!');
                }

                return ModelResponse::success(200, Helper::SUCCESS, 'Profile updated successfully!', $profile, $profile->id);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return ModelResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Update the basic profile information for a user.
     *
     * @param BasicProfileDTO $basicProfileDTO
     * @return ModelResponse
     */
    public function updateBasicProfile(BasicProfileDTO $basicProfileDTO): ModelResponse
    {
        try {
            // Fetch the profile by email
            $profile = $this->fetch
                ->showQuery(Profile::class, $basicProfileDTO->profile_id)
                ->firstOrFail();

            // Update the profile with the basic profile data
            $profile = $this->base->update($profile, [
                'nickname' => $basicProfileDTO->nickname,
                'position' => $basicProfileDTO->position,
                'contact_numbers' => $basicProfileDTO->contact_numbers,
                'updated_at' => now(),
                'updated_by' => $this->currentUser->getProfileId(),
            ]);

            if (!$profile) {
                throw new RuntimeException('Profile update failed!', 500);
            }

            $user = $this->fetch
                ->showQuery(User::class, $basicProfileDTO->user_id)
                ->firstOrFail();

            $user = $this->base->update($user, [
                'email' => $basicProfileDTO->email,
                'updated_at' => now()
            ]);

            if (!$user) {
                throw new RuntimeException('User update failed!', 500);
            }

            return ModelResponse::success(200, Helper::SUCCESS, 'Basic profile updated successfully!', $profile, $profile->id);
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return ModelResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Change the password for the authenticated user's profile.
     *
     * @param ChangePasswordDTO $changePasswordDTO
     * @return ModelResponse
     */
    public function changeUserProfilePassword(ChangePasswordDTO $changePasswordDTO): ModelResponse
    {
        try {
            // Get the profile by profile ID
            $profile = $this->fetch
                ->showQuery(Profile::class, $changePasswordDTO->profile_id)
                ->with('user')
                ->firstOrFail();

            $user = $profile->user;

            if (!$user) {
                throw new RuntimeException('User associated with the profile not found.');
            }

            // check if password is valid
            $checkResult = $this->checkPasswordIsCorrect($user->id, $changePasswordDTO->current_password);

            if (!$checkResult) {
                // rollback is automatic when throwing inside transaction
                throw new RuntimeException('Invalid current password!');
            }

            // Update user password
            $user = $this->base->update($user, [
                'is_first_login' => false,
                'password' => bcrypt($changePasswordDTO->new_password), // Access DTO property
            ]);

            if (!$user) {
                throw new RuntimeException('User password update failed!');
            }

            // update profile updated_at and updated_by
            $profile = $this->base->update($profile, [
                'updated_at' => now(),
                'updated_by' => $this->currentUser->getProfileId(),
            ]);

            if (!$profile) {
                throw new RuntimeException('Profile update failed!');
            }

            return ModelResponse::success(
                200,
                Helper::SUCCESS,
                'Password changed successfully!',
                $profile,
                $profile->id
            );
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return ModelResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Reset user password
     *
     * @param integer $userId
     * @return ModelResponse
     */
    public function resetPassword(int $userId): ModelResponse
    {
        try {
            return DB::transaction(function () use ($userId) {
                $user = $this->fetch->showQuery(User::class, $userId)->firstOrFail();

                // set default password to username
                $new_password = bcrypt($user->username);

                $user->update([
                    'password' => $new_password,
                    'is_first_login' => true,
                ]);

                return ModelResponse::success(200, Helper::SUCCESS, 'Successfully reset password!');
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return ModelResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Set user active status
     *
     * @param integer $userId
     * @return ModelResponse
     */
    public function setUserActiveStatus(int $userId): ModelResponse
    {
        try {
            return DB::transaction(function () use ($userId) {
                $user = $this->fetch->showQuery(User::class, $userId)->firstOrFail();

                $currentStatus = $user->status;

                $newStatus = match ($currentStatus) {
                    Helper::ACCOUNT_STATUS_ACTIVE   => Helper::ACCOUNT_STATUS_INACTIVE,
                    Helper::ACCOUNT_STATUS_INACTIVE => Helper::ACCOUNT_STATUS_ACTIVE,
                    default => null,
                };

                if (is_null($newStatus)) {
                    throw new RuntimeException("User status is in an unexpected state: {$currentStatus}");
                }

                $user->update(['status' => $newStatus]);

                return ModelResponse::success(200, Helper::SUCCESS, "Successfully changed status to {$newStatus}!");
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return ModelResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Update first login password
     *
     * @param FirstLoginChangePasswordDTO $firstLoginChangePasswordDTO
     * @return ModelResponse
     */
    public function firstLoginChangePassword(FirstLoginChangePasswordDTO $firstLoginChangePasswordDTO): ModelResponse
    {
        try {
            // Get the profile by profile ID
            $profile = $this->fetch
                ->showQuery(Profile::class, $firstLoginChangePasswordDTO->profile_id)
                ->with('user')
                ->firstOrFail();

            $user = $profile->user;

            if (!$user) {
                throw new RuntimeException('User associated with the profile not found.');
            }

            // Update user password
            $user = $this->base->update($user, [
                'is_first_login' => false,
                'password' => bcrypt($firstLoginChangePasswordDTO->password), // Access DTO property
            ]);

            if (!$user) {
                throw new RuntimeException('User password update failed!');
            }

            // update profile updated_at and updated_by
            $profile = $this->base->update($profile, [
                'updated_at' => now(),
                'updated_by' => $this->currentUser->getProfileId(),
            ]);

            if (!$profile) {
                throw new RuntimeException('Profile update failed!');
            }

            return ModelResponse::success(
                200,
                Helper::SUCCESS,
                'Password changed successfully!',
                $profile,
                $profile->id
            );
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return ModelResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Change profile avatar
     *
     * @param integer $profileId
     * @param UploadedFile $file
     * @return ModelResponse
     */
    public function changeProfileAvatar(int $profileId, UploadedFile $file): ModelResponse
    {
        try {
            // Get the profile by profile ID
            $profile = $this->fetch
                ->showQuery(Profile::class, $profileId)
                ->firstOrFail();

            // Delete the existing avatar from storage if present
            if (!empty($profile->avatar) && Storage::disk('private')->exists($profile->avatar)) {
                Storage::disk('private')->delete($profile->avatar);
            }

            // Store the new avatar in the avatars folder
            $avatarPath = $file->store('avatars', 'private');

            if (!$avatarPath) {
                throw new RuntimeException('Failed to store avatar file.');
            }

            // Update profile with the new avatar path
            $profile = $this->base->update($profile, [
                'avatar' => $avatarPath,
                'updated_at' => now(),
                'updated_by' => $this->currentUser->getProfileId(),
            ]);

            if (!$profile) {
                throw new RuntimeException('Profile avatar update failed!');
            }

            return ModelResponse::success(
                200,
                Helper::SUCCESS,
                'Profile avatar updated successfully!',
                $profile,
                $profile->id
            );
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return ModelResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Remove profile avatar
     *
     * @param integer $profileId
     * @return ModelResponse
     */
    public function removeProfileAvatar(int $profileId): ModelResponse
    {
        try {
            // Get the profile by profile ID
            $profile = $this->fetch
                ->showQuery(Profile::class, $profileId)
                ->firstOrFail();

            // Delete the existing avatar from storage if present
            if (!empty($profile->avatar) && Storage::disk('private')->exists($profile->avatar)) {
                Storage::disk('private')->delete($profile->avatar);
            }

            // Update profile to remove the avatar path
            $profile = $this->base->update($profile, [
                'avatar' => null,
                'updated_at' => now(),
                'updated_by' => $this->currentUser->getProfileId(),
            ]);

            if (!$profile) {
                throw new RuntimeException('Profile avatar removal failed!');
            }

            return ModelResponse::success(
                200,
                Helper::SUCCESS,
                'Profile avatar removed successfully!',
                $profile,
                $profile->id
            );
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return ModelResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * check if the inputed password is correct
     *
     * @param integer $userId
     * @param string $currentPassword
     * @return boolean
     */
    private function checkPasswordIsCorrect(int $userId, string $currentPassword): bool
    {
        $user = $this->fetch->showQuery(User::class, $userId)->firstOrFail();
        $result = Hash::check($currentPassword, $user->password);

        return $result;
    }
}
