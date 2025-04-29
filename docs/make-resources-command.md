g# Make Resources Command Documentation

## Overview

The `make:resources` command is a powerful utility that automatically generates a complete set of files needed for implementing a model in a structured layered architecture. It creates form requests, repositories, services, and controllers, following best practices and design patterns.

## Purpose

The command streamlines the development process by automating the creation of boilerplate code required when implementing a new model or entity in your application. It helps maintain consistent code structure across your project and reduces the time spent on repetitive tasks.

## Prerequisites

Before using the `make:resources` command, ensure:

1. Your model class already exists in the `App\Models` namespace
2. Database table for the model is already created and has the correct schema
3. Your stub files are properly set up in the `stubs` directory (form-request.stub, repository.stub, repository-interface.stub, service.stub, service-interface.stub, controller.stub)

## Usage

```bash
php artisan make:resources ModelName
```

Replace `ModelName` with the name of your existing model.

## Generated Files

For a model named `Example`, the command will generate the following files:

### Form Requests

Located in `app/Http/Requests/Example/`

1. `StoreExampleRequest.php` - Form request for creating a new resource
2. `UpdateExampleRequest.php` - Form request for updating an existing resource
3. `ListExampleRequest.php` - Form request for listing/filtering resources

### Repository Pattern Files

1. `app/Repositories/Example/ExampleRepository.php` - Repository implementation
2. `app/Repositories/Contracts/ExampleRepositoryInterface.php` - Repository interface

### Service Pattern Files

1. `app/Services/Example/ExampleService.php` - Service implementation
2. `app/Services/Contracts/ExampleServiceInterface.php` - Service interface

### Controller

`app/Http/Controllers/ExampleController.php` - RESTful controller with standard CRUD operations

## Features

### Automatic Validation Rules

The command analyzes your database table schema and generates appropriate validation rules for your form requests based on:

- Column data types (string, integer, boolean, etc.)
- Nullability constraints
- Unique indexes
- Foreign key relationships

### CRUD Operations

The generated controller includes standard RESTful endpoints:

- `index()` - List resources with filtering
- `store()` - Create a new resource
- `show()` - Retrieve a specific resource
- `update()` - Update an existing resource
- `destroy()` - Delete a resource

### Repository Pattern Integration

The command implements the repository pattern to separate the data access logic from your business logic:

- Interface definition with standard methods
- Implementation that handles database operations

### Service Layer

Services act as an intermediary between controllers and repositories:

- Provides a place for business logic
- Implements standard CRUD operations
- Handles any additional business requirements

## Architecture Overview

The generated code follows a layered architecture approach:

```
Controller → Service → Repository → Model
```

1. **Controller** - Handles HTTP requests/responses and input validation
2. **Service** - Contains business logic and orchestrates operations
3. **Repository** - Manages data access and database operations
4. **Model** - Represents the database entity

## Example Workflow

When using the generated classes, the typical flow is:

1. HTTP request is received by the Controller
2. Controller uses Form Request for validation
3. Controller delegates to Service for business logic
4. Service uses Repository for data access
5. Repository interacts with the Model
6. Results flow back up through the layers

## Advanced Features

### Filtering System

The generated list form request includes a robust filtering system that supports various filter types:

- String filters
- Exact matches
- Array filters
- Numeric ranges
- Boolean filters
- Date filters
- Custom filters

### Pagination Support

The list endpoint automatically supports pagination with:

- Configurable items per page
- Page navigation
- Sorting options

## Best Practices

1. Review and customize the generated files to match your specific requirements
2. Add additional validation rules as needed
3. Extend interfaces with model-specific methods
4. Add custom logic to services for complex business requirements

## Troubleshooting

If you encounter issues:

1. Ensure your model exists in the correct namespace
2. Verify database table exists and has the expected schema
3. Check that stub files exist and contain valid content
4. Review any error messages in the console output

## Extending the Command

The command can be extended to generate additional files or customize the generated code by:

1. Modifying the stub files
2. Adding new methods to the command class
3. Creating additional generators for other components

