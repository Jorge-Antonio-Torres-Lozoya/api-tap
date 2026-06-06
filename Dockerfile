# PHP 8.2 base image
FROM php:8.2-cli

# Composer running as root in CI/containers needs this flag
ENV COMPOSER_ALLOW_SUPERUSER=1

# System dependencies needed to build the PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libssl-dev \
    && rm -rf /var/lib/apt/lists/*

# Native PHP extensions:
#   gd  -> required by phpspreadsheet (Excel export)
#   zip -> required by phpspreadsheet / Excel
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" gd zip

# MongoDB extension (installed via PECL, not bundled with PHP)
RUN pecl install mongodb \
    && docker-php-ext-enable mongodb

# Composer binary
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Install dependencies first (better layer caching)
COPY composer.json composer.lock ./
RUN composer install --optimize-autoloader --no-dev --no-interaction --no-scripts

# Copy the rest of the application
COPY . .

# Finish composer setup (package discovery) now that all files are present
RUN composer dump-autoload --optimize --no-dev \
    && php artisan storage:link || true

# Railway provides the port via $PORT
EXPOSE 8080
CMD ["sh", "-c", "php artisan serve --host 0.0.0.0 --port ${PORT:-8080}"]
