# Laravel Repository and Service Pattern

This project implements a flexible and robust Repository and Service pattern for Laravel applications, making it easy to create, maintain, and extend large-scale projects.

## Features

- Base Repository and Service implementation with common CRUD operations
- Custom Artisan commands to generate repositories, services, and form requests
- Automatic form request validation rules based on database schema
- Transaction support for complex operations
- Interface-based architecture for easy testing and dependency injection
- Service provider for automatic binding of interfaces to implementations

## Installation

Clone the repository and install dependencies:

```bash
composer install
```

## Usage

### Generating Resources

#### 1. Create a Model and Migration

```bash
php artisan make:model Post -m
```

Edit the migration to define your schema:

```php
// database/migrations/xxxx_xx_xx_create_posts_table.php
public function up()
{
    Schema::create('posts', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->text('content');
        $table->unsignedBigInteger('user_id');
        $table->boolean('is_published')->default(false);
        $table->timestamps();
        
        $table->foreign('user_id')->references('id')->on('users');
    });
}
```

Update the model with fillable attributes:

```php
// app/Models/Post.php
protected $fillable = [
    'title',
    'content',
    'user_id',
    'is_published'
];
```

Run the migration:

```bash
php artisan migrate
```

#### 2. Generate Repository

```bash
php artisan make:repository Post --model=Post
```

This creates:
- `app/Repositories/Contracts/PostRepositoryInterface.php`
- `app/Repositories/Post/PostRepository.php`

#### 3. Generate Service

```bash
php artisan make:service Post --repository=Post --register
```

This creates:
- `app/Services/Contracts/PostServiceInterface.php`
- `app/Services/Post/PostService.php`
- Registers bindings in the RepositoryServiceProvider

#### 4. Generate Form Requests

```bash
php artisan make:form-request Post --model=Post
```

This creates:
- `app/Http/Requests/Post/StorePostRequest.php` - For creating posts
- `app/Http/Requests/Post/UpdatePostRequest.php` - For updating posts
- `app/Http/Requests/Post/ListPostRequest.php` - For filtering and pagination

### Controller Example

```php
<?php

namespace App\Http\Controllers;

use App\Http\Requests\Post\ListPostRequest;
use App\Http\Requests\Post\StorePostRequest;
use App\Http\Requests\Post\UpdatePostRequest;
use App\Services\Contracts\PostServiceInterface;

class PostController extends Controller
{
    protected $postService;
    
    public function __construct(PostServiceInterface $postService)
    {
        $this->postService = $postService;
    }
    
    public function index(ListPostRequest $request)
    {
        $this->postService->setRelation('user');
        $this->postService->setAttributes(['id', 'title', 'user_id', 'is_published', 'created_at']);
        $this->postService->setFilters($request->validated());
        return response()->json($this->postService->get());
    }
    
    public function store(StorePostRequest $request)
    {
        $post = $this->postService->create($request->validated());
        return response()->json(['message' => 'Post created successfully', 'data' => $post]);
    }
    
    public function show($id)
    {
        return response()->json($this->postService->show($id));
    }
    
    public function update(UpdatePostRequest $request, $id)
    {
        $post = $this->postService->edit($request->validated(), $id);
        return response()->json(['message' => 'Post updated successfully', 'data' => $post]);
    }
    
    public function destroy($id)
    {
        $this->postService->delete($id);
        return response()->json(['message' => 'Post deleted successfully']);
    }
}
```

## Architecture

### Repository Pattern

The repository pattern abstracts the data layer, providing a clean API for data access:

```php
// Using a repository
$userRepository->getById(1); // Returns user with ID 1
$userRepository->store(['name' => 'John Doe', 'email' => 'john@example.com']); // Creates a new user
$userRepository->update(['name' => 'Jane Doe'], 1); // Updates user with ID 1
$userRepository->destroy(1); // Deletes user with ID 1
```

### Service Layer

The service layer contains business logic and coordinates between multiple repositories:

```php
// Using a service
$userService->create(['name' => 'John Doe', 'email' => 'john@example.com']); // Creates a user with validation
$userService->findByEmail('john@example.com'); // Custom method to find a user by email
```

### Form Requests

Form requests handle validation based on the model's table structure:

```php
// StoreUserRequest
public function rules()
{
    return [
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'email', 'unique:users,email'],
        'password' => ['required', 'string', 'min:8'],
    ];
}
```

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development/)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
