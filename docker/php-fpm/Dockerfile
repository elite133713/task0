FROM php:8.0.9-fpm

# install main dependencies

RUN apt-get update \
  && apt-get install -y --no-install-recommends \
    curl \
    libmemcached-dev \
    libz-dev \
    libpq-dev \
    libjpeg-dev \
    libpng-dev \
    libfreetype6-dev \
    libssl-dev \
    libmcrypt-dev \
    libbz2-dev\
    libxml2-dev\
    libssh2-1-dev\
    procps\
    htop\
    libzip-dev\
    unzip\
    p7zip-full\
  && rm -rf /var/lib/apt/lists/*

# install sub dependencies

RUN rm /etc/apt/preferences.d/no-debian-php

RUN apt-get update -yqq && \
    apt-get -y install libxml2-dev php-soap && \
    docker-php-ext-install soap && \
    docker-php-ext-install zip && \
    docker-php-ext-install bcmath && \
    docker-php-ext-install pdo_mysql && \
    docker-php-ext-install bz2 && \
    docker-php-ext-install pdo_pgsql && \
    docker-php-ext-install sockets && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install gd

# install xdebug dependencies

ADD ./xdebug.ini /usr/local/etc/php/conf.d
RUN pecl install xdebug-3.0.4 && \
        docker-php-ext-enable xdebug

ADD symfony.ini /usr/local/etc/php/conf.d
RUN chown -R www-data:www-data /var/www

RUN apt-get update && \
    apt-get -y install curl nano && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www

EXPOSE 9000
