FROM php:7.2-cli

LABEL maintainer="Mikołaj Żyłkowski <mzylkowski@gmail.com>"

COPY . /usr/src/chess
WORKDIR /usr/src/chess
