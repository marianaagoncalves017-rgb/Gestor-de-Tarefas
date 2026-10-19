FROM php:8.2-apache

# Instalar extensão de PostgreSQL necessária para o PHP
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Copiar os ficheiros do projeto para a pasta do servidor Apache
COPY . /var/www/html/

EXPOSE 80
