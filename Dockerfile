FROM dunglas/frankenphp:php8.5 AS production

RUN apt-get update && apt-get install -y --no-install-recommends \
    ca-certificates \
    ffmpeg \
    default-mysql-client \
    git \
    unzip \
    && install-php-extensions \
        pdo_mysql \
        intl \
        zip \
        bcmath \
        gd \
        pcntl \
        igbinary \
        redis \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

COPY --chown=www-data:www-data . /app

RUN cp .env.example .env \
    && composer install \
        --no-dev \
        --optimize-autoloader \
        --no-interaction \
        --no-progress \
        --prefer-dist \
    && rm -f .env \
    && chown -R www-data:www-data /app/storage /app/bootstrap/cache

COPY docker/production/php.ini /usr/local/etc/php/conf.d/99-app.ini

USER www-data