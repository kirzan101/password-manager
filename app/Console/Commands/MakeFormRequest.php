<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeFormRequest extends Command
{
    protected $signature = 'make:request-form {model : Model name}';
    protected $description = 'Create a new custom form request';

    public function handle(): void
    {
        $base = Str::studly($this->argument('model'));   // e.g., "UserGroup"
        $basePlural = Str::pluralStudly($base);          // e.g., "UserGroups"
        $variable = Str::camel($base);                   // e.g., "userGroup"
        $route = Str::kebab($basePlural);                // e.g., "user-groups"

        // check if form request already exists
        $requestPath = app_path("Http/Requests/{$base}FormRequest.php");
        if (File::exists($requestPath)) {
            $this->error("Form Request already exists: {$requestPath}");
            return;
        }

        // create form request from stub
        $stubPath = base_path('stubs/request-form.stub');
        if (!File::exists($stubPath)) {
            $this->error("Stub file not found: {$stubPath}");
            return;
        }

        $stub = File::get($stubPath);
        $stub = str_replace('{{ model }}', $base, $stub);
        $stub = str_replace('{{ modelPlural }}', $basePlural, $stub);
        $stub = str_replace('{{ variable }}', $variable, $stub);
        $stub = str_replace('{{ route }}', $route, $stub);

        File::put($requestPath, $stub);
        $this->info("Form Request created successfully: {$requestPath}");
    }
}
