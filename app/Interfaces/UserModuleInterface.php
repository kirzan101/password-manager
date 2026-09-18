<?php

namespace App\Interfaces;

use App\Data\ModelResponse;
use App\DTOs\UserModuleDTO;

interface UserModuleInterface
{
    /**
     * Get the list of modules that the authenticated user has access to based on their permissions.
     *
     * This method checks the user's profile and retrieves all active permissions associated with that profile's roles.
     * It then extracts the unique module names from those permissions and caches the result for 30 minutes.
     * If the user is an admin, all active modules will be returned regardless of their specific permissions.
     *
     * @return array List of module names that the user has access to (e.g. ['profiles', 'user_management']).
     *               Returns an empty array if the user is not authenticated or has no permissions.
     */
    public function getAccessibleModules(?int $profileId): array;

    /**
     * Get all permissions for the given profile across every module.
     *
     * Returns a flat array of permission strings (e.g. ['view-users', 'create-roles'])
     * for every active permission assigned to the profile via its roles.
     * Results are cached using the global permissions version so the cache is
     * automatically invalidated whenever refreshGlobalPermissionsVersion() is called.
     *
     * @param int $profileId The ID of the profile to evaluate.
     * @return array List of permission strings across all modules.
     */
    public function getAllPermissions(int $profileId): array;

    /**
     * Refresh the global permissions version.
     *
     * This method increments the global permissions version in the cache. It is used to invalidate
     * cached module access data for all users when permissions are updated.
     */
    public function refreshGlobalPermissionsVersion(): void;
}
