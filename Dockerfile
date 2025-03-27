FROM php:8.0-alpine

# Installation des dépendances système nécessaires
RUN apk add --no-cache \
    apache2 \
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

# Activer mod_rewrite pour Apache
RUN sed -i '/#LoadModule rewrite_module/s/^#//g' /etc/apache2/httpd.conf \
    && echo "ServerName localhost" >> /etc/apache2/httpd.conf

# Installer Symfony CLI
RUN curl -sS https://get.symfony.com/cli/installer | bash \
    && mv /root/.symfony*/bin/symfony /usr/local/bin/symfony

# Définition du répertoire de travail
WORKDIR /var/www/html

RUN chown -R www-data:www-data /var/www/html

# Copier uniquement composer.json et composer.lock pour optimiser le cache Docker
COPY . .

# Installer Composer 1.x pour cause de compatibilité avec Symfony Flex
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer self-update --1

USER www-data

# Installer les dépendances PHP de Symfony
RUN composer install --no-dev --optimize-autoloader

CMD ["httpd", "-D", "FOREGROUND"]
