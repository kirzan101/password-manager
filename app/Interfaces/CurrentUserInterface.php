<?php

namespace App\Interfaces;

interface CurrentUserInterface
{
    /**
     * get the authenticated user's profile ID.
     *
     * @return integer
     */
    public function getProfileId(): ?int;

    /**
     * Get the authenticated user's ID.
     *
     * @return integer
     */
    public function getUserId(): ?int;

    /**
     * Check if the authenticated user is on their first login.
     *
     * @return bool
     */
    public function isFirstLogin(): bool;
}
