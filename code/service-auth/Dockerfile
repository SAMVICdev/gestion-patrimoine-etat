FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
    libpq-dev unzip git curl \
    && docker-php-ext-install pdo pdo_pgsql \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
COPY . .

ENV COMPOSER_HTTP_STALL_TIMEOUT=600 COMPOSER_HTTP_TIMEOUT=600

RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

EXPOSE 8000
CMD ["sh", "-c", "php artisan migrate --force && exec php artisan serve --host=0.0.0.0 --port=${PORT:-8000}"]