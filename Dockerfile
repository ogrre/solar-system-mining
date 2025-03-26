# Utilisez l'image officielle PHP 8.2 FPM
FROM php:8.2-fpm as base

# Arguments must be at the very top
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

RUN groupadd -g ${GROUP_ID} laravel && \
    useradd -u ${USER_ID} -g laravel -m laravel && \
    install -d -m 0755 -o laravel -g laravel /home/laravel

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

# Configure le répertoire de travail
WORKDIR /var/www/html

# Transfert ownership au user laravel
RUN chown -R laravel:laravel /var/www/html

# Passe à l'utilisateur non-root
USER laravel

# Étape de développement avec Node.js
FROM base as node-builder
USER root
RUN curl -fsSL https://deb.nodesource.com/setup_lts.x | bash - \
    && apt-get update \
    && apt-get install -y nodejs \
    && npm install -g npm@latest

FROM base as development
COPY --from=node-builder /usr/local/bin/node /usr/local/bin/
COPY --from=node-builder /usr/local/bin/npm /usr/local/bin/

# Revenir à l'utilisateur non-privilégié
USER laravel


