<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## 🚀 API-Focused Laravel Application

A lightweight Laravel setup optimized for building RESTful APIs and backend services. This configuration focuses on API development with essential tooling for modern web applications.

### 📦 Quick Setup for API Development

```bash
# 1. Clone and install dependencies
git clone [repository-url]
cd [project-name]
composer install

# 2. Configure environment
cp .env.example .env
php artisan key:generate

# 3. Set up database (SQLite by default)
touch database/database.sqlite
php artisan migrate

# 4. Install API authentication (optional)
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate

