#!/usr/bin/env bash
# ─────────────────────────────────────────────────────────────
# Bootstrap PHP 8.2, Composer 2, Docker & docker-compose v2
# for Codex / GitHub Codespaces-style runners.
# Safe to run repeatedly (idempotent).
# ─────────────────────────────────────────────────────────────

set -euo pipefail

log() { printf "\e[1;34m%s\e[0m\n" ">> $*"; }

###############################################################################
# Detect apt-based distro & refresh apt metadata
###############################################################################
if ! command -v apt-get >/dev/null 2>&1; then
  echo "This setup script currently supports apt-based images only."
  exit 1
fi

log "Updating package index"
sudo apt-get update -qq

###############################################################################
# PHP 8.2 + common extensions
###############################################################################
if ! php -v 2>/dev/null | grep -q "^PHP 8.2"; then
  log "Installing PHP 8.2 and extensions"
  sudo apt-get install -y \
    lsb-release ca-certificates curl software-properties-common
  sudo add-apt-repository -y ppa:ondrej/php
  sudo apt-get update -qq
  sudo apt-get install -y php8.2 php8.2-cli php8.2-mbstring php8.2-xml \
                          php8.2-intl php8.2-curl php8.2-zip php8.2-gd
fi

###############################################################################
# Composer 2 (single-file installer to /usr/local/bin)
###############################################################################
if ! command -v composer >/dev/null 2>&1; then
  log "Installing Composer 2"
  EXPECTED_CHECKSUM="$(curl -s https://composer.github.io/installer.sig)"
  php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
  ACTUAL_CHECKSUM="$(php -r "echo hash_file('sha384','composer-setup.php');")"
  if [ "$EXPECTED_CHECKSUM" != "$ACTUAL_CHECKSUM" ]; then
    echo 'ERROR: Invalid Composer installer checksum' >&2
    rm composer-setup.php
    exit 1
  fi
  sudo php composer-setup.php --quiet --install-dir=/usr/local/bin --filename=composer
  rm composer-setup.php
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
    "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] \
     https://download.docker.com/linux/ubuntu $UBUNTU_FLAVOR stable" | \
    sudo tee /etc/apt/sources.list.d/docker.list >/dev/null

  sudo apt-get update -qq
  sudo apt-get install -y docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin
fi

###############################################################################
# Enable current user to run Docker without sudo (session only)
###############################################################################
if ! groups "$USER" | grep -q '\bdocker\b'; then
  log "Adding $USER to 'docker' group (effective next login)"
  sudo usermod -aG docker "$USER"
fi

log "All tools installed. PHP: $(php -r 'echo PHP_VERSION;') | Composer: $(composer --version | cut -d" " -f2) | Docker: $(docker --version)"
