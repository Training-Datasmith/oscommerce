#!/usr/bin/env bash
# Cloud agent: PHP 8.4 + extensions, MySQL, Apache for osCommerce integration tests.
# Refine during W0 (documents/cloud-grind-agent.md); must exit 0 before PHPUnit integration.
set -euo pipefail

repo_root="$(cd "$(dirname "$0")/.." && pwd)"
export DEBIAN_FRONTEND=noninteractive

php_ver="$(php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;' 2>/dev/null || echo "8.4")"

ensure_ondrej() {
  if ! apt-cache show "php${php_ver}-mysql" &>/dev/null 2>&1; then
    sudo apt-get update -qq
    sudo apt-get install -y --no-install-recommends software-properties-common ca-certificates gnupg
    sudo add-apt-repository -y ppa:ondrej/php
    sudo apt-get update -qq
  fi
}

ensure_ondrej
sudo apt-get install -y --no-install-recommends \
  "php${php_ver}" "php${php_ver}-cli" "php${php_ver}-mysql" "php${php_ver}-mbstring" \
  "php${php_ver}-xml" "php${php_ver}-gd" "php${php_ver}-curl" "php${php_ver}-zip" \
  "php${php_ver}-sqlite3" "php${php_ver}-pcov" \
  mysql-server apache2 libapache2-mod-php"${php_ver}" composer

for ext in pdo_mysql pdo_sqlite mbstring xml gd curl; do
  php -m | grep -qi "^${ext}$"
done

sudo service mysql start || true
sudo service apache2 start || true

# Default DB for harness (override via env in tests).
sudo mysql -e "CREATE DATABASE IF NOT EXISTS oscommerce_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>/dev/null || true
sudo mysql -e "CREATE USER IF NOT EXISTS 'oscommerce'@'localhost' IDENTIFIED BY 'oscommerce';" 2>/dev/null || true
sudo mysql -e "GRANT ALL ON oscommerce_test.* TO 'oscommerce'@'localhost'; FLUSH PRIVILEGES;" 2>/dev/null || true

apache_site="/etc/apache2/sites-available/oscommerce.conf"
if [[ ! -f "${apache_site}" ]]; then
  sudo tee "${apache_site}" >/dev/null <<EOF
<VirtualHost *:80>
  ServerName localhost
  DocumentRoot ${repo_root}
  <Directory ${repo_root}>
    AllowOverride All
    Require all granted
  </Directory>
</VirtualHost>
EOF
  sudo a2dissite 000-default.conf 2>/dev/null || true
  sudo a2ensite oscommerce.conf
  sudo a2enmod rewrite
  sudo service apache2 reload
fi

# Installer writes merged config via Setup step 3 (www-data).
sudo chown -R www-data:www-data "${repo_root}/osCommerce/OM/Config" "${repo_root}/osCommerce/OM/Work" 2>/dev/null || true
sudo chmod -R u+rwX,g+rwX,o+rwX "${repo_root}/osCommerce/OM/Config" "${repo_root}/osCommerce/OM/Work" 2>/dev/null || true

cd "${repo_root}"
if [[ -f composer.json ]]; then
  composer install --no-interaction
fi

echo "install.sh: PHP ${php_ver}, MySQL, Apache ready (docroot=${repo_root})"
