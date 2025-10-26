<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Attribute extends Model
{
    protected $fillable = ['name', 'type', 'unit'];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_attribute');
    }

    public function values()
    {
        return $this->hasMany(ProductAttributeValue::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(AttributeOption::class)->orderBy('sort_order');
    }

}
