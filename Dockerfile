FROM php:8-fpm-alpine

COPY app .

RUN apk update --no-cache \
    && apk add autoconf g++ make\
    icu-dev \
    oniguruma-dev \
    tzdata \
    && pecl install pcov \
    && apk del autoconf g++ make
 
RUN docker-php-ext-install intl

RUN docker-php-ext-install pcntl

RUN docker-php-ext-install pdo_mysql

RUN docker-php-ext-install mbstring

RUN docker-php-ext-enable pcov
 
RUN rm -rf /var/cache/apk/*
 
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

RUN composer install 
RUN composer dump-autoload

WORKDIR /app

CMD ["php-fpm"]