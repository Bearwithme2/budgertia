############################
# 1. Front-end build stage #
############################
FROM node:18 AS frontend

WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci

COPY assets ./assets
COPY webpack.config.js ./
RUN npm run build


###########################
# 2. PHP-FPM run-time     #
###########################
FROM php:8.2-fpm

# ––––– Build-time args let us inject your host UID/GID –––––
ARG UID=1000
ARG GID=1000

# Basic tools + PHP extensions
RUN apt-get update && apt-get install -y \
        git unzip libicu-dev \
    && docker-php-ext-install intl opcache

# Create matching user/group inside the image
RUN groupadd -g ${GID} app \
 && useradd  -m -u ${UID} -g app app

# Trust the project directory for every Git user in the image
RUN git config --system --add safe.directory /var/www/html

# Composer binary from the official image
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

###########################
# 3. Application files    #
###########################
WORKDIR /var/www/html
COPY . .
COPY --from=frontend /app/public/build ./public/build

# Install PHP deps (dev packages will be pulled if APP_ENV ≠ prod)
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

USER app            # run everything as the non-root user
CMD ["php-fpm"]
