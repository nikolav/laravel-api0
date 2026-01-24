#!/usr/bin/env bash
set -euo pipefail

docker compose up -d --build api
docker compose ps
docker compose logs --tail=80 api

# docker compose down -v --rmi all --remove-orphans
# docker system prune --all --volumes --force
