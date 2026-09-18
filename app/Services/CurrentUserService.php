<?php

namespace App\Services;

use App\Helpers\Helper;
use App\Interfaces\CurrentUserInterface;
use App\Traits\HttpErrorCodeTrait;
use App\Traits\ReturnModelCollectionTrait;
use App\Traits\ReturnModelTrait;
use App\Interfaces\BaseInterface;
use App\Interfaces\FetchInterfaces\BaseFetchInterface;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use RuntimeException;

class CurrentUserService implements CurrentUserInterface
{
    use HttpErrorCodeTrait;
    use ReturnModelCollectionTrait;
    use ReturnModelTrait;

    public function __construct(
        private BaseInterface $base,
        private BaseFetchInterface $fetch,
    ) {}

    private ?User $cachedUser = null;

    /**
     * get the authenticated user's profile ID.
     *
     * @return integer|null
     */
    public function getProfileId(): ?int
    {
        $user = $this->getUser();

        return $user?->profile?->id;
    }

    /**
     * Get the authenticated user's ID.
     *
     * @return integer|null
     */
    public function getUserId(): ?int
    {
        $user = $this->getUser();

        return $user?->id;
    }

    /**
     * Check if the authenticated user is on their first login.
     *
     * @return bool
     */
    public function isFirstLogin(): bool
    {
        $user = $this->getUser();

        // For non-local environments, fail if missing
        if (!$user) {
            throw new RuntimeException('Authenticated user or profile not found.');
        }

        return $user->is_first_login;
    }


    #methods start
    /**
     * Get the authenticated user.
     *
     * @return User|null
     */
    private function getUser(): ?User
    {
        if ($this->cachedUser === null) {
            $this->cachedUser = Auth::user();
        }

        return $this->cachedUser;
    }
    #methods end
}
