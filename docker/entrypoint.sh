#!/bin/bash

echo "==> Generando clave de aplicación si no existe..."
php artisan key:generate --no-interaction --force

echo "==> Ejecutando migraciones..."
php artisan migrate --force --no-interaction

echo "==> Ejecutando seeders..."
php artisan db:seed --force --no-interaction

echo "==> Enlazando almacenamiento público..."
php artisan storage:link --force 2>/dev/null || true

echo "==> Ajustando permisos de storage..."
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

echo "==> Limpiando caché..."
php artisan config:clear || true
php artisan view:clear   || true
php artisan cache:clear  || true

echo "==> Todo listo. Iniciando PHP-FPM..."
exec php-fpm
