FROM php:8.2-fpm

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libicu-dev \
    libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql intl

RUN pecl install apcu && docker-php-ext-enable apcu

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . .

RUN composer update

RUN composer install --no-scripts --no-autoloader

RUN composer dump-autoload --optimize --no-scripts

CMD ["php-fpm"]