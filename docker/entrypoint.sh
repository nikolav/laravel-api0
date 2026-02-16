#!/usr/bin/env bash
set -euo pipefail

cd /usr/app

# db file exists
mkdir -p /usr/app/database
touch /usr/app/database/database.sqlite

# storage dirs
mkdir -p /usr/app/storage /usr/app/bootstrap/cache
chown -R www:www /usr/app/storage /usr/app/bootstrap/cache /usr/app/database

# views dir, guarantees it exists even if a volume mount wipes it
mkdir -p /usr/app/resources/views
chown -R www:www /usr/app/resources/views || true

# Warn if APP_KEY missing
if [ -z "${APP_KEY:-}" ]; then
    echo "WARNING: APP_KEY is not set. Set it in .env or compose env."
fi

# optional: wait for redis (only if using redis host)
if [ -n "${REDIS_HOST:-}" ]; then
    echo "Waiting for Redis at ${REDIS_HOST}:${REDIS_PORT:-6379}..."

    for i in $(seq 1 30); do
        if nc -z "${REDIS_HOST}" "${REDIS_PORT:-6379}" >/dev/null 2>&1; then
            echo "Redis is up."
            break
        fi
        sleep 1
    done
fi

sleep 1

if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    echo "Running migrations..."
    su -s /bin/sh -c "php artisan migrate --force" www
fi

if [ "${CACHE_ARTISAN:-false}" = "true" ]; then
    su -s /bin/sh -c "php artisan config:cache || true" www
    su -s /bin/sh -c "php artisan route:cache || true" www
fi

if [ "${CLEAR_CACHES_ON_BOOT:-false}" = "true" ]; then
  su -s /bin/sh -c "php artisan config:clear || true" www
  su -s /bin/sh -c "php artisan cache:clear || true" www
  su -s /bin/sh -c "php artisan route:clear || true" www
  # su -s /bin/sh -c "php artisan optimize:clear || true" www
fi

exec "$@"
