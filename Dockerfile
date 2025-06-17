# project-root/Dockerfile
FROM php:8.3-fpm

WORKDIR /var/www/html

# 1) Build‑args: URL do repositório e branch
ARG GIT_REPO=https://github.com/ximass/atividade_GCS.git
ARG GIT_BRANCH=master

# 2) Instala Git, dependências e extensão Postgres
RUN apt-get update \
 && apt-get install -y git unzip libpq-dev \
 && docker-php-ext-install pdo pdo_pgsql

# 3) Clona somente a branch desejada
RUN git clone --depth=1 --branch "$GIT_BRANCH" "$GIT_REPO" . \
 && rm -rf .git

# 4) Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# 5) Permissões
RUN chown -R www-data:www-data storage bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
