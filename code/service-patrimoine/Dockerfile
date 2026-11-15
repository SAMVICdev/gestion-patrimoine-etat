# ── Étape 1 : build des assets Vite (public/build est gitignoré) ──
FROM node:22-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
RUN npm run build

# ── Étape 2 : application PHP ──
FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
    libpq-dev libzip-dev libpng-dev libjpeg62-turbo-dev unzip git curl \
    && docker-php-ext-configure gd --with-jpeg \
    && docker-php-ext-install pdo pdo_pgsql zip gd \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
COPY . .
COPY --from=assets /app/public/build ./public/build

ENV COMPOSER_HTTP_STALL_TIMEOUT=600 COMPOSER_HTTP_TIMEOUT=600

RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

EXPOSE 8000
CMD ["sh", "-c", "php artisan migrate --force && exec php artisan serve --host=0.0.0.0 --port=${PORT:-8000}"]
