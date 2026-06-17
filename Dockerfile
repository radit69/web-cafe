FROM php:8.2-apache

RUN docker-php-ext-install \
    bcmath \
    gd \
    intl \
    mbstring \
    pdo_mysql \
    pcntl \
    zip \
    opcache

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER=1
ENV APP_ENV=production

WORKDIR /app
COPY . .

RUN composer install --no-dev --no-interaction --optimize-autoloader

RUN a2enmod rewrite && \
    chown -R www-data:www-data storage bootstrap/cache

RUN sed -i 's!/var/www/html!/app/public!g' /etc/apache2/sites-available/000-default.conf && \
    sed -i 's!/var/www/!/app/public!g' /etc/apache2/apache2.conf

EXPOSE 8080

CMD sed -i "s/80/$PORT/g" /etc/apache2/sites-available/000-default.conf && \
    sed -i "s/80/$PORT/g" /etc/apache2/ports.conf && \
    apache2-foreground
