<?php

namespace App\Services;

use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use App\Models\FileUser;
use Illuminate\Support\Facades\Hash;

class FileUserProvider implements UserProvider
{
    protected $usersFile;

    public function __construct()
    {
        $this->usersFile = storage_path('app/users.json');
        $this->ensureUsersFileExists();
    }

    protected function ensureUsersFileExists()
    {
        if (!file_exists($this->usersFile)) {
            // Create the storage/app directory if it doesn't exist
            if (!file_exists(dirname($this->usersFile))) {
                mkdir(dirname($this->usersFile), 0755, true);
            }

            // Create default admin user
            $defaultUser = [
                'username' => 'admin',
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('admin'),
            ];

            file_put_contents($this->usersFile, json_encode([$defaultUser], JSON_PRETTY_PRINT));
        }
    }

    public function retrieveById($identifier)
    {
        $users = $this->getUsers();
        foreach ($users as $userData) {
            if ($userData['username'] === $identifier) {
                return new FileUser($userData);
            }
        }
        return null;
    }

    public function retrieveByToken($identifier, $token)
    {
        return null;
    }

    public function updateRememberToken(Authenticatable $user, $token)
    {
        // Not implemented
    }

    public function retrieveByCredentials(array $credentials)
    {
        if (!isset($credentials['username'])) {
            return null;
        }

        $users = $this->getUsers();
        foreach ($users as $userData) {
            if ($userData['username'] === $credentials['username']) {
                return new FileUser($userData);
            }
        }
        return null;
    }

    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        return isset($credentials['password']) && 
               Hash::check($credentials['password'], $user->getAuthPassword());
    }

    public function rehashPasswordIfRequired(Authenticatable $user, array $credentials, bool $force = false): void
    {
        // Not implemented as we're using a file-based system
    }

    protected function getUsers()
    {
        return json_decode(file_get_contents($this->usersFile), true) ?? [];
    }
}
