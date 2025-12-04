<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Attribute extends Model
{
    protected $fillable = ['name', 'type', 'unit', 'is_filterable'];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_attribute');
    }



    public function options(): HasMany
    {
        return $this->hasMany(AttributeOption::class)->orderBy('sort_order');
    }

}
