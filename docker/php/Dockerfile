FROM php:8.3-zts-bullseye AS php-base

FROM php-base AS php-with-extensions

RUN --mount=type=cache,target=/var/cache/apt,sharing=private \
    apt update \
    && apt install -y git libsodium-dev libzip-dev libpq-dev curl

RUN docker-php-ext-install pcntl sodium zip pgsql

WORKDIR /app

FROM php-with-extensions AS composer-base

COPY --link composer.json .
COPY --link composer.lock .

FROM composer-base AS composer-prod

RUN --mount=type=cache,target=/root/.composer \
    --mount=from=composer:2,source=/usr/bin/composer,target=/usr/bin/composer \
    composer validate && composer install --prefer-dist --no-dev --no-scripts

FROM composer-base AS composer-dev

RUN --mount=type=cache,target=/root/.composer \
    --mount=from=composer:2,source=/usr/bin/composer,target=/usr/bin/composer \
    composer install --prefer-dist --no-scripts

FROM composer-base AS composer-phpunit

RUN mkdir -p tools/phpunit
COPY --link tools/phpunit/composer.json tools/phpunit/composer.json

RUN --mount=type=cache,target=/root/.composer \
    --mount=from=composer:2,source=/usr/bin/composer,target=/usr/bin/composer \
    cd tools/phpunit && composer install --prefer-dist --no-scripts

FROM composer-base AS composer-psalm

RUN mkdir -p tools/psalm
COPY --link tools/psalm/composer.json tools/psalm/composer.json

RUN --mount=type=cache,target=/root/.composer \
    --mount=from=composer:2,source=/usr/bin/composer,target=/usr/bin/composer \
    cd tools/psalm && composer install --prefer-dist --no-scripts


FROM composer-base AS composer-update
RUN --mount=type=cache,target=/root/.composer \
    --mount=from=composer:2,source=/usr/bin/composer,target=/usr/bin/composer \
    composer update --prefer-dist --no-scripts

FROM scratch AS composer-output

COPY --link --from=composer-dev /app/composer.lock .
COPY --link --from=composer-dev /app/vendor vendor
COPY --link --from=composer-phpunit /app/tools/phpunit/composer.lock tools/phpunit/composer.lock
COPY --link --from=composer-phpunit /app/tools/phpunit/vendor tools/phpunit/vendor
COPY --link --from=composer-psalm /app/tools/psalm/composer.lock tools/psalm/composer.lock
COPY --link --from=composer-psalm /app/tools/psalm/vendor tools/psalm/vendor

FROM scratch AS composer-output-updated

COPY --link --from=composer-update /app/composer.lock .
COPY --link --from=composer-update /app/vendor vendor

FROM scratch AS migrations-output

COPY --link --from=php-with-extensions /app/resources/migrations resources/migrations/

FROM php-with-extensions AS php-with-code

COPY --link annotated-container.xml .
COPY --link resources resources
COPY --link src src
COPY --link --from=composer-prod /app/vendor vendor

RUN --mount=type=cache,target=/root/.composer \
    --mount=source=composer.json,target=composer.json \
    --mount=source=composer.lock,target=composer.lock \
    --mount=from=composer:2,source=/usr/bin/composer,target=/usr/bin/composer \
    composer install --prefer-dist --no-dev

FROM php-with-code AS php-toolbox

RUN pecl install pcov
RUN docker-php-ext-enable pcov

COPY --link phpunit.xml .
COPY --link tests tests
COPY --link tools tools
COPY --link --from=composer-dev /app/vendor vendor

RUN --mount=type=cache,target=/root/.composer \
    --mount=source=composer.json,target=composer.json \
    --mount=source=composer.lock,target=composer.lock \
    --mount=from=composer:2,source=/usr/bin/composer,target=/usr/bin/composer \
    composer install --prefer-dist

RUN --mount=type=cache,target=/root/.composer \
    --mount=source=tools/phpunit/composer.json,target=composer.json \
    --mount=from=composer:2,source=/usr/bin/composer,target=/usr/bin/composer \
    composer install --prefer-dist

FROM php-with-code AS http-server

COPY --link app.php app.php
COPY --link --from=composer-prod /app/vendor vendor

RUN --mount=type=cache,target=/root/.composer \
    --mount=source=composer.json,target=composer.json \
    --mount=source=composer.lock,target=composer.lock \
    --mount=from=composer:2,source=/usr/bin/composer,target=/usr/bin/composer \
    composer install --prefer-dist --no-dev

HEALTHCHECK --interval=10s --start-period=10s --retries=5 --timeout=5s CMD curl --fail http://localhost/health-check

ENTRYPOINT ["php", "/app/app.php"]
