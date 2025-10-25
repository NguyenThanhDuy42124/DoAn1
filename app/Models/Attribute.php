<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    protected $fillable = ['name', 'type'];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_attribute');
    }

    public function values()
    {
        return $this->hasMany(ProductAttributeValue::class);
    }
}
