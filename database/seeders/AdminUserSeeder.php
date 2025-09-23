<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Kiểm tra admin đã tồn tại chưa, nếu chưa thì tạo
        if (!User::where('email', 'admin@example.com')->exists()) {
            User::create([
                'name' => 'Admin',
                'email' => 'admin@admin.com',
                'password' => Hash::make('Pa$$w0rd123!'), // đổi mật khẩu theo ý mày
                'role' => 'admin',
            ]);
        }
    }
}
