<?php

namespace App\Interfaces;

/**
 * Make the module name snake_case and pluralized.
 */
interface ModuleNameResolverInterface
{
    public function resolve(?string $module): ?string;
}
