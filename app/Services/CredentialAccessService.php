<?php

namespace App\Services;

use App\Data\ModelResponse;
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
}
