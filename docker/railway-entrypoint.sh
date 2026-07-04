#!/usr/bin/env bash
# Render the nginx config with Railway's dynamic $PORT, start php-fpm in the
# background, then run nginx in the foreground.
set -e

: "${PORT:=80}"

# Safety net: if a freshly attached uploads volume mounts owned by root,
# php-fpm workers (www-data) can't write and media uploads fail with
# "could not be moved to wp-content/uploads/...". Repair ownership only when
# www-data actually can't write — avoids a costly recursive chown on every
# start when the volume already has correct permissions (the normal case).
UPLOADS_DIR="/var/www/html/wp-content/uploads"
mkdir -p "$UPLOADS_DIR"
if ! su -s /bin/sh www-data -c "test -w '$UPLOADS_DIR'" 2>/dev/null; then
    echo "[entrypoint] uploads not writable by www-data — repairing ownership"
    chown -R www-data:www-data "$UPLOADS_DIR" 2>/dev/null || true
fi

# Only substitute ${PORT}; leave nginx's own $variables intact.
envsubst '${PORT}' < /etc/nginx/nginx.conf.template > /etc/nginx/nginx.conf

# php-fpm listens on 127.0.0.1:9000 (default); run it daemonized.
php-fpm --daemonize

exec nginx
