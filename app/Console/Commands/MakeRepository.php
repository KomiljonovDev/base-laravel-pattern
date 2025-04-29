<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeRepository extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:repository {name} {--model=} {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new repository class and interface';

    /**e
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $model = $this->option('model') ?: $name;
        $force = $this->option('force') ?? false;

        $this->createRepositoryInterface($name, $model, $force);
        $this->createRepository($name, $model, $force);

        $this->info('Repository created successfully');
    }

    /**
     * Create a repository interface.
     *
     * @param string $name
     * @param string $model
     * @param bool $force
     * @return void
     */
    protected function createRepositoryInterface(string $name, string $model, bool $force = false): void
    {
        $interfaceName = "{$name}RepositoryInterface";
        $interfacePath = app_path("Repositories/Contracts/{$interfaceName}.php");

        if (File::exists($interfacePath) && !$force) {
            $this->error("Repository interface {$interfaceName} already exists!");
            return;
        }

        // Create directory if it doesn't exist
        $directory = dirname($interfacePath);
        if (!File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $stubPath = __DIR__ . '/stubs/repository.interface.stub';
        $stub = File::get($stubPath);

        $stub = str_replace(
            ['{{name}}', '{{model}}'],
            [$name, $model],
            $stub
        );

        File::put($interfacePath, $stub);
        $this->info("Repository interface [{$interfaceName}] created successfully.");
    }

    /**
     * Create a repository implementation.
     *
     * @param string $name
     * @param string $model
     * @param bool $force
     * @return void
     */
    protected function createRepository(string $name, string $model, bool $force = false): void
    {
        $repositoryName = "{$name}Repository";
        $repositoryPath = app_path("Repositories/{$name}/{$repositoryName}.php");

        if (File::exists($repositoryPath) && !$force) {
            $this->error("Repository {$repositoryName} already exists!");
            return;
        }

        // Create directory if it doesn't exist
        $directory = dirname($repositoryPath);
        if (!File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $stubPath = __DIR__ . '/stubs/repository.stub';
        $stub = File::get($stubPath);

        $stub = str_replace(
            ['{{name}}', '{{model}}'],
            [$name, $model],
            $stub
        );

        File::put($repositoryPath, $stub);
        $this->info("Repository [{$repositoryName}]" . " created successfully.");
    }
}
