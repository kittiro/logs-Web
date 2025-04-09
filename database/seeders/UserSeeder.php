<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        // ค้นหาผู้ใช้ที่มีอยู่และอัปเดตรหัสผ่าน
        $user = User::where('username', 'admin')->first();

        if ($user) {
            // อัปเดตรหัสผ่านใหม่
            $user->password = Hash::make('admin');
            $user->save();
        } else {
            // หากไม่มีผู้ใช้ ให้สร้างใหม่
            User::create([
                'username' => 'admin',
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('admin'),
            ]);
        }
    }
}

