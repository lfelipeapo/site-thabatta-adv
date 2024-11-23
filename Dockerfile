ARG PHP_VERSION=8.3
ARG WORDPRESS_VERSION=latest
ARG USER=www-data

# Build Stage
FROM dunglas/frankenphp:latest-builder-php${PHP_VERSION} as builder

COPY --from=caddy:builder /usr/bin/xcaddy /usr/bin/xcaddy

ENV CGO_ENABLED=1 XCADDY_SETCAP=1 XCADDY_GO_BUILD_FLAGS='-ldflags="-w -s" -trimpath'

COPY ./sidekick/middleware/cache ./cache

RUN xcaddy build \
    --output /usr/local/bin/frankenphp \
    --with github.com/dunglas/frankenphp=./ \
    --with github.com/dunglas/frankenphp/caddy=./caddy/ \
    --with github.com/dunglas/caddy-cbrotli \
    --with github.com/stephenmiracle/frankenwp/sidekick/middleware/cache=./cache

# WordPress Stage
FROM wordpress:${WORDPRESS_VERSION} as wp

# Final Stage
FROM dunglas/frankenphp:latest-php${PHP_VERSION} AS base

LABEL org.opencontainers.image.title=FrankenWP
LABEL org.opencontainers.image.description="Optimized WordPress containers to run everywhere. Built with FrankenPHP & Caddy."
LABEL org.opencontainers.image.url=https://wpeverywhere.com
LABEL org.opencontainers.image.source=https://github.com/StephenMiracle/frankenwp
LABEL org.opencontainers.image.licenses=MIT
LABEL org.opencontainers.image.vendor="Stephen Miracle"

# Environment Setup
ENV WP_DEBUG=${DEBUG:+1}
ENV FORCE_HTTPS=0
ENV PHP_INI_SCAN_DIR=$PHP_INI_DIR/conf.d

# Copy FrankenPHP and WordPress files
COPY --from=builder /usr/local/bin/frankenphp /usr/local/bin/frankenphp
COPY --from=wp /usr/src/wordpress /usr/src/wordpress
COPY --from=wp /usr/local/bin/docker-entrypoint.sh /usr/local/bin/
COPY --from=wp /usr/local/etc/php/conf.d /usr/local/etc/php/conf.d/

# Install dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    ca-certificates \
    ghostscript \
    curl \
    libonig-dev \
    libxml2-dev \
    libcurl4-openssl-dev \
    libssl-dev \
    libzip-dev \
    unzip \
    git \
    libjpeg-dev \
    libwebp-dev \
    libzip-dev \
    libmemcached-dev \
    zlib1g-dev \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN install-php-extensions \
    bcmath \
    exif \
    gd \
    intl \
    mysqli \
    zip \
    imagick/imagick@master \
    opcache

# PHP Configuration
COPY php.ini $PHP_INI_DIR/conf.d/wp.ini
RUN cp $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php.ini

# Configure PHP and WordPress
RUN set -eux; \
    { \
    echo 'opcache.memory_consumption=128'; \
    echo 'opcache.interned_strings_buffer=8'; \
    echo 'opcache.max_accelerated_files=4000'; \
    echo 'opcache.revalidate_freq=2'; \
    } > $PHP_INI_DIR/conf.d/opcache-recommended.ini

RUN { \
    echo 'error_reporting = E_ERROR | E_WARNING | E_PARSE | E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_COMPILE_WARNING | E_RECOVERABLE_ERROR'; \
    echo 'display_errors = Off'; \
    echo 'display_startup_errors = Off'; \
    echo 'log_errors = On'; \
    echo 'error_log = /dev/stderr'; \
    echo 'log_errors_max_len = 1024'; \
    echo 'ignore_repeated_errors = On'; \
    echo 'ignore_repeated_source = Off'; \
    echo 'html_errors = Off'; \
    } > $PHP_INI_DIR/conf.d/error-logging.ini

# WordPress Directory Setup
WORKDIR /var/www/html

RUN mkdir -p /var/www/html/wp-content/plugins && \
    mkdir -p /var/www/html/wp-content/themes && \
    mkdir -p /var/www/html/wp-content/uploads && \
    mkdir -p /var/www/html/wp-content/cache

# Configure WordPress and FrankenPHP
RUN sed -i 's/<?php/<?php if (!!getenv("FORCE_HTTPS")) { \$_SERVER["HTTPS"] = "on"; } define( "FS_METHOD", "direct" ); set_time_limit(300); /g' /usr/src/wordpress/wp-config-docker.php

RUN sed -i \
    -e 's/\[ "$1" = '\''php-fpm'\'' \]/\[\[ "$1" == frankenphp* \]\]/g' \
    -e 's/php-fpm/frankenphp/g' \
    /usr/local/bin/docker-entrypoint.sh

# Setup Caddy
COPY Caddyfile /etc/caddy/Caddyfile
RUN sed -i -e 's#root \* public/#root \* /var/www/html/#g' /etc/caddy/Caddyfile

# WordPress CLI
RUN curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar && \
    chmod +x wp-cli.phar && \
    mv wp-cli.phar /usr/local/bin/wp

# Permissions
RUN useradd -D ${USER} && \
    setcap CAP_NET_BIND_SERVICE=+eip /usr/local/bin/frankenphp

RUN chown -R ${USER}:${USER} /var/www/html && \
    chmod -R 755 /var/www/html && \
    chmod -R 775 /var/www/html/wp-content && \
    chown -R ${USER}:${USER} /data/caddy && \
    chown -R ${USER}:${USER} /config/caddy && \
    chown -R ${USER}:${USER} /usr/src/wordpress && \
    chown -R ${USER}:${USER} /usr/local/bin/docker-entrypoint.sh

# Volume Configuration
VOLUME /var/www/html

USER ${USER}

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]
