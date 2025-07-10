# Utilisez l'image officielle PHP 8.2 FPM
FROM php:8.2-fpm as base

# Arguments au début (personnalisables via --build-arg)
ARG USER_ID=1000
ARG GROUP_ID=1000

# Installation des dépendances système
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    libicu-dev \
    libonig-dev \
    libpng-dev \
    libcurl4-openssl-dev \
    librdkafka-dev \
    unzip \
    git \
    && rm -rf /var/lib/apt/lists/*

# Création de l'utilisateur "laravel" avec l'UID/GID de l'hôte
RUN groupadd -g ${GROUP_ID} laravel && \
    useradd -u ${USER_ID} -g laravel -m laravel && \
    install -d -m 0755 -o laravel -g laravel /home/laravel

# Installation des extensions PHP
RUN docker-php-ext-install \
    pdo_pgsql \
    pgsql \
    mbstring \
    zip \
    intl \
    gd \
    bcmath \
    opcache \
    pcntl \
    exif \
    curl

# Installation de rdkafka
RUN pecl install rdkafka && docker-php-ext-enable rdkafka

# Installation de Composer (global, accessible par tous les utilisateurs)
RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin \
    --filename=composer \
    --2

# Installation des outils de développement en tant que "laravel"
USER laravel
RUN composer global require phpstan/phpstan rector/rector laravel/pint
ENV PATH="/home/laravel/.composer/vendor/bin:${PATH}"

# Répertoire de travail et permissions
WORKDIR /var/www/html
USER root
RUN chown -R laravel:laravel /var/www/html
USER laravel

# Étape de développement avec Node.js
FROM base as node-builder
USER root
RUN curl -fsSL https://deb.nodesource.com/setup_lts.x | bash - \
    && apt-get update \
    && apt-get install -y nodejs \
    && npm install -g npm@latest \
    && chown -R laravel:laravel /var/www/html
USER laravel
