<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id',
        'code',
        'name',
        'type',                 // 'fixed' hoặc 'percent'
        'value',                // Giá trị (VD: 50000 hoặc 10)
        'max_discount_amount',  // Giảm tối đa bao nhiêu (cho loại %)
        'min_order_value',      // Đơn tối thiểu
        'quantity',             // Tổng số lượng
        'used_count',           // Đã dùng
        'start_date',
        'expiry_date',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'expiry_date' => 'datetime',
        'is_active' => 'boolean',
        'value' => 'decimal:2',
        'min_order_value' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
    ];

    // Helper check xem voucher có hợp lệ không
    public function isValid()
    {
        $now = now();
        
        // 1. Check active
        if (!$this->is_active) return false;

        // 2. Check số lượng
        if ($this->used_count >= $this->quantity) return false;

        // 3. Check thời gian bắt đầu
        if ($this->start_date && $now->lt($this->start_date)) return false;

        // 4. Check hết hạn
        if ($this->expiry_date && $now->gt($this->expiry_date)) return false;

        return true;
    }

    // Quan hệ với Seller (User)
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }
    
    // Quan hệ với Orders
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}