#!/bin/sh
set -e
cd /var/www

if [ ! -f .env ]; then
    echo ""
    echo "ERROR: .env is missing."
    echo "Copy .env.example to .env and set AUTH0_DOMAIN, AUTH0_AUDIENCE, and VITE_AUTH0_CLIENT_ID (see README)."
    echo ""
    exit 1
fi

if [ ! -f vendor/autoload.php ]; then
    echo "Installing Composer dependencies (first run may take a minute)..."
    composer install --no-interaction --prefer-dist --no-progress --optimize-autoloader
fi

mkdir -p database bootstrap/cache storage/framework/{sessions,views,cache} storage/logs
chmod -R ug+rwx storage bootstrap/cache 2>/dev/null || true

if ! grep -qE '^APP_KEY=base64:[A-Za-z0-9+/=]+' .env 2>/dev/null; then
    echo "Generating APP_KEY..."
    php artisan key:generate --force --no-interaction
fi

echo "Running database migrations..."
php artisan migrate --force --no-interaction

exec docker-php-entrypoint "$@"
