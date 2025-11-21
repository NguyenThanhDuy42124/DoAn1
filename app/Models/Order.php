<?php

namespace App\Models;

use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Model;


class Order extends Model
{
    protected $fillable = ['user_id', 'seller_id', 'product_id', 'status', 'subtotal', 'discount_amount', 'voucher_id', 'payment_status', 'tracking_code', 'cancellation_reason', 'total_price', 'session_id', 'buyer_name', 'buyer_email', 'buyer_phone', 'shipping_address'];
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
    public function reviews()
{
    // Giả định bạn đã thêm cột 'order_id' vào bảng 'reviews'
    return $this->hasMany(Review::class); 
}
}
