#!/bin/bash
echo "==> Iniciando queue worker..."
exec php artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
