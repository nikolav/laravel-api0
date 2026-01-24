#!/usr/bin/env bash
set -euo pipefail

docker pull 0imbn7v6rkw/laravel-api0 && \
docker stop laravel-api || true && \
docker rm laravel-api || true && \
docker run -d \
  --name laravel-api \
  -p 127.0.0.1:9000:9000 \
  --env-file ./.env \
  -e APP_ENV=production \
  -e APP_DEBUG=false \
  -e RUN_MIGRATION=true \
  -e CACHE_ARTISAN=true \
  -e LOG_CHANNEL=stderr \
  -e CACHE_STORE=redis \
  -e SESSION_DRIVER=redis \
  -e QUEUE_CONNECTION=redis \
  --restart unless-stopped \
  0imbn7v6rkw/laravel-api0

docker ps -a
docker logs --tail=122 laravel-api

# docker compose down -v --rmi all --remove-orphans
# docker system prune --all --volumes --force
