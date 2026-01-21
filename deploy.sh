#!/usr/bin/env bash
set -euo pipefail

docker compose down
docker compose build --no-cache api
docker compose up -d --force-recreate

# show a quick status + last logs (won't block forever)
docker compose ps
docker compose logs --tail=200 api
