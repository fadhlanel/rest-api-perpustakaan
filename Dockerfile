FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --no-progress --optimize-autoloader --no-scripts

FROM php:8.3-cli
WORKDIR /app

RUN apt-get update \
    && apt-get install -y --no-install-recommends libpq-dev libicu-dev libzip-dev unzip \
    && docker-php-ext-install pdo_pgsql intl bcmath opcache \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Kode image immutable, jadi validasi timestamp opcache boleh dimatikan.
RUN { \
        echo 'opcache.enable=1'; \
        echo 'opcache.memory_consumption=128'; \
        echo 'opcache.max_accelerated_files=20000'; \
        echo 'opcache.validate_timestamps=0'; \
        echo 'expose_php=Off'; \
    } > /usr/local/etc/php/conf.d/zz-app.ini

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY --from=vendor /app/vendor ./vendor
COPY . .

RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chmod -R ug+rwX storage bootstrap/cache

# Cloud Run mengirim PORT (default 8080). PHP built-in server single-thread,
# jadi pakai worker agar beberapa request bisa ditangani bersamaan.
ENV PHP_CLI_SERVER_WORKERS=4
ENV PORT=8080

EXPOSE 8080

# Migration tidak dijalankan di sini: multi-instance Cloud Run bisa balapan.
# Jalankan `php artisan migrate --force` sekali lewat Cloud Run Job (lihat DEPLOYMENT.md).
CMD ["sh", "-c", "php artisan config:cache && php artisan route:cache && php artisan view:cache && exec php artisan serve --host=0.0.0.0 --port=${PORT}"]
