<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Wallet;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Balance;
use Illuminate\Support\Facades\Cache;

class WalletDashboard extends Component
{
    use WithPagination;

    public function render()
    {
        // 1. Ví Admin (Doanh thu sàn)
        $adminBalance = Wallet::where('user_id', Auth::id())->value('balance') ?? 0;

        Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
        try {
        // 2. Gọi API lấy Balance
        // Lưu ý: Việc gọi API tốn thời gian, nên Cache lại khoảng 5-10 phút để trang web load nhanh hơn
        $balance = Cache::remember('stripe_balance', 300, function () {
            return Balance::retrieve();
        });
        $testballace = $balance->available[0]->amount / 100; // Chuyển từ cent sang đơn vị chính
        } catch (\Exception $e) {
            // Xử lý lỗi khi gọi API Stripe
            $testballace = 0;
        }

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
            'transactions' => $transactions,
            'testballace' => $testballace,
        ])->layout('layouts.AdminDashBoard');
    }

}
