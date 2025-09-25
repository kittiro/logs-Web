#!/bin/bash

set -e  # Exit on any error

echo "🎨 Starting Render deployment build process..."

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
mkdir -p storage/logs || handle_error "Failed to create logs directory"
mkdir -p storage/framework/cache || handle_error "Failed to create cache directory"
mkdir -p storage/framework/sessions || handle_error "Failed to create sessions directory"
mkdir -p storage/framework/views || handle_error "Failed to create views directory"
mkdir -p storage/app || handle_error "Failed to create app storage directory"
mkdir -p bootstrap/cache || handle_error "Failed to create bootstrap cache directory"

# Create database directory
log "📁 Creating database directory..."
mkdir -p database || handle_error "Failed to create database directory"

# Set permissions for storage directories
log "🔐 Setting storage permissions..."
chmod -R 775 storage || log "⚠️  Warning: Could not set storage permissions"
chmod -R 775 bootstrap/cache || log "⚠️  Warning: Could not set bootstrap cache permissions"

# Create SQLite database
log "🗄️  Creating SQLite database..."
touch database/database.sqlite || handle_error "Failed to create database file"
chmod 664 database/database.sqlite || log "⚠️  Warning: Could not set database permissions"

# Create users file for authentication
log "👤 Creating default users file..."
cat > storage/app/users.json << 'EOF' || handle_error "Failed to create users file"
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

# Set proper permissions for users file
chmod 664 storage/app/users.json || log "⚠️  Warning: Could not set users file permissions"

# Create .env file for Render
log "📝 Creating .env file for Render..."
cat > .env << 'EOF'
APP_NAME="WebManga Demo"
APP_ENV=production
APP_DEBUG=false
APP_TIMEZONE=Asia/Bangkok
DB_CONNECTION=sqlite
DB_DATABASE=/opt/render/project/src/database/database.sqlite
SESSION_DRIVER=cookie
SESSION_LIFETIME=120
LOG_CHANNEL=stack
LOG_LEVEL=info
APP_STORAGE_PATH=/opt/render/project/src/storage
USERS_FILE_PATH=/opt/render/project/src/storage/app/users.json
EOF

# Validate critical files exist
log "✅ Validating build artifacts..."
if [ ! -f "database/database.sqlite" ]; then
    handle_error "Database file not created"
fi

if [ ! -f "storage/app/users.json" ]; then
    handle_error "Users file not created"
fi

if [ ! -d "storage" ]; then
    handle_error "Storage directory not created"
fi

log "✅ Build completed successfully!"
log "📊 Build summary:"
log "   - Storage directories: ✅ Created"
log "   - Database file: ✅ Created"
log "   - Users file: ✅ Created"
log "   - Permissions: ✅ Set"
log "   - Environment: ✅ Configured"

echo "🎨 Render deployment build process completed successfully!"