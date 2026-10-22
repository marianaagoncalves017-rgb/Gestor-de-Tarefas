# Usa a imagem oficial do PHP com Apache
FROM php:8.2-apache

# Instala as extensões necessárias do PHP para ligar ao PostgreSQL (Supabase)
# O Supabase usa PostgreSQL, por isso precisamos do pdo_pgsql
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql

# Ativa o mod_rewrite do Apache (útil se usares rotas amigáveis)
RUN a2enmod rewrite

# Define o diretório de trabalho
WORKDIR /var/www/html

# Copia todos os ficheiros do teu repositório para a pasta do Apache
COPY api/ /var/www/html/

# Ajusta as permissões para o utilizador do Apache (www-data)
RUN chown -R www-data:www-data /var/www/html

# O Render define a porta através da variável de ambiente PORT. 
# O Apache por defeito ouve na 80, por isso vamos forçá-lo a ouvir na porta do Render.
RUN sed -i 's/80/${PORT}/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

# Expõe a porta (apenas informativo)
EXPOSE 80

# Comando para iniciar o Apache em primeiro plano
CMD ["apache2-foreground"]
