<?php

namespace App\Services;

use App\Data\ModelResponse;
use App\Data\StandardResponse;
use App\DTOs\ProfileRoleDTO;
use App\Helpers\Helper;
use App\Interfaces\ProfileRoleInterface;
use App\Traits\HttpErrorCodeTrait;
use App\Traits\ReturnModelCollectionTrait;
use App\Traits\ReturnModelTrait;
use App\Interfaces\CurrentUserInterface;
use App\Interfaces\BaseInterface;
use App\Interfaces\FetchInterfaces\BaseFetchInterface;
use App\Traits\CheckIfColumnExistsTrait;
use App\Traits\DetectsSoftDeletesTrait;
use App\Traits\EnsureDataTrait;
use App\Traits\EnsureSuccessTrait;
use Illuminate\Support\Facades\DB;
use App\Models\ProfileRole;

class ProfileRoleService implements ProfileRoleInterface
{
    use HttpErrorCodeTrait,
        ReturnModelCollectionTrait,
        ReturnModelTrait,
        DetectsSoftDeletesTrait,
        CheckIfColumnExistsTrait,
        EnsureSuccessTrait,
        EnsureDataTrait;

    public function __construct(
        private BaseInterface $base,
        private BaseFetchInterface $fetch,
        private CurrentUserInterface $currentUser
    ) {}

    /**
     * Store a new profile role in the database.
     *
     * @param ProfileRoleDTO $profileRoleDTO
     * @return ModelResponse
     */
    public function storeProfileRole(ProfileRoleDTO $profileRoleDTO): ModelResponse
    {
        try {
            return DB::transaction(function () use ($profileRoleDTO) {

                $profileRoleData = $profileRoleDTO->toArray();
                $profileRole = $this->base->store(ProfileRole::class, $profileRoleData);

                return ModelResponse::success(201, Helper::SUCCESS, 'Profile role created successfully!', $profileRole, $profileRole->id);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return ModelResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Update an existing profile role in the database.
     *
     * @param ProfileRoleDTO $profileRoleDTO
     * @param int $profileRoleId
     * @return ModelResponse
     */
    public function updateProfileRole(ProfileRoleDTO $profileRoleDTO, int $profileRoleId): ModelResponse
    {
        try {
            return DB::transaction(function () use ($profileRoleDTO, $profileRoleId) {
                $profileRole = $this->fetch->showQuery(ProfileRole::class, $profileRoleId)->firstOrFail();

                $profileRoleDTO = ProfileRoleDTO::fromModel($profileRole, $profileRoleDTO->toArray());

                $profileRoleData = $profileRoleDTO->toArray();
                $profileRole = $this->base->update($profileRole, $profileRoleData);

                return ModelResponse::success(200, Helper::SUCCESS, 'Profile role updated successfully!', $profileRole, $profileRoleId);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return ModelResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Delete the given profile role in the database.
     *
     * @param int $profileRoleId
     * @return ModelResponse
     */
    public function deleteProfileRole(int $profileRoleId): ModelResponse
    {
        try {
            return DB::transaction(function () use ($profileRoleId) {
                $profileRole = $this->fetch->showQuery(ProfileRole::class, $profileRoleId)->firstOrFail();

                $this->base->delete($profileRole);

                return ModelResponse::success(204, Helper::SUCCESS, 'Profile role deleted successfully!', null, $profileRoleId);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return ModelResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Store multiple profile roles in the database.
     *
     * @param int $profileId
     * @param array $roleIds
     * @return StandardResponse
     */
    public function storeMultipleProfileRoles(int $profileId, array $roleIds = []): StandardResponse
    {
        try {
            return DB::transaction(function () use ($profileId, $roleIds) {
                if (empty($roleIds)) {
                    throw new \Exception('No role IDs provided for profile roles creation.');
                }

                $profileRolesData = [];
                foreach ($roleIds as $roleId) {
                    $profileRolesData[] = [
                        'profile_id' => $profileId,
                        'role_id' => $roleId
                    ];
                }

                $isSuccess = $this->base->storeMultiple(ProfileRole::class, $profileRolesData);

                if (!$isSuccess) {
                    throw new \Exception('Failed to create profile roles.');
                }

                return StandardResponse::success(201, Helper::SUCCESS, 'Profile roles created successfully!');
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return StandardResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Update multiple profile roles for a given profile in the database.
     *
     * @param int $profileId
     * @param array $roleIds
     * @return StandardResponse
     */
    public function updateMultipleProfileRoles(int $profileId, array $roleIds = []): StandardResponse
    {
        try {
            return DB::transaction(function () use ($profileId, $roleIds) {
                // Delete existing profile roles for the profile
                $existingProfileRoles = $this->fetch->indexQuery(ProfileRole::class)
                    ->where('profile_id', $profileId)
                    ->pluck('id');

                // Only attempt to delete if there are existing profile roles
                if ($existingProfileRoles->isNotEmpty()) {
                    $deletedRows = $this->base->deleteMultiple(ProfileRole::class, $existingProfileRoles->toArray());
                    if ($deletedRows === 0) {
                        throw new \Exception('Failed to delete existing profile roles.');
                    }
                }

                $storeNewRoleResponse = $this->storeMultipleProfileRoles($profileId, $roleIds);
                $this->ensureSuccess($storeNewRoleResponse->toArray(), 'Failed to update profile roles.');

                return StandardResponse::success(200, Helper::SUCCESS, 'Profile roles updated successfully!');
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return StandardResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Delete the given profile role in the database by role ID.
     *
     * @param integer $roleId
     * @return StandardResponse
     */
    public function deleteProfileRolesByRoleId(int $roleId): StandardResponse
    {
        try {
            return DB::transaction(function () use ($roleId) {

                // We remove the role from the profiles if the role is deactivated/deleted.
                $profileRoles = $this->fetch->indexQuery(ProfileRole::class)
                    ->where('role_id', $roleId)
                    ->pluck('id');

                $deletedRows = $this->base->deleteMultiple(ProfileRole::class, $profileRoles->toArray());
                if ($deletedRows === 0) {
                    return StandardResponse::success(200, Helper::SUCCESS, 'Successfully deleted role, but no profile roles were deleted.');
                }

                return StandardResponse::success(200, Helper::SUCCESS, 'Profile roles associated with the role deleted successfully!');
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return StandardResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Delete the given profile role in the database by profile ID.
     *
     * @param integer $profileId
     * @return StandardResponse
     */
    public function removeProfileRolesByProfileId(int $profileId): StandardResponse
    {
        try {
            return DB::transaction(function () use ($profileId) {

                // We remove the profile roles associated with the profile.
                $profileRoles = $this->fetch->indexQuery(ProfileRole::class)
                    ->where('profile_id', $profileId)
                    ->pluck('id');

                $deletedRows = $this->base->deleteMultiple(ProfileRole::class, $profileRoles->toArray());
                if ($deletedRows === 0) {
                    throw new \Exception('Failed to delete profile roles associated with the profile.');
                }

                return StandardResponse::success(200, Helper::SUCCESS, 'Profile roles associated with the profile deleted successfully!');
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return StandardResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }
}
