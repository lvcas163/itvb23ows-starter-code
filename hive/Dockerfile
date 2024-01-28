FROM php:8.2-cli

RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli
RUN docker-php-ext-install session && docker-php-ext-enable session

WORKDIR /usr/src/hive

COPY .src /usr/src/hive

EXPOSE 80
