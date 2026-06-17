FROM dunglas/frankenphp:php8.2-bookworm

RUN install-php-extensions \
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

RUN chown -R www-data:www-data storage bootstrap/cache

EXPOSE 8080

CMD ["frankenphp", "php-server"]
