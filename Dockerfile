FROM php:8.2-fpm

# Instalar a extensão pdo_mysql
RUN docker-php-ext-install pdo_mysql
