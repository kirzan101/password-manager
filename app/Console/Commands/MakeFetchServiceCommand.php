<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeFetchServiceCommand extends Command
{
    protected $signature = 'make:fetch-service {name}';
    protected $description = 'Generate a new fetch service class and corresponding fetch interface using stubs';

    public function handle(): void
    {
        $baseName = Str::studly($this->argument('name'));      // e.g., "UserGroup"
        $basePlural = Str::plural($baseName);                  // e.g., "UserGroups"
        $variablePlural = Str::camel($basePlural);              // e.g., "userGroups"
        $variableName = Str::camel($baseName);                 // e.g., "userGroup"
        $className = "{$baseName}FetchService";                // e.g., "UserGroupFetchService"
        $interfaceName = "{$baseName}FetchInterface";          // e.g., "UserGroupFetchInterface"
        $readableLabel = ucfirst(strtolower(preg_replace('/(?<!^)[A-Z]/', ' $0', $baseName))); // e.g., "User Group"
        $readableDescription = strtolower(strtolower(preg_replace('/(?<!^)[A-Z]/', ' $0', $baseName))); // e.g., "user group"
        $readableDescriptionPlural = strtolower(strtolower(preg_replace('/(?<!^)[A-Z]/', ' $0', $basePlural))); // e.g., "user groups"

        // Paths
        $servicePath = app_path("Services/FetchServices/{$className}.php");
        $interfacePath = app_path("Interfaces/FetchInterfaces/{$interfaceName}.php");

        $serviceStubPath = base_path('stubs/fetch-service.stub');
        $interfaceStubPath = base_path('stubs/fetch-interface.stub');

        // Validate stub files
        if (!File::exists($serviceStubPath) || !File::exists($interfaceStubPath)) {
            $this->error('One or both stub files are missing in /stubs directory.');
            return;
        }

        // Prevent overwrite
        if (File::exists($servicePath)) {
            $this->error("Fetch Service already exists at: {$servicePath}");
            return;
        }
        if (File::exists($interfacePath)) {
            $this->error("Fetch Interface already exists at: {$interfacePath}");
            return;
        }

        // Generate service file
        $serviceStub = File::get($serviceStubPath);
        $serviceContent = str_replace(
            ['{{ namespace }}', '{{ class }}', '{{ base }}', '{{ interface }}', '{{ readable }}', '{{ description }}', '{{ variable }}', '{{ basePlural }}', '{{ variablePlural }}', '{{ descriptionPlural }}'],
            ['App\\Services\\FetchServices', $className, $baseName, $interfaceName, $readableLabel, $readableDescription, $variableName, $basePlural, $variablePlural, $readableDescriptionPlural],
            $serviceStub
        );
        File::ensureDirectoryExists(app_path('Services\\FetchServices'));
        File::put($servicePath, $serviceContent);

        // Generate interface file
        $interfaceStub = File::get($interfaceStubPath);
        $interfaceContent = str_replace(
            ['{{ namespace }}', '{{ interface }}', '{{ base }}', '{{ readable }}', '{{ description }}', '{{ variable }}', '{{ basePlural }}', '{{ variablePlural }}', '{{ descriptionPlural }}'],
            ['App\\Interfaces\\FetchInterfaces', $interfaceName, $baseName, $readableLabel, $readableDescription, $variableName, $basePlural, $variablePlural, $readableDescriptionPlural],
            $interfaceStub
        );
        File::ensureDirectoryExists(app_path('Interfaces\\FetchInterfaces'));
        File::put($interfacePath, $interfaceContent);

        // Success messages
        $this->components->info("Interface [app/Interfaces/FetchInterfaces/{$interfaceName}.php] created successfully.");
        $this->components->info("Service [app/Services/FetchServices/{$className}.php] created successfully.");
    }
}
