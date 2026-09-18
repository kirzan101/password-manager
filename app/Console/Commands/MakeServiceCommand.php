<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeServiceCommand extends Command
{
    protected $signature = 'make:service {name}';
    protected $description = 'Generate a new service class and corresponding interface using stubs';

    public function handle(): void
    {
        $baseName = Str::studly($this->argument('name')); // e.g., "UserGroup"
        $variableName = Str::camel($baseName);            // e.g., "userGroup"
        $className = "{$baseName}Service";                // e.g., "UserGroupService"
        $interfaceName = "{$baseName}Interface";          // e.g., "UserGroupInterface"
        $dtoName = "{$baseName}DTO";                      // e.g., "UserGroupDTO"
        $dtoVariableName = Str::camel($baseName) . "DTO"; // e.g., "userGroupDTO"
        $readableLabel = ucfirst(strtolower(preg_replace('/(?<!^)[A-Z]/', ' $0', $baseName)));
        $readableDescription = strtolower(strtolower(preg_replace('/(?<!^)[A-Z]/', ' $0', $baseName)));

        // Paths
        $servicePath = app_path("Services/{$className}.php");
        $interfacePath = app_path("Interfaces/{$interfaceName}.php");
        $dtoPath = app_path("DTOs/{$dtoName}.php");

        $serviceStubPath = base_path('stubs/service.stub');
        $interfaceStubPath = base_path('stubs/interface.stub');
        $dtoStubPath = base_path('stubs/dto.stub');

        // Validate stub files
        if (!File::exists($serviceStubPath) || !File::exists($interfaceStubPath) || !File::exists($dtoStubPath)) {
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
            ['{{ namespace }}', '{{ class }}', '{{ base }}', '{{ interface }}', '{{ readable }}', '{{ description }}', '{{ variable }}', '{{ dto }}', '{{ dtoVariable }}'],
            ['App\\Services', $className, $baseName, $interfaceName, $readableLabel, $readableDescription, $variableName, $dtoName, $dtoVariableName],
            $serviceStub
        );
        File::ensureDirectoryExists(app_path('Services'));
        File::put($servicePath, $serviceContent);

        // Generate interface file
        $interfaceStub = File::get($interfaceStubPath);
        $interfaceContent = str_replace(
            ['{{ namespace }}', '{{ interface }}', '{{ base }}', '{{ readable }}', '{{ description }}', '{{ variable }}', '{{ dto }}', '{{ dtoVariable }}'],
            ['App\\Interfaces', $interfaceName, $baseName, $readableLabel, $readableDescription, $variableName, $dtoName, $dtoVariableName],
            $interfaceStub
        );
        File::ensureDirectoryExists(app_path('Interfaces'));
        File::put($interfacePath, $interfaceContent);

        // Generate DTO file
        $dtoStub = File::get($dtoStubPath);
        $dtoContent = str_replace(
            ['{{ namespace }}', '{{ dto }}', '{{ base }}', '{{ readable }}', '{{ description }}', '{{ variable }}'],
            ['App\\DTOs', $dtoName, $baseName, $readableLabel, $readableDescription, $variableName],
            $dtoStub
        );
        File::ensureDirectoryExists(app_path('DTOs'));
        File::put($dtoPath, $dtoContent);

        // Success messages
        $this->components->info("Interface [app/Interfaces/{$interfaceName}.php] created successfully.");
        $this->components->info("Service [app/Services/{$className}.php] created successfully.");
        $this->components->info("DTO [app/DTOs/{$dtoName}.php] created successfully.");
    }
}
