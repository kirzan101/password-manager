<?php

namespace App\Interfaces;

use App\Data\ModelResponse;
use App\DTOs\CredentialAccessDTO;

interface CredentialAccessInterface
{
    /**
     * Store a new credential access in the database.
     *
     * @param  CredentialAccessDTO $credentialAccessDTO
     * @return ModelResponse
     */
    public function storeCredentialAccess(CredentialAccessDTO $credentialAccessDTO): ModelResponse;

    /**
     * Update an existing credential access in the database.
     *
     * @param  CredentialAccessDTO $credentialAccessDTO
     * @param  int    $credentialAccessId
     * @return ModelResponse
     */
    public function updateCredentialAccess(CredentialAccessDTO $credentialAccessDTO, int $credentialAccessId): ModelResponse;

    /**
     * Delete the given credential access in the database.
     *
     * @param  int  $credentialAccessId
     * @return ModelResponse
     */
    public function deleteCredentialAccess(int $credentialAccessId): ModelResponse;
}
