FROM php:8.0-apache-alpine

# Installation des dépendances système nécessaires
RUN apk add --no-cache \
    bash \
    zip \
    unzip \
    git \
    nano \
    pkgconfig \
    libzip-dev \
    mariadb-client \
    curl \
    && docker-php-ext-configure zip \
    && docker-php-ext-install pdo pdo_mysql zip \
    && docker-php-ext-enable opcache

# Installer Symfony CLI
RUN curl -sS https://get.symfony.com/cli/installer | bash \
    && mv /root/.symfony*/bin/symfony /usr/local/bin/symfony

# Définition du répertoire de travail
WORKDIR /var/www/html

RUN chown -R www-data:www-data /var/www/html
USER www-data

# Copier uniquement composer.json et composer.lock pour optimiser le cache Docker
COPY . .

# Installer Composer 1.x pour cause de compatibilité avec Symfony Flex
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
USER root
RUN composer self-update --1
USER www-data

# Installer les dépendances PHP de Symfony
RUN composer install --no-dev --optimize-autoloader

# Apache: activer mod_rewrite pour Symfony
RUN a2enmod rewrite

CMD ["apache2-foreground"]
