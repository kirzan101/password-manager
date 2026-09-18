<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeServiceDto extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:service-dto {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new service DTO class';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $baseName = Str::studly($this->argument('name')); // e.g., "UserGroup"
        $variableName = Str::camel($baseName);            // e.g., "userGroup"
        $dtoName = "{$baseName}DTO";                      // e.g., "UserGroupDTO"
        $readableLabel = ucfirst(strtolower(preg_replace('/(?<!^)[A-Z]/', ' $0', $baseName)));
        $readableDescription = strtolower(strtolower(preg_replace('/(?<!^)[A-Z]/', ' $0', $baseName)));

        $dtoPath = app_path("DTOs/{$dtoName}.php");
        $dtoStubPath = base_path('stubs/dto.stub');

        if (!File::exists($dtoStubPath)) {
            $this->error('One or more stub files are missing in /stubs directory.');
            return;
        }

        // Generate DTO file
        $dtoStub = File::get($dtoStubPath);
        $dtoContent = str_replace(
            ['{{ namespace }}', '{{ dto }}', '{{ base }}', '{{ readable }}', '{{ description }}', '{{ variable }}'],
            ['App\\DTOs', $dtoName, $baseName, $readableLabel, $readableDescription, $variableName],
            $dtoStub
        );
        File::ensureDirectoryExists(app_path('DTOs'));
        File::put($dtoPath, $dtoContent);

        $this->components->info("DTO [app/DTOs/{$baseName}DTO.php] created successfully.");
    }
}
