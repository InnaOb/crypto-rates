#!/bin/sh
set -e

echo "[entrypoint] Waiting for MySQL to be ready..."
until php bin/console doctrine:query:sql "SELECT 1" > /dev/null 2>&1; do
    sleep 2
done
echo "[entrypoint] MySQL is ready."

echo "[entrypoint] Running migrations..."
php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration
echo "[entrypoint] Migrations done."

echo "[entrypoint] Starting..."
exec "$@"
