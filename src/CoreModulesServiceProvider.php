<?php

namespace Raza9798\LaravelCoreModules;

use Illuminate\Support\ServiceProvider;
use Raza9798\LaravelCoreModules\Commands\CoreModulesInstallCommand;
use Raza9798\LaravelCoreModules\Commands\ModuleCreateCommand;
use Raza9798\LaravelCoreModules\Commands\ModuleMakeCommand;
use Raza9798\LaravelCoreModules\Commands\ThirdPartyInstallationCommand;
use Illuminate\Support\Facades\File;

class CoreModulesServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/laravel-core-modules.php',
            'laravel-core-modules'
        );
    }


    public function boot()
    {

        if ($this->app->runningInConsole()) {
            $this->commands([
                CoreModulesInstallCommand::class,
                ModuleCreateCommand::class,
                ModuleMakeCommand::class,
                ThirdPartyInstallationCommand::class,
            ]);

            $this->publishes([
                __DIR__ . '/../config/laravel-core-modules.php'
                    => config_path('laravel-core-modules.php'),
            ], 'laravel-core-modules-config');
        }

        $modulesPath = config('laravel-core-modules.modules_path', base_path('Modules'));

        if (File::exists($modulesPath)) {
            foreach (File::directories($modulesPath) as $module) {
                $migrationPath = $module . '/database/migrations';
                if (File::isDirectory($migrationPath)) {
                    $this->loadMigrationsFrom($migrationPath);
                }
                $this->loadRoutesFrom($module . '\\routes\\web.php');
                $this->loadRoutesFrom($module . '\\routes\\api.php');
            }
        }
    }
}