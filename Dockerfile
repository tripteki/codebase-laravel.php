FROM php:8.0-alpine3.16 as codebase

    ARG APP_TIMEZONE
    ARG APP_NAME
    ARG APP_ENV
    ARG SERVER_HOST
    ARG SERVER_PORT

    ENV TZ=${APP_TIMEZONE}
    ENV APP_NAME=${APP_NAME}
    ENV APP_ENV=${APP_ENV}
    ENV SERVER_HOST=${SERVER_HOST}
    ENV SERVER_PORT=${SERVER_PORT}

    WORKDIR /var/www/${APP_NAME}/
    COPY . .
    RUN apk update \
        && \
        if [ ! -e /tmp/.cache ]; then \
            php -r $(echo "cmVhZGZpbGUoJ2h0dHA6Ly9nZXRjb21wb3Nlci5vcmcvaW5zdGFsbGVyJyk7Cg==" | base64 -d) | php -- --install-dir=/usr/bin/ --filename=$(echo "Y29tcG9zZXIK" | base64 -d) \
            && \
            apk add --no-cache --virtual .phpize-deps $PHPIZE_DEPS linux-headers $(echo "dHpkYXRhIG5vZGVqcyBucG0gY3VybC1kZXYgbGliemlwLWRldiB6aXAgdW56aXAK" | base64 -d) \
            freetype libjpeg-turbo libpng \
            freetype-dev libjpeg-turbo-dev libpng-dev \
            rabbitmq-c-dev librdkafka-dev \
            && \
            docker-php-ext-configure gd \
            --with-freetype=/usr/include/ \
            --with-jpeg=/usr/include/ \
            # --with-png=/usr/include/ \ #
            && \
            NPROC=$(grep -c ^processor /proc/cpuinfo 2>/dev/null || 1) \
            && \
            docker-php-ext-install -j${NPROC} zip gd curl bcmath pcntl sockets pdo pdo_mysql session tokenizer \
            && \
            pecl channel-update pecl.php.net; \
            printf "yes\n" | pecl install xdebug && docker-php-ext-enable xdebug; \
            printf "yes\n" | pecl install swoole && docker-php-ext-enable swoole; \
            printf "yes\n" | pecl install amqp && docker-php-ext-enable amqp; \
            printf "yes\n" | pecl install rdkafka && docker-php-ext-enable rdkafka; \
            printf "no\n" | pecl install redis && docker-php-ext-enable redis; \
            apk del --no-cache freetype-dev libjpeg-turbo-dev libpng-dev && rm -rf /tmp/pear/ \
            && \
            if [ "${APP_ENV}" = "production" ]; then \
                cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" ; \
                echo "Set : php.ini-production" ; \
            else \
                cp "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini" ; \
                echo "Set : php.ini-development" ; \
            fi; \
            npm install --legacy-peer-deps && composer install; \
            touch /tmp/.cache; \
        fi \
        && \
        APP_ENV=production ./bin/project -b -l -t -e -c;

    EXPOSE ${SERVER_PORT}

    ENTRYPOINT []
    CMD ["php", "artisan", "supervisor:start", "foreground"]
