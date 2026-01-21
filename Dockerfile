FROM php:8.2-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libwebp-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    oniguruma-dev \
    sqlite-dev \
    icu-dev \
    zip \
    unzip \
    redis

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install \
        pdo \
        # pdo_mysql \
        # pdo_pgsql \
        pdo_sqlite \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        intl \
        sockets \
        opcache

# Install Redis PHP extension
RUN pecl install redis && docker-php-ext-enable redis

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /usr/app

# Copy application
COPY . .

# Set permissions
RUN chown -R www-data:www-data /usr/app \
    && chmod -R 755 /usr/app/storage

# Create SQLite database directory if needed
RUN mkdir -p /usr/app/database/db \
  && chmod 755 /usr/app/database/db \
  && touch /usr/app/database/db/db.sqlite \
  && chmod 755 /usr/app/database/db/db.sqlite

USER www-data
