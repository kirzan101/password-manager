<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeServiceController extends Command
{
    protected $signature = 'make:service-controller {model : Model name}';
    protected $description = 'Create a new controller';

    public function handle(): void
    {
        $base = Str::studly($this->argument('model'));   // e.g., "UserGroup"
        $basePlural = Str::pluralStudly($base);          // e.g., "UserGroups"
        $variable = Str::camel($base);                   // e.g., "userGroup"
        $variablePlural = Str::camel($basePlural);       // e.g., "userGroups"
        $module = Str::snake($basePlural);               // e.g., "user_groups"

        // Policy Path
        $policyDir = app_path('Policies');
        $policyPath = "{$policyDir}/{$base}Policy.php";
        $policyStubPath = base_path('stubs/policy.stub');

        // check if policy already exists
        if (!File::exists($policyPath)) {
            if (!File::exists($policyStubPath)) {
                $this->error("Stub file not found: {$policyStubPath}");
                return;
            }
            // create policy
            File::ensureDirectoryExists($policyDir);
            $policyStub = File::get($policyStubPath);
            $policyContent = str_replace(
                ['{{ base }}', '{{ basePlural }}', '{{ variable }}', '{{ variablePlural }}', '{{ module }}'],
                [$base, $basePlural, $variable, $variablePlural, $module],
                $policyStub
            );
            File::put($policyPath, $policyContent);
            $this->info("Policy [App\\Policies\\{$base}Policy.php] created successfully.");
        } else {
            $this->info("Policy [App\\Policies\\{$base}Policy.php] already exists. Skipping policy creation.");
        }

        // controller paths
        $controllerName = "{$base}Controller";
        $controllerDir = app_path('Http/Controllers');
        $controllerPath = "{$controllerDir}/{$controllerName}.php";
        $stubPath = base_path('stubs/service-controller.stub');

        if (!File::exists($stubPath)) {
            $this->error("Stub file not found: {$stubPath}");
            return;
        }

        // cheeck if controller already exists
        if (File::exists($controllerPath)) {
            $this->error("Controller [App\\Http\\Controllers\\{$controllerName}.php] already exists!");
            return;
        }

        // create controller
        File::ensureDirectoryExists($controllerDir);
        $controllerStub = File::get($stubPath);

        $controllerContent = str_replace(
            ['{{ base }}', '{{ basePlural }}', '{{ variable }}', '{{ variablePlural }}', '{{ module }}'],
            [$base, $basePlural, $variable, $variablePlural, $module],
            $controllerStub
        );

        File::put($controllerPath, $controllerContent);

        $this->info("Controller [App\\Http\\Controllers\\{$controllerName}.php] created successfully.");
    }
}
