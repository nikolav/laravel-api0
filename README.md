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

