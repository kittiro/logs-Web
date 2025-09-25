#!/bin/bash

# Create necessary directories
mkdir -p /tmp/storage/logs
mkdir -p /tmp/storage/framework/cache
mkdir -p /tmp/storage/framework/sessions  
mkdir -p /tmp/storage/framework/views
mkdir -p /tmp/storage/app

# Set permissions
chmod -R 755 /tmp/storage

# Create SQLite database
touch /tmp/database.sqlite
chmod 666 /tmp/database.sqlite

# Create users file
cat > /tmp/users.json << 'EOF'
[
    {
        "username": "admin",
        "name": "Admin User", 
        "email": "admin@example.com",
        "password": "$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi"
    }
]
EOF

echo "Build completed successfully"