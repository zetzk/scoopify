FROM php:8.0-apache

#deps
RUN apt-get update && apt-get install -y \
    zip \
    unzip \
    git \
    && docker-php-ext-install pdo_mysql

#routes
RUN a2enmod rewrite

# Copiar o app
WORKDIR /var/www/html
COPY ./ /var/www/html

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer install  --optimize-autoloader
RUN chown -R www-data:www-data /var/www/html
