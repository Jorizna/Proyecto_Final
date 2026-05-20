FROM php:8.4-fpm

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Instalar extensiones PHP
RUN docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Crear usuario de aplicación
RUN groupadd -g 1000 www && useradd -u 1000 -ms /bin/bash -g www www

WORKDIR /var/www

# Copiar solo composer.json y composer.lock primero (mejor caché de capas)
COPY --chown=www:www composer.json composer.lock* /var/www/

# Instalar dependencias PHP sin scripts (no hay artisan ni .env aún)
RUN COMPOSER_MEMORY_LIMIT=-1 composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --no-interaction \
    --prefer-dist

# Copiar el resto del proyecto
COPY --chown=www:www . /var/www

# Generar autoloader optimizado ahora que está todo el código
RUN COMPOSER_MEMORY_LIMIT=-1 composer dump-autoload \
    --optimize \
    --no-scripts \
    --no-interaction

# Permisos correctos
RUN chown -R www:www /var/www \
    && chmod -R 755 /var/www/storage \
    && chmod -R 755 /var/www/bootstrap/cache

# Copiar y activar entrypoint
COPY --chown=www:www docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

USER www

EXPOSE 9000

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
