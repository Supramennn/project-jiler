FROM dunglas/frankenphp:1-php8.4-alpine AS base

WORKDIR /app

RUN cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    && install-php-extensions intl pdo_pgsql pgsql

FROM base AS dependencies

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN apk add --no-cache unzip

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts

FROM base AS runtime

COPY --from=dependencies /app/vendor ./vendor
COPY . .

COPY docker/php.ini /usr/local/etc/php/conf.d/zz-app.ini
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
COPY Caddyfile /etc/caddy/Caddyfile
RUN chmod +x /usr/local/bin/docker-entrypoint.sh \
    && mkdir -p /tmp/writable/cache /tmp/writable/debugbar /tmp/writable/emails \
        /tmp/writable/logs /tmp/writable/session /tmp/writable/uploads

ENV PORT=80 \
    WRITABLE_PATH=/tmp/writable \
    CI_ENVIRONMENT=production

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]
