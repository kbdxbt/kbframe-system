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
    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);

        $this->registerModuleDatabase();

        parent::register();
    }

    protected function registerModuleDatabase(): void
    {
        config([
            'database.connections.system' => [
                'driver' => 'mysql',
                'url' => env('DATABASE_URL'),
                'host' => env('DB_HOST', '127.0.0.1'),
                'port' => env('DB_PORT', '3306'),
                'database' => env('DB_SYSTEM_DATABASE', 'system'),
                'username' => env('DB_USERNAME', 'forge'),
                'password' => env('DB_PASSWORD', ''),
                'unix_socket' => env('DB_SOCKET', ''),
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'prefix_indexes' => true,
                'strict' => true,
                'engine' => null,
                'options' => extension_loaded('pdo_mysql') ? array_filter([
                    \PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
//                PDO::ATTR_PERSISTENT => true, // 开启持久化连接
//                PDO::ATTR_TIMEOUT => 10, // 连接超时时间（秒）
                ]) : [],
            ],
        ]);
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [];
    }
}
