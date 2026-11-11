FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --no-progress --optimize-autoloader --no-scripts

FROM node:22-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY resources ./resources
COPY vite.config.js ./
RUN npm run build

FROM php:8.3-cli
WORKDIR /app

RUN apt-get update \
    && apt-get install -y --no-install-recommends libpq-dev libicu-dev libzip-dev unzip \
    && docker-php-ext-install pdo_pgsql intl bcmath opcache \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Back4App free tier hanya 256 MB RAM, jadi opcache dan memory_limit dibatasi.
RUN { \
        echo 'opcache.enable=1'; \
        echo 'opcache.memory_consumption=64'; \
        echo 'opcache.max_accelerated_files=10000'; \
        echo 'opcache.validate_timestamps=0'; \
        echo 'memory_limit=192M'; \
        echo 'expose_php=Off'; \
    } > /usr/local/etc/php/conf.d/zz-app.ini

COPY --from=vendor /app/vendor ./vendor
COPY . .
COPY --from=assets /app/public/build ./public/build

RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chmod -R ug+rwX storage bootstrap/cache \
    && php artisan package:discover --ansi

# Back4App mengirim PORT (default 10000). Worker dibatasi agar hemat RAM.
ENV PHP_CLI_SERVER_WORKERS=2
ENV PORT=8080
EXPOSE 8080

# Entrypoint menjalankan migration (Back4App tidak punya job terpisah),
# lalu menyalakan server pada 0.0.0.0:$PORT.
CMD ["sh", "docker-entrypoint.sh"]
