#!/bin/sh
set -e

# Railway injects a different $PORT on every deploy, so we render the
# nginx config from a template at container start instead of baking a
# fixed port in at build time.
export PORT="${PORT:-8080}"

envsubst '${PORT}' < /etc/nginx/conf.d/app.conf.template > /etc/nginx/conf.d/default.conf

echo "Starting nginx on port ${PORT}"

exec supervisord -n -c /etc/supervisor/conf.d/supervisord.conf
