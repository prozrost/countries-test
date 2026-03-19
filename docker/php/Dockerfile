# -----------------------------------------------------------------------------
# Stage: base – PHP-FPM + extensions + Composer (used for dev with volume mount)
# -----------------------------------------------------------------------------
FROM php:8.2-fpm-alpine AS base

RUN apk add --no-cache \
    libzip-dev \
    libpng-dev \
    libxml2-dev \
    oniguruma-dev \
    icu-dev \
    sqlite-dev \
    $PHPIZE_DEPS \
    && docker-php-ext-configure intl \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_sqlite \
        mbstring \
        intl \
        zip \
        bcmath \
        opcache \
    && apk del $PHPIZE_DEPS

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY docker/php/docker-app-entrypoint.sh /usr/local/bin/docker-app-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-app-entrypoint.sh

EXPOSE 9000
ENTRYPOINT ["docker-app-entrypoint.sh"]
CMD ["php-fpm"]

# -----------------------------------------------------------------------------
# Stage: production – copy app and install deps (optional, for non-mounted deploy)
# -----------------------------------------------------------------------------
# FROM base AS production
# COPY . /var/www
# RUN composer install --no-dev --optimize-autoloader --no-interaction
# USER www-data
# CMD ["php-fpm"]
