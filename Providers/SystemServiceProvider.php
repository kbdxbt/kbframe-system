<?php

namespace Modules\System\Providers;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class SystemServiceProvider extends PackageServiceProvider
{
    protected string $moduleName = 'System';

    protected string $moduleNameLower = 'system';

    public function configurePackage(Package $package): void
    {
        $package
            ->name($this->moduleName)
            ->hasConfigFile(['config']);
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);

        parent::register();
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [];
    }
}
