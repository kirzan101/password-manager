<?php

namespace App\Interfaces\FetchInterfaces;

use App\Data\PaginateResponse;
use App\Data\CollectionResponse;
use App\Data\ModelResponse;

interface CredentialFetchInterface
{
    /**
     * Fetch a list of credential with optional search functionality.
     *
     * @param array $request Optional parameters for filtering or pagination.
     * @param bool $isPaginated Whether to paginate the results.
     * @param class-string<\Illuminate\Http\Resources\Json\JsonResource>|null $resourceClass The resource class to transform the results.
     * @return PaginateResponse|CollectionResponse A response containing the list of credentials, either paginated or as a collection.
     */
    public function indexCredentials(array $request = [], bool $isPaginated = false, ?string $resourceClass = null): PaginateResponse|CollectionResponse;

    /**
     * Fetch a single credential by ID.
     *
     * @param integer $credentialId
     * @param class-string<\Illuminate\Http\Resources\Json\JsonResource>|null $resourceClass
     * @return ModelResponse A model response containing the credential.
     */
    public function showCredential(int $credentialId, ?string $resourceClass = null): ModelResponse;
}
