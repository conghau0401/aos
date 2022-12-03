FROM php:8.1-apache

RUN apt-get update \
    && apt-get install -y libicu-dev libssl-dev ssl-cert openssl vim less cron \
    && docker-php-ext-install intl pdo_mysql

#install some base extensions
RUN apt-get install -y \
    libzip-dev \
    zip \
    && docker-php-ext-install zip

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN a2enmod ssl rewrite headers

COPY files/php.ini /usr/local/etc/php/php.ini
COPY files/vhosts_ssl.conf /etc/apache2/sites-available/vhosts_ssl.conf

RUN a2ensite vhosts_ssl
