FROM dunglas/frankenphp:php8.2-bookworm

RUN install-php-extensions \
    bcmath \
    calendar \
    exif \
    gd \
    intl \
    mbstring \
    pdo_mysql \
    pdo_pgsql \
    pcntl \
    shmop \
    soap \
    sockets \
    sysvmsg \
    sysvsem \
    sysvshm \
    zip \
    curl \
    gettext \
    opcache

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER=1
ENV APP_ENV=production

WORKDIR /app

COPY . .

RUN composer install --no-dev --no-interaction --optimize-autoloader

RUN php artisan optimize:clear && \
    php artisan optimize

EXPOSE 8080

CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]
