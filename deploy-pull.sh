#!/usr/bin/env bash
set -euo pipefail

docker pull 0imbn7v6rkw/laravel-api0 && \
docker run -d \
  -p 127.0.0.1:9000:9000 \
  --env-file ./.env \
  -e APP_ENV=production \
  -e APP_DEBUG=false \
  -e LOG_CHANNEL=stderr \
  -e LOG_LEVEL=info \
  -e CACHE_STORE=redis \
  -e SESSION_DRIVER=redis \
  -e RUN_MIGRATION=false \
  -e CACHE_ARTISAN=true \
  --pull=always \
  --restart unless-stopped \
  0imbn7v6rkw/laravel-api0

docker ps -a

# docker compose down -v --rmi all --remove-orphans
# docker system prune --all --volumes --force
