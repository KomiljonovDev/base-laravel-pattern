# Quick Start Guide

## Installation

1. **Install in a Laravel Project**:
   ```bash
   # Clone or copy the implementation files into your Laravel project
   
   # Register the service provider in config/app.php
   # Add this line to the 'providers' array:
   App\Providers\RepositoryServiceProvider::class,
   ```

2. **Generate Your First Resource**:
   ```bash
   # Create model and migration
   php artisan make:model YourModel -m
   
   # Define your migration and run it
   php artisan migrate
   
   # Generate repository
   php artisan make:repository YourModel --model=YourModel
   
   # Generate service
   php artisan make:service YourModel --repository=YourModel --register
   
   # Generate form requests
   php artisan make:form-request YourModel --model=YourModel
   ```

## Usage

### Controller Implementation

```php
namespace App\Http\Controllers;

use App\Http\Requests\YourModel\ListYourModelRequest;
use App\Http\Requests\YourModel\StoreYourModelRequest;
use App\Http\Requests\YourModel\UpdateYourModelRequest;
use App\Services\Contracts\YourModelServiceInterface;

class YourModelController extends Controller
{
    protected $service;
    
    public function __construct(YourModelServiceInterface $service)
    {
        $this->service = $service;
    }
    
    public function index(ListYourModelRequest $request)
    {
        // Load relations if needed
        $this->service->setRelation('relation');
        
        // Select specific columns
        $this->service->setAttributes(['id', 'name', 'created_at']);
        
        // Apply filters from the request
        $this->service->setFilters($request->validated());
        
        // Return results (paginated or collection)
        return response()->json($this->service->get());
    }
    
    public function store(StoreYourModelRequest $request)
    {
        $model = $this->service->create($request->validated());
        return response()->json([
            'message' => 'Resource created successfully',
            'data' => $model
        ]);
    }
    
    public function show($id)
    {
        return response()->json($this->service->show($id));
    }
    
    public function update(UpdateYourModelRequest $request, $id)
    {
        $model = $this->service->edit($request->validated(), $id);
        return response()->json([
            'message' => 'Resource updated successfully',
            'data' => $model
        ]);
    }
    
    public function destroy($id)
    {
        $this->service->delete($id);
        return response()->json(['message' => 'Resource deleted successfully']);
    }
}
```

### Route Registration

```php
// routes/api.php
use App\Http\Controllers\YourModelController;

Route::apiResource('your-models', YourModelController::class);
```

## Extending the Base Classes

### Custom Repository Methods

```php
namespace App\Repositories\YourModel;

use App\Models\YourModel;
use App\Repositories\BaseRepository;
use App\Repositories\Contracts\YourModelRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class YourModelRepository extends BaseRepository implements YourModelRepositoryInterface
{
    public function __construct(YourModel $model)
    {
        $this->entity = $model;
    }
    
    /**
     * Find records by custom condition
     */
    public function findByCustomCondition(string $value): Collection
    {
        return $this->entity->where('custom_field', $value)->get();
    }
    
    /**
     * Get the latest records
     */
    public function getLatest(int $limit = 5): Collection
    {
        return $this->entity->latest()->limit($limit)->get();
    }
}
```

### Custom Service Methods

```php
namespace App\Services\YourModel;

use App\Services\BaseService;
use App\Services\Contracts\YourModelServiceInterface;
use App\Repositories\Contracts\YourModelRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class YourModelService extends BaseService implements YourModelServiceInterface
{
    public function __construct(YourModelRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
    
    /**
     * Custom business logic with transaction support
     */
    public function complexOperation(array $data): mixed
    {
        return $this->transaction(function () use ($data) {
            // Step 1: Create a record
            $model = $this->create($data);
            
            // Step 2: Perform additional operations
            // ...
            
            return $model;
        });
    }
    
    /**
     * Get latest records with custom repository method
     */
    public function getLatestRecords(int $limit = 5): Collection
    {
        return $this->repo->getLatest($limit);
    }
}
```

## Advanced Filtering

The BaseService provides advanced filtering capabilities:

```php
$filters = [
    'name' => [
        'type' => 'string',
        'value' => 'search term'
    ],
    'status' => [
        'type' => 'exact_string',
        'value' => 'active'
    ],
    'created_at_from' => [
        'type' => 'datefrom',
        'value' => '2023-01-01'
    ],
    'created_at_to' => [
        'type' => 'dateto',
        'value' => '2023-12-31'
    ],
    'sort_by' => 'created_at',
    'order_by' => 'desc',
    'per_page' => 15
];

$this->service->setFilters($filters);
$result = $this->service->get(); // Returns paginated results
```

## That's It!

The architecture is now ready for use in your Laravel application. For more details, refer to the following documentation:

- `README.md`: Main documentation with usage instructions
- `SUMMARY.md`: Detailed explanation of components
- `NEXT_STEPS.md`: Implementation status and next steps
- `VERIFICATION.md`: Component structure verification

