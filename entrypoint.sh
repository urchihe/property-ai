#!/bin/bash
set -e

# Run migrations (optional)
php artisan migrate --force

# Clear caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
#php artisan redis:listen-property

# Start Supervisor
exec /usr/bin/supervisord -c /etc/supervisord.conf
