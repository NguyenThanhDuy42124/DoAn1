<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;
use App\Models\Category;

class CategoryProduct extends Component
{
    use WithPagination;

    public $category;
    
    // Các biến bộ lọc
    public $selectedBrands = []; 
    public $selectedAttributes = []; 
    public $minPrice = null;
    public $maxPrice = null;

    // *** THÊM BIẾN NÀY ĐỂ SỬA LỖI ***
    public $priceRange = ''; 

    // Reset phân trang về trang 1 khi lọc
    public function updated($propertyName)
    {
        $this->resetPage(); 
        
        // Nếu người dùng tự nhập tay vào ô input Min/Max
        // Thì bỏ chọn cái nút khoảng giá (cho đỡ bị highlight sai)
        if ($propertyName == 'minPrice' || $propertyName == 'maxPrice') {
            $this->priceRange = '';
        }
    }

    // *** THÊM HÀM NÀY ĐỂ XỬ LÝ NÚT BẤM KHOẢNG GIÁ ***
    public function setPrice($range)
    {
        $this->priceRange = $range; // Lưu lại để highlight nút đang chọn
        
        // Tách chuỗi '0-5000000' thành min và max
        $parts = explode('-', $range);
        
        if (count($parts) == 2) {
            $this->minPrice = $parts[0];
            $this->maxPrice = $parts[1];
        }
        
        $this->resetPage(); // Reset về trang 1
    }

    public function mount(Category $category)
    {
        $this->category = $category;
    }

    public function render()
    {
        $query = Product::query()
            ->where('category_id', $this->category->id)
            ->where('status', 'Approved');

        // --- 1. Lọc Brand ---
        if (!empty($this->selectedBrands)) {
            $query->whereIn('brand_id', $this->selectedBrands);
        }

        // --- 2. Lọc Giá ---
        if ($this->minPrice) $query->where('price', '>=', (float)$this->minPrice);
        if ($this->maxPrice) $query->where('price', '<=', (float)$this->maxPrice);

        // --- 3. Lọc Thuộc tính JSON ---
       $attributeTypes = $this->category->attributes()->pluck('type', 'attributes.id')->toArray();

        foreach ($this->selectedAttributes as $attributeId => $values) {
            
            // ===> THÊM DÒNG NÀY ĐỂ FIX LỖI <===
            // Nếu giá trị nhận được không phải là mảng (ví dụ: true/false/null), thì bỏ qua ngay
            if (!is_array($values)) continue; 
            
            $values = array_filter($values); // Giờ thì an toàn để filter rồi
            
            if (empty($values)) continue;
            if (!isset($attributeTypes[$attributeId])) continue;

            $type = $attributeTypes[$attributeId];

            $query->where(function($q) use ($type, $attributeId, $values) {
                foreach ($values as $val) {
                    if ($type == 'number') {
                        $range = explode('-', $val);
                        if (count($range) == 2) {
                            $min = (float)$range[0];
                            $max = (float)$range[1];
                            $q->orWhereRaw("CAST(JSON_UNQUOTE(JSON_EXTRACT(attributes, '$.\"$attributeId\"')) AS DECIMAL(15,2)) BETWEEN ? AND ?", [$min, $max]);
                        }
                    } else {
                        $q->orWhere('attributes->' . $attributeId, $val);
                    }
                }
            });
        }

        $products = $query->paginate(12);

        $filterableAttributes = $this->category->attributes()
            ->where('is_filterable', 1)
            ->with(['options' => function($q) {
                $q->orderBy('sort_order');
            }])
            ->get();

        return view('livewire.category-product', [
            'products' => $products,
            'filterableAttributes' => $filterableAttributes,
            'categoryBrands' => $this->category->brands,
        ])->layout('layouts.app');
    }
}   