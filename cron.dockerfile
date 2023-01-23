FROM php:8.1.0-fpm-alpine

RUN docker-php-ext-install pdo pdo_mysql

Copy crontab /etc/crontabs/root

CMD ["crond","-f"]
