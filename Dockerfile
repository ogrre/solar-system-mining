FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    zip \
    unzip \
    nodejs \
    npm \
    librdkafka-dev \
    libzip-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install pdo_pgsql pgsql mbstring exif pcntl bcmath gd zip

# Install and configure xdebug
RUN pecl install xdebug && docker-php-ext-enable xdebug
RUN echo "xdebug.mode=coverage,develop" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Install rdkafka extension for Kafka support
RUN pecl install rdkafka && docker-php-ext-enable rdkafka

# Install language servers for IDE support
RUN npm install -g \
    dockerfile-language-server-nodejs \
    yaml-language-server \
    vscode-json-languageserver \
    bash-language-server

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Create composer directory to avoid permission issues
RUN mkdir -p /root/.composer

# Install global PHP tools
RUN composer global require \
    laravel/pint \
    phpstan/phpstan \
    phpactor/phpactor \
    rector/rector \
    && composer clear-cache

# Add composer global bin to PATH
ENV PATH="/root/.composer/vendor/bin:${PATH}"

# Test installations
RUN php -v && \
    phpstan --version && \
    pint --version && \
    rector --version && \
    phpactor --version

# PHP configuration optimisations for Laravel
RUN echo "memory_limit=512M" > /usr/local/etc/php/conf.d/memory-limit.ini \
    && echo "upload_max_filesize=100M" > /usr/local/etc/php/conf.d/upload-limit.ini \
    && echo "post_max_size=100M" >> /usr/local/etc/php/conf.d/upload-limit.ini \
    && echo "max_execution_time=600" > /usr/local/etc/php/conf.d/max-execution-time.ini \
    && echo "opcache.enable=1" > /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.validate_timestamps=1" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.revalidate_freq=0" >> /usr/local/etc/php/conf.d/opcache.ini

# Create necessary directories with proper permissions
RUN mkdir -p /var/www/html/storage/logs \
    && mkdir -p /var/www/html/storage/framework/cache \
    && mkdir -p /var/www/html/storage/framework/sessions \
    && mkdir -p /var/www/html/storage/framework/views \
    && mkdir -p /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html
