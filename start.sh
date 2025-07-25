#!/bin/bash

# Create SQLite database if it doesn't exist
mkdir -p /tmp
touch /tmp/database.sqlite
chmod 666 /tmp/database.sqlite

# Create storage directories
mkdir -p storage/framework/{cache,sessions,views}
mkdir -p storage/logs
mkdir -p bootstrap/cache

# Set permissions
chmod -R 775 storage bootstrap/cache

# Clear any cached config
php artisan config:clear
php artisan cache:clear

# Run Laravel commands
php artisan config:cache
php artisan migrate --force
php artisan db:seed --force

# Start the server
php artisan serve --host=0.0.0.0 --port=$PORT