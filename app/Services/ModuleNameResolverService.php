<?php

namespace App\Services;

use App\Interfaces\ModuleNameResolverInterface;
use App\Helpers\Helper;

class ModuleNameResolverService implements ModuleNameResolverInterface
{
    /**
     * Make the module name snake_case and pluralized.
     *
     * @param string|null $module
     * @return string
     */
    public function resolve(?string $module): ?string
    {
        return Helper::getModuleName($module);
    }
}
