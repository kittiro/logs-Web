# Use PHP-FPM base image
FROM php:8.4-fpm

# Install system dependencies
RUN apt update && apt install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    gnupg \
    locales \
    nginx

# Install PHP extensions
RUN docker-php-ext-install zip pdo_mysql

# Copy Composer from official image  
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application files
WORKDIR /var/www

COPY . .

# Install dependencies  
RUN composer install --no-dev --optimize-autoloader 

# Set permissions
RUN chown -R www-data:www-data /var/www && chmod -R 755 /var/www/storage

# Copy Nginx configuration
COPY nginx.conf /etc/nginx/nginx.conf

# Expose ports for Nginx
EXPOSE 80

# Start Nginx and PHP-FPM
CMD ["sh", "-c", "php-fpm & nginx -g 'daemon off;'"]