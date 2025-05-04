# sidenote: for prod use, we would probably use more something like (php-)fpm* + load balancer
# this is just for an easy dev setup
FROM php:8.3-apache
RUN a2enmod rewrite
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN apt update && apt install git libzip-dev unzip 7zip --yes
# zip => composer
RUN docker-php-ext-install pdo_mysql zip
RUN apt install unzip --yes

# hide src from outside snooping
# RUN chown -R www-data:www-data /var/www/html
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

USER 1000:1000
WORKDIR /var/www/html