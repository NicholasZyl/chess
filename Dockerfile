FROM php:7.2-cli

LABEL maintainer="Mikołaj Żyłkowski <mzylkowski@gmail.com>"

WORKDIR /usr/src/chess
COPY . ./
RUN composer install --no-dev --no-interaction -o