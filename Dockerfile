FROM php:8.3-fpm

WORKDIR /var/www/html

# instalar dependências do sistema e do PostgreSQL
RUN apt-get update && \
    apt-get install -y libpq-dev git unzip && \
    docker-php-ext-install pdo pdo_pgsql

# instalar composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# copiar o fonte e instalar dependências PHP
COPY . .
RUN composer install --no-dev --optimize-autoloader

# permissões
RUN chown -R www-data:www-data storage bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
