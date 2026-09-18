<?php

namespace Database\Seeders;

use App\Helpers\Helper;
use App\Models\Module;
use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            [
                'module' => 'users',
                'icon' => 'PeopleIcon',
                'types' => ['create', 'view', 'update', 'delete', 'reset', 'set-status', 'set-avatar'],
                'order' => 1,
                'category' => null,
            ],
            [
                'module' => 'activity_logs',
                'icon' => 'HistoryIcon',
                'types' => ['view'], // only view permission for activity logs
                'order' => 2,
                'category' => null,
            ],
            [
                'module' => 'user_groups',
                'icon' => 'GroupsIcon',
                'types' => ['create', 'view', 'update', 'delete'],
                'order' => 3,
                'category' => Helper::MODULE_CATEGORY_SETTINGS,
            ],
            [
                'module' => 'roles',
                'icon' => 'RoleIcon',
                'types' => ['create', 'view', 'update', 'delete'],
                'order' => 4,
                'category' => Helper::MODULE_CATEGORY_SETTINGS,
            ],
            [
                'module' => 'permissions',
                'icon' => 'SecurityIcon',
                'types' => ['create', 'view', 'update', 'delete'],
                'order' => 5,
                'category' => Helper::MODULE_CATEGORY_SETTINGS,
            ],
            // [
            //     'module' => 'modules',
            //     'icon' => 'ViewModuleIcon',
            //     'types' => ['create', 'view', 'update', 'delete'],
            //     'order' => 6,
            //     'category' => Helper::MODULE_CATEGORY_SETTINGS,
            // ],
        ];

        $accessTypes = ['create', 'view', 'update', 'delete', 'reset', 'set-status', 'set-avatar'];

        foreach ($permissions as $permission) {
            foreach ($accessTypes as $type) {
                // skip if the current access type is not defined for this permission module
                if (!in_array($type, $permission['types'], true)) {
                    continue;
                }

                $exists = Permission::where('module', $permission['module'])
                    ->where('type', $type)
                    ->exists();

                // skip if the permission already exists in the database
                if ($exists) {
                    continue;
                }

                Permission::create([
                    'module' => $permission['module'],
                    'type' => $type,
                    'is_active' => in_array($type, $permission['types']),
                ]);
            }

            // create module
            Module::create([
                'name' => Str::title(str_replace('_', ' ', $permission['module'])),
                'icon' => $permission['icon'],
                'category' => $permission['category'] ?? null,
                'route' => '/' . str_replace('_', '-', $permission['module']),
                'order' => $permission['order'],
                'base_name' => $permission['module'],
            ]);
        }
    }
}
