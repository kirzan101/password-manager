<?php

namespace App\Helpers;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Helper
{
    /**
     * set default success status
     */
    const SUCCESS = 'success';

    /**
     * set default error status
     */
    const ERROR = 'error';

    /**
     * Action types for user permissions.
     */
    const ACTION_TYPES = [
        'create',
        'update',
        'delete',
        'view',
        'restore',
        'reset',
        'set-status',
        'set-avatar'
    ];
    const ACTION_TYPE_CREATE = 'create';
    const ACTION_TYPE_UPDATE = 'update';
    const ACTION_TYPE_DELETE = 'delete';
    const ACTION_TYPE_VIEW = 'view';
    const ACTION_TYPE_RESTORE = 'restore';
    const ACTION_TYPE_RESET = 'reset';
    const ACTION_TYPE_SET_STATUS = 'set-status';
    const ACTION_TYPE_SET_AVATAR = 'set-avatar';

    /**
     * Normalize name: remove accents and convert ñ to n.
     */
    protected static function normalizeName(string $name): ?string
    {
        // Transliterate to ASCII (é → e, ü → u, etc.)
        $name = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $name);

        // Fallback in case transliteration fails
        return $name ?: '';
    }

    /**
     * Generate a username based on first and last names.
     *
     * @param string $first_name
     * @param string $last_name
     * @return string|null
     */
    public static function generateUsername(string $first_name, string  $last_name): ?string
    {
        // If either name is empty, return null
        if (trim($first_name) === '' || trim($last_name) === '') {
            return null;
        }

        // Normalize: convert special characters (like ñ, é) to ASCII equivalents
        $first_name = self::normalizeName($first_name);
        $last_name = self::normalizeName($last_name);

        // Lowercase
        $first_name = strtolower(trim($first_name));
        $last_name = strtolower(trim($last_name));

        // Get first initial
        $initial = substr($first_name, 0, 1);

        // Clean last name: remove spaces and non-alphabetic characters
        $clean_last_name = preg_replace('/[^a-z]/', '', str_replace(' ', '', $last_name));

        // Base username
        $base_username = $initial . $clean_last_name;
        $username = $base_username;

        // Add numeric suffix if username already exists
        $suffix = 1;
        while (User::where('username', $username)->exists()) {
            $username = $base_username . $suffix;
            $suffix++;
        }

        return $username;
    }

    /**
     * Generate an email address ending with "@mail.com" using first and last names.
     *
     * @param string $first_name
     * @param string $last_name
     * @return string|null
     */
    public static function generateEmail(string $first_name, string $last_name): ?string
    {
        // If either name is empty, return null
        if (trim($first_name) === '' || trim($last_name) === '') {
            return null;
        }

        // Normalize names
        $first_name = self::normalizeName($first_name);
        $last_name = self::normalizeName($last_name);

        // Lowercase and strip spaces + non-alphabetic characters
        $first = strtolower(preg_replace('/[^a-z]/', '', str_replace(' ', '', $first_name)));
        $last = strtolower(preg_replace('/[^a-z]/', '', str_replace(' ', '', $last_name)));

        // Combine for email
        return $first . '.' . $last . '@mail.com';
    }

    /**
     * Get the module name from the request and convert it to snake_case.
     *
     * @param string|null $originalModuleName
     * @return string|null
     */
    public static function getModuleName(?string $originalModuleName): ?string
    {
        // If the module name is empty or null, return null
        if (empty($originalModuleName)) {
            return null;
        }

        // Convert the module name to snake_case
        $singularName = Str::snake($originalModuleName);

        // Try to pluralize the snake_case version of the name
        $pluralName = Str::plural($singularName);

        // Return the plural name if it differs from the singular, otherwise return the singular
        return ($pluralName === $singularName) ? $singularName : $pluralName;
    }

    /**
     * Get a list of all unique module names from the permissions table.
     *
     * @return array
     */
    public static function getModuleList(): array
    {
        $modules = DB::table('permissions')->distinct()->pluck('module')->toArray();
        return $modules;
    }

    /**
     * account types
     */
    const ACCOUNT_TYPES = [
        'ADMIN',
        'USER'
    ];
    const ACCOUNT_TYPE_ADMIN = 'ADMIN';
    const ACCOUNT_TYPE_USER = 'USER';

    /**
     * account statuses
     */
    const ACCOUNT_STATUSES = [
        'active',
        'inactive'
    ];
    const ACCOUNT_STATUS_ACTIVE = 'active';
    const ACCOUNT_STATUS_INACTIVE = 'inactive';

    /**
     * user group codes
     */
    const USER_GROUP_CODE_TYPES = ['ADMIN', 'USER'];
    const USER_GROUP_CODE_ADMIN = 'ADMIN';
    const USER_GROUP_CODE_USER = 'USER';

    /**
     * module categories
     */
    const MODULE_CATEGORIES = [
        'settings',
        // Add more categories as needed
    ];
    const MODULE_CATEGORY_SETTINGS = 'settings';
}
