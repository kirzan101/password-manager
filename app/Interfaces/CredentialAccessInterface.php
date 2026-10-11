<?php

namespace App\Interfaces;

use App\Data\ModelResponse;
use App\Data\StandardResponse;
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

    /**
     * Store multiple credential accesses in the database.
     *
     * @param array $credentialAccessDTOs
     * @return StandardResponse
     */
    public function storeMultipleCredentialAccess(array $credentialAccessDTOs): StandardResponse;

    /**
     * Update the access level for a specific credential access in the database.
     *
     * @param int $credentialAccessId
     * @param string $newAccessLevel
     * @return StandardResponse
     */
    public function updateCredentialAccessLevel(int $credentialAccessId, string $newAccessLevel): StandardResponse;
}
