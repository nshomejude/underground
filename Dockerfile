# syntax=docker/dockerfile:1

# ---------------------------------------------------------------------------
# Stage 1 — frontend assets (Vite build)
# ---------------------------------------------------------------------------
FROM node:22-alpine AS frontend

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY resources/ resources/
COPY vite.config.js ./

RUN npm run build

# ---------------------------------------------------------------------------
# Stage 2 — PHP dependencies (Composer, no dev packages)
# ---------------------------------------------------------------------------
FROM composer:2 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-scripts \
    --no-plugins \
    --ignore-platform-reqs \
    --prefer-dist \
    --optimize-autoloader

# ---------------------------------------------------------------------------
# Stage 3 — runtime image
# ---------------------------------------------------------------------------
FROM php:8.3-cli-alpine AS app

RUN apk add --no-cache \
        libzip \
        icu-libs \
        oniguruma \
    && apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        libzip-dev \
        icu-dev \
    && docker-php-ext-install -j"$(nproc)" \
        pdo_mysql \
        bcmath \
        intl \
        zip \
        opcache \
    && apk del .build-deps

WORKDIR /var/www/html

# Application source (see .dockerignore for what is excluded — notably
# vendor/, node_modules/ and .env, which are supplied by the stages below
# and by the runtime environment respectively).
COPY . .

# Dependencies and built assets from the earlier stages.
COPY --from=vendor /app/vendor ./vendor
COPY --from=frontend /app/public/build ./public/build

RUN php artisan config:clear \
    && php artisan package:discover --ansi \
    && chown -R www-data:www-data storage bootstrap/cache

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

USER www-data

EXPOSE 8000

ENTRYPOINT ["entrypoint.sh"]
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
