FROM php:7.4-fpm-alpine

ADD ./php-fpm/www.conf /usr/local/etc/php-fpm.d/www.conf

RUN addgroup -g 1000 laravel && adduser -G laravel -g laravel -s /bin/sh -D laravel

RUN mkdir -p /var/www/backend

RUN chown laravel:laravel /var/www/backend

WORKDIR /var/www/backend

RUN set -ex \
    && apk --no-cache add \
    postgresql-dev

RUN apk add --no-cache --repository http://dl-3.alpinelinux.org/alpine/edge/testing gnu-libiconv
ENV LD_PRELOAD /usr/lib/preloadable_libiconv.so php

# RUN apk add --update sendmail
# RUN apk add --update zlib1g-dev 

# RUN docker-php-ext-install mbstring

# RUN docker-php-ext-install zip

RUN apk update \
    && apk upgrade \
    && apk add --no-cache \
        freetype \
        libpng \
        libjpeg-turbo \
        freetype-dev \
        libpng-dev \
        jpeg-dev \
        libjpeg \
        libjpeg-turbo-dev

# RUN docker-php-ext-configure gd \
#         --with-freetype-dir=/usr/lib/ \
#         --with-png-dir=/usr/lib/ \
#         --with-jpeg-dir=/usr/lib/ \
#         --with-gd

# RUN NUMPROC=$(grep -c ^processor /proc/cpuinfo 2>/dev/null || 1) \
#     && docker-php-ext-install -j${NUMPROC} gd

RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install -j$(nproc) gd

RUN docker-php-ext-install pdo pdo_pgsql
