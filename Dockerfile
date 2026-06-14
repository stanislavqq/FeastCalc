FROM php:8.4-fpm

COPY ./app /application
WORKDIR /application

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN apt-get update \
    && apt-get install -y zip unzip git imagemagick libmagickwand-dev libfreetype6-dev libjpeg62-turbo-dev libpng-dev libzip-dev --no-install-recommends \
    && pecl install imagick \
    && docker-php-ext-enable imagick \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install zip \
    && mkdir -p /application/tmp \
    && chmod 777 /application/tmp \
    && chmod 777 /application/var/cache -R

RUN composer install