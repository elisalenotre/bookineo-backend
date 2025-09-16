FROM php:8.4-fpm

# libs nécessaires à Symfony + extensions
RUN apt-get update && apt-get install -y \
    git unzip libicu-dev libzip-dev libpng-dev libjpeg-dev libfreetype6-dev libonig-dev libxml2-dev \
 && docker-php-ext-configure gd --with-jpeg=/usr/include \
 && docker-php-ext-install -j$(nproc) pdo_mysql intl zip gd opcache \
 && rm -rf /var/lib/apt/lists/*

# Composer 2.8.11
COPY --from=composer:2.8.11 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# prime cache build
COPY composer.json composer.lock ./
RUN COMPOSER_CACHE_DIR=/tmp/composer composer install --no-interaction --prefer-dist

# code
COPY . .

# permissions cache/log
RUN mkdir -p var/cache var/log && chown -R www-data:www-data var

EXPOSE 9000
CMD ["php-fpm"]
