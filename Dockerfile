FROM php:8.2-fpm-alpine AS base

RUN apk add --no-cache \
    mysql-client \
    curl \
    zip \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_mysql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./

RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

COPY . .

RUN mkdir -p /var/log/php \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

EXPOSE 8000

USER www-data

CMD ["php", "-S", "0.0.0.0:8000", "-t", "src/Infra/Http"]

FROM base AS development

USER root

RUN composer install --optimize-autoloader --no-interaction --no-progress

RUN apk add --no-cache $PHPIZE_DEPS \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug

USER www-data
