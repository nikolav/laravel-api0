#!/usr/bin/env bash
set -euo pipefail

docker compose down --remove-orphans
docker compose build --no-cache --pull api
docker compose up -d --force-recreate

# show a quick status
docker compose ps
docker compose logs --tail=80 api

# docker compose down -v --rmi all --remove-orphans
# docker system prune --all --volumes --force
