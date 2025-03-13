FROM php:8.3-fpm

# Define user
ARG user=laravel
ARG uid=1000

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

# Install Composer 2 with verification
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer --2 \
    && php -r "unlink('composer-setup.php');" \
    && /usr/local/bin/composer --version

# Create system user
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

# Create necessary directories with proper permissions
RUN mkdir -p /var/www/html/storage/logs \
    && mkdir -p /var/www/html/storage/framework/cache \
    && mkdir -p /var/www/html/storage/framework/sessions \
    && mkdir -p /var/www/html/storage/framework/views \
    && mkdir -p /var/www/html/bootstrap/cache \
    && chown -R $user:$user /var/www/html

# PHP configuration optimisations for Laravel
RUN echo "memory_limit=512M" > /usr/local/etc/php/conf.d/memory-limit.ini \
    && echo "upload_max_filesize=100M" > /usr/local/etc/php/conf.d/upload-limit.ini \
    && echo "post_max_size=100M" >> /usr/local/etc/php/conf.d/upload-limit.ini \
    && echo "max_execution_time=600" > /usr/local/etc/php/conf.d/max-execution-time.ini \
    && echo "opcache.enable=1" > /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.validate_timestamps=1" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.revalidate_freq=0" >> /usr/local/etc/php/conf.d/opcache.ini

# Set working directory
WORKDIR /var/www/html

# Set correct PATH order (system bin first, then user bin)
ENV PATH="/usr/local/bin:/usr/local/sbin:/usr/bin:/usr/sbin:/bin:/sbin:/home/$user/.composer/vendor/bin"

# Switch to non-root user
USER $user

# Set COMPOSER_HOME explicitly
ENV COMPOSER_HOME="/home/$user/.composer"

# Install global PHP tools with explicit path to Composer 2
RUN /usr/local/bin/composer global require \
    laravel/pint \
    phpstan/phpstan \
    rector/rector \
    phpmd/phpmd \
    squizlabs/php_codesniffer \
    friendsofphp/php-cs-fixer \
    && /usr/local/bin/composer clear-cache
