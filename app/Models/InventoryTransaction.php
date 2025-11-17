<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryTransaction extends Model
{
    use HasFactory;

    /**
     * Tên bảng trong database
     */
    protected $table = 'inventory_transactions';

    /**
     * Các cột được phép gán hàng loạt (mass-assignment)
     */
    protected $fillable = [
        'product_id',
        'seller_id',
        'transaction_type',
        'quantity',
        'notes',
    ];

    /**
     * Định nghĩa quan hệ: Mỗi log này thuộc về MỘT sản phẩm
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Định nghĩa quan hệ: Mỗi log này thuộc về MỘT seller (User)
     */
    public function seller(): BelongsTo
    {
        // Giả định Seller dùng Model User
        return $this->belongsTo(User::class, 'seller_id');
    }
}