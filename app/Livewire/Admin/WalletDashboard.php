<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Wallet;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class WalletDashboard extends Component
{
    use WithPagination; // Để phân trang mượt mà

    public function render()
    {
        // 1. Lấy ví của Admin hiện tại (Hoặc fix cứng ID admin chính chủ)
        // Giả sử mày đang login bằng Admin
        $adminWallet = Wallet::where('user_id', Auth::id())->first();
        
        // 2. Tính tổng tiền đang nằm trong ví các Seller (Đây là tiền sàn NỢ seller)
        // Lấy tất cả ví trừ ví Admin ra
        $totalSellerBalance = Wallet::where('user_id', '!=', Auth::id())->sum('balance');

        // 3. Lấy lịch sử giao dịch (Mới nhất lên đầu)
        $transactions = Transaction::with(['wallet.user']) // Eager load để lấy tên user cho nhanh
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.admin.wallet-dashboard', [
            'adminBalance' => $adminWallet ? $adminWallet->balance : 0,
            'totalSellerBalance' => $totalSellerBalance,
            'transactions' => $transactions
        ])->layout('layouts.AdminDashBoard');
    }
}