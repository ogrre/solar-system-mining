# Utilisez l'image officielle PHP 8.2 FPM
FROM php:8.2-fpm as base

# Définit le répertoire de travail
WORKDIR /var/www/html

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

# Installation des extensions PHP nécessaires
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

# Installation de l'extension rdkafka pour Kafka
RUN pecl install rdkafka && docker-php-ext-enable rdkafka

# Installation de Composer 2
RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin \
    --filename=composer \
    --2

# Installation des outils de développement PHP (globaux)
RUN composer global require phpstan/phpstan rector/rector laravel/pint

# Ajout des binaires Composer au PATH
ENV PATH="/root/.composer/vendor/bin:${PATH}"

# Étape de développement avec Node.js
FROM base as development

# Installation de Node.js et npm
RUN curl -fsSL https://deb.nodesource.com/setup_lts.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g npm@latest
