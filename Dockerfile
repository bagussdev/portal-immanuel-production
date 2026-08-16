# syntax=docker/dockerfile:1

FROM node:22-alpine AS frontend
WORKDIR /build
COPY package.json package-lock.json ./
RUN npm ci
COPY resources ./resources
COPY public ./public
COPY vite.config.js tailwind.config.js postcss.config.js ./
RUN npm run build

FROM composer:2 AS vendor
WORKDIR /build
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-progress --no-scripts
COPY . .
RUN composer dump-autoload --no-dev --optimize --no-interaction

FROM composer:2 AS vendor-dev
WORKDIR /build
COPY composer.json composer.lock ./
RUN composer install --prefer-dist --no-interaction --no-progress --no-scripts
COPY . .
RUN composer dump-autoload --optimize --no-interaction

FROM php:8.3-fpm-alpine AS app
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    && apk add --no-cache icu-libs libzip libpng libjpeg-turbo freetype oniguruma su-exec \
    && apk add --no-cache --virtual .build-deps $PHPIZE_DEPS icu-dev libzip-dev libpng-dev libjpeg-turbo-dev freetype-dev oniguruma-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) bcmath gd intl opcache pcntl pdo_mysql zip \
    && apk del .build-deps
WORKDIR /var/www/html
COPY --chown=www-data:www-data . .
COPY --from=vendor --chown=www-data:www-data /build/vendor ./vendor
COPY --from=frontend --chown=www-data:www-data /build/public/build ./public/build
COPY docker/php/php.ini /usr/local/etc/php/conf.d/99-immanuel.ini
COPY docker/php/entrypoint.sh /usr/local/bin/immanuel-entrypoint
RUN chmod +x /usr/local/bin/immanuel-entrypoint \
    && mkdir -p storage/app/private storage/app/public storage/framework/cache storage/framework/sessions storage/framework/views storage/logs \
    && chown -R www-data:www-data storage bootstrap/cache
ENTRYPOINT ["immanuel-entrypoint"]
CMD ["php-fpm", "-F"]

FROM app AS test
RUN touch .env
COPY --from=vendor-dev --chown=www-data:www-data /build/vendor ./vendor
ENV APP_ENV=testing \
    APP_DEBUG=false
CMD ["php", "artisan", "test"]

FROM nginx:1.30-alpine AS web
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY --from=app /var/www/html/public /var/www/html/public
