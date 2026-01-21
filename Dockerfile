FROM php:8.2-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    autoconf \
    gcc \
    g++ \
    make \
    freetype-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    libpng-dev \
    libzip-dev \
    icu-dev \
    sqlite-dev \
    oniguruma-dev \
    libexif-dev

# Configure and install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install \
        pdo \
        pdo_sqlite \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        intl \
        opcache

# Install Redis PHP extension with build tools
RUN pecl install redis && docker-php-ext-enable redis

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /usr/app

# Copy application
COPY . .

RUN mkdir -p /usr/app/database/db && touch /usr/app/database/db/db.sqlite
