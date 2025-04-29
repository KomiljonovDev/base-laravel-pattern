<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
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
     * Register all repository interfaces with their implementations.
     */
    protected function registerRepositories(): void
    {
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
            $interfaceNamespace = "App\\Repositories\\Contracts\\{$interfaceName}";

            // Skip if the interface doesn't exist
            if (!interface_exists($interfaceNamespace)) {
                continue;
            }

            // Determine the implementation class
            // Format: {Name}RepositoryInterface => Repositories\{Name}\{Name}Repository
            $entityName = str_replace('RepositoryInterface', '', $interfaceName);
            $implementationNamespace = "App\\Repositories\\{$entityName}\\{$entityName}Repository";

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
            $interfaceNamespace = "App\\Services\\Contracts\\{$interfaceName}";

            // Skip if the interface doesn't exist
            if (!interface_exists($interfaceNamespace)) {
                continue;
            }

            // Determine the implementation class
            // Format: {Name}ServiceInterface => Services\{Name}\{Name}Service
            $entityName = str_replace('ServiceInterface', '', $interfaceName);
            $implementationNamespace = "App\\Services\\{$entityName}\\{$entityName}Service";

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
        $provided = [];

        // Collect repository interfaces
        $repoPath = app_path('Repositories/Contracts');
        if (File::isDirectory($repoPath)) {
            $files = File::allFiles($repoPath);
            foreach ($files as $file) {
                if ($file->getFilename() !== 'RepositoryInterface.php') {
                    $interfaceName = str_replace('.php', '', $file->getFilename());
                    $interfaceNamespace = "App\\Repositories\\Contracts\\{$interfaceName}";
                    if (interface_exists($interfaceNamespace)) {
                        $provided[] = $interfaceNamespace;
                    }
                }
            }
        }

        // Collect service interfaces
        $servicePath = app_path('Services/Contracts');
        if (File::isDirectory($servicePath)) {
            $files = File::allFiles($servicePath);
            foreach ($files as $file) {
                if ($file->getFilename() !== 'ServiceInterface.php') {
                    $interfaceName = str_replace('.php', '', $file->getFilename());
                    $interfaceNamespace = "App\\Services\\Contracts\\{$interfaceName}";
                    if (interface_exists($interfaceNamespace)) {
                        $provided[] = $interfaceNamespace;
                    }
                }
            }
        }

        return $provided;
    }
}
