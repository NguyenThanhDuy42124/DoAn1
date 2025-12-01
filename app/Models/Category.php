<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Category extends Model
{
    protected $fillable = ['name', 'description'];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }


    public function attributes()
    {
        return $this->belongsToMany(Attribute::class, 'category_attribute');
    }
     
    public function brands()
    {
        return $this->belongsToMany(Brand::class, 'brand_category');
    }

}