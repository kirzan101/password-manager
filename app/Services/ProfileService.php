<?php

namespace App\Services;

use App\Data\ModelResponse;
use App\DTOs\ProfileDTO;
use App\Helpers\Helper;
use App\Interfaces\CurrentUserInterface;
use App\Interfaces\BaseInterface;
use App\Interfaces\FetchInterfaces\BaseFetchInterface;
use App\Interfaces\ProfileInterface;
use App\Models\Profile;
use App\Services\FetchServices\BaseFetchService;
use App\Traits\CheckIfColumnExistsTrait;
use App\Traits\DetectsSoftDeletesTrait;
use App\Traits\HttpErrorCodeTrait;
use App\Traits\ReturnModelCollectionTrait;
use App\Traits\ReturnModelTrait;
use Illuminate\Support\Facades\DB;

class ProfileService implements ProfileInterface
{
    use HttpErrorCodeTrait,
        ReturnModelCollectionTrait,
        ReturnModelTrait,
        DetectsSoftDeletesTrait,
        CheckIfColumnExistsTrait;

    public function __construct(
        private BaseInterface $base,
        private BaseFetchInterface $fetch,
        private CurrentUserInterface $currentUser
    ) {}

    /**
     * Store a new profile in the database.
     *
     * @param ProfileDTO $profileDTO
     * @return ModelResponse
     */
    public function storeProfile(ProfileDTO $profileDTO): ModelResponse
    {
        try {
            return DB::transaction(function () use ($profileDTO) {
                $currentUserProfileId = $this->currentUser->getProfileId();

                $profileData = $profileDTO->withDefaultAudit($currentUserProfileId)->toArray();
                $profile = $this->base->store(Profile::class, $profileData);

                return ModelResponse::success(201, Helper::SUCCESS, 'Profile created successfully!', $profile, $profile->id);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return ModelResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * update an existing profile in the database.
     *
     * @param ProfileDTO $profileDTO
     * @param integer $profileId
     * @return ModelResponse
     */
    public function updateProfile(ProfileDTO $profileDTO, int $profileId): ModelResponse
    {
        try {
            return DB::transaction(function () use ($profileDTO, $profileId) {
                $currentUserProfileId = $this->currentUser->getProfileId();

                $profile = $this->fetch->showQuery(Profile::class, $profileId)->firstOrFail();

                // Update the profile with the provided data
                $profileData = ProfileDTO::fromModel($profile, $profileDTO->toArray())
                    ->touchUpdatedBy($currentUserProfileId)
                    ->toArray();

                $profile = $this->base->update($profile, $profileData);

                return ModelResponse::success(200, Helper::SUCCESS, 'Profile updated successfully!', $profile, $profile->id);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return ModelResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * delete a profile from the database.
     *
     * @param integer $profileId
     * @return ModelResponse
     */
    public function deleteProfile(int $profileId): ModelResponse
    {
        try {
            return DB::transaction(function () use ($profileId) {
                $profile = $this->fetch->showQuery(Profile::class, $profileId)->firstOrFail();

                if ($this->modelUsesSoftDeletes($profile)) {
                    if ($this->modelHasColumn($profile, 'updated_by')) {
                        // record who deleted the activity log
                        $this->base->update($profile, [
                            'updated_by' => $this->currentUser->getProfileId(),
                        ]);
                    }
                }

                $this->base->delete($profile);

                return ModelResponse::success(204, Helper::SUCCESS, 'Profile deleted successfully!', null, $profileId);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return ModelResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }
}
