#!/usr/bin/env bash
set -euo pipefail

IMAGE="0imbn7v6rkw/laravel-api0"
NAME="api"

# remove old container if exists
docker rm -f "$NAME" >/dev/null 2>&1 || true \
&& docker run -d \
  --name "$NAME" \
  -p 127.0.0.1:9000:9000 \
  --env-file ./.env \
  -e APP_ENV=production \
  -e APP_DEBUG="false" \
  -e LOG_CHANNEL=stack \
  -e LOG_LEVEL=info \
  -e CACHE_STORE=redis \
  -e SESSION_DRIVER=redis \
  -e QUEUE_CONNECTION=redis \
  -e CACHE_ARTISAN="false" \
  -e CLEAR_CACHES_ON_BOOT="true" \
  -e RUN_QUEUE="true" \
  -e QUEUE_WORK_QUEUES="broadcasts,default,low" \
  --restart unless-stopped \
  --init \
  --stop-timeout 30 \
  --health-cmd='sh -c "curl -fsS -H \"Internal-Auth: $NGINX_INTERNAL_AUTH_TOKEN\" http://127.0.0.1:9000/api/health || exit 1"' \
  --health-interval 10s \
  --health-timeout 3s \
  --health-retries 10 \
  --health-start-period 20s \
  "$IMAGE"

# --pull=always \


# docker ps -a --filter "name=$NAME"
# docker logs --tail=122 "$NAME"

# docker rm -f api
# docker system prune --all --volumes --force
