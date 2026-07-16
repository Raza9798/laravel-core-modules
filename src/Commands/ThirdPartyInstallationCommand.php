<?php

namespace Raza9798\LaravelCoreModules\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Raza9798\LaravelCoreModules\Services\ClassGenerator;

class ThirdPartyInstallationCommand extends Command
{
    protected $signature = 'module:install {params?}';

    protected $description = 'Install third-party package for Laravel Core Modules';

    public function handle()
    {
        $params = $this->argument('params');

        if (!$params) {
            $params = $this->choice('Select a package to install', array_values($this->paramsList()));
            $this->info("Selected package: $params");

            $packageKey = array_search($params, $this->paramsList());
            switch ($packageKey) {
                case 'api_authentication':
                    $this->installApiAuthenticationPackage();
                    break;
                default:
                    $this->error("Installation for the selected package is not implemented yet.");
                    break;
            }
        }
    }

    protected function paramsList()
    {
        return [
            'api_authentication' => 'API Authentication Package',
        ];
    }

    protected function installApiAuthenticationPackage()
    {
        $this->info('Installing API Authentication Package...');
        Artisan::call('module:create Authentication');
        File::copy(__DIR__ . '/../stubs/Authentication/LoginController.php', base_path('Modules/Authentication/app/Http/Controllers/LoginController.php'));
        File::copy(__DIR__ . '/../stubs/Authentication/LogoutController.php', base_path('Modules/Authentication/app/Http/Controllers/LogoutController.php'));
        File::copy(__DIR__ . '/../stubs/Authentication/add_authentication_columns_to_users_table.php', base_path("Modules/Authentication/database/migrations/" . date('Y_m_d_His') . "_add_authentication_columns_to_users_table.php"));
        File::copy(__DIR__ . '/../stubs/Authentication/api.php', base_path('Modules/Authentication/routes/api.php'));

        $userModel = app_path('Models/User.php');
        $contents = File::get($userModel);
        $content = str_replace(
            "#[Fillable(['name', 'email', 'password'])]",
            "#[Fillable(['name', 'email', 'password', 'two_factor_secret', 'two_factor_verified_at', 'has_otp_login', 'has_login', 'is_active'])]", $contents
        );
        File::put($userModel, $content);

        $contents = File::get($userModel);
        $content = str_replace(
            "#[Hidden(['password', 'remember_token'])]",
            "#[Hidden(['password', 'remember_token', 'two_factor_secret', 'two_factor_verified_at', 'has_otp_login', 'has_login'])]", $contents
        );
        File::put($userModel, $content);
        
        $contents = File::get($userModel);
        $content = str_replace(
            "use Illuminate\Foundation\Auth\User as Authenticatable;",
            "use Illuminate\Foundation\Auth\User as Authenticatable;\nuse Laravel\Sanctum\HasApiTokens;", $contents
        );
        File::put($userModel, $content);

        $contents = File::get($userModel);
        $content = str_replace(
            "use HasFactory, Notifiable;",
            "use HasFactory, Notifiable, HasApiTokens;", $contents
        );
        File::put($userModel, $content);
        $this->info('API Authentication Package installed successfully.');
    }
}