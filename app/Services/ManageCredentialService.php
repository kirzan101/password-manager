<?php

namespace App\Services;

use App\Data\ModelResponse;
use App\Data\StandardResponse;
use App\DTOs\ManageCredentialDTO;
use App\Helpers\Helper;
use App\Interfaces\ManageCredentialInterface;
use App\Traits\HttpErrorCodeTrait;
use App\Interfaces\CredentialAccessInterface;
use App\Interfaces\CredentialInterface;
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

class ManageCredentialService implements ManageCredentialInterface
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
        private CurrentUserInterface $currentUser,
        private CredentialInterface $crdential,
        private CredentialAccessInterface $credentialAccess
    ) {}

    /**
     * Store a new credential in the database along with its access details.
     *
     * @param ManageCredentialDTO $manageCredentialDTO
     * @return ModelResponse
     */
    public function storeNewCredential(ManageCredentialDTO $manageCredentialDTO): ModelResponse
    {
        try {
            return DB::transaction(function () use ($manageCredentialDTO) {

                $response = $this->crdential->storeCredential($this->processCredentialDTO($manageCredentialDTO));
                $this->ensureSuccess($response->toArray(), 'Failed to store credential');

                $credential = $response->getData();
                $this->ensureModel($credential, 'Failed to retrieve stored credential');

                // Process and store credential access details associated with the newly created credential.
                $credentialAccessDTOs = $this->processCredentialAccessDTOs($credential->id, $manageCredentialDTO);
                if (!empty($credentialAccessDTOs)) {
                    $responseAccess = $this->credentialAccess->storeMultipleCredentialAccess($credentialAccessDTOs);
                    $this->ensureSuccess($responseAccess->toArray(), 'Failed to store credential access');
                }

                return ModelResponse::success(201, Helper::SUCCESS, 'Manage credential created successfully!', $credential, $credential->id);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return ModelResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Update an existing credential in the database along with its access details.
     *
     * @param ManageCredentialDTO $manageCredentialDTO
     * @param int $credentialId
     * @return ModelResponse
     */
    public function updateCredential(ManageCredentialDTO $manageCredentialDTO, int $credentialId): ModelResponse
    {
        try {
            return DB::transaction(function () use ($manageCredentialDTO, $credentialId) {

                $response = $this->crdential->updateCredential($this->processCredentialDTO($manageCredentialDTO), $credentialId);
                $this->ensureSuccess($response->toArray(), 'Failed to update credential');

                $credential = $response->getData();
                $this->ensureModel($credential, 'Failed to retrieve updated credential');

                return ModelResponse::success(200, Helper::SUCCESS, 'Manage credential updated successfully!', $credential, $credential->id);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return ModelResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Delete the given credential in the database along with its access details.
     *
     * @param int $credentialId
     * @return ModelResponse
     */
    public function deleteCredential(int $credentialId): ModelResponse
    {
        try {
            return DB::transaction(function () use ($credentialId) {
                $response = $this->crdential->deleteCredential($credentialId);
                $this->ensureSuccess($response->toArray(), 'Failed to delete credential');

                return ModelResponse::success(200, Helper::SUCCESS, 'Manage credential deleted successfully!', null, $credentialId);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return ModelResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Add access to a credential in the database.
     *
     * @param \App\DTOs\CredentialAccessDTO $credentialAccessDTO
     * @return StandardResponse
     */
    public function addAccessToCredential(\App\DTOs\CredentialAccessDTO $credentialAccessDTO): StandardResponse
    {
        try {
            return DB::transaction(function () use ($credentialAccessDTO) {
                $response = $this->credentialAccess->storeCredentialAccess($credentialAccessDTO);
                $this->ensureSuccess($response->toArray(), 'Failed to add access to credential');

                return StandardResponse::success(200, Helper::SUCCESS, 'Access added to credential successfully!', $response->getData());
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return StandardResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Remove access to a credential in the database.
     *
     * @param int $credentialAccessId
     * @return StandardResponse
     */
    public function removeAccessToCredential(int $credentialAccessId): StandardResponse
    {
        try {
            return DB::transaction(function () use ($credentialAccessId) {
                $response = $this->credentialAccess->deleteCredentialAccess($credentialAccessId);
                $this->ensureSuccess($response->toArray(), 'Failed to remove access to credential');

                return StandardResponse::success(200, Helper::SUCCESS, 'Access removed from credential successfully!', $response->getData());
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return StandardResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Update the access level for a specific credential access to a credential in the database.
     *
     * @param int $credentialAccessId
     * @param string $newAccessLevel
     * @return StandardResponse
     */
    public function updateAccessLevelToCredential(int $credentialAccessId, string $newAccessLevel): StandardResponse
    {
        try {
            return DB::transaction(function () use ($credentialAccessId, $newAccessLevel) {

                $response = $this->credentialAccess->updateCredentialAccessLevel($credentialAccessId, $newAccessLevel);
                $this->ensureSuccess($response->toArray(), 'Failed to update access level for credential');

                return StandardResponse::success(200, Helper::SUCCESS, 'Access level updated for credential successfully!');
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return StandardResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Reveal the secret for a specific credential.
     *
     * @param int $credentialId
     * @return string|null
     */
    public function revealCredentialSecret(int $credentialId): ?string
    {
        $credential = $this->fetch->showQuery(\App\Models\Credential::class, $credentialId)->firstOrFail();

        return $credential->secret;
    }

    /**
     * Process a ManageCredentialDTO into a CredentialDTO.
     */
    protected function processCredentialDTO(ManageCredentialDTO $manageCredentialDTO): \App\DTOs\CredentialDTO
    {
        $credentialDTO = \App\DTOs\CredentialDTO::fromArray([
            'owner_profile_id' => $manageCredentialDTO->owner_profile_id ?? $this->currentUser->getProfileId(),
            'name' => $manageCredentialDTO->name,
            'username' => $manageCredentialDTO->username,
            'secret' => $manageCredentialDTO->secret,
            'is_favorite' => $manageCredentialDTO->is_favorite,
            'category' => $manageCredentialDTO->category,
            'properties' => $manageCredentialDTO->properties,
            'created_by' => $manageCredentialDTO->created_by ?? $this->currentUser->getProfileId(),
            'updated_by' => $manageCredentialDTO->updated_by ?? $this->currentUser->getProfileId(),
        ]);

        return $credentialDTO;
    }

    /**
     * Process an array of access items into CredentialAccessDTOs.
     */
    protected function processCredentialAccessDTOs(int $credentialId, ManageCredentialDTO $manageCredentialDTO): array
    {
        $access = $manageCredentialDTO->access ?? [];
        if (empty($access)) {
            return [];
        }

        $credentialAccessDTOs = [];
        foreach ($access as $accessItem) {
            $credentialAccessDTOs[] = \App\DTOs\CredentialAccessDTO::fromArray([
                'credential_id' => $credentialId,
                'access_level' => $accessItem['access_level'] ?? null,
                'profile_id' => $accessItem['profile_id'] ?? null,
                'user_group' => $accessItem['user_group'] ?? null,
                'created_by' => $manageCredentialDTO->created_by ?? $this->currentUser->getProfileId(),
                'updated_by' => $manageCredentialDTO->updated_by ?? $this->currentUser->getProfileId(),
            ]);
        }

        return $credentialAccessDTOs;
    }
}
