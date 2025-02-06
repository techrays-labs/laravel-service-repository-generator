# Laravel Service & Repository Generator

A powerful and flexible Laravel package that simplifies the implementation of the Service-Repository design pattern. Generate Service and Repository classes with ease, customize their namespace and path, and streamline your application's architecture. Perfect for developers who follow clean code principles and want a structured approach to business logic and data handling in Laravel applications.

![Laravel Version](https://img.shields.io/badge/Laravel-8%20|%209%20|%2010%20|%2011%20-blue.svg?style=flat-square)  
![License](https://img.shields.io/badge/license-MIT-green.svg?style=flat-square)

## Features

- Generate **Service & Repository** classes automatically
- Supports **custom namespaces & paths**
- Option to generate a **Repository Interface**
- **Automatic binding** in `AppServiceProvider`
- Uses **stub files** for customization
- **Publishable configuration file**
- Compatible with **Laravel 8, 9, and 10**

## Installation

Install via **Composer**

```
composer require laravel-service-repository-generator
```

Publish the configuration file

```

php artisan vendor:publish --tag=service-repository-config

```

Publish the stub files (optional)

```

php artisan vendor:publish --tag=service-repository-stubs

```

## Configuration

The package allows you to define default namespaces and paths in `config/service-repository.php`

```

<?php
return [
    'service_namespace' => 'App\Services',
    'repository_namespace' => 'App\Repositories',
];
```

You can modify these settings to match your project structure.

## Usage

### Basic Usage

Generate a Service & Repository for `User`

```
php artisan make:service-repository User
```

This will create

```
app/
├── Services/
│   ├── UserService.php
├── Repositories/
│   ├── UserRepository.php
```

### Custom Namespace for Service

```
php artisan make:service-repository User --serviceNamespace="Domain\Services"
```

This will place the service in `Domain\Services`

```
Domain/
├── Services/
│   ├── UserService.php
```

### Custom Path for Repository

```
php artisan make:service-repository User --repositoryNamespace="Domain\Repositories"
```

This will place the repository in `Domain\Repositories`

```
Domain/
├── Repositories/
│   ├── UserRepository.php
```

### Generate Repository Interface

```
php artisan make:service-repository User --interface
```

This will generate an interface alongside the repository

```
app/
├── Repositories/
│   ├── UserRepository.php
│   ├── UserRepositoryInterface.php
```

### Combine Multiple Options

```
php artisan make:service-repository User --serviceNamespace="Domain\Services" --repositoryNamespace="Domain\Repositories" --interface
```

This will generate

```
Domain/
├── Services/
│   ├── UserService.php
├── Repositories/
│   ├── UserRepository.php
│   ├── UserRepositoryInterface.php
```

### Stubs Customization

After publishing the stub files, you can find them in `stubs/service-repository-generator/`

Modify these stubs to customize the generated files

- `service.stub` – Template for service class
- `repository.stub` – Template for repository class
- `repository-interface.stub` – Template for repository interface

Example of a custom service stub (`service.stub`)

```
<?php

namespace {{ namespace }};

class {{ className }}
{
    protected $repository;

    public function __construct({{ className }}Repository $repository)
    {
        $this->repository = $repository;
    }
}

```

## Automatic Binding in Service Provider

If you generate an interface, the package automatically binds it in `AppServiceProvider.php`

```
$this->app->bind(UserRepositoryInterface::class, UserRepository::class);
```

## Example Usage in Code

Inject Service into a Controller

```
<?php

use App\Services\UserService;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
}
```

Use Repository in a Service

```
<?php

namespace App\Services;

use App\Repositories\UserRepository;

class UserService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getAllUsers()
    {
        return $this->userRepository->all();
    }
}

```

## Contributing

Want to improve this package? Contributions are welcome!

- Fork the repository
- Create a new branch `feature/awesome-feature`
- Commit your changes
- Push the branch
- Submit a Pull Request

## License

This package is open-sourced software licensed under the MIT License.

Happy Coding! 🎉
