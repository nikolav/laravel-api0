FROM php:8.3-fpm-alpine

# ------------------------------------------------------------
# System dependencies + PHP extensions
# ------------------------------------------------------------
RUN apk add --no-cache \
    iproute2 netcat-openbsd nginx supervisor bash curl git unzip \
    icu oniguruma libzip sqlite-libs \
  && apk add --no-cache --virtual .build-deps \
    $PHPIZE_DEPS icu-dev oniguruma-dev libzip-dev sqlite-dev pkgconf \
  && docker-php-ext-install intl mbstring zip opcache pdo_sqlite \
  && pecl install redis \
  && docker-php-ext-enable redis \
  && apk del .build-deps

# ------------------------------------------------------------
# Configure PHP-FPM to listen on 127.0.0.1:9001
# (Nginx listens on 9000 and proxies to FPM)
# ------------------------------------------------------------
RUN set -eux; \
  CONF="/usr/local/etc/php-fpm.d/www.conf"; \
  test -f "$CONF"; \
  sed -i -E 's~^[;[:space:]]*listen[[:space:]]*=.*~listen = 127.0.0.1:9001~' "$CONF"; \
  grep -nE '^[[:space:]]*listen[[:space:]]*=' "$CONF"

# ------------------------------------------------------------
# Create user & required directories
# ------------------------------------------------------------
RUN addgroup -g 1000 -S www \
  && adduser -u 1000 -S www -G www \
  && mkdir -p \
    /usr/app \
    /var/log/nginx \
    /run/nginx \
    /var/lib/nginx \
    /var/log/supervisor \
  && chown -R www:www \
    /usr/app \
    /var/log/nginx \
    /run/nginx \
    /var/lib/nginx

# --- Fix PHP-FPM logging when running as non-root (supervisor user=www) ---
RUN set -eux; \
  mkdir -p /var/log/php; \
  touch /var/log/php/fpm-error.log /var/log/php/fpm-access.log; \
  chown -R www:www /var/log/php; \
  chmod 664 /var/log/php/fpm-error.log /var/log/php/fpm-access.log; \
  DOCKERCONF="/usr/local/etc/php-fpm.d/docker.conf"; \
  test -f "$DOCKERCONF"; \
  sed -i -E 's~^error_log\s*=.*~error_log = /var/log/php/fpm-error.log~' "$DOCKERCONF"; \
  sed -i -E 's~^access\.log\s*=.*~access.log = /var/log/php/fpm-access.log~' "$DOCKERCONF"

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

COPY composer.json composer.lock ./
RUN composer install \
  --no-dev \
  --no-interaction \
  --prefer-dist \
  --optimize-autoloader

COPY . .

# Fix permissions for runtime dirs
RUN mkdir -p \
    /usr/app/storage \
    /usr/app/bootstrap/cache \
    /usr/app/database \
  && chown -R www:www \
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
