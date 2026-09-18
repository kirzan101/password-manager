<?php

namespace App\Http\Controllers\System;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Interfaces\FetchInterfaces\ModuleFetchInterface;
use App\Interfaces\FetchInterfaces\PermissionFetchInterface;
use App\Traits\ReturnMessageTrait;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class SettingsController extends Controller
{
    use ReturnMessageTrait;

    public function __construct(
        private PermissionFetchInterface $permissionFetch,
        private ModuleFetchInterface $moduleFetch
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        [
            'permissions' => $permissions,
            'moduleLists' => $moduleLists,
            'settingsModules' => $settingsModules,
        ] = $this->getCacheData();

        if (empty($settingsModules)) {
            return Inertia::render('Error', [
                'code' => 403,
                'message' => 'You do not have permission to view this page.'
            ]);
        }

        $fetchResult = $this->permissionFetch->permissionsByModule();
        $permissionsByModule = $fetchResult->data ?? [];

        return Inertia::render('System/Settings', [
            'userGroupTypes' => Helper::USER_GROUP_CODE_TYPES,      // user group props
            'permissions' => $permissions,                          // role props
            'permissionsByModule' => $permissionsByModule,          // permissions by module props
            'moduleLists' => $moduleLists,                          // role props
            'categories' => Helper::MODULE_CATEGORIES,              // module props
        ]);
    }

    /**
     * Fetch and cache the required data for the settings page.
     *
     * @return array
     */
    protected function getCacheData(): array
    {
        $permissions = Cache::remember('permission_fetch_list', now()->addHour(), function () {
            // Fetch the result and extract only the 'data' part
            $result = $this->permissionFetch->indexPermissions();
            return $result->data ?? []; // Only return 'data' part
        });

        $modules = Cache::remember('module_lists', now()->addHour(), function () {
            $result = $this->moduleFetch->indexModules();
            return $result->data ?? collect(); // Only return 'data' part
        });

        $moduleLists = $modules->pluck('name')->toArray();
        $settingsModules = $modules->where('category', Helper::MODULE_CATEGORY_SETTINGS)->pluck('name')->toArray();

        return [
            'permissions' => $permissions,
            'moduleLists' => $moduleLists,
            'modules' => $modules,
            'settingsModules' => $settingsModules,
        ];
    }
}
