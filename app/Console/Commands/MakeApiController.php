<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeApiController extends Command
{
    protected $signature = 'make:api-controller {model : Model name}';
    protected $description = 'Create a new controller in the API folder with API methods';

    public function handle(): void
    {
        $base = Str::studly($this->argument('model'));   // e.g., "UserGroup"
        $basePlural = Str::pluralStudly($base);          // e.g., "UserGroups"
        $variable = Str::camel($base);                   // e.g., "userGroup"

        // Check if Fetch interface exists
        $fetchInterfacePath = app_path("Interfaces/FetchInterfaces/{$base}FetchInterface.php");
        if (!File::exists($fetchInterfacePath)) {
            $this->error("Fetch interface not found: {$fetchInterfacePath}");
            return;
        }

        // resource paths
        $resourcePath = app_path("Http/Resources/{$base}Resource.php");
        $indexResourceDir = app_path("Http/Resources/IndexResource");
        $indexResourcePath = "{$indexResourceDir}/{$base}IndexResource.php";

        // Ensure IndexResource directory exists
        File::ensureDirectoryExists($indexResourceDir);

        // Generate resource files if they do not exist
        if (!File::exists($resourcePath)) {
            Artisan::call('make:resource', ['name' => "{$base}Resource"]);
            $this->info("Resource [App\\Http\\Resources\\{$base}Resource.php] created successfully.");
        }

        if (!File::exists($indexResourcePath)) {
            Artisan::call('make:resource', ['name' => "IndexResource/{$base}IndexResource"]);
            $this->info("Resource [App\\Http\\Resources\\IndexResource\\{$base}IndexResource.php] created successfully.");
        }

        // controller paths
        $controllerName = "{$base}ApiController";
        $controllerDir = app_path('Http/Controllers/API');
        $controllerPath = "{$controllerDir}/{$controllerName}.php";
        $stubPath = base_path('stubs/api-controller.stub');

        if (!File::exists($stubPath)) {
            $this->error("Stub file not found: {$stubPath}");
            return;
        }

        // create controller
        File::ensureDirectoryExists($controllerDir);
        $controllerStub = File::get($stubPath);

        $controllerContent = str_replace(
            ['{{ base }}', '{{ basePlural }}', '{{ variable }}'],
            [$base, $basePlural, $variable],
            $controllerStub
        );

        File::put($controllerPath, $controllerContent);

        $this->info("API controller [App\\Http\\Controllers\\API\\{$controllerName}.php] created successfully.");
    }
}
