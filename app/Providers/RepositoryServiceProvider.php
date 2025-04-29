<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use ReflectionClass;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register services.
     */
    public function register(): void
    {
        // Register base interfaces
        $this->registerBaseInterfaces();
        
        // Register all repositories
        $this->registerRepositories();

        // Register all services
        $this->registerServices();
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Register base interfaces.
     */
    protected function registerBaseInterfaces(): void
    {
        // Bind the base repository interface to the base repository
        $this->app->bind(
            'App\Repositories\Contracts\RepositoryInterface',
            'App\Repositories\BaseRepository'
        );

        // Bind the base service interface to the base service
        $this->app->bind(
            'App\Services\Contracts\ServiceInterface',
            'App\Services\BaseService'
        );
    }

    /**
     * Register all repository interfaces with their implementations.
     */
    protected function registerRepositories(): void
    {
        // Get all files in the Repositories/Contracts directory
        $path = app_path('Repositories/Contracts');
        
        if (!File::isDirectory($path)) {
            return;
        }
        
        $files = File::allFiles($path);
        
        foreach ($files as $file) {
            // Skip the base repository interface
            if ($file->getFilename() === 'RepositoryInterface.php') {
                continue;
            }
            
            // Get the repository interface class name
            $interfaceName = str_replace('.php', '', $file->getFilename());
            $interfaceNamespace = 'App\\Repositories\\Contracts\\' . $interfaceName;
            
            // Skip if the interface doesn't exist
            if (!class_exists($interfaceNamespace)) {
                continue;
            }
            
            // Determine the implementation class
            // Format: {Name}RepositoryInterface => {Name}Repository
            $entityName = str_replace('RepositoryInterface', '', $interfaceName);
            $implementationNamespace = 'App\\Repositories\\' . $entityName . '\\' . $entityName . 'Repository';
            
            // Skip if the implementation doesn't exist
            if (!class_exists($implementationNamespace)) {
                continue;
            }
            
            // Bind the interface to the implementation
            $this->app->bind($interfaceNamespace, $implementationNamespace);
        }
    }

    /**
     * Register all service interfaces with their implementations.
     */
    protected function registerServices(): void
    {
        // Get all files in the Services/Contracts directory
        $path = app_path('Services/Contracts');
        
        if (!File::isDirectory($path)) {
            return;
        }
        
        $files = File::allFiles($path);
        
        foreach ($files as $file) {
            // Skip the base service interface
            if ($file->getFilename() === 'ServiceInterface.php') {
                continue;
            }
            
            // Get the service interface class name
            $interfaceName = str_replace('.php', '', $file->getFilename());
            $interfaceNamespace = 'App\\Services\\Contracts\\' . $interfaceName;
            
            // Skip if the interface doesn't exist
            if (!class_exists($interfaceNamespace)) {
                continue;
            }
            
            // Determine the implementation class
            // Format: {Name}ServiceInterface => {Name}Service
            $entityName = str_replace('ServiceInterface', '', $interfaceName);
            $implementationNamespace = 'App\\Services\\' . $entityName . '\\' . $entityName . 'Service';
            
            // Skip if the implementation doesn't exist
            if (!class_exists($implementationNamespace)) {
                continue;
            }
            
            // Bind the interface to the implementation
            $this->app->bind($interfaceNamespace, $implementationNamespace);
        }
    }
    
    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(): array
    {
        return [
            'App\Repositories\Contracts\RepositoryInterface',
            'App\Services\Contracts\ServiceInterface',
        ];
    }
}

