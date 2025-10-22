<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModerationLog extends Model
{
    protected $fillable = [
        'product_id', 'admin_id', 'action', 'from_status', 'to_status', 'note'
    ];

    public function product() { return $this->belongsTo(Product::class); }
    public function admin() { return $this->belongsTo(User::class, 'admin_id'); }
}
