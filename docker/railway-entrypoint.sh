#!/usr/bin/env bash
# Render the nginx config with Railway's dynamic $PORT, start php-fpm in the
# background, then run nginx in the foreground.
set -e

: "${PORT:=80}"

# Only substitute ${PORT}; leave nginx's own $variables intact.
envsubst '${PORT}' < /etc/nginx/nginx.conf.template > /etc/nginx/nginx.conf

# php-fpm listens on 127.0.0.1:9000 (default); run it daemonized.
php-fpm --daemonize

exec nginx
