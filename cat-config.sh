#!/usr/bin/env bash
set -euo pipefail

echo '@@ ./.env'
cat ./.env

echo '@@ ./nginx-env.conf'
cat ./nginx-env.conf
