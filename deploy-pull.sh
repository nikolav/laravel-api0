#!/usr/bin/env bash
set -euo pipefail

docker run -d \
  --name laravel-api0 \
  -p 9000:9000 \
  -e APP_ENV=production \
  0imbn7v6rkw/laravel-api0

docker ps -a
docker logs --tail=122

# docker compose down -v --rmi all --remove-orphans
# docker system prune --all --volumes --force
