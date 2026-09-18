<?php

namespace App\Interfaces;

use App\Data\ModelResponse;
use App\DTOs\ProfileDTO;

interface ProfileInterface
{
    /**
     * Store a new profile in the database.
     *
     * @param ProfileDTO $profileDTO
     * @return ModelResponse
     */
    public function storeProfile(ProfileDTO $profileDTO): ModelResponse;

    /**
     * update an existing profile in the database.
     *
     * @param ProfileDTO $profileDTO
     * @param integer $profileId
     * @return ModelResponse
     */
    public function updateProfile(ProfileDTO $profileDTO, int $profileId): ModelResponse;

    /**
     * delete a profile from the database.
     *
     * @param integer $profileId
     * @return ModelResponse
     */
    public function deleteProfile(int $profileId): ModelResponse;
}
