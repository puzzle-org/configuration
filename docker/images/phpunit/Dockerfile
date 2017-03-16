FROM php:5.6-cli

RUN pecl install xdebug-2.5.0 \
    && docker-php-ext-enable xdebug

COPY php.ini /usr/local/etc/php/conf.d
