<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Wallet;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class WalletDashboard extends Component
{
    use WithPagination;

    public function render()
    {
        // 1. Ví Admin (Doanh thu sàn)
        $adminBalance = Wallet::where('user_id', Auth::id())->value('balance') ?? 0;
        
        // 2. Ví System (Tiền treo - Escrow) - ID 12
        $systemBalance = Wallet::where('user_id', 12)->value('balance') ?? 0;

        // 3. Tổng ví Seller (Nợ phải trả)
        $sellerBalance = Wallet::whereNotIn('user_id', [Auth::id(), 12])->sum('balance');

        $transactions = Transaction::with(['wallet.user'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.admin.wallet-dashboard', [
            'adminBalance' => $adminBalance,
            'systemHoldingBalance' => $systemBalance,
            'totalSellerBalance' => $sellerBalance,
            'transactions' => $transactions
        ])->layout('layouts.AdminDashBoard');
    }
}