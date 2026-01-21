#!/usr/bin/env bash
set -euo pipefail

cd /usr/app

# Ensure sqlite db file exists
mkdir -p /usr/app/database
touch /usr/app/database/database.sqlite

# Storage dirs
mkdir -p /usr/app/storage /usr/app/bootstrap/cache

chown -R www:www /usr/app/storage /usr/app/bootstrap/cache /usr/app/database

# If APP_KEY missing, warn (don't auto-generate in prod without you knowing)
if [ -z "${APP_KEY:-}" ]; then
  echo "WARNING: APP_KEY is not set. Set it in .env or compose env."
fi

# Optional: run migrations automatically (uncomment if you want)
php artisan migrate --force

# Optimize for prod (safe for API)
php artisan config:cache || true
php artisan route:cache || true

exec "$@"
