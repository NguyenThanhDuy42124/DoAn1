<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\Notification; // Model này đã có sẵn
use Illuminate\Support\Facades\Log;

class ProductObserver
{
    /**
     * Xử lý sự kiện "created" của Product.
     */
    public function created(Product $product)
    {
        // Sử dụng hằng số chính xác từ Product.php
        if ($product->status === Product::STATUS_APPROVED) {
            $this->notifyFollowers($product);
        }
    }

    /**
     * Xử lý sự kiện "updated" của Product.
     */
    public function updated(Product $product)
    {
        // Kiểm tra nếu 'status' bị thay đổi VÀ giá trị mới là 'Approved'
        if ($product->wasChanged('status') && $product->status === Product::STATUS_APPROVED) {
            $this->notifyFollowers($product);
        }
    }

    /**
     * Hàm riêng để gửi thông báo.
     */
    private function notifyFollowers(Product $product)
    {
        // Sử dụng quan hệ 'seller' chính xác từ Product.php
        $seller = $product->seller; 

        if (!$seller) {
            Log::warning("ProductObserver: Không tìm thấy seller cho product ID: {$product->id}");
            return;
        }

        // Sử dụng quan hệ 'followers' chúng ta đã thêm vào User.php (Bước 2)
        $followers = $seller->followers;

        if ($followers->isEmpty()) {
            return; // Không có ai theo dõi
        }

        $message = "Người bán '{$seller->name}' bạn theo dõi vừa đăng sản phẩm mới: {$product->name}";
        $now = now();
        $notificationsData = [];

        foreach ($followers as $follower) {
            // Không gửi thông báo cho chính người bán
            if ($follower->id === $seller->id) {
                continue;
            }

            $notificationsData[] = [
                'user_id'   => $follower->id,
                'type'      => 'new_product',
                'message'   => $message,
                'data'      => json_encode([
                    'product_id' => $product->id,
                    'seller_id'  => $seller->id
                ]),
                'is_read'    => false,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (!empty($notificationsData)) {
            Notification::insert($notificationsData);
        }
    }
}