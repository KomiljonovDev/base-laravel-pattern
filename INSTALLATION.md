# Installation Instructions

Follow these steps to integrate the Laravel Repository and Service Pattern into your existing Laravel project.

## Step 1: Structure the Project

Create the required directories for the architecture components:

```bash
# Create the required directories
mkdir -p app/Repositories/Contracts
mkdir -p app/Services/Contracts
mkdir -p app/Console/Commands/stubs
mkdir -p app/Providers
```

## Step 2: Copy Base Files

Copy the core implementation files to your Laravel project:

```bash
# Clone the repository or download the files
git clone https://github.com/your-username/laravel-repository-service-pattern.git

# Copy core files
cp -r laravel-repository-service-pattern/app/Repositories/* app/Repositories/
cp -r laravel-repository-service-pattern/app/Services/* app/Services/
cp -r laravel-repository-service-pattern/app/Console/Commands/* app/Console/Commands/
cp -r laravel-repository-service-pattern/app/Providers/RepositoryServiceProvider.php app/Providers/
```

Alternatively, you can manually copy the following files:

1. Repository files:
   - `app/Repositories/Contracts/RepositoryInterface.php`
   - `app/Repositories/BaseRepository.php`

2. Service files:
   - `app/Services/Contracts/ServiceInterface.php`
   - `app/Services/BaseService.php`

3. Command files:
   - `app/Console/Commands/MakeRepository.php`
   - `app/Console/Commands/MakeService.php`
   - `app/Console/Commands/FormRequest.php`

4. Stub files:
   - `app/Console/Commands/stubs/repository.stub`
   - `app/Console/Commands/stubs/repository.interface.stub`
   - `app/Console/Commands/stubs/service.stub`
   - `app/Console/Commands/stubs/service.interface.stub`
   - `app/Console/Commands/stubs/form-request.stub`

5. Provider file:
   - `app/Providers/RepositoryServiceProvider.php`

## Step 3: Register the Service Provider

Open your `config/app.php` file and add the RepositoryServiceProvider to the providers array:

```php
// config/app.php
'providers' => [
    // ...
    App\Providers\RepositoryServiceProvider::class,
],
```

## Step 4: Verify Installation

Run the following commands to verify that your custom artisan commands are available:

```bash
php artisan list | grep make:repository
php artisan list | grep make:service
php artisan list | grep make:form-request
```

You should see the custom commands listed in the output.

## Step 5: Test with Example

Generate a test resource to ensure everything is working correctly:

```bash
# Generate a model and migration
php artisan make:model Test -m

# Edit the migration to add some columns
# Edit app/database/migrations/xxxx_xx_xx_create_tests_table.php
# Add columns like: $table->string('name'); $table->text('description');

# Edit the model to add fillable attributes
# Edit app/Models/Test.php
# Add: protected $fillable = ['name', 'description'];

# Run the migration
php artisan migrate

# Generate repository
php artisan make:repository Test --model=Test

# Generate service
php artisan make:service Test --repository=Test --register

# Generate form requests
php artisan make:form-request Test --model=Test
```

Verify that the following files were created:

- `app/Repositories/Contracts/TestRepositoryInterface.php`
- `app/Repositories/Test/TestRepository.php`
- `app/Services/Contracts/TestServiceInterface.php`
- `app/Services/Test/TestService.php`
- `app/Http/Requests/Test/StoreTestRequest.php`
- `app/Http/Requests/Test/UpdateTestRequest.php`
- `app/Http/Requests/Test/ListTestRequest.php`

## Step 6: Create a Controller

Create a controller to use your new service:

```bash
php artisan make:controller TestController
```

Edit the controller to use your service:

```php
<?php

namespace App\Http\Controllers;

use App\Http\Requests\Test\ListTestRequest;
use App\Http\Requests\Test\StoreTestRequest;
use App\Http\Requests\Test\UpdateTestRequest;
use App\Services\Contracts\TestServiceInterface;

class TestController extends Controller
{
    protected $service;
    
    public function __construct(TestServiceInterface $service)
    {
        $this->service = $service;
    }
    
    public function index(ListTestRequest $request)
    {
        $this->service->setFilters($request->validated());
        return response()->json($this->service->get());
    }
    
    public function store(StoreTestRequest $request)
    {
        $test = $this->service->create($request->validated());
        return response()->json(['message' => 'Test created successfully', 'data' => $test]);
    }
    
    // Add other methods like show, update, destroy
}
```

## Step 7: Register Routes

Add routes for your controller in `routes/api.php`:

```php
use App\Http\Controllers\TestController;

Route::apiResource('tests', TestController::class);
```

## Step 8: Test the API

Test your API endpoints using a tool like Postman or curl:

```bash
# Get all tests
curl http://your-app-url/api/tests

# Create a new test
curl -X POST http://your-app-url/api/tests \
    -H "Content-Type: application/json" \
    -d '{"name": "Test Name", "description": "Test Description"}'
```

## Installation Complete

The Laravel Repository and Service Pattern is now installed and ready to use in your project. Refer to the other documentation files for more information on using and extending the architecture:

- `README.md`: Main documentation
- `QUICK_START.md`: Quick start guide
- `SUMMARY.md`: Detailed component explanation
- `NEXT_STEPS.md`: Next steps and future improvements
- `VERIFICATION.md`: Component verification
- `STATUS.md`: Implementation status

## Troubleshooting

If you encounter any issues during installation:

1. Ensure all directories and files are correctly placed
2. Check that the service provider is registered
3. Make sure the database configuration is correct
4. Verify that you can run other artisan commands
5. Check storage permissions

For more help, refer to the other documentation files or open an issue in the repository.

