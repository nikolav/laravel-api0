#!/usr/bin/env bash
set -euo pipefail

HOST="1.22.333"
USER="root"
PATH="/root/app"
KEY_ID="id_ed25519_vultr"

# .env, deploy-env.debian.sh => host
scp -i ~/.ssh/$KEY_ID ./.env $USER@$HOST:$PATH/
scp -i ~/.ssh/$KEY_ID ./deploy-env.debian.sh $USER@$HOST:$PATH/
