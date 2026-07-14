<?php

namespace Raza9798\LaravelCoreModules\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ModuleCreateCommand extends Command
{

    protected $signature = 'module:create {name}';
    protected $description = 'Create a new application module';

    public function handle()
    {
        $name = ucfirst($this->argument('name'));
        $basePath = config('laravel-core-modules.modules_path', base_path('Modules'));
        $path = "$basePath/$name";
        if (File::exists($path)) {
            $this->error("\n\n  Module already exists: $name\n");
            return;
        }

        $folders = [
            "app/Http/Controllers",
            "app/Models",
            "app/Policies",
            "database/migrations",
            "database/seeders",
            "database/factories",
            "routes",
            "tests/Feature",
            "tests/Unit"
        ];


        foreach($folders as $folder){
            File::makeDirectory("$path/$folder",0755,true);
        }

        $this->info("Module $name created");
    }
}