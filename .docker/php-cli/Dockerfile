FROM php:7.4-cli

RUN apt-get update

RUN apt-get install -y \
        libzip-dev \
        zip \
  && docker-php-ext-install zip pdo pdo_mysql

RUN curl -sS https://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer

WORKDIR /var/www/orders
