<?php
namespace Raza9798\LaravelCoreModules\Services;
use illuminate\Support\Facades\File;
use illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class ClassGenerator
{
    public function generate(string $module, string $name, string $generator)
    {
        $module = Str::studly($module);
        $name = Str::studly($name);
        
        switch ($generator) {
            case 'controller':
                $this->controller($module, $name);
                break;
            case 'model':
                $this->model($module, $name);
                break;
            case 'migration':
                $this->migration($module, $name);
                break;
            case 'seeder':
                $this->seeder($module, $name);
                break;
            case 'factory':
                $this->factory($module, $name);
                break;
            case 'class':
                $this->class($module, $name);
                break;
            case 'command':
                $this->command($module, $name);
                break;
            case 'config':
                $this->config($module, $name);
                break;
            case 'enum':
                $this->enum($module, $name);
                break;
            case 'event':
                $this->event($module, $name);
                break;
            case 'exception':
                $this->exception($module, $name);
                break;
            case 'job':
                $this->job($module, $name);
                break;
            case 'mail':
                $this->mail($module, $name);
                break;
            case 'observer':
                $this->observer($module, $name);
                break;
            case 'policy':
                $this->policy($module, $name);
                break;
            default:
                break;
                // throw new \InvalidArgumentException("Unknown generator: $generator");
        }

        $this->generateRoutes($module, $name);
    }

    protected function fileConfig($source, $destination, $module, $subNamespace)
    {
        File::ensureDirectoryExists(dirname($destination));
        File::move($source, $destination);
        $this->fixNamespace($destination, $module, $subNamespace);
    }

    protected function fixNamespace(string $filePath, string $module, string $subNamespace)
    {
        $module = Str::studly($module);
        $namespace = "Modules\\{$module}\\{$subNamespace}";
        $content = File::get($filePath);
        $content = preg_replace( '/namespace\s+[^;]+;/', "namespace {$namespace};", $content);
        File::put($filePath, $content);
    }

    public function controller(string $module, string $name, ?bool $isResource = true, ?bool $isApi = true)
    {
        $filename = "{$name}Controller";

        Artisan::call('make:controller', [
            'name' => $filename,
            '--resource' => $isResource,
            '--api' => $isApi,
        ]);

        $source = app_path("Http/Controllers/{$filename}.php");
        $destination = base_path("Modules/{$module}/app/Http/Controllers/{$filename}.php");
        $this->fileConfig($source,$destination,$module,'app\\Http\\Controllers');
        $this->fixControllerImport($destination);
    }

    protected function fixControllerImport(string $filePath)
    {
        $content = File::get($filePath);

        if (str_contains($content, 'use App\Http\Controllers\Controller;')) {
            $content = str_replace(
                'use App\Http\Controllers\Controller;',
                'use Raza9798\LaravelCoreModules\Services\ResourceService;',
                $content
            );
        } else {
            $content = preg_replace(
                '/namespace\s+[^;]+;\s*/',
                "$0\nuse Raza9798\LaravelCoreModules\Services\ResourceService;\n",
                $content,
                1
            );
        }

        $content = preg_replace(
            '/extends\s+\w+/',
            'extends ResourceService',
            $content,
            1
        );

        File::put($filePath, $content);
    }

    protected function model(string $module, string $name)
    {
        $filename = "{$name}";
        Artisan::call('make:model', [
            'name' => $filename,
        ]);

        $source = app_path("Models/{$filename}.php");
        $destination = base_path("Modules/{$module}/app/Models/{$filename}.php");
        $this->fileConfig($source,$destination,$module,'app\\Models');
    }

    protected function migration(string $module, string $name)
    {
        $tableName = Str::snake(Str::pluralStudly($name));
        $migrationName = "create_{$tableName}_table";

        Artisan::call('make:migration', [
            'name' => $migrationName,
            '--create' => $tableName,
        ]);

        $migrationFiles = File::files(database_path('migrations'));
        $latestMigrationFile = collect($migrationFiles)->sortByDesc(fn ($file) => $file->getMTime())->first();
        $source = $latestMigrationFile->getPathname();
        $destination = base_path("Modules/{$module}/database/migrations/{$latestMigrationFile->getFilename()}");
        $this->fileConfig($source,$destination,$module,'database\\migrations');
    }

    protected function seeder(string $module, string $name)
    {
        $filename = "{$name}Seeder";
        Artisan::call('make:seeder', [
            'name' => $filename,
        ]);

        $source = database_path("seeders/{$filename}.php");
        $destination = base_path("Modules/{$module}/database/seeders/{$filename}.php");
        $this->fileConfig($source,$destination,$module,'database\\seeders');
    }

    protected function factory(string $module, string $name)
    {
        $filename = "{$name}Factory";
        Artisan::call('make:factory', [
            'name' => $filename,
        ]);

        $source = database_path("factories/{$filename}.php");
        $destination = base_path("Modules/{$module}/database/factories/{$filename}.php");
        $this->fileConfig($source,$destination,$module,'database\\factories');
    }

    protected function class(string $module, string $name)
    {
        $filename = Str::studly(trim("{$name}Class"));
        Artisan::call('make:class', [
            'name' => "{$filename}",
        ]);

        $source = app_path("{$filename}.php");
        $destination = base_path("Modules/{$module}/app/Classes/{$filename}.php");
        $this->fileConfig($source,$destination,$module,'app\\Classes');
    }

    protected function command(string $module, string $name)
    {
        $filename = Str::studly(trim("{$name}Command"));
        Artisan::call('make:command', [
            'name' => "{$filename}",
        ]);

        $source = app_path("Console/Commands/{$filename}.php");
        $destination = base_path("Modules/{$module}/app/Console/Commands/{$filename}.php");
        $this->fileConfig($source,$destination,$module,'app\\Console\\Commands');
    }

    protected function config(string $module, string $name)
    {
        $filename = Str::studly(trim("{$name}Config"));
        Artisan::call('make:config', [
            'name' => "{$filename}",
        ]);

        $source = config_path("{$filename}.php");
        $destination = base_path("Modules/{$module}/config/{$filename}.php");
        $this->fileConfig($source,$destination,$module,'config');
    }

    protected function enum(string $module, string $name)
    {
        $filename = Str::studly(trim("{$name}Enum"));
        Artisan::call('make:enum', [
            'name' => "{$filename}",
        ]);

        $source = app_path("Enums/{$filename}.php");
        $destination = base_path("Modules/{$module}/app/Enums/{$filename}.php");
        $this->fileConfig($source,$destination,$module,'app\\Enums');
    }

    protected function event(string $module, string $name)
    {
        $filename = Str::studly(trim("{$name}Event"));
        Artisan::call('make:event', [
            'name' => "{$filename}",
        ]);

        $source = app_path("Events/{$filename}.php");
        $destination = base_path("Modules/{$module}/app/Events/{$filename}.php");
        $this->fileConfig($source,$destination,$module,'app\\Events');
    }

    protected function exception(string $module, string $name)
    {
        $filename = Str::studly(trim("{$name}Exception"));
        Artisan::call('make:exception', [
            'name' => "{$filename}",
        ]);

        $source = app_path("Exceptions/{$filename}.php");
        $destination = base_path("Modules/{$module}/app/Exceptions/{$filename}.php");
        $this->fileConfig($source,$destination,$module,'app\\Exceptions');
    }

    protected function job(string $module, string $name)
    {
        $filename = Str::studly(trim("{$name}Job"));
        Artisan::call('make:job', [
            'name' => "{$filename}",
        ]);

        $source = app_path("Jobs/{$filename}.php");
        $destination = base_path("Modules/{$module}/app/Jobs/{$filename}.php");
        $this->fileConfig($source,$destination,$module,'app\\Jobs');
    }

    protected function mail(string $module, string $name)
    {
        $filename = Str::studly(trim("{$name}Mail"));
        Artisan::call('make:mail', [
            'name' => "{$filename}",
        ]);

        $source = app_path("Mail/{$filename}.php");
        $destination = base_path("Modules/{$module}/app/Mail/{$filename}.php");
        $this->fileConfig($source,$destination,$module,'app\\Mail');
    }

    protected function observer(string $module, string $name)
    {
        $filename = Str::studly(trim("{$name}Observer"));
        Artisan::call('make:observer', [
            'name' => "{$filename}",
        ]);

        $source = app_path("Observers/{$filename}.php");
        $destination = base_path("Modules/{$module}/app/Observers/{$filename}.php");
        $this->fileConfig($source,$destination,$module,'app\\Observers');
    }

    protected function policy(string $module, string $name)
    {
        $filename = Str::studly(trim("{$name}Policy"));
        Artisan::call('make:policy', [
            'name' => "{$filename}",
        ]);

        $source = app_path("Policies/{$filename}.php");
        $destination = base_path("Modules/{$module}/app/Policies/{$filename}.php");
        $this->fileConfig($source,$destination,$module,'app\\Policies');
    }

    public function generateRoutes(string $module, string $name)
    {
        $webRouteFile = base_path("Modules/{$module}/routes/web.php");
        if (!File::exists($webRouteFile)) {
            File::put($webRouteFile, "<?php\n\nuse Illuminate\Support\Facades\Route;\n\nRoute::middleware(['web'])->group(function () {\n    // Define your routes here\n});\n");
        }

        $apiRouteFile = base_path("Modules/{$module}/routes/api.php");
        if (!File::exists($apiRouteFile)) {
            File::put($apiRouteFile, "<?php\n\nuse Illuminate\Support\Facades\Route;\n\nRoute::middleware(['auth:sanctum'])->group(function () {\n    // Define your API routes here\n});\n");
        }
    }
}