#!/usr/bin/env bash
set -euo pipefail

sudo apt-get update -qq

# Install PHP 8.2 CLI + common extensions
if ! php -v 2>/dev/null | grep -q '^PHP 8\.2'; then
  sudo apt-get install -y lsb-release ca-certificates curl software-properties-common
  sudo add-apt-repository -y ppa:ondrej/php
  sudo apt-get update -qq
  sudo apt-get install -y --no-install-recommends \
    php8.2-cli php8.2-mbstring php8.2-xml php8.2-intl php8.2-curl php8.2-zip php8.2-gd
fi

# Install Composer 2
if ! command -v composer >/dev/null 2>&1; then
  curl -fsSL https://getcomposer.org/download/latest-stable/composer.phar -o composer.phar || \
  curl -fsSL https://github.com/composer/composer/releases/latest/download/composer.phar -o composer.phar || \
  { sudo apt-get install -y composer && exit 0; }
  sudo mv composer.phar /usr/local/bin/composer
  sudo chmod +x /usr/local/bin/composer
fi
