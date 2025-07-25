#!/bin/bash

# Create directories
mkdir -p storage/framework/{cache,sessions,views}
mkdir -p storage/logs
mkdir -p bootstrap/cache

# Set permissions
chmod -R 775 storage bootstrap/cache

# Create SQLite database
touch /tmp/database.sqlite
chmod 666 /tmp/database.sqlite

# Run Laravel setup
php artisan migrate --force
php artisan db:seed --force

echo "Build completed successfully!"