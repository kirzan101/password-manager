<?php

namespace App\Providers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // register Telescope service provider if in local environment
        if ($this->app->environment('local') && class_exists(\Laravel\Telescope\TelescopeServiceProvider::class)) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }

        // register the Inertia service provider
        $this->app->bind(\App\Interfaces\UserModuleInterface::class, \App\Services\UserModuleService::class);

        // system services
        $this->app->bind(\App\Interfaces\BaseInterface::class, \App\Services\BaseService::class);
        $this->app->bind(\App\Interfaces\FetchInterfaces\BaseFetchInterface::class, \App\Services\FetchServices\BaseFetchService::class);
        $this->app->bind(\App\Interfaces\AuthInterface::class, \App\Services\AuthService::class);
        $this->app->bind(\App\Interfaces\CurrentUserInterface::class, \App\Services\CurrentUserService::class);
        $this->app->bind(\App\Interfaces\ManageAccountInterface::class, \App\Services\ManageAccountService::class);

        // module services
        $this->app->bind(\App\Interfaces\UserInterface::class, \App\Services\UserService::class);
        $this->app->bind(\App\Interfaces\FetchInterfaces\UserFetchInterface::class, \App\Services\FetchServices\UserFetchService::class);
        $this->app->bind(\App\Interfaces\ProfileInterface::class, \App\Services\ProfileService::class);
        $this->app->bind(\App\Interfaces\FetchInterfaces\ProfileFetchInterface::class, \App\Services\FetchServices\ProfileFetchService::class);
        $this->app->bind(\App\Interfaces\ModuleNameResolverInterface::class, \App\Services\ModuleNameResolverService::class);
        $this->app->bind(\App\Interfaces\ActivityLogInterface::class, \App\Services\ActivityLogService::class);
        $this->app->bind(\App\Interfaces\FetchInterfaces\ActivityLogFetchInterface::class, \App\Services\FetchServices\ActivityLogFetchService::class);
        $this->app->bind(\App\Interfaces\UserGroupInterface::class, \App\Services\UserGroupService::class);
        $this->app->bind(\App\Interfaces\FetchInterfaces\UserGroupFetchInterface::class, \App\Services\FetchServices\UserGroupFetchService::class);
        $this->app->bind(\App\Interfaces\PermissionInterface::class, \App\Services\PermissionService::class);
        $this->app->bind(\App\Interfaces\FetchInterfaces\PermissionFetchInterface::class, \App\Services\FetchServices\PermissionFetchService::class);
        $this->app->bind(\App\Interfaces\ProfileUserGroupInterface::class, \App\Services\ProfileUserGroupService::class);
        $this->app->bind(\App\Interfaces\RoleInterface::class, \App\Services\RoleService::class);
        $this->app->bind(\App\Interfaces\RolePermissionInterface::class, \App\Services\RolePermissionService::class);
        $this->app->bind(\App\Interfaces\ManageRoleInterface::class, \App\Services\ManageRoleService::class);
        $this->app->bind(\App\Interfaces\ProfileRoleInterface::class, \App\Services\ProfileRoleService::class);
        $this->app->bind(\App\Interfaces\FetchInterfaces\RoleFetchInterface::class, \App\Services\FetchServices\RoleFetchService::class);
        $this->app->bind(\App\Interfaces\ModuleInterface::class, \App\Services\ModuleService::class);
        $this->app->bind(\App\Interfaces\FetchInterfaces\ModuleFetchInterface::class, \App\Services\FetchServices\ModuleFetchService::class);
        $this->app->bind(\App\Interfaces\ActivityLoggerInterface::class, \App\Services\ActivityLoggerService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        JsonResource::withoutWrapping();
    }
}
