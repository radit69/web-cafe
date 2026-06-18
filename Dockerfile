FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libicu-dev \
    zip \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
    bcmath \
    gd \
    intl \
    mbstring \
    pdo_mysql \
    pcntl \
    zip \
    opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Disable conflicting MPMs and force prefork (build time)
RUN a2dismod mpm_event mpm_worker || true
RUN a2enmod mpm_prefork || true

# Set Apache document root to Laravel's public directory
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --no-interaction --optimize-autoloader

# Create required storage directories (may be missing from git)
RUN mkdir -p storage/framework/cache/data \
    && mkdir -p storage/framework/sessions \
    && mkdir -p storage/framework/testing \
    && mkdir -p storage/framework/views \
    && mkdir -p storage/logs \
    && mkdir -p storage/app/public \
    && mkdir -p bootstrap/cache

# Set permissions
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Create storage link
RUN php artisan storage:link || true

# Clear and cache config for production
RUN php artisan config:clear \
    && php artisan route:clear \
    && php artisan view:clear

# Use a startup script to handle PORT dynamically and force disable duplicate MPMs at runtime
RUN echo '#!/bin/bash\n\
    # Force delete duplicate MPM configurations if they get re-enabled\n\
    rm -f /etc/apache2/mods-enabled/mpm_event.load\n\
    rm -f /etc/apache2/mods-enabled/mpm_event.conf\n\
    rm -f /etc/apache2/mods-enabled/mpm_worker.load\n\
    rm -f /etc/apache2/mods-enabled/mpm_worker.conf\n\
    a2enmod mpm_prefork || true\n\
    \n\
    # Set port\n\
    sed -i "s/Listen 80/Listen ${PORT:-8080}/" /etc/apache2/ports.conf\n\
    sed -i "s/:80/:${PORT:-8080}/" /etc/apache2/sites-available/000-default.conf\n\
    \n\
    # Run migrations and seed\n\
    php artisan migrate --force || true\n\
    php artisan db:seed --force || true\n\
    \n\
    # Start Apache\n\
    exec apache2-foreground' > /usr/local/bin/start.sh \
    && chmod +x /usr/local/bin/start.sh

EXPOSE 8080

CMD ["/usr/local/bin/start.sh"]
