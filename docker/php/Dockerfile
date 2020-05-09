FROM php:7.4.5-fpm-alpine3.11

LABEL maintainer="umarbatalov@gmail.com"

# Install gmp
RUN apk add --no-cache gmp gmp-dev

RUN docker-php-ext-install mysqli pdo pdo_mysql gmp
RUN docker-php-ext-configure opcache --enable-opcache && \
    docker-php-ext-install opcache

ADD docker/php/php.ini /usr/local/etc/php

# Composer
# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN composer global require "hirak/prestissimo" --prefer-dist --no-progress --no-suggest --classmap-authoritative \
    && composer clear-cache
# -- Composer

WORKDIR /srv

COPY . ./

RUN composer install --prefer-dist --no-progress --no-suggest --optimize-autoloader

RUN chown -R www-data:www-data var

VOLUME ["/srv/vendor"]
VOLUME ["/srv/var"]

CMD ["php-fpm"]

EXPOSE 9000
