FROM php:8.2-apache

# Instala extensões para PostgreSQL (Supabase)
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql

# Ativa o mod_rewrite
RUN a2enmod rewrite

# Altera a configuração do Apache para ouvir na porta do Render
RUN sed -i 's/Listen 80/Listen ${PORT}/' /etc/apache2/ports.conf \
    && sed -i 's/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/' /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html

# É ESTA LINHA QUE MUDA: Copia o conteúdo da pasta 'api' para a raiz
COPY api/ /var/www/html/

RUN chown -R www-data:www-data /var/www/html

CMD ["apache2-foreground"]
