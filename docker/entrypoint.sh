#!/bin/sh
set -e

# Change to app directory
cd /app

# Check if vendor/autoload.php exists, if not, install dependencies
if [ ! -f "vendor/autoload.php" ]; then
    echo "vendor/autoload.php not found. Installing Composer dependencies..."
    composer install --no-interaction --prefer-dist --optimize-autoloader
fi

# Check if node_modules exists, if not, install npm dependencies
if [ ! -d "node_modules" ]; then
    echo "node_modules not found. Installing npm dependencies..."
    npm install
fi

# Set proper permissions for storage and bootstrap/cache
if [ -d "storage" ]; then
    chmod -R 775 storage bootstrap/cache 2>/dev/null || true
fi

# Execute the main command (octane:frankenphp with arguments)
exec php artisan octane:frankenphp "$@"

