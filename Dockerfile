# syntax=docker/dockerfile:1                          ## habilita COPY --from

FROM composer:2.8 AS vendor
WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
    --prefer-dist --optimize-autoloader \
    --ignore-platform-req=ext-gd \
    --no-scripts --no-interaction

FROM php:8.1-apache

RUN apt-get update && apt-get install -y \
    git unzip libpng-dev libjpeg-dev libfreetype6-dev libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql zip \
    && a2enmod rewrite \
    && apt-get clean && rm -rf /var/lib/apt/lists/*


WORKDIR /var/www/html

COPY --from=vendor /app/vendor ./vendor

COPY --from=vendor /usr/bin/composer /usr/bin/composer

COPY . .

RUN mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache

RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R ug+rw storage bootstrap/cache

COPY docker/php.ini /usr/local/etc/php/conf.d/laravel.ini

RUN cp .env.example .env \
    && php artisan key:generate --ansi \
    && php artisan package:discover --ansi \
    && composer dump-autoload --optimize

COPY docker/000-default.conf /etc/apache2/sites-available/000-default.conf

RUN a2ensite 000-default.conf

USER www-data

EXPOSE 80
CMD ["apache2-foreground"]

