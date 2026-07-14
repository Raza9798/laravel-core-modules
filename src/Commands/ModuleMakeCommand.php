<?php

namespace Raza9798\LaravelCoreModules\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class ModuleMakeCommand extends Command
{

    protected $signature = 'module:make';
    protected $description = 'Make a new module class';

    private const CREATE_NEW_MODULE = 'CREATE NEW MODULE';

    public function handle()
    {
        $module = $this->selectModule();
        $artifacts = $this->selectArtifacts();
        $selectedGenerators = collect($artifacts)->map(fn ($key) => $this->artifactMap()[$key]['generators'])->flatten()->unique()->values()->toArray();
        $this->info("Selected module: $module");
        $this->info("Selected artifacts: " . implode(', ', $artifacts));
        
        if (empty($selectedGenerators)) {
            $this->warn('No artifacts selected. Exiting.');
            return;
        }

        if($artifacts === ['CUSTOM']) {
            $customGenerators = $this->artifactMap()['CUSTOM']['generators'];
            $selectedCustomGenerators = $this->choice('Select custom generators to create (for multiple selection use comma)', $customGenerators, null, null, true);
            $this->serviceGenerator($selectedCustomGenerators, $module, $this->ask("Enter the name for the custom file"));
            return $this->info("Custom generators created successfully.");
        }
        
        $this->serviceGenerator($selectedGenerators, $module, $this->ask("Enter the name for the file"));
    }

    protected function serviceGenerator(array $items, string $module, string $ask){
        $this->info("Selected generators: " . implode(', ', $items));
        foreach ($items as $generator) {
            (new \Raza9798\LaravelCoreModules\Services\ClassGenerator())->generate($module, $ask, $generator);
        }
    }

    protected function selectModule() : string
    {
        $modulesPath = config( 'laravel-core-modules.modules_path', base_path('Modules'));
        File::ensureDirectoryExists($modulesPath);
        $modules = collect(File::directories($modulesPath))->map(fn ($directory) => basename($directory))->sort()->values()->toArray();
        if (empty($modules)) {
            $this->warn('No modules found. Please create a module first.');
            return $this->createNewModule();
        }
        $modules[] = self::CREATE_NEW_MODULE;
        $selection = $this->choice( 'Select a module', $modules );

        return $selection === self::CREATE_NEW_MODULE ? $this->createNewModule() : $selection;
    }

    protected function createNewModule() : string
    {
        $name = Str::studly(trim($this->ask('Enter the name of the new module')));
        Artisan::call('module:create', ['name' => $name], $this->getOutput());
        return $name;
    }

    protected function artifactMap(): array
    {
        return [
            'API' => [
                'label' => 'API Resource',
                'generators' => ['controller', 'model', 'migration', 'seeder', 'factory']
            ],
            'DB' => [
                'label' => 'Database',
                'generators' => ['model', 'migration', 'seeder', 'factory']
            ],
            'CUSTOM' => [
                'label' => 'Custom',
                'generators' => [
                    'class',
                    'command',
                    'config',
                    'controller',
                    'enum',
                    'event',
                    'exception',
                    'factory',
                    'job',
                    'mail',
                    'migration',
                    'model',
                    'observer',
                    'policy',
                    'seeder',
                    // 'cache-table',
                    // 'channel',
                    // 'component',
                    // 'interface',
                    // 'job-middleware',
                    // 'listener',
                    // 'middleware',
                    // 'notification',
                    // 'notifications-table',
                    // 'provider',
                    // 'queue-batches-table',
                    // 'queue-failed-table',
                    // 'queue-table',
                    // 'request',
                    // 'resource',
                    // 'rule',
                    // 'scope',
                    // 'session-table',
                    // 'test',
                    // 'trait',
                    // 'view'
                ]
            ],
        ];
    }

    protected function selectArtifacts() : array
    {
        $map = $this->artifactMap();
        $labels = collect($map)->mapWithKeys(fn ($item, $key) => [$key =>  implode(', ', $item['generators'])])->toArray();
        $selection = $this->choice('Select artifacts to generate', $labels, null, null, true);
        return $selection;
    }
}