FROM php:8.0-fpm

# Installation des dépendances système nécessaires
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    nano \
    pkg-config \
    && docker-php-ext-configure zip \
    && docker-php-ext-install pdo pdo_mysql opcache zip \
    && docker-php-ext-enable opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Installer Symfony CLI
RUN curl -sS https://get.symfony.com/cli/installer | bash \
    && mv /root/.symfony*/bin/symfony /usr/local/bin/symfony

# Définition du répertoire de travail
WORKDIR /var/www

# Copier uniquement composer.json et composer.lock pour optimiser le cache Docker
COPY . .

# Installer Composer 1.x pour cause de compatibilité avec Symfony Flex
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer self-update --1

# Installer les dépendances PHP de Symfony
RUN composer install --no-dev --optimize-autoloader

CMD ["php-fpm"]
