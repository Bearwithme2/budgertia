FROM node:18 AS frontend
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY assets ./assets
COPY webpack.config.js ./
RUN npm run build

FROM php:8.2-fpm
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libicu-dev \
    && docker-php-ext-install intl opcache
RUN git config --system --add safe.directory /var/www/html

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html
COPY . .
COPY --from=frontend /app/public/build ./public/build
RUN composer install --no-interaction --prefer-dist --optimize-autoloader
CMD ["php-fpm"]
