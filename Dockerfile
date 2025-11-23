ARG WORDPRESS_VERSION=latest
ARG PHP_VERSION=8.3
ARG USER=www-data

FROM dunglas/frankenphp:latest-builder-php${PHP_VERSION} as builder

# Copia o xcaddy da imagem builder do Caddy
COPY --from=caddy:builder /usr/bin/xcaddy /usr/bin/xcaddy

# CGO deve estar habilitado para compilar o FrankenPHP
ENV CGO_ENABLED=1 XCADDY_SETCAP=1 XCADDY_GO_BUILD_FLAGS='-ldflags="-w -s" -trimpath'

COPY ./sidekick/middleware/cache ./cache

RUN xcaddy build \
    --output /usr/local/bin/frankenphp \
    --with github.com/dunglas/frankenphp=./ \
    --with github.com/dunglas/frankenphp/caddy=./caddy/ \
    --with github.com/dunglas/caddy-cbrotli \
    --with github.com/stephenmiracle/frankenwp/sidekick/middleware/cache=./cache

FROM wordpress:$WORDPRESS_VERSION as wp
FROM dunglas/frankenphp:latest-php${PHP_VERSION} AS base

LABEL org.opencontainers.image.title=FrankenWP
LABEL org.opencontainers.image.description="Optimized WordPress containers to run everywhere. Built with FrankenPHP & Caddy."
LABEL org.opencontainers.image.url=https://wpeverywhere.com
LABEL org.opencontainers.image.source=https://github.com/StephenMiracle/frankenwp
LABEL org.opencontainers.image.licenses=MIT
LABEL org.opencontainers.image.vendor="Stephen Miracle"

# Substitui o binário oficial pelo nosso build customizado
COPY --from=builder /usr/local/bin/frankenphp /usr/local/bin/frankenphp
ENV WP_DEBUG=${DEBUG:+1}
ENV FORCE_HTTPS=0
ENV PHP_INI_SCAN_DIR=$PHP_INI_DIR/conf.d

RUN apt-get update && apt-get install -y --no-install-recommends \
    ca-certificates \
    ghostscript \
    curl \
    wget \
    libonig-dev \
    libxml2-dev \
    libcurl4-openssl-dev \
    libssl-dev \
    libzip-dev \
    libnss3-tools \
    unzip \
    git \
    libjpeg-dev \
    libwebp-dev \
    libzip-dev \
    libmemcached-dev \
    zlib1g-dev \
    netcat-openbsd \
    sqlite3

# Instala as extensões PHP necessárias
RUN install-php-extensions \
    bcmath \
    exif \
    gd \
    intl \
    mysqli \
    zip \
    imagick/imagick@master \
    opcache

RUN cp $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php.ini
COPY php.ini $PHP_INI_DIR/conf.d/wp.ini

COPY --from=wp /usr/src/wordpress/ /var/www/html/
COPY --from=wp /usr/local/etc/php/conf.d /usr/local/etc/php/conf.d/
COPY --from=wp /usr/local/bin/docker-entrypoint.sh /usr/local/bin/

# Configurações recomendadas do PHP.ini
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

WORKDIR /var/www/html

VOLUME /var/www/html/wp-content

COPY data/wp-content/mu-plugins /var/www/html/wp-content/mu-plugins
RUN mkdir /var/www/html/wp-content/cache

# Configuração de fuso horário
ENV TZ=America/Sao_Paulo
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# Instala WP-CLI
RUN curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar \
    && chmod +x wp-cli.phar \
    && mv wp-cli.phar /usr/local/bin/wp

# Instala PHPStan
RUN curl -sSLO https://github.com/phpstan/phpstan/releases/latest/download/phpstan.phar \
    && chmod a+x phpstan.phar \
    && mv phpstan.phar /usr/local/bin/phpstan

# Configuração do Git pre-commit
RUN apt-get update && apt-get install -y git \
    && mkdir -p /app/.git/hooks \
    && echo '#!/bin/sh\nphpstan analyse --configuration=/app/public/phpstan.neon' > /app/.git/hooks/pre-commit \
    && chmod +x /app/.git/hooks/pre-commit

# Copia os arquivos do projeto
COPY data/ /var/www/html/

# Adiciona o entrypoint wrapper customizado
COPY docker-entrypoint-wrapper.sh /usr/local/bin/docker-entrypoint-wrapper.sh
RUN chmod +x /usr/local/bin/docker-entrypoint-wrapper.sh

COPY Caddyfile /etc/caddy/Caddyfile

# Configura as capacidades necessárias para o Caddy
RUN useradd -D ${USER} && \
    setcap CAP_NET_BIND_SERVICE=+eip /usr/local/bin/frankenphp

RUN chown -R ${USER}:${USER} /data/caddy && \
    chown -R ${USER}:${USER} /config/caddy && \
    chown -R ${USER}:${USER} /var/www/html && \
    chown -R ${USER}:${USER} /usr/local/bin/docker-entrypoint.sh && \
    chown -R ${USER}:${USER} /usr/local/bin/docker-entrypoint-wrapper.sh

USER $USER

ENTRYPOINT ["/usr/local/bin/docker-entrypoint-wrapper.sh"]
CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]
