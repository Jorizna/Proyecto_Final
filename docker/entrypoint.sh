#!/bin/bash

echo "==> Generando clave de aplicación si no existe..."
php artisan key:generate --no-interaction --force

echo "==> Ejecutando migraciones..."
php artisan migrate --force --no-interaction

echo "==> Verificando si la BD ya está inicializada..."
USER_COUNT=$(php -r "
\$host = getenv('DB_HOST') ?: 'db';
\$db   = getenv('DB_DATABASE') ?: 'fishspot';
\$user = getenv('DB_USERNAME') ?: 'fishspot_user';
\$pass = getenv('DB_PASSWORD') ?: 'fishspot_pass';
try {
    \$pdo = new PDO(\"mysql:host=\$host;dbname=\$db\", \$user, \$pass);
    echo (int)\$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
} catch (Exception \$e) {
    echo 0;
}
" 2>/dev/null || echo 0)

if [ "${USER_COUNT:-0}" = "0" ]; then
    echo "==> BD vacía, ejecutando seeders iniciales..."
    php artisan db:seed --force --no-interaction
else
    echo "==> BD ya inicializada ($USER_COUNT usuarios). Omitiendo seeders."
fi

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
