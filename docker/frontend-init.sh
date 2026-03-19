#!/bin/sh
set -e
cd /var/www

if [ -f public/build/manifest.json ]; then
    exit 0
fi

npm install --no-audit --no-fund --legacy-peer-deps
npm run build
