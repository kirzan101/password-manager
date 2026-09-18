<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeServiceGuardCommand extends Command
{
    protected $signature = 'make:service-guard {name}';
    protected $description = 'Generate a new guard service class and corresponding interface using stubs';

    public function handle(): void
    {
        $baseName = Str::studly($this->argument('name')); // e.g., "UserGroup"
        $variableName = Str::camel($baseName);            // e.g., "userGroup"
        $className = "Guard{$baseName}Service";           // e.g., "GuardUserGroupService"
        $interfaceName = "Guard{$baseName}Interface";     // e.g., "GuardUserGroupInterface"
        $readableLabel = ucfirst(strtolower(preg_replace('/(?<!^)[A-Z]/', ' $0', $baseName)));
        $readableDescription = strtolower(strtolower(preg_replace('/(?<!^)[A-Z]/', ' $0', $baseName)));

        // Paths
        $servicePath = app_path("Services/GuardServices/{$className}.php");
        $interfacePath = app_path("Interfaces/GuardInterfaces/{$interfaceName}.php");

        $serviceStubPath = base_path('stubs/guard-service.stub');
        $interfaceStubPath = base_path('stubs/guard-interface.stub');

        // Validate stub files
        if (!File::exists($serviceStubPath) || !File::exists($interfaceStubPath)) {
            $this->error('One or more stub files are missing in /stubs directory.');
            return;
        }

        // Prevent overwrite
        if (File::exists($servicePath)) {
            $this->error("Service already exists at: {$servicePath}");
            return;
        }
        if (File::exists($interfacePath)) {
            $this->error("Interface already exists at: {$interfacePath}");
            return;
        }

        // Generate service file
        $serviceStub = File::get($serviceStubPath);
        $serviceContent = str_replace(
            ['{{ namespace }}', '{{ class }}', '{{ base }}', '{{ interface }}', '{{ readable }}', '{{ description }}', '{{ variable }}'],
            ['App\\Services\\GuardServices', $className, $baseName, $interfaceName, $readableLabel, $readableDescription, $variableName],
            $serviceStub
        );
        File::ensureDirectoryExists(app_path('Services/GuardServices'));
        File::put($servicePath, $serviceContent);

        // Generate interface file
        $interfaceStub = File::get($interfaceStubPath);
        $interfaceContent = str_replace(
            ['{{ namespace }}', '{{ interface }}', '{{ base }}', '{{ readable }}', '{{ description }}', '{{ variable }}'],
            ['App\\Interfaces\\GuardInterfaces', $interfaceName, $baseName, $readableLabel, $readableDescription, $variableName],
            $interfaceStub
        );
        File::ensureDirectoryExists(app_path('Interfaces/GuardInterfaces'));
        File::put($interfacePath, $interfaceContent);

        // Success messages
        $this->components->info("Interface [app/Interfaces/GuardInterfaces/{$interfaceName}.php] created successfully.");
        $this->components->info("Service [app/Services/GuardServices/{$className}.php] created successfully.");
    }
}
