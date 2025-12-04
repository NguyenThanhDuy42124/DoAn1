<?php

namespace App\Exports;

use App\Models\Product;
use App\Models\Attribute;
use App\Models\Brand;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class ProductBulkEditExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithEvents
{
    protected $categoryId;
    protected $sellerId;
    protected $attributes;
    protected $brands;
    protected $maxImageCount = 0;

    public function __construct($categoryId, $sellerId)
    {
        $this->categoryId = $categoryId;
        $this->sellerId = $sellerId;
        
        $this->attributes = Attribute::whereHas('categories', function ($q) {
            $q->where('category_id', $this->categoryId);
        })->with('options')->orderBy('name')->get();

        $this->brands = Brand::whereHas('categories', function ($q) {
            $q->where('category_id', $this->categoryId);
        })->orderBy('name')->get();

        // --- SỬA ĐOẠN TÍNH SỐ CỘT ẢNH ---
        // 1. Lấy số lượng ảnh nhiều nhất thực tế trong DB
        $realMaxImages = Product::where('seller_id', $this->sellerId)
            ->where('category_id', $this->categoryId)
            ->withCount('images')
            ->get()
            ->max('images_count');
            
        // 2. Logic mới: Lấy số lớn nhất giữa "Thực tế" và "10"
        // - Nếu thực tế là 1 ảnh -> Lấy 10 (để user nhập thêm ảnh 2, 3...)
        // - Nếu thực tế là 15 ảnh -> Lấy 15 (để không bị mất ảnh cũ)
        $this->maxImageCount = max(($realMaxImages ?? 0), 10); 
    }

    public function collection()
    {
        return Product::where('seller_id', $this->sellerId)
                      ->where('category_id', $this->categoryId)
                      ->with(['brand', 'images'])
                      ->get();
    }

    public function headings(): array
    {
        $headers = ['id', 'name', 'price', 'brand', 'description'];

        // Loop theo con số maxImageCount đã fix (tối thiểu là 10)
        for ($i = 1; $i <= $this->maxImageCount; $i++) {
            $headers[] = "image_" . $i;
        }

        foreach ($this->attributes as $attr) {
            $headers[] = $attr->name . ($attr->unit ? " ({$attr->unit})" : "");
        }

        return $headers;
    }

    public function map($product): array
    {
        $row = [
            $product->id,
            $product->name,
            $product->price,
            $product->brand ? $product->brand->name : '',
            $product->description,
        ];

        // Map ảnh
        $images = $product->images;
        // Loop đủ 10 (hoặc hơn) lần. Nếu không có ảnh thì điền rỗng.
        for ($i = 0; $i < $this->maxImageCount; $i++) {
            if (isset($images[$i])) {
                $row[] = basename($images[$i]->image_path);
            } else {
                $row[] = ''; // Ô trống để user điền ảnh mới vào
            }
        }

        // Map thuộc tính
        $productAttributes = $product->attributes ?? []; 
        foreach ($this->attributes as $attr) {
            $attrId = (string)$attr->id;
            $row[] = $productAttributes[$attrId] ?? '';
        }

        return $row;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '2196F3']]],
            'A' => ['fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'E0E0E0']]], 
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet;
                $rowCount = 5000; 

                // 1. DROPDOWN BRAND
                if ($this->brands->isNotEmpty()) {
                    $brandCol = 'D';
                    $brandOptions = '"' . implode(',', $this->brands->pluck('name')->toArray()) . '"';
                    if (strlen($brandOptions) < 255) {
                        $validation = $sheet->getCell("{$brandCol}2")->getDataValidation();
                        $validation->setType(DataValidation::TYPE_LIST);
                        $validation->setAllowBlank(true);
                        $validation->setShowDropDown(true);
                        $validation->setFormula1($brandOptions);
                        $sheet->setDataValidation("{$brandCol}2:{$brandCol}{$rowCount}", $validation);
                    }
                }

                // 2. TÍNH LẠI VỊ TRÍ CỘT THUỘC TÍNH
                // 5 cột cố định + số lượng cột ảnh (tối thiểu 10)
                $startAttrIndex = 5 + $this->maxImageCount;

                foreach ($this->attributes as $index => $attr) {
                    $currentColIndex = $startAttrIndex + $index + 1;
                    $colLetter = Coordinate::stringFromColumnIndex($currentColIndex);
                    $range = "{$colLetter}2:{$colLetter}{$rowCount}";

                    $validation = $sheet->getCell("{$colLetter}2")->getDataValidation();

                    if ($attr->type === 'select' && $attr->options->isNotEmpty()) {
                        $options = '"' . implode(',', $attr->options->pluck('value')->toArray()) . '"';
                        if (strlen($options) < 255) {
                            $validation->setType(DataValidation::TYPE_LIST);
                            $validation->setAllowBlank(true);
                            $validation->setShowDropDown(true);
                            $validation->setFormula1($options);
                            $sheet->setDataValidation($range, $validation);
                        }
                    } elseif ($attr->type === 'number') {
                        $validation->setType(DataValidation::TYPE_DECIMAL);
                        $validation->setOperator(DataValidation::OPERATOR_GREATERTHANOREQUAL);
                        $validation->setFormula1(0);
                        if (!empty($attr->unit)) {
                            $validation->setShowInputMessage(true);
                            $validation->setPromptTitle('Lưu ý');
                            $validation->setPrompt("Đơn vị: {$attr->unit}");
                        }
                        $sheet->setDataValidation($range, $validation);
                    }
                }
            },
        ];
    }
}