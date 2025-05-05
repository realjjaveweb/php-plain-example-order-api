# sidenote: for prod use, we would probably use more something like (php-)fpm* + load balancer
# this is just for an easy dev setup
FROM php:8.3-apache
RUN a2enmod rewrite

RUN apt update

# adding the repo will need the following:
RUN apt install lsb-release gnupg2 --yes

# add deb.sury.org repo for libmpdec-dev required by decimal
RUN curl --silent --show-error https://packages.sury.org/php/apt.gpg | apt-key add --
RUN echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" > /etc/apt/sources.list.d/sury-php.list

# install system packages
RUN apt update && apt install git libzip-dev unzip 7zip unzip libmpdec-dev --yes
# install php extensions zip => composer; Decimal/Decimal => don't use float for money...
RUN docker-php-ext-install pdo_mysql zip
RUN pecl install decimal
# install composer
RUN curl --silent --show-error https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# hide src from outside snooping
# RUN chown -R www-data:www-data /var/www/html
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# prepare user to keep permissions healthy
USER 1000:1000
WORKDIR /var/www/html