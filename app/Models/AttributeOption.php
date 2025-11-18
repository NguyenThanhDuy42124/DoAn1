<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttributeOption extends Model
{
    protected $fillable = ['attribute_id', 'value', 'sort_order'];
    public $timestamps = false; // Bảng này không cần created_at/updated_at

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class);
    }
}