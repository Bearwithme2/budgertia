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

# Inject host UID/GID so files created in the container
# match permissions on the host bind-mount
ARG UID=1000
ARG GID=1000

# ── Base tools + PHP extensions ────────────────────────────
RUN apt-get update && apt-get install -y \
        git unzip libicu-dev libsqlite3-dev \
    && docker-php-ext-install intl opcache pdo pdo_sqlite

# Remove user and group directives as PHP-FPM runs under the
# non-root "app" user defined below. Leaving these directives
# triggers noisy notices during container start-up.
RUN sed -i '/^user =/d;/^group =/d' /usr/local/etc/php-fpm.d/www.conf

# ── Non-root user matching host ────────────────────────────
RUN groupadd -g ${GID} app \
 && useradd  -m -u ${UID} -g app app

# ── Git safety for bind-mounted working tree ───────────────
RUN git config --system --add safe.directory /var/www/html

# ── Composer binary ────────────────────────────────────────
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ── Writable Composer cache for the non-root user ──────────
ENV COMPOSER_HOME=/home/app/.composer
RUN mkdir -p $COMPOSER_HOME/cache && chown -R app:app $COMPOSER_HOME

# ── Project sources ────────────────────────────────────────
WORKDIR /var/www/html
COPY . .
COPY --from=frontend /app/public/build ./public/build
RUN chown -R app:app /var/www/html       # make sources writable

# ── Switch to non-root and install PHP deps ────────────────
USER app
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

CMD ["php-fpm"]
