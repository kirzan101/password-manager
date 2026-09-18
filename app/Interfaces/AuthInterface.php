<?php

namespace App\Interfaces;

use App\Data\ModelResponse;

interface AuthInterface
{
    /**
     * Log in a user with the provided credentials.
     *
     * @param array $request
     * @return ModelResponse
     */
    public function login(array $request): ModelResponse;

    /**
     * Log out the currently authenticated user.
     *
     * @return ModelResponse
     */
    public function logout(): ModelResponse;

    /**
     * Get the API token of the currently authenticated user.
     *
     * @return string|null
     */
    public function getToken(): ?string;
}
