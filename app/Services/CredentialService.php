<?php

namespace App\Services;

use App\Data\ModelResponse;
use App\DTOs\CredentialDTO;
use App\Helpers\Helper;
use App\Interfaces\CredentialInterface;
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
use App\Models\Credential;

class CredentialService implements CredentialInterface
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
     * Store a new credential in the database.
     *
     * @param CredentialDTO $credentialDTO
     * @return ModelResponse
     */
    public function storeCredential(CredentialDTO $credentialDTO): ModelResponse
    {
        try {
            return DB::transaction(function () use ($credentialDTO) {

                $currentUserProfileId = $this->currentUser->getProfileId(); // current logged in profile
                if ($this->modelHasColumns(Credential::class, ['created_by', 'updated_by'])) {
                    $credentialDTO = $credentialDTO->withDefaultAudit($currentUserProfileId);
                }

                $credentialData = $credentialDTO->toArray();
                $credential = $this->base->store(Credential::class, $credentialData);

                return ModelResponse::success(201, Helper::SUCCESS, 'Credential created successfully!', $credential, $credential->id);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return ModelResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Update an existing credential in the database.
     *
     * @param CredentialDTO $credentialDTO
     * @param int $credentialId
     * @return ModelResponse
     */
    public function updateCredential(CredentialDTO $credentialDTO, int $credentialId): ModelResponse
    {
        try {
            return DB::transaction(function () use ($credentialDTO, $credentialId) {
                $credential = $this->fetch->showQuery(Credential::class, $credentialId)->firstOrFail();

                $credentialDTO = CredentialDTO::fromModel($credential, $credentialDTO->toArray());

                $currentUserProfileId = $this->currentUser->getProfileId(); // current logged in profile
                if ($this->modelHasColumn($credential, 'updated_by')) {
                    $credentialDTO = $credentialDTO->touchUpdatedBy($currentUserProfileId);
                }

                $credentialData = $credentialDTO->toArray();
                $credential = $this->base->update($credential, $credentialData);

                return ModelResponse::success(200, Helper::SUCCESS, 'Credential updated successfully!', $credential, $credentialId);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return ModelResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Delete the given credential in the database.
     *
     * @param int $credentialId
     * @return ModelResponse
     */
    public function deleteCredential(int $credentialId): ModelResponse
    {
        try {
            return DB::transaction(function () use ($credentialId) {
                $credential = $this->fetch->showQuery(Credential::class, $credentialId)->firstOrFail();

                // record who deleted the activity log
                $this->base->update($credential, [
                    'updated_by' => $this->currentUser->getProfileId(),
                    'deleted_by' => $this->currentUser->getProfileId()
                ]);

                $this->base->delete($credential);

                return ModelResponse::success(204, Helper::SUCCESS, 'Credential deleted successfully!', null, $credentialId);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return ModelResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }
}
