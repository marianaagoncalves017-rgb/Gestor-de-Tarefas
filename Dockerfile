FROM php:8.2-apache

# Instalar extensão do PostgreSQL
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Copiar os arquivos do projeto
COPY . /var/www/html/

# Ajustar a porta do Apache para usar a variável PORT da Vercel
RUN sed -i 's/80/${PORT}/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

EXPOSE 80
