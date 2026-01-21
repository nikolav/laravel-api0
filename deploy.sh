#!/bin/bash
docker compose down
docker compose build --no-cache
docker compose up -d api
docker compose logs -f api
