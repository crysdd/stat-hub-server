FROM php:8.4-fpm

ENV TZ=UTC
ENV DEBIAN_FRONTEND="noninteractive"
ENV COMPOSER_ALLOW_SUPERUSER 1
ENV ENV=/root/.bashrc

# Install system dependencies and build libraries
RUN apt-get update && apt-get install -y --no-install-recommends \
    curl \
    unzip \
    libpq-dev \
    libonig-dev \
    libssl-dev \
    libxml2-dev \
    libcurl4-openssl-dev \
    libicu-dev \
    libzip-dev \
    && docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    pdo_pgsql \
    pgsql \
    intl \
    zip \
    bcmath \
    soap \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && apt-get autoremove -y && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

COPY .bashrc /root/

COPY --from=composer /usr/bin/composer /usr/local/bin/composer

COPY php.ini /usr/local/etc/php/
COPY ini/ /usr/local/etc/php/conf.d/

COPY *.conf /usr/local/etc/php-fpm.d/

ARG USER_UID
ARG USER_GID
ARG ENTRYPOINT
ARG CONTAINER_USER=root

ENV USER_UID=$USER_UID
ENV USER_GID=$USER_GID

ENV ENTRYPOINT=$ENTRYPOINT

RUN echo "USER_UID: $USER_UID | USER_GID: $USER_GID | CONTAINER_USER: $CONTAINER_USER | ENTRYPOINT: $ENTRYPOINT"

COPY --chmod=755 $ENTRYPOINT /usr/local/bin/

RUN addgroup --gid $USER_GID www
RUN adduser --uid $USER_UID --ingroup www --disabled-password www
COPY .bashrc /home/www/

USER $CONTAINER_USER

WORKDIR /app

ENTRYPOINT $ENTRYPOINT

EXPOSE 9000
