#!/usr/bin/env bash
set -euo pipefail

echo '@@ ./.env'
cat ./.env

echo '@@ ./storage/app/keys/firebase-service-account.json'
cat ./storage/app/keys/firebase-service-account.json
