<?php

namespace App\Models;

use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Model;


class Order extends Model
{
    protected $fillable = ['user_id', 'seller_id', 'product_id', 'status', 'subtotal', 'discount_amount', 'voucher_id', 'payment_status', 'tracking_code', 'cancellation_reason', 'total_price', 'session_id','transaction_id','transaction_fee', 'buyer_name', 'buyer_email', 'buyer_phone', 'shipping_address'];
    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }
    // Hàm này tạo ra thuộc tính ảo tên là: status_vn
public function getStatusVnAttribute()
{
    $map = [
        'pending'   => 'Chờ xác nhận',
        'shipping'  => 'Đang giao hàng',
        'delivered' => 'Đã giao',
        'completed' => 'Hoàn thành',
        'cancelled' => 'Đã hủy',
    ];

    // Trả về tiếng Việt, nếu không có thì trả về nguyên gốc
    return $map[$this->status] ?? $this->status;
}

// Hàm này tạo ra thuộc tính ảo tên là: payment_status_vn
public function getPaymentStatusVnAttribute()
{
    $map = [
        'paid'   => 'Đã thanh toán',
        'unpaid' => 'Chưa thanh toán',
    ];
    return $map[$this->payment_status] ?? $this->payment_status;
}
    public function reviews()
{
    // Giả định bạn đã thêm cột 'order_id' vào bảng 'reviews'
    return $this->hasMany(Review::class);
}
}
