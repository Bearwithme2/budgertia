#!/usr/bin/env bash
# ─────────────────────────────────────────────────────────────
# Bootstrap PHP 8.2, Composer 2, Docker & docker-compose v2
# Tested on Ubuntu 24.04 runners (Codex / Codespaces style)
# ─────────────────────────────────────────────────────────────
set -euo pipefail
log() { printf "\e[1;34m%s\e[0m\n" ">> $*"; }

###############################################################################
# Verify we’re on an apt-based distro
###############################################################################
if ! command -v apt-get >/dev/null 2>&1; then
  echo "This setup script currently supports apt-based images only." >&2
  exit 1
fi

log "Updating package index"
sudo apt-get update -qq

###############################################################################
# PHP 8.2 (cli only) + common extensions
###############################################################################
if ! php -v 2>/dev/null | grep -q "^PHP 8.2"; then
  log "Installing PHP 8.2 and extensions"
  sudo apt-get install -y \
    lsb-release ca-certificates curl software-properties-common
  sudo add-apt-repository -y ppa:ondrej/php
  sudo apt-get update -qq
  sudo apt-get install -y --no-install-recommends \
    php8.2-cli php8.2-mbstring php8.2-xml php8.2-intl \
    php8.2-curl php8.2-zip php8.2-gd
fi

###############################################################################
# Composer 2 – try getcomposer.org first, fall back to GitHub or apt
###############################################################################
if ! command -v composer >/dev/null 2>&1; then
  log "Installing Composer 2"
  install_composer () {
    sudo mv composer.phar /usr/local/bin/composer
    sudo chmod +x /usr/local/bin/composer
    log "Composer installed: $(composer --version | cut -d' ' -f2)"
  }

  if curl -fsSL https://getcomposer.org/download/latest-stable/composer.phar -o composer.phar; then
    install_composer
  elif curl -fsSL https://github.com/composer/composer/releases/latest/download/composer.phar -o composer.phar; then
    install_composer
  else
    log "Curl failed twice, installing via apt"
    sudo apt-get install -y composer
  fi
fi

###############################################################################
# Docker Engine + docker-compose v2 (CLI plugin)
###############################################################################
if ! command -v docker >/dev/null 2>&1; then
  log "Installing Docker Engine"
  sudo install -m 0755 -d /etc/apt/keyrings
  curl -fsSL https://download.docker.com/linux/ubuntu/gpg | \
       sudo gpg --dearmor -o /etc/apt/keyrings/docker.gpg
  sudo chmod a+r /etc/apt/keyrings/docker.gpg

  UBUNTU_FLAVOR=$(lsb_release -cs)
  echo \
    "deb [arch=\$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] \
     https://download.docker.com/linux/ubuntu \$UBUNTU_FLAVOR stable" | \
    sudo tee /etc/apt/sources.list.d/docker.list >/dev/null

  sudo apt-get update -qq
  sudo apt-get install -y docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin
fi

###############################################################################
# Allow current user to run Docker without sudo (next login)
###############################################################################
if ! groups "\$USER" | grep -q '\bdocker\b'; then
  log "Adding \$USER to 'docker' group (effective next login)"
  sudo usermod -aG docker "\$USER"
fi

log "Bootstrap finished. PHP $(php -r 'echo PHP_VERSION;') | Docker $(docker --version)"
