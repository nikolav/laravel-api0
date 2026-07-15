# stage: frontend deps
FROM node:22-alpine AS frontend-deps

WORKDIR /build

COPY package.json ./
COPY package-lock.json* ./
RUN if [ -f package-lock.json ]; then npm ci; else npm install; fi

# stage: frontend build
FROM node:22-alpine AS assets

WORKDIR /build

COPY --from=frontend-deps /build/node_modules ./node_modules
COPY package.json ./
COPY package-lock.json* ./
COPY . .

RUN npm run build

# stage: node runtime deps for app scripts
FROM node:22-alpine AS node-runtime-deps

WORKDIR /app

COPY package.json ./
COPY package-lock.json* ./
RUN if [ -f package-lock.json ]; then npm ci --omit=dev; else npm install --omit=dev; fi

# stage: php runtime
FROM php:8.4-fpm-bookworm

# browsershot / chromium env
ENV \
  APP_DIR=/usr/app \
  PUPPETEER_SKIP_DOWNLOAD=true \
  CHROME_BIN=/usr/bin/chromium \
  CHROME_PATH=/usr/lib/chromium/ \
  CHROME_OPTS="--headless --disable-gpu --no-sandbox --disable-setuid-sandbox --disable-dev-shm-usage" \
  BROWSERSHOT_DISABLE_SANDBOX=true \
  HEADLESS=true \
  NODE_PATH=/usr/app/node_modules \
  DEBIAN_FRONTEND=noninteractive

# System dependencies, Debian
RUN apt-get update && apt-get install -y \
    ca-certificates \
    curl \
    git \
    iproute2 \
    netcat-openbsd \
    nginx \
    supervisor \
    unzip \
    wget \
    libicu-dev \
    libzip-dev \
    libonig-dev \
    libpq-dev \
    libsqlite3-dev \
    libfreetype6-dev \
    libharfbuzz-dev \
    nodejs \
    npm \
    libnss3 \
    fonts-dejavu \
    fonts-freefont-ttf \
    fonts-noto \
    fonts-noto-cjk \
    chromium \
    libstdc++6 \
    gcc \
    openssl \
    zlib1g \
    libffi-dev \
    autoconf \
    g++ \
    make \
    pkg-config \
    && rm -rf /var/lib/apt/lists/*

# PHP extensions, using docker-php-ext-install
RUN docker-php-ext-configure intl \
    && docker-php-ext-configure zip \
    && docker-php-ext-install -j"$(nproc)" \
    bcmath \
    ctype \
    ftp \
    intl \
    mbstring \
    opcache \
    pcntl \
    pdo_pgsql \
    pdo_sqlite \
    posix \
    zip

# PECL extensions
RUN pecl install redis-6.3.0 > /dev/null 2>&1
RUN pecl install mongodb-2.3.3 > /dev/null 2>&1
RUN pecl install grpc-1.82.0 > /dev/null 2>&1
RUN pecl install protobuf-5.35.1 > /dev/null 2>&1
RUN docker-php-ext-enable redis mongodb grpc protobuf

# Cleanup
RUN apt-get clean \
    && rm -rf \
        /tmp/* \
        /var/cache/apt/* \
        /root/.npm \
        /usr/local/lib/php/.registry/.channel.pecl.php.net/* \
        /var/lib/apt/lists/*

# opcache configuration for production
RUN { \
    echo 'opcache.memory_consumption=128'; \
    echo 'opcache.interned_strings_buffer=8'; \
    echo 'opcache.max_accelerated_files=10000'; \
    echo 'opcache.revalidate_freq=2'; \
    echo 'opcache.fast_shutdown=1'; \
    echo 'opcache.enable_cli=1'; \
    echo 'realpath_cache_size=4096K'; \
    echo 'realpath_cache_ttl=600'; \
    } > /usr/local/etc/php/conf.d/app.ini

# compare the required/recommended PHP extensions for Laravel
# against currently loaded extensions
COPY docker/check-php-extensions.sh /usr/local/bin/check-php-extensions.sh
RUN chmod +x /usr/local/bin/check-php-extensions.sh

# setup permissions, required directories
RUN addgroup --system --gid 1000 www \
  && adduser --system --uid 1000 --gid 1000 --no-create-home www \
  && mkdir -p \
    /usr/app \
    /var/log/nginx \
    /run/nginx \
    /var/lib/nginx \
    /var/tmp/nginx \
    /var/log/supervisor \
    /var/log/php \
    /home/www/.cache \
    /home/www/Downloads \
    /var/run \
&& chown -R www:www \
    /usr/app \
    /var/log/nginx \
    /run/nginx \
    /var/lib/nginx \
    /var/tmp/nginx \
    /var/log/supervisor \
    /var/log/php \
    /home/www \
    /var/run

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

# queue worker launcher
COPY docker/queue-start.sh /usr/local/bin/queue-start.sh
RUN chmod +x /usr/local/bin/queue-start.sh

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
  --no-progress \
  && composer clear-cache

# app source
COPY . .

# copy local node runtime deps
COPY --from=node-runtime-deps /app/node_modules /usr/app/node_modules
COPY --from=node-runtime-deps /app/package.json /usr/app/package.json
COPY --from=node-runtime-deps /app/package-lock.json /usr/app/package-lock.json

# copy built frontend assets from the Node stage
COPY --from=assets /build/public/build /usr/app/public/build

# run the scripts now that artisan exists (package discovery, etc.)
RUN composer run-script post-autoload-dump --no-interaction
# or more explicit:
# RUN php artisan package:discover --ansi

# fix permissions for runtime dirs
RUN mkdir -p \
    /usr/app/storage \
    /usr/app/bootstrap/cache \
    /usr/app/database \
    /usr/app/resources/views \
  && chown -R www:www \
    /usr/app/storage \
    /usr/app/bootstrap/cache \
    /usr/app/database \
    /usr/app/resources/views

# verify chromium tooling
RUN set -eux; \
  node --version; \
  npm --version; \
  chromium --version; \
  test -d /usr/app/node_modules; \
  test -f /usr/app/node_modules/juice/package.json; \
  test -f /usr/app/public/build/manifest.json

HEALTHCHECK --interval=30s --timeout=3s --start-period=20s --retries=3 \
  CMD sh -c 'curl -fsS -H "Internal-Auth: $NGINX_INTERNAL_AUTH_TOKEN" \
    http://127.0.0.1:9000/api/health || exit 1'

# expose http port (nginx)
EXPOSE 9000

ENTRYPOINT ["/entrypoint.sh"]
# start supervisor (nginx + php-fpm)
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
