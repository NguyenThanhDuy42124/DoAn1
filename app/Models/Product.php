<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id',
        'category_id',
        'name',
        'price',
        'brand_id',
        'stock',
        'description',
        'status',
        'attributes',
    ];
    protected $casts = [
        'attributes' => 'array',
    ];

    const STATUS_PENDING = 'Pending';
    const STATUS_APPROVED = 'Approved';
    const STATUS_HIDDEN = 'Hidden';
    const STATUS_REJECTED = 'Rejected';
    const STATUS_DELETED = 'Deleted';

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }
    
}
