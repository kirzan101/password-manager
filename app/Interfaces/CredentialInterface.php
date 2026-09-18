<?php

namespace App\Interfaces;

use App\Data\ModelResponse;
use App\DTOs\CredentialDTO;

interface CredentialInterface
{
    /**
     * Store a new credential in the database.
     *
     * @param  CredentialDTO $credentialDTO
     * @return ModelResponse
     */
    public function storeCredential(CredentialDTO $credentialDTO): ModelResponse;

    /**
     * Update an existing credential in the database.
     *
     * @param  CredentialDTO $credentialDTO
     * @param  int    $credentialId
     * @return ModelResponse
     */
    public function updateCredential(CredentialDTO $credentialDTO, int $credentialId): ModelResponse;

    /**
     * Delete the given credential in the database.
     *
     * @param  int  $credentialId
     * @return ModelResponse
     */
    public function deleteCredential(int $credentialId): ModelResponse;
}
