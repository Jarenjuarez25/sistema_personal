FROM php:8.3-apache

RUN apt-get update && apt-get install -y libpq-dev unzip git \
    && docker-php-ext-install pgsql pdo_pgsql

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./

RUN composer install --no-dev --optimize-autoloader

COPY . .

RUN a2enmod rewrite

EXPOSE 80