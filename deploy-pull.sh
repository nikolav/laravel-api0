#!/usr/bin/env bash
set -euo pipefail

IMAGE="0imbn7v6rkw/laravel-api0"
NAME="laravel-api"

# remove old container if exists
docker rm -f "$NAME" >/dev/null 2>&1 || true \
&& docker run -d \
  --name "$NAME" \
  -p 127.0.0.1:9000:9000 \
  --env-file ./.env \
  -e APP_ENV=production \
  -e APP_DEBUG="true" \
  -e LOG_CHANNEL=stderr \
  -e LOG_LEVEL=info \
  -e CACHE_STORE=redis \
  -e SESSION_DRIVER=redis \
  -e RUN_MIGRATIONS="false" \
  -e CACHE_ARTISAN="true" \
  --pull=always \
  --restart unless-stopped \
  --init \
  --stop-timeout 30 \
  --health-cmd 'wget -qO- http://127.0.0.1:9000/healthz >/dev/null || exit 1' \
  --health-interval 10s \
  --health-timeout 3s \
  --health-retries 10 \
  --health-start-period 20s \
  "$IMAGE"

# docker ps -a --filter "name=$NAME"
# docker logs --tail=122 "$NAME"

# docker compose down -v --rmi all --remove-orphans
# docker system prune --all --volumes --force

# docker rm -f laravel-reverb
# docker volume rm pgdata
