FROM php:8.2-apache

# Instalar extensão do PostgreSQL
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Copiar os ficheiros do projeto
COPY . /var/www/html/

EXPOSE 80
