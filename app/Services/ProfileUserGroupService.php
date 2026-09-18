<?php

namespace App\Services;

use App\Data\ModelResponse;
use App\DTOs\ProfileUserGroupDTO;
use App\Helpers\Helper;
use App\Interfaces\ProfileUserGroupInterface;
use App\Traits\HttpErrorCodeTrait;
use App\Traits\ReturnModelCollectionTrait;
use App\Traits\ReturnModelTrait;
use App\Interfaces\BaseInterface;
use App\Interfaces\FetchInterfaces\BaseFetchInterface;
use App\Models\ProfileUserGroup;
use Illuminate\Support\Facades\DB;

class ProfileUserGroupService implements ProfileUserGroupInterface
{
    use HttpErrorCodeTrait;
    use ReturnModelCollectionTrait;
    use ReturnModelTrait;

    public function __construct(
        private BaseInterface $base,
        private BaseFetchInterface $fetch
    ) {}

    /**
     * Store a new profile user group in the database.
     * 
     * @param ProfileUserGroupDTO $profileUserGroupDTO
     * @return ModelResponse
     */
    public function storeProfileUserGroup(ProfileUserGroupDTO $profileUserGroupDTO): ModelResponse
    {
        try {
            return DB::transaction(function () use ($profileUserGroupDTO) {

                $profileUserGroupData = $profileUserGroupDTO->toArray();
                $profileUserGroup = $this->base->store(ProfileUserGroup::class, $profileUserGroupData);

                return ModelResponse::success(201, Helper::SUCCESS, 'Profile user group created successfully!', $profileUserGroup, $profileUserGroup->id);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return ModelResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Update an existing profile user group in the database.
     * 
     * @param ProfileUserGroupDTO $profileUserGroupDTO
     * @param int $profileUserGroupId
     * @return ModelResponse
     */
    public function updateProfileUserGroup(ProfileUserGroupDTO $profileUserGroupDTO, int $profileUserGroupId): ModelResponse
    {
        try {
            return DB::transaction(function () use ($profileUserGroupDTO, $profileUserGroupId) {
                $profileUserGroup = $this->fetch->showQuery(ProfileUserGroup::class, $profileUserGroupId)->firstOrFail();

                $profileUserGroupData = ProfileUserGroupDTO::fromModel($profileUserGroup, $profileUserGroupDTO->toArray())->toArray();
                $profileUserGroup = $this->base->update($profileUserGroup, $profileUserGroupData);

                // $this->returnModel(code, status, message, model, last_id);
                return ModelResponse::success(200, Helper::SUCCESS, 'Profile user group updated successfully!', $profileUserGroup, $profileUserGroupId);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return ModelResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Update an existing profile user group in the database using profile id.
     * 
     * @param ProfileUserGroupDTO $profileUserGroupDTO
     * @param int $profileId
     * @return ModelResponse
     */
    public function updateProfileUserGroupWithProfileId(ProfileUserGroupDTO $profileUserGroupDTO, int $profileId): ModelResponse
    {
        try {
            return DB::transaction(function () use ($profileUserGroupDTO, $profileId) {
                $profileUserGroup = $this->fetch->showQuery(ProfileUserGroup::class, $profileId, 'profile_id')->firstOrFail();

                $profileUserGroupData = ProfileUserGroupDTO::fromModel($profileUserGroup, $profileUserGroupDTO->toArray())->toArray();
                $profileUserGroup = $this->base->update($profileUserGroup, $profileUserGroupData);

                // $this->returnModel(code, status, message, model, last_id);
                return ModelResponse::success(200, Helper::SUCCESS, 'Profile user group updated successfully!', $profileUserGroup, $profileUserGroup->id);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return ModelResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Delete the given profile user group in the database.
     * 
     * @param int $profileUserGroupId
     * @return ModelResponse
     */
    public function deleteProfileUserGroup(int $profileUserGroupId): ModelResponse
    {
        try {
            return DB::transaction(function () use ($profileUserGroupId) {
                $profileUserGroup = $this->fetch->showQuery(ProfileUserGroup::class, $profileUserGroupId)->firstOrFail();

                $this->base->delete($profileUserGroup);

                return ModelResponse::success(204, Helper::SUCCESS, 'Profile user group deleted successfully!', null, $profileUserGroupId);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return ModelResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Delete the given profile user group in the database with profile id.
     * 
     * @param int $profileId
     * @return ModelResponse
     */
    public function deleteProfileUserGroupWithProfileId(int $profileId): ModelResponse
    {
        try {
            return DB::transaction(function () use ($profileId) {
                $profileUserGroup = $this->fetch->showQuery(ProfileUserGroup::class, $profileId, 'profile_id')->firstOrFail();

                $this->base->delete($profileUserGroup);

                return ModelResponse::success(204, Helper::SUCCESS, 'Profile user group deleted successfully!', null, $profileUserGroup->id);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return ModelResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }
}
