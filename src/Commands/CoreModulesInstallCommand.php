<?php

namespace Raza9798\LaravelCoreModules\Commands;

use Illuminate\Console\Command;

class CoreModulesInstallCommand extends Command
{
    protected $signature = 'core-modules:install';

    protected $description = 'Install Laravel Core Modules package';

    public function handle()
    {
        $this->info('');
        $this->info('✅ Laravel Core Modules installed successfully!');
        $this->info('');
        $this->warn('✅ Next step (IMPORTANT):');
        $this->line('Run: php artisan vendor:publish --tag=laravel-core-modules-config');
        $this->info('');
    }
}