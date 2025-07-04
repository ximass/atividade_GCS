FROM php:8.3-fpm

WORKDIR /var/www/html

# Build args: repositório e branch
ARG GIT_REPO=https://github.com/ximass/atividade_GCS.git
ARG GIT_BRANCH=master

# Instalações
RUN apt-get update \
 && apt-get install -y git unzip libpq-dev \
 && docker-php-ext-install pdo pdo_pgsql

# Clona a branch e mantém .git para futuros pulls
RUN git clone --depth=1 --branch "$GIT_BRANCH" "$GIT_REPO" .

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# Permissões
RUN chown -R www-data:www-data storage bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]