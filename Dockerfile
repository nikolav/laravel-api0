FROM php:8.3-fpm-alpine

# ------------------------------------------------------------
# System dependencies + PHP extensions
# ------------------------------------------------------------
RUN apk add --no-cache \
    nginx \
    supervisor \
    bash \
    curl \
    git \
    unzip \
    icu-dev \
    oniguruma-dev \
    libzip-dev \
  && docker-php-ext-install \
    intl \
    mbstring \
    zip \
    opcache \
    pdo \
    pdo_sqlite \
  && pecl install redis \
  && docker-php-ext-enable redis

# ------------------------------------------------------------
# Configure PHP-FPM to listen on 127.0.0.1:9001
# (Nginx listens on 9000 and proxies to FPM)
# ------------------------------------------------------------
RUN sed -i 's|^listen = .*|listen = 127.0.0.1:9001|' \
    /usr/local/etc/php-fpm.d/www.conf

# ------------------------------------------------------------
# Create user & required directories
# ------------------------------------------------------------
RUN addgroup -g 1000 -S www \
  && adduser -u 1000 -S www -G www \
  && mkdir -p \
    /usr/app \
    /run/nginx \
    /var/lib/nginx \
    /var/log/supervisor \
  && chown -R www:www \
    /usr/app \
    /run/nginx \
    /var/lib/nginx

# ------------------------------------------------------------
# Nginx & Supervisor configs
# ------------------------------------------------------------
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisord.conf

# ------------------------------------------------------------
# Entrypoint
# ------------------------------------------------------------
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# ------------------------------------------------------------
# App setup
# ------------------------------------------------------------
WORKDIR /usr/app

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy application
COPY . .

# Install PHP deps (production)
RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader

# Fix permissions for runtime dirs
RUN chown -R www:www \
    /usr/app/storage \
    /usr/app/bootstrap/cache \
    /usr/app/database

# ------------------------------------------------------------
# Expose HTTP port (Nginx)
# ------------------------------------------------------------
EXPOSE 9000

# ------------------------------------------------------------
# Start Supervisor (nginx + php-fpm)
# ------------------------------------------------------------
ENTRYPOINT ["/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
