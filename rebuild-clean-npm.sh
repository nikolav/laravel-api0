#!/usr/bin/env bash
set -euo pipefail

echo "🧹 Cleaning build artifacts..."
rm -rf node_modules

echo "🧾 Re-resolving deps..."
rm -f package-lock.json
npm install --no-audit --no-fund

echo "✅ Done."
