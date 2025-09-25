<?php

// Create necessary directories
$tmpDirs = ['/tmp/storage', '/tmp/storage/logs', '/tmp/storage/framework', '/tmp/storage/framework/cache', '/tmp/storage/framework/sessions', '/tmp/storage/framework/views'];
foreach ($tmpDirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Create SQLite database if it doesn't exist
$dbPath = '/tmp/database.sqlite';
if (!file_exists($dbPath)) {
    touch($dbPath);
    chmod($dbPath, 0666);
}

// Create users.json file if it doesn't exist
$usersPath = '/tmp/users.json';
if (!file_exists($usersPath)) {
    $defaultUser = [
        [
            'username' => 'admin',
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' // password
        ]
    ];
    file_put_contents($usersPath, json_encode($defaultUser, JSON_PRETTY_PRINT));
}

// Set environment variables for Railway deployment
$_ENV['APP_STORAGE_PATH'] = '/tmp/storage';
$_ENV['USERS_FILE_PATH'] = $usersPath;

// Set Laravel paths for API routing
$_SERVER['SCRIPT_NAME'] = '/api/index.php';
$_SERVER['SCRIPT_FILENAME'] = __FILE__;

// Forward API requests to Laravel
require __DIR__ . '/../public/index.php';