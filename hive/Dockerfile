FROM php:8.2-cli

RUN docker-php-ext-install mysqli pdo pdo_mysql
RUN docker-php-ext-enable mysqli

WORKDIR /usr/src/hive

COPY . /usr/src/hive

EXPOSE 80
