FROM codemix/yii2-base:2.0.10-php7-apache

# Apache config and composer wrapper
COPY ./apache2.conf /etc/apache2/apache2.conf

RUN a2enmod headers && service apache2 restart

WORKDIR /var/www/html