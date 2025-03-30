#!/bin/sh

# Démarrage de PHP-FPM en arrière-plan
php-fpm &

# Démarrage d'Apache en premier plan
exec httpd -DFOREGROUND 