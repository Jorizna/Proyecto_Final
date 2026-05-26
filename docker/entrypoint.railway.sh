#!/bin/bash
set -e

echo "==> Ajustando permisos de storage..."
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache 2>/dev/null || true
chmod -R 775 /var/www/storage /var/www/bootstrap/cache 2>/dev/null || true

echo "==> Limpiando caché de arranque anterior..."
php artisan config:clear
php artisan cache:clear

echo "==> Comprobando APP_KEY..."
if [ -z "$APP_KEY" ]; then
    echo "    APP_KEY no definida, generando..."
    php artisan key:generate --no-interaction --force
else
    echo "    APP_KEY ya configurada."
fi

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

echo "==> Optimizando para producción..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Iniciando nginx + php-fpm vía supervisor..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
