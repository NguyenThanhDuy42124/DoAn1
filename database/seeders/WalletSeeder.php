<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Wallet;

class WalletSeeder extends Seeder
{
    public function run()
    {
        // Lấy tất cả user ra
        $users = User::all();

        foreach ($users as $user) {
            // Kiểm tra xem thằng này có ví chưa, chưa thì tạo
            // firstOrCreate sẽ giúp mày chạy đi chạy lại mà không bị tạo trùng
            Wallet::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'balance' => 0,
                    'currency' => 'VND',
                    'status' => 'active'
                ]
            );
        }
    }
}