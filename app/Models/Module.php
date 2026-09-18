<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Module extends Model
{
    /**
     * Boot method to clear cache when module is saved or deleted.
     *
     * @return void
     */
    protected static function booted()
    {
        static::saved(fn($module) => $module->clearModuleCache());
        static::deleted(fn($module) => $module->clearModuleCache());
    }

    /**
     * Clear the module cache for the current module.
     *
     * This method clears the cache for all profiles that have permissions related to this module.
     */
    public function clearModuleCache()
    {
        Cache::forget('active_modules');
        Cache::forget('module_lists');
        Cache::forget('active_modules');
    }

    protected $fillable = [
        'name',
        'icon',
        'route',
        'category',
        'order',
        'base_name', // format name same as the module name in permissions table. e.g "User Groups" => "user_groups"
        'is_active',
    ];
}
