FROM php:7.2-cli

LABEL maintainer="Mikołaj Żyłkowski <mzylkowski@gmail.com>"

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
    git zip \
    && curl -sS https://getcomposer.org/installer | \
    php -- --install-dir=/usr/bin/ --filename=composer \
    && apt-get autoremove -y \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /usr/src/chess
COPY . ./
RUN composer install --no-dev --no-interaction -o