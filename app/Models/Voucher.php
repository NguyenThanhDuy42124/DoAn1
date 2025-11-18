<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Voucher extends Model
{
    use HasFactory;

    // Nếu bảng tên khác với tên model thì thêm $table
    protected $table = 'vouchers';

    // Cho phép gán dữ liệu hàng loạt
    protected $fillable = [
        'seller_id',
        'discount_rate',
        'condition',
        'expiry_date',
    ];

    // Cast để Laravel tự chuyển đổi
    protected $casts = [
        'discount_rate' => 'float',
        'expiry_date'   => 'datetime',
    ];

    /**
     * Quan hệ: Một voucher thuộc về một seller
     */
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    /**
     * Kiểm tra voucher còn hạn không
     */
    public function isValid()
    {
        return $this->expiry_date && $this->expiry_date->isFuture();
    }

    /**
     * Tự động format ngày khi lấy ra
     */
    public function getExpiryDateFormattedAttribute()
    {
        return $this->expiry_date
            ? $this->expiry_date->format('d/m/Y')
            : null;
    }

    /**
     * Scope: Lấy các voucher còn hạn
     */
    public function scopeActive($query)
    {
        return $query->where('expiry_date', '>', Carbon::now());
    }

    /**
     * Scope: Lọc theo seller
     */
    public function scopeBySeller($query, $sellerId)
    {
        return $query->where('seller_id', $sellerId);
    }
}
