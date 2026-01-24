FROM php:8.3-fpm-alpine

# system dependencies + php extensions
RUN apk add --no-cache \
    iproute2 netcat-openbsd nginx supervisor bash curl git unzip \
    icu oniguruma libzip sqlite-libs postgresql-libs \
  && apk add --no-cache --virtual .build-deps \
    $PHPIZE_DEPS icu-dev oniguruma-dev libzip-dev sqlite-dev postgresql-dev pkgconf \
  && docker-php-ext-install intl mbstring zip opcache pdo_sqlite pdo_pgsql \
  && pecl install redis \
  && docker-php-ext-enable redis \
  && apk del .build-deps

# create user & required directories
RUN addgroup -g 1000 -S www \
  && adduser -u 1000 -S www -G www \
  && mkdir -p \
    /usr/app \
    /var/log/nginx \
    /run/nginx \
    /var/lib/nginx \
    /var/tmp/nginx \
    /var/log/supervisor \
  && chown -R www:www \
    /usr/app \
    /var/log/nginx \
    /run/nginx \
    /var/lib/nginx \
    /var/tmp/nginx \
    /var/log/supervisor

# configure php-fpm to listen on 127.0.0.1:9001
#   (nginx listens on 9000 and proxies to fpm)
# remove user and group from the pool config
#   (silences "user directive ignored" notices when running phpfpm as !root)
RUN set -eux; \
  CONF="/usr/local/etc/php-fpm.d/www.conf"; \
  test -f "$CONF"; \
  sed -i -E 's~^[;[:space:]]*listen[[:space:]]*=.*~listen = 127.0.0.1:9001~' "$CONF"; \
  sed -i -E '/^\s*(user|group)\s*=.*/d' "$CONF"; \
  grep -nE '^(user|group|listen)\s*=' "$CONF" || true

# --- fix php-fpm logging when running as non-root (supervisor user=www) ---
RUN set -eux; \
  mkdir -p /var/log/php; \
  touch /var/log/php/fpm-error.log /var/log/php/fpm-access.log; \
  chown -R www:www /var/log/php; \
  chmod 664 /var/log/php/fpm-error.log /var/log/php/fpm-access.log; \
  DOCKERCONF="/usr/local/etc/php-fpm.d/docker.conf"; \
  test -f "$DOCKERCONF"; \
  sed -i -E 's~^error_log\s*=.*~error_log = /var/log/php/fpm-error.log~' "$DOCKERCONF"; \
  sed -i -E 's~^access\.log\s*=.*~access.log = /var/log/php/fpm-access.log~' "$DOCKERCONF"

# set php-fpm log level
RUN set -eux; \
  CONF="/usr/local/etc/php-fpm.conf"; \
  test -f "$CONF"; \
  if grep -qE '^[;[:space:]]*log_level' "$CONF"; then \
    sed -i -E 's~^[;[:space:]]*log_level\s*=.*~log_level = notice~' "$CONF"; \
  else \
    echo 'log_level = notice' >> "$CONF"; \
  fi; \
  grep -n 'log_level' "$CONF"

# set nginx worker user to www
RUN set -eux; \
  NGINXCONF="/etc/nginx/nginx.conf"; \
  test -f "$NGINXCONF"; \
  sed -i -E 's/^\s*user\s+nginx\s*;/user www;/' "$NGINXCONF"; \
  grep -n '^user ' "$NGINXCONF"

# nginx & supervisor configs
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf
# COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY docker/supervisord.conf /etc/supervisord.conf

# entrypoint
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# app setup
WORKDIR /usr/app

# composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock ./
RUN composer install \
  --no-dev \
  --no-interaction \
  --prefer-dist \
  --optimize-autoloader \
  --no-scripts \
  --no-progress

COPY . .

# run the scripts now that artisan exists (package discovery, etc.)
RUN composer run-script post-autoload-dump --no-interaction
# or more explicit:
# RUN php artisan package:discover --ansi

# fix permissions for runtime dirs
RUN mkdir -p \
    /usr/app/storage \
    /usr/app/bootstrap/cache \
    /usr/app/database \
  && chown -R www:www \
    /usr/app/storage \
    /usr/app/bootstrap/cache \
    /usr/app/database

HEALTHCHECK --interval=30s --timeout=3s --retries=3 \
  CMD curl -fsS http://127.0.0.1:9000/healthz || exit 1

# expose http port (nginx)
EXPOSE 9000

ENTRYPOINT ["/entrypoint.sh"]
# start supervisor (nginx + php-fpm)
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
