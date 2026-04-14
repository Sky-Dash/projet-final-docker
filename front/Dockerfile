FROM php:8.2-apache

COPY . /var/www/

RUN sed -i 's|/var/www/html|/var/www/public|g' /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www

RUN chown -R www-data:www-data /var/www

EXPOSE 80