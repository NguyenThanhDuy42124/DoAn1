<?php

namespace App\Exports;

use App\Models\Attribute;
use App\Models\Brand;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class ProductTemplateExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles, WithEvents
{
    protected $categoryId;
    protected $attributes;
    protected $brands;
    protected $imageCount = 10; // Cấu hình cho phép nhập tối đa 10 ảnh

    public function __construct($categoryId)
    {
        $this->categoryId = $categoryId;
        
        if ($this->categoryId) {
            $this->attributes = Attribute::whereHas('categories', function ($q) {
                $q->where('category_id', $this->categoryId);
            })->with('options')->orderBy('name')->get();

            $this->brands = Brand::whereHas('categories', function ($q) {
                $q->where('category_id', $this->categoryId);
            })->orderBy('name')->get();

        } else {
            $this->attributes = collect();
            $this->brands = collect();
        }
    }

    public function headings(): array
    {
        $headers = [
            'name', 'price', 'brand', 'description'
        ];

        // --- SỬA: Loop tạo cột ảnh động ---
        for ($i = 1; $i <= $this->imageCount; $i++) {
            $headers[] = "image_" . $i;
        }

        foreach ($this->attributes as $attribute) {
            $headerName = $attribute->name;
            if (!empty($attribute->unit)) {
                $headerName .= " ({$attribute->unit})";
            }
            $headers[] = $headerName;
        }

        return $headers;
    }

    public function array(): array
    {
        $dummyRow = [
            'Samsung Galaxy S24', '20000000', '', 'Mô tả sản phẩm...'
        ];
        
        // Tạo ô trống cho các cột ảnh
        for ($i = 1; $i <= $this->imageCount; $i++) {
            $dummyRow[] = ($i === 1) ? 'anh1.jpg' : ''; // Ví dụ mẫu cho cột 1
        }

        foreach ($this->attributes as $attribute) {
            $dummyRow[] = '';
        }

        return [$dummyRow];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4CAF50']] 
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet;
                $rowCount = 1000; 
                
                // --- TÍNH LẠI SỐ CỘT CỐ ĐỊNH ---
                // Name, Price, Brand, Desc (4) + ImageCount (10) = 14
                $staticColumnCount = 4 + $this->imageCount; 

                // 1. DROPDOWN BRAND
                if ($this->brands->isNotEmpty()) {
                    $brandColumn = 'C'; 
                    $brandOptions = '"' . implode(',', $this->brands->pluck('name')->toArray()) . '"';
                    if (strlen($brandOptions) < 255) {
                        $validation = $sheet->getCell($brandColumn . '2')->getDataValidation();
                        $validation->setType(DataValidation::TYPE_LIST);
                        $validation->setErrorStyle(DataValidation::STYLE_STOP);
                        $validation->setAllowBlank(true);
                        $validation->setShowDropDown(true);
                        $validation->setError('Chọn thương hiệu từ danh sách.');
                        $validation->setFormula1($brandOptions);
                        $sheet->setDataValidation($brandColumn . '2:' . $brandColumn . $rowCount, $validation);
                    }
                }

                // 2. XỬ LÝ ATTRIBUTES
                foreach ($this->attributes as $index => $attribute) {
                    $columnIndex = $staticColumnCount + $index + 1; 
                    $columnLetter = Coordinate::stringFromColumnIndex($columnIndex);
                    $range = $columnLetter . '2:' . $columnLetter . $rowCount;
                    $validation = $sheet->getCell($columnLetter . '2')->getDataValidation();

                    if ($attribute->type === 'select' && $attribute->options->isNotEmpty()) {
                        $optionsString = '"' . implode(',', $attribute->options->pluck('value')->toArray()) . '"';
                        if (strlen($optionsString) < 255) {
                            $validation->setType(DataValidation::TYPE_LIST);
                            $validation->setErrorStyle(DataValidation::STYLE_STOP);
                            $validation->setAllowBlank(true);
                            $validation->setShowDropDown(true);
                            $validation->setFormula1($optionsString);
                            $validation->setError('Vui lòng chọn từ danh sách.');
                        }
                    } elseif ($attribute->type === 'number') {
                        $validation->setType(DataValidation::TYPE_DECIMAL);
                        $validation->setOperator(DataValidation::OPERATOR_GREATERTHANOREQUAL);
                        $validation->setFormula1(0);
                        $validation->setErrorStyle(DataValidation::STYLE_STOP);
                        $validation->setError('Vui lòng chỉ nhập số dương.');

                        if (!empty($attribute->unit)) {
                            $validation->setShowInputMessage(true);
                            $validation->setPromptTitle('Lưu ý');
                            $validation->setPrompt("Nhập số (Đơn vị: {$attribute->unit})");
                        }
                    }
                    $sheet->setDataValidation($range, $validation);
                }
            },
        ];
    }
}