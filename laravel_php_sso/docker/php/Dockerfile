FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    libpq-dev \
    git curl libzip-dev \
    && docker-php-ext-install pdo pdo_mysql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
