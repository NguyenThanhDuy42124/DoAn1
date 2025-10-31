<?php

namespace App\Providers;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Log;
use App\Models\Category;
use App\Models\Brand;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ResetPassword::toMailUsing(function ($notifiable, string $token) {
            $url = route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ]);
            return (new MailMessage)
                ->from(config('mail.from.address'), config('mail.from.name')) // có thể đổi FROM tại đây
                ->subject('Khôi phục mật khẩu - TenShop')
                ->greeting('Xin chào '.($notifiable->username ?? 'bạn').'!')
                ->line('Chúng tôi nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn.')
                ->action('Xác nhận', $url)
                ->line('Liên kết có hiệu lực trong '.config('auth.passwords.users.expire').' phút.')
                ->line('Nếu không phải bạn yêu cầu, bạn có thể bỏ qua email này.')
                ->salutation('Trân trọng, TenShop Team.');
        });
        View::composer('*', function ($view) {
        $totalItems = 0;
        if(Auth::check()) {
            $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);
            $totalItems = $cart->items()->sum('quantity');
        }
        $view->with('totalItems', $totalItems);
    });
        try {
        View::composer('layouts.navbar', function ($view) {
            // Lấy các danh mục cha (Điện thoại, Laptop...)
            $navbar_categories = Category::orderBy('name')
                                        ->get(['id', 'name']);

            // Lấy TẤT CẢ thương hiệu
            $navbar_brands = Brand::orderBy('name')->get(['id', 'name']);

            $view->with('navbar_categories', $navbar_categories);
            $view->with('navbar_brands', $navbar_brands);
        });
    } catch (\Exception $e) {
        // Xử lý lỗi nếu database chưa sẵn sàng (ví dụ khi chạy migrate)
        Log::error("Không thể tải dữ liệu cho View Composer: " . $e->getMessage());
    }
    }
}
