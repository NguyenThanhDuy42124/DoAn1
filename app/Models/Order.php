<?php

namespace App\Models;

use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }
}
