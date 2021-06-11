FROM nginx:stable-alpine

COPY nginx/nginx.conf /etc/nginx/nginx.conf
COPY nginx/lk.conf /etc/nginx/conf.d/lk.conf

RUN mkdir -p /var/www/backend

RUN addgroup -g 1000 laravel && adduser -G laravel -g laravel -s /bin/sh -D laravel

RUN chown laravel:laravel /var/www/backend