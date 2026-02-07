#!/bin/sh
set -eu

echo "==> Queue worker bootstrap..."

# Optional toggle
if [ "${RUN_QUEUE:-true}" != "true" ]; then
  echo "RUN_QUEUE=false → skipping queue worker"
  exec sleep infinity
fi

# Default queue list (override via env if desired)
QUEUES="${QUEUE_WORK_QUEUES:-broadcasts,default}"

echo "Starting Laravel queue worker..."
echo "  Connection: redis"
echo "  Queues:     $QUEUES"

exec php /usr/app/artisan queue:work redis \
  --queue="$QUEUES" \
  --sleep=1 \
  --tries=3 \
  --timeout=90 \
  --memory=256 \
  --no-interaction \
  -vv
