<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeService extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:service {name} {--repository=} {--model=} {--register} {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new service class and interface with repository injection';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $repository = $this->option('repository') ?: $name;
        $model = $this->option('model') ?: $name;
        $register = $this->option('register') ?? false;
        $force = $this->option('force') ?? false;

        $this->createServiceInterface($name, $model, $force);
        $this->createService($name, $repository, $model, $force);

        if ($register) {
            $this->registerServiceProvider($name, $force);
        }

        $this->info('Service created successfully');
    }

    /**
     * Create a service interface.
     *
     * @param string $name
     * @param string $model
     * @param bool $force
     * @return void
     */
    protected function createServiceInterface(string $name, string $model, bool $force = false): void
    {
        $interfaceName = "{$name}ServiceInterface";
        $interfacePath = app_path("Services/Contracts/{$interfaceName}.php");

        if (File::exists($interfacePath) && !$force) {
            $this->error("Service interface {$interfaceName} already exists!");
            return;
        }

        // Create directory if it doesn't exist
        $directory = dirname($interfacePath);
        if (!File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $stubPath = __DIR__ . '/stubs/service.interface.stub';
        $stub = File::get($stubPath);

        $stub = str_replace(
            ['{{name}}', '{{model}}'],
            [$name, $model],
            $stub
        );

        File::put($interfacePath, $stub);
        $this->info("Service interface [{$interfaceName}] created successfully.");
    }

    /**
     * Create a service implementation.
     *
     * @param string $name
     * @param string $repository
     * @param string $model
     * @param bool $force
     * @return void
     */
    protected function createService(string $name, string $repository, string $model, bool $force = false): void
    {
        $serviceName = "{$name}Service";
        $servicePath = app_path("Services/{$name}/{$serviceName}.php");

        if (File::exists($servicePath) && !$force) {
            $this->error("Service {$serviceName} already exists!");
            return;
        }

        // Create directory if it doesn't exist
        $directory = dirname($servicePath);
        if (!File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $stubPath = __DIR__ . '/stubs/service.stub';
        $stub = File::get($stubPath);

        $stub = str_replace(
            ['{{name}}', '{{model}}'],
            [$name, $model],
            $stub
        );

        File::put($servicePath, $stub);
        $this->info("Service [{$serviceName}] created successfully.");
    }
    
    /**
     * Register service and repository in service provider.
     *
     * @param string $name
     * @param bool $force
     * @return void
     */
    protected function registerServiceProvider(string $name, bool $force = false): void
    {
        // Check if the base service provider exists
        $serviceProviderPath = app_path('Providers/RepositoryServiceProvider.php');
        
        if (!File::exists($serviceProviderPath)) {
            // Create the repository service provider if it doesn't exist
            $this->createRepositoryServiceProvider($serviceProviderPath);
        }
        
        // Read the service provider file
        $contents = File::get($serviceProviderPath);
        
        // Get the repository and service interface names
        $repositoryInterface = "{$name}RepositoryInterface";
        $repository = "{$name}Repository";
        $serviceInterface = "{$name}ServiceInterface";
        $service = "{$name}Service";
        
        // Check if the bindings already exist
        if (strpos($contents, $repositoryInterface) !== false && strpos($contents, $serviceInterface) !== false) {
            $this->warn('Service and repository bindings already exist in service provider.');
            return;
        }
        
        // Prepare the new binding lines
        $repositoryBinding = "\$this->app->bind(\\App\\Repositories\\Contracts\\{$repositoryInterface}::class, \\App\\Repositories\\{$name}\\{$repository}::class);";
        $serviceBinding = "\$this->app->bind(\\App\\Services\\Contracts\\{$serviceInterface}::class, \\App\\Services\\{$name}\\{$service}::class);";
        
        // Find the register method's closing brace
        $registerMethodEnd = strrpos($contents, '    }');
        if ($registerMethodEnd === false) {
            $this->error('Could not find the register method in service provider.');
            return;
        }
        
        // Insert the new bindings before the register method's closing brace
        $newContents = substr($contents, 0, $registerMethodEnd) . 
                       "\n        // {$name} bindings\n        {$repositoryBinding}\n        {$serviceBinding}\n" . 
                       substr($contents, $registerMethodEnd);
        
        // Save the updated service provider
        File::put($serviceProviderPath, $newContents);
        
        $this->info('Service and repository bindings registered in service provider.');
    }
    
    /**
     * Create a repository service provider for binding interfaces.
     *
     * @param string $path
     * @return void
     */
    protected function createRepositoryServiceProvider(string $path): void
    {
$content = <<<'EOT'
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Repository and service bindings will be added here
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
EOT;

        // Create directory if it doesn't exist
        $directory = dirname($path);
        if (!File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        File::put($path, $content);
        
        // Register the service provider in config/app.php
        $this->registerServiceProviderInAppConfig();
        
        $this->info('Repository service provider created successfully.');
    }
    
    /**
     * Register the RepositoryServiceProvider in config/app.php
     *
     * @return void
     */
    protected function registerServiceProviderInAppConfig(): void
    {
        $configPath = base_path('config/app.php');
        
        if (!File::exists($configPath)) {
            $this->error('Config/app.php not found. Please register the RepositoryServiceProvider manually.');
            return;
        }
        
        $contents = File::get($configPath);
        
        // Check if provider is already registered
        if (strpos($contents, 'RepositoryServiceProvider') !== false) {
            return;
        }
        
        // Find the providers array end
        $providersArrayEnd = strpos($contents, '    ],', strpos($contents, "'providers' => ["));
        
        if ($providersArrayEnd === false) {
            $this->error('Could not find providers array in config/app.php. Please register the RepositoryServiceProvider manually.');
            return;
        }
        
        // Insert the new provider
        $newContents = substr($contents, 0, $providersArrayEnd) . 
                       "        App\\Providers\\RepositoryServiceProvider::class,\n" . 
                       substr($contents, $providersArrayEnd);
        
        // Save the updated config
        File::put($configPath, $newContents);
        
        $this->info('RepositoryServiceProvider registered in config/app.php');
    }
}

