#!/bin/bash

set -e  # Exit on any error

echo "🚀 Starting Railway deployment build process..."

# Function to log with timestamp
log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1"
}

# Function to handle errors
handle_error() {
    log "❌ Error occurred in build process: $1"
    exit 1
}

# Create necessary storage directories
log "📁 Creating storage directories..."
mkdir -p /tmp/storage/logs || handle_error "Failed to create logs directory"
mkdir -p /tmp/storage/framework/cache || handle_error "Failed to create cache directory"
mkdir -p /tmp/storage/framework/sessions || handle_error "Failed to create sessions directory"
mkdir -p /tmp/storage/framework/views || handle_error "Failed to create views directory"
mkdir -p /tmp/storage/app || handle_error "Failed to create app storage directory"

# Create local storage directories as fallback
log "📁 Creating local storage directories..."
mkdir -p storage/logs
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/app
mkdir -p bootstrap/cache

# Set permissions for temporary storage
log "🔐 Setting storage permissions..."
chmod -R 755 /tmp/storage || log "⚠️  Warning: Could not set /tmp/storage permissions"
chmod -R 755 storage || log "⚠️  Warning: Could not set local storage permissions"
chmod -R 755 bootstrap/cache || log "⚠️  Warning: Could not set bootstrap cache permissions"

# Create SQLite database
log "🗄️  Creating SQLite database..."
touch /tmp/database.sqlite || handle_error "Failed to create database file"
chmod 666 /tmp/database.sqlite || log "⚠️  Warning: Could not set database permissions"

# Create local database as fallback
mkdir -p database
touch database/database.sqlite || log "⚠️  Warning: Could not create local database"
chmod 666 database/database.sqlite 2>/dev/null || true

# Create users file for authentication
log "👤 Creating default users file..."
cat > /tmp/users.json << 'EOF' || handle_error "Failed to create users file"
[
    {
        "username": "admin",
        "name": "Admin User", 
        "email": "admin@example.com",
        "password": "$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi"
    },
    {
        "username": "demo",
        "name": "Demo User",
        "email": "demo@example.com", 
        "password": "$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi"
    }
]
EOF

# Create local users file as fallback
mkdir -p storage/app
cp /tmp/users.json storage/app/users.json 2>/dev/null || log "⚠️  Warning: Could not create local users file"

# Set environment variables for Railway
log "🌍 Setting environment variables..."
export APP_STORAGE_PATH="/tmp/storage"
export USERS_FILE_PATH="/tmp/users.json"
export DB_DATABASE="/tmp/database.sqlite"

# Validate critical files exist
log "✅ Validating build artifacts..."
if [ ! -f "/tmp/database.sqlite" ]; then
    handle_error "Database file not created"
fi

if [ ! -f "/tmp/users.json" ]; then
    handle_error "Users file not created"
fi

if [ ! -d "/tmp/storage" ]; then
    handle_error "Storage directory not created"
fi

# Create .env file if it doesn't exist
if [ ! -f ".env" ]; then
    log "📝 Creating .env file..."
    cat > .env << 'EOF'
APP_NAME="WebManga Demo"
APP_ENV=production
APP_DEBUG=false
APP_TIMEZONE=Asia/Bangkok
DB_CONNECTION=sqlite
DB_DATABASE=/tmp/database.sqlite
SESSION_DRIVER=cookie
SESSION_LIFETIME=120
LOG_CHANNEL=stack
LOG_LEVEL=info
EOF
fi

log "✅ Build completed successfully!"
log "📊 Build summary:"
log "   - Storage directories: ✅ Created"
log "   - Database file: ✅ Created"
log "   - Users file: ✅ Created"
log "   - Permissions: ✅ Set"
log "   - Environment: ✅ Configured"

echo "🎉 Railway deployment build process completed successfully!"