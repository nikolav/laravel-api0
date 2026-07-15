#!/usr/bin/env bash
set -euo pipefail

IMAGE="0imbn7v6rkw/laravel-api0"
NAME="api"

# get image
docker pull "$IMAGE"

# remove old container if exists
docker rm -f "$NAME" >/dev/null 2>&1 || true

# Create network if it doesn't exist (for Redis/MySQL integration)
docker network inspect laravel-network >/dev/null 2>&1 || \
  docker network create laravel-network

docker run -d \
  --name "$NAME" \
  -p 127.0.0.1:9000:9000 \
  --network laravel-network \
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
  -e QUEUE_WORK_QUEUES="high,broadcasts,default,low" \
  -e RUN_SCHEDULER="true" \
  --restart unless-stopped \
  --init \
  --stop-timeout 30 \
  --health-cmd='sh -c "curl -fsS -H \"Internal-Auth: $NGINX_INTERNAL_AUTH_TOKEN\" http://127.0.0.1:9000/api/health || exit 1"' \
  --health-interval 30s \
  --health-timeout 3s \
  --health-retries 3 \
  --health-start-period 20s \
  --log-driver json-file \
  --log-opt max-size="10m" \
  --log-opt max-file="3" \
  "$IMAGE"

# Wait for container to be healthy
echo "⏳ Waiting for container to be healthy..."
sleep 5

# Check if container is running
if docker ps --filter "name=$NAME" --filter "status=running" | grep -q "$NAME"; then
  echo "✅ Container '$NAME' is running"
  echo "📋 Container logs (last 20 lines):"
  docker logs --tail=20 "$NAME"
else
  echo "❌ Container failed to start. Showing logs:"
  docker logs --tail=50 "$NAME"
  exit 1
fi

# Useful commands (commented out)
# docker ps -a --filter "name=$NAME"
# docker logs --tail=122 "$NAME"
# docker rm -f api
# docker system prune --all --volumes --force
