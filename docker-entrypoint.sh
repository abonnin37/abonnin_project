#!/bin/sh

# Attendre que PHP-FPM soit prêt
php-fpm --daemonize

# Attendre un peu pour s'assurer que PHP-FPM est bien démarré
sleep 2

# Démarrer Apache en premier plan
exec httpd -DFOREGROUND 