#!/usr/bin/env bash
set -euo pipefail

cd /usr/app

# ensure runtime dirs
mkdir -p \
  /usr/app/storage/framework/{cache,sessions,views} \
  /usr/app/storage/logs \
  /usr/app/bootstrap/cache \
  /usr/app/database

if [ "$(id -u)" = "0" ]; then
  chown -R www:www /usr/app/storage /usr/app/bootstrap/cache /usr/app/database
fi

find /usr/app/storage -type d -exec chmod 775 {} \;
find /usr/app/storage -type f -exec chmod 664 {} \;
chmod -R 775 /usr/app/bootstrap/cache
chmod -R 775 /usr/app/database

# db:sqlite file writrable
touch /usr/app/database/database.sqlite
chmod 664 /usr/app/database/database.sqlite

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

# start laravel scheduler in the background
if [ "${RUN_SCHEDULER:-false}" = "true" ]; then
    echo "starting scheduler..."
    while true; do
        su -s /bin/sh -c "php artisan schedule:run --no-ansi --quiet || true" www
        sleep 60
    done &
fi

exec "$@"
