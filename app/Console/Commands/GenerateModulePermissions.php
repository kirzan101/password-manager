<?php

namespace App\Console\Commands;

use App\Helpers\Helper;
use App\Models\Module;
use App\Models\Permission;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GenerateModulePermissions extends Command
{
    protected $signature = 'app:generate-module-permissions 
        {model : The name of the module} 
        {--create : Include create permission} 
        {--view : Include view permission} 
        {--update : Include update permission} 
        {--delete : Include delete permission}';

    protected $description = 'Generate module CRUD permissions and assign them to all roles';

    public function handle()
    {
        DB::transaction(function () {
            $originalModelName = $this->argument('model');

            $modelName = Helper::getModuleName($originalModelName);
            $moduleName = ucwords(str_replace('_', ' ', $modelName)); // add space between words and capitalize. 

            //if no arguments are provided, default to all permissions
            if (
                !$this->option('create') &&
                !$this->option('view') &&
                !$this->option('update') &&
                !$this->option('delete')
            ) {
                $types = [
                    'create' => true,
                    'view'   => true,
                    'update' => true,
                    'delete' => false, // default to false for delete permission
                ];
            } else {
                $types = [
                    'create' => $this->option('create'),
                    'view'   => $this->option('view'),
                    'update' => $this->option('update'),
                    'delete' => $this->option('delete'),
                ];
            }

            $createdPermissions = [];

            foreach ($types as $type => $isActive) {
                $permission = Permission::firstOrCreate(
                    ['module' => $modelName, 'type' => $type],
                    ['is_active' => $isActive]
                );

                $createdPermissions[] = $permission->id;
            }

            // Note: newly generated permissions are not automatically assigned to any role.
            // A role_permission record's existence now represents an active assignment, so
            // permissions must be explicitly granted to a role via the Roles UI.

            // Create the module entry
            Module::create([
                'name' => $moduleName,
                'icon' => 'DashboardIcon', // default icon, can be updated later
                'route' => '/' . Str::kebab($modelName),
                'category' => null, // default to null, can be updated later
                'order' => Module::max('order') + 1,
                'base_name' => $modelName,
            ]);
        });

        $this->info('Permissions and roles links generated successfully.');
    }
}
