<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class MakeServicePolicy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:service-policy {model : The name of the model for which to create the service policy}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a service policy for the specified model';

    /**
     * Execute the console command.
     */
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
    }
}
