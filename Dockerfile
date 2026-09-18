FROM php:8.5-fpm AS builder

RUN apt-get update && apt-get install -y --no-install-recommends \
    curl \
    unzip \
    git \
    libonig-dev \
    libssl-dev \
    libxml2-dev \
    libcurl4-openssl-dev \
    libicu-dev \
    libzip-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        intl \
        zip \
        bcmath \
        gd \
        pcntl \
    && pecl install igbinary \
    && docker-php-ext-enable igbinary \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get autoremove -y \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

WORKDIR /app

COPY . /app

RUN cp .env.example .env

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --no-progress \
    --prefer-dist

RUN rm -f .env

FROM php:8.5-fpm AS production

RUN apt-get update && apt-get install -y --no-install-recommends \
    ca-certificates \
    ffmpeg \
    default-mysql-client \
    libicu76 \
    libzip5 \
    libpng16-16t64 \
    libjpeg62-turbo \
    libfreetype6 \
    && apt-get autoremove -y \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

COPY --from=builder /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/
COPY --from=builder /usr/local/etc/php/conf.d/ /usr/local/etc/php/conf.d/

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

COPY docker/production/php.ini /usr/local/etc/php/conf.d/99-app.ini

COPY --from=builder /app /app

WORKDIR /app

RUN chown -R www-data:www-data /app

USER www-data

EXPOSE 9000

CMD ["php-fpm"]