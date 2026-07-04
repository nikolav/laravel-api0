# Spatie Laravel Data — Quick Overview

A lightweight and powerful DTO + validation + transformation layer for Laravel.

## Installation

```bash
composer require spatie/laravel-data
```

## What it does

- Defines typed Data Transfer Objects (DTOs)
- Validates input automatically
- Transforms requests & responses
- Handles nested data structures
- Reduces need for Form Requests + manual mapping

## Basic Usage

### 1. Create a Data class

```php
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Email;

class UserData extends Data
{
    public function __construct(
        #[Required]
        public string $name,

        #[Email]
        public string $email,

        public ?int $age
    ) {}
}
```

### 2. Use in Controller

```php
class UserController
{
    public function store(UserData $data)
    {
        return response()->json($data);
    }
}
```

Laravel automatically:
- validates input
- hydrates DTO
- injects typed object

### 3. Manual creation

```php
$data = UserData::from([
    'name' => 'John',
    'email' => 'john@example.com',
    'age' => 25,
]);
```

## Key Benefits

- Clean architecture (DTO-based)
- Strong typing
- Less boilerplate than Form Requests
- Great for APIs + frontend contracts
- Works well with Vue / React backends

## Ideal Use Case

- REST APIs
- Laravel + SPA apps
- Service-layer architecture
- Replacing Form Requests + resource mapping

---

Simple, modern, and production-ready Laravel data layer.
