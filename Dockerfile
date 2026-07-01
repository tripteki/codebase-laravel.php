ARG PHP_VERSION=8.3

FROM composer:2 AS composer

FROM php:${PHP_VERSION}-cli-alpine AS base

COPY --from=mlocati/php-extension-installer:2 /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions swoole redis pdo_mysql intl zip gd bcmath pcntl sockets exif opcache \
    && apk add --no-cache tzdata

COPY docker/php.ini ${PHP_INI_DIR}/conf.d/zz-codebase.ini

WORKDIR /app

FROM base AS vendor

COPY --from=composer /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock ./
COPY src ./src

RUN --mount=type=cache,target=/root/.composer/cache \
    composer install --no-dev --no-scripts --no-autoloader --no-interaction --no-progress --prefer-dist

COPY . .

RUN mkdir -p storage/app/public storage/app/private storage/framework/cache/data storage/framework/sessions \
        storage/framework/testing storage/framework/views storage/logs storage/swagger bootstrap/cache \
    && composer dump-autoload --optimize --no-dev --no-interaction \
    && rm -rf storage/logs/* bootstrap/cache/config.php bootstrap/cache/routes-v7.php bootstrap/cache/events.php

FROM base AS runtime

ENV APP_ENV=production \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr \
    OCTANE_SERVER=swoole

COPY --from=vendor --chown=www-data:www-data /app /app

RUN chmod +x /app/docker/entrypoint.sh \
    && ln -sfn ../storage/app/public /app/public/storage

USER www-data

EXPOSE 8000 8080

ENTRYPOINT ["/app/docker/entrypoint.sh"]

CMD ["php", "artisan", "octane:start", "--server=swoole", "--host=0.0.0.0", "--port=8000"]
