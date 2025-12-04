<?php

namespace App\Livewire\Admin;

use Stripe\Stripe;
use Stripe\Balance;
use App\Models\Order;
use App\Models\Wallet;
use Livewire\Component;
use Stripe\ExchangeRate;
use App\Models\Transaction;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class WalletDashboard extends Component
{
    use WithPagination;
    public static function getUsdToVndRate()
    {
        return Cache::remember('stripe_exchange_rate_usd_vnd', 60 * 60 * 12, function () { // Cache 12 tiếng
            Stripe::setApiKey(env('STRIPE_SECRET'));

            try {
                // Lấy bảng tỷ giá của đồng USD
                $exchangeRates = \Stripe\ExchangeRate::all(['currency' => 'usd']);
                $exchangeRate = $exchangeRates->data[0] ?? null;

                // Trả về tỷ giá VND (ví dụ: 25450.50)
                return $exchangeRate->rates['vnd'] ?? 25000; // Fallback nếu lỗi
            } catch (\Exception $e) {
                return 25000; // Tỷ giá mặc định nếu gọi API lỗi
            }
        });
    }

    public function render()
    {
        $exchangeRate = self::getUsdToVndRate();

        Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
        try {
            // 2. Gọi API lấy Balance
            // Lưu ý: Việc gọi API tốn thời gian, nên Cache lại khoảng 5-10 phút để trang web load nhanh hơn
            $balance = Cache::remember('stripe_balance', 300, function () {
                return Balance::retrieve();
            });

            $Stripebalance = $balance->available[0]->amount / 100 * $exchangeRate; // Chuyển từ cent sang đơn vị chính
        } catch (\Exception $e) {
            // Xử lý lỗi khi gọi API Stripe
            $Stripebalance = "Không thể lấy số dư từ Stripe";
        }
        $StripeIncoming = $balance->pending[0]->amount / 100 * $exchangeRate; // Chuyển từ cent sang đơn vị chính




        $orders = Order::with(['buyer:id,name,email', 'seller:id,name,email'])
            ->select('id', 'user_id', 'seller_id', 'total_price', 'transaction_id', 'transaction_fee', 'pay_to_seller', 'created_at','status','status')
            ->where('pay_to_seller', 0)
            ->where('status', '!=', 'cancelled')
            ->where('payment_status', 'paid')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $totalLoanSeller = Order::where('pay_to_seller', 0)
            ->where('status', '!=', 'cancelled')
            ->where('payment_status', 'paid')
            ->sum('total_price');

        $fees = Order::where('pay_to_seller', 0)
            ->where('status', '!=', 'cancelled')
            ->where('payment_status', 'paid')
            ->sum('transaction_fee');
        $totalLoanSeller = $totalLoanSeller - $fees;

        $realIncome = $Stripebalance - $totalLoanSeller;





        return view('livewire.admin.wallet-dashboard', [
            'StripeIncoming' => $StripeIncoming,
            'orders' => $orders,
            'Stripebalance' => $Stripebalance,
            'exchangeRate' => $exchangeRate,
            'totalLoanSeller' => $totalLoanSeller,
            'realIncome' => $realIncome,
        ])->layout('layouts.AdminDashBoard');
    }
}
