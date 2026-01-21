#!/bin/bash
docker compose down
docker compose build --no-cache
docker compose up -d api
docker compose logs -f api

# docker stop $(docker ps -q) 2>/dev/null; docker system prune -a --volumes -f
