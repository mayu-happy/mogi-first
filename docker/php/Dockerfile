FROM php:8.2-fpm

COPY php.ini /usr/local/etc/php/

RUN apt update \
  && apt install -y \
      default-mysql-client \
      zlib1g-dev \
      libzip-dev \
      unzip \
      libpng-dev \
      libjpeg62-turbo-dev \
      libfreetype6-dev \
  && docker-php-ext-configure gd \
       --with-freetype \
       --with-jpeg \
  && docker-php-ext-install \
       gd \
       pdo_mysql \
       zip

RUN curl -sS https://getcomposer.org/installer | php \
  && mv composer.phar /usr/local/bin/composer \
  && composer self-update

WORKDIR /var/www
