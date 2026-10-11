<?php

namespace App\Services;

use App\Data\ModelResponse;
use App\Data\StandardResponse;
use App\DTOs\CredentialAccessDTO;
use App\Helpers\Helper;
use App\Interfaces\CredentialAccessInterface;
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
use App\Models\CredentialAccess;

class CredentialAccessService implements CredentialAccessInterface
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
     * Store a new credential access in the database.
     *
     * @param CredentialAccessDTO $credentialAccessDTO
     * @return ModelResponse
     */
    public function storeCredentialAccess(CredentialAccessDTO $credentialAccessDTO): ModelResponse
    {
        try {
            return DB::transaction(function () use ($credentialAccessDTO) {

                $currentUserProfileId = $this->currentUser->getProfileId(); // current logged in profile
                if ($this->modelHasColumns(CredentialAccess::class, ['created_by', 'updated_by'])) {
                    $credentialAccessDTO = $credentialAccessDTO->withDefaultAudit($currentUserProfileId);
                }

                $credentialAccessData = $credentialAccessDTO->toArray();
                $credentialAccess = $this->base->store(CredentialAccess::class, $credentialAccessData);

                return ModelResponse::success(201, Helper::SUCCESS, 'Credential access created successfully!', $credentialAccess, $credentialAccess->id);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return ModelResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Update an existing credential access in the database.
     *
     * @param CredentialAccessDTO $credentialAccessDTO
     * @param int $credentialAccessId
     * @return ModelResponse
     */
    public function updateCredentialAccess(CredentialAccessDTO $credentialAccessDTO, int $credentialAccessId): ModelResponse
    {
        try {
            return DB::transaction(function () use ($credentialAccessDTO, $credentialAccessId) {
                $credentialAccess = $this->fetch->showQuery(CredentialAccess::class, $credentialAccessId)->firstOrFail();

                $credentialAccessDTO = CredentialAccessDTO::fromModel($credentialAccess, $credentialAccessDTO->toArray());

                $currentUserProfileId = $this->currentUser->getProfileId(); // current logged in profile
                if ($this->modelHasColumn($credentialAccess, 'updated_by')) {
                    $credentialAccessDTO = $credentialAccessDTO->touchUpdatedBy($currentUserProfileId);
                }

                $credentialAccessData = $credentialAccessDTO->toArray();
                $credentialAccess = $this->base->update($credentialAccess, $credentialAccessData);

                return ModelResponse::success(200, Helper::SUCCESS, 'Credential access updated successfully!', $credentialAccess, $credentialAccessId);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return ModelResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Delete the given credential access in the database.
     *
     * @param int $credentialAccessId
     * @return ModelResponse
     */
    public function deleteCredentialAccess(int $credentialAccessId): ModelResponse
    {
        try {
            return DB::transaction(function () use ($credentialAccessId) {
                $credentialAccess = $this->fetch->showQuery(CredentialAccess::class, $credentialAccessId)->firstOrFail();

                if ($this->modelUsesSoftDeletes($credentialAccess)) {
                    if ($this->modelHasColumn($credentialAccess, 'updated_by')) {
                        // record who deleted the credential access by updating the 'updated_by' column
                        $this->base->update($credentialAccess, [
                            'updated_by' => $this->currentUser->getProfileId(),
                        ]);
                    }
                }

                $this->base->delete($credentialAccess);

                return ModelResponse::success(204, Helper::SUCCESS, 'Credential access deleted successfully!', null, $credentialAccessId);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return ModelResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Store multiple credential accesses in the database.
     *
     * @param array $credentialAccessDTOs
     * @return StandardResponse
     */
    public function storeMultipleCredentialAccess(array $credentialAccessDTOs): StandardResponse
    {
        try {
            return DB::transaction(function () use ($credentialAccessDTOs) {
                $forStoringAccesses = [];
                foreach ($credentialAccessDTOs as $credentialAccessDTO) {
                    // ensure is instance of CredentialAccessDTO
                    if (!($credentialAccessDTO instanceof \App\DTOs\CredentialAccessDTO)) {
                        throw new \RuntimeException('Invalid credential access DTO provided', 400);
                    }

                    $forStoringAccesses[] = $credentialAccessDTO->toArray();
                }

                $isSuccess = $this->base->storeMultiple(CredentialAccess::class, $forStoringAccesses);

                if (!$isSuccess) {
                    throw new \RuntimeException('Failed to store multiple credential accesses', 500);
                }

                return StandardResponse::success(201, Helper::SUCCESS, 'Multiple credential accesses created successfully!');
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return StandardResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Update the access level for a specific credential access in the database.
     *
     * @param int $credentialAccessId
     * @param string $newAccessLevel
     * @return StandardResponse
     */
    public function updateCredentialAccessLevel(int $credentialAccessId, string $newAccessLevel): StandardResponse
    {
        try {
            return DB::transaction(function () use ($credentialAccessId, $newAccessLevel) {
                $response = $this->fetch->showQuery(CredentialAccess::class, $credentialAccessId)->firstOrFail();

                if ($this->modelHasColumn($response, 'access_level')) {
                    $this->base->update($response, [
                        'access_level' => $newAccessLevel,
                        'updated_by' => $this->currentUser->getProfileId(),
                    ]);
                }

                return StandardResponse::success(200, Helper::SUCCESS, 'Access level updated for credential successfully!', $response);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return StandardResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }
}
