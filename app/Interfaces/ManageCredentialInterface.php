<?php

namespace App\Interfaces;

use App\Data\ModelResponse;
use App\Data\StandardResponse;
use App\DTOs\ManageCredentialDTO;

interface ManageCredentialInterface
{
    /**
     * Store a new credential in the database.
     *
     * @param ManageCredentialDTO $manageCredentialDTO
     * @return ModelResponse
     */
    public function storeNewCredential(ManageCredentialDTO $manageCredentialDTO): ModelResponse;

    /**
     * Update an existing credential in the database.
     *
     * @param ManageCredentialDTO $manageCredentialDTO
     * @param int $credentialId
     * @return ModelResponse
     */
    public function updateCredential(ManageCredentialDTO $manageCredentialDTO, int $credentialId): ModelResponse;

    /**
     * Delete a credential from the database.
     *
     * @param int $credentialId
     * @return ModelResponse
     */
    public function deleteCredential(int $credentialId): ModelResponse;

    /**
     * Add access to a credential in the database.
     *
     * @param \App\DTOs\CredentialAccessDTO $credentialAccessDTO
     * @return StandardResponse
     */
    public function addAccessToCredential(\App\DTOs\CredentialAccessDTO $credentialAccessDTO): StandardResponse;

    /**
     * Remove access to a credential in the database.
     *
     * @param int $credentialAccessId
     * @return StandardResponse
     */
    public function removeAccessToCredential(int $credentialAccessId): StandardResponse;

    /**
     * Update the access level for a specific credential access to a credential in the database.
     *
     * @param int $credentialAccessId
     * @param string $newAccessLevel
     * @return StandardResponse
     */
    public function updateAccessLevelToCredential(int $credentialAccessId, string $newAccessLevel): StandardResponse;

    /**
     * Reveal the secret for a specific credential.
     *
     * @param int $credentialId
     * @return string|null
     */
    public function revealCredentialSecret(int $credentialId): ?string;
}
