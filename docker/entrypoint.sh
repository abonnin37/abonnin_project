#!/bin/sh
set -e

# Génération des clés JWT
php bin/console lexik:jwt:generate-keypair --env=prod --overwrite

# Lancer PHP-FPM
exec php-fpm