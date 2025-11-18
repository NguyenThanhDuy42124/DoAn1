<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Review extends Model
{
   protected $fillable = [
        'buyer_id',
        'product_id',
        'rating',
        'comment',
        'reply', // Thêm 'reply' dựa trên CSDL của bạn
        'order_id', // Bạn cũng nên xem xét thêm cột này
        'buyer_additional_feedback',
    ];

    /**
     * Lấy thông tin người mua.
     */
    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    /**
     * Lấy thông tin sản phẩm.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
    //lay thong tin don hang
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
