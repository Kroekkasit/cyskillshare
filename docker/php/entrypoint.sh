#!/bin/sh
set -e

# Ensure storage is writable when the bind mount overrides image ownership.
mkdir -p /var/www/html/storage/logs /var/www/html/storage/uploads /var/www/html/storage/challenges /var/www/html/storage/projects /var/www/html/storage/writeups
chown -R www-data:www-data /var/www/html/storage || true
chmod -R ug+rwX /var/www/html/storage || true

exec apache2-foreground
