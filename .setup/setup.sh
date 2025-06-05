#!/usr/bin/env bash
set -euo pipefail
sudo apt-get update -qq

if ! php -v 2>/dev/null | grep -q '^PHP 8\.2'; then
  sudo apt-get install -y lsb-release ca-certificates curl software-properties-common
  sudo add-apt-repository -y ppa:ondrej/php
  sudo apt-get update -qq
  sudo apt-get install -y --no-install-recommends php8.2-cli php8.2-mbstring php8.2-xml php8.2-intl php8.2-curl php8.2-zip php8.2-gd
fi

if ! command -v composer >/dev/null 2>&1; then
  curl -fsSL https://getcomposer.org/download/latest-stable/composer.phar -o composer.phar || \
  curl -fsSL https://github.com/composer/composer/releases/latest/download/composer.phar -o composer.phar || \
  { sudo apt-get install -y composer && exit 0; }
  sudo mv composer.phar /usr/local/bin/composer && sudo chmod +x /usr/local/bin/composer
fi

if ! command -v docker >/dev/null 2>&1; then
  sudo install -m 0755 -d /etc/apt/keyrings
  curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /etc/apt/keyrings/docker.gpg
  sudo chmod a+r /etc/apt/keyrings/docker.gpg
  UBUNTU_FLAVOR=$(lsb_release -cs)
  echo "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] https://download.docker.com/linux/ubuntu ${UBUNTU_FLAVOR} stable" | sudo tee /etc/apt/sources.list.d/docker.list >/dev/null
  sudo apt-get update -qq
  sudo apt-get install -y docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin
fi

groups "$USER" | grep -q '\bdocker\b' || sudo usermod -aG docker "$USER"
