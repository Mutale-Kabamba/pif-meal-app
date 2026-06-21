#!/bin/bash
set -e

echo "=== Nutrition Monitoring System - Startup ==="

# Ensure database file exists
if [ ! -f /var/www/html/database/database.sqlite ]; then
    echo "Creating SQLite database..."
    touch /var/www/html/database/database.sqlite
fi

# Ensure storage symlink exists
if [ ! -L /var/www/html/public/storage ]; then
    echo "Creating storage symlink..."
    ln -sf /var/www/html/storage/app/public /var/www/html/public/storage
fi

# Set permissions
chown -R www-data:www-data /var/www/html/database
chmod 775 /var/www/html/database/database.sqlite
chown -R www-data:www-data /var/www/html/storage
chmod -R 775 /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache

# Generate app key if not set
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "base64:your-key-here-change-in-production=" ]; then
    echo "Generating application key..."
    php artisan key:generate --force
fi

# Run migrations
echo "Running database migrations..."
php artisan migrate --force

# Seed database if no users exist
echo "Checking if seeding is needed..."
USER_COUNT=$(php -r "
require 'vendor/autoload.php';
\$app = require_once 'bootstrap/app.php';
\$kernel = \$app->make(Illuminate\Contracts\Console\Kernel::class);
\$kernel->bootstrap();
try {
    echo App\Models\User::count();
} catch (Exception \$e) {
    echo '0';
}
" 2>/dev/null || echo "0")

if [ "$USER_COUNT" = "0" ]; then
    echo "Seeding database with default data..."
    php artisan db:seed --force
else
    echo "Database already seeded ($USER_COUNT users found)."
fi

# Clear and cache config
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo "=== NMS is ready ==="
echo "Access the application at: ${APP_URL:-http://localhost:8000}"
echo ""
echo "Default accounts:"
echo "  alice@nms.local / password (Head of Programmes)"
echo "  bob@nms.local / password (System Manager)"
echo "  cook.a@nms.local / password (Cook)"

# Execute the main command
exec "$@"
