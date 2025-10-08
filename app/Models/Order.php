<?php

namespace App\Models;

use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['buyer_id', 'seller_id', 'status', 'total_price', 'session_id', 'buyer_name', 'buyer_email', 'buyer_phone', 'shipping_address'];
    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }
    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }
}
