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
            'name', 'price', 'brand', 'description', 
            'image_1', 'image_2', 'image_3'
        ];

        foreach ($this->attributes as $attribute) {
            // *** 1. GẮN ĐƠN VỊ VÀO HEADER ***
            // Nếu có unit thì nối thêm vào. VD: "Pin (mAh)"
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
        // Dữ liệu mẫu (Giữ nguyên logic của mày)
        $dummyRow = [
            'Samsung Galaxy S24', '20000000', '', 'Hàng chính hãng...', 
            'anh1.jpg', '', '' 
        ];
        
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
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4CAF50']] // Tô màu header cho đẹp
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet;
                $rowCount = 1000; 
                $staticColumnCount = 7; // Số cột cố định đầu tiên

                // --- 1. DROPDOWN BRAND (Giữ nguyên code của mày) ---
                if ($this->brands->isNotEmpty()) {
                    $brandColumn = 'C'; 
                    $brandOptions = '"' . implode(',', $this->brands->pluck('name')->toArray()) . '"';
                    if (strlen($brandOptions) < 255) {
                        $validation = $sheet->getCell($brandColumn . '2')->getDataValidation();
                        $validation->setType(DataValidation::TYPE_LIST);
                        $validation->setErrorStyle(DataValidation::STYLE_STOP);
                        $validation->setAllowBlank(true);
                        $validation->setShowDropDown(true);
                        $validation->setErrorTitle('Lỗi nhập liệu');
                        $validation->setError('Chọn thương hiệu từ danh sách.');
                        $validation->setFormula1($brandOptions);
                        $sheet->setDataValidation($brandColumn . '2:' . $brandColumn . $rowCount, $validation);
                    }
                }

                // --- 2. XỬ LÝ CÁC CỘT THUỘC TÍNH ---
                foreach ($this->attributes as $index => $attribute) {
                    $columnIndex = $staticColumnCount + $index + 1; 
                    $columnLetter = Coordinate::stringFromColumnIndex($columnIndex);
                    $range = $columnLetter . '2:' . $columnLetter . $rowCount;

                    // Lấy đối tượng validation cho ô đầu tiên rồi áp dụng cho cả cột
                    $validation = $sheet->getCell($columnLetter . '2')->getDataValidation();

                    // TRƯỜNG HỢP: SELECT (Dropdown)
                    if ($attribute->type === 'select' && $attribute->options->isNotEmpty()) {
                        $optionsString = '"' . implode(',', $attribute->options->pluck('value')->toArray()) . '"';
                        // Giới hạn chuỗi của Excel là 255 ký tự cho list
                        if (strlen($optionsString) < 255) {
                            $validation->setType(DataValidation::TYPE_LIST);
                            $validation->setErrorStyle(DataValidation::STYLE_STOP);
                            $validation->setAllowBlank(true);
                            $validation->setShowDropDown(true);
                            $validation->setFormula1($optionsString);
                            $validation->setErrorTitle('Sai dữ liệu');
                            $validation->setError('Vui lòng chọn từ danh sách.');
                        }
                    } 
                    // TRƯỜNG HỢP: NUMBER (Validation số + Tooltip unit)
                    elseif ($attribute->type === 'number') {
                        // *** 2. VALIDATION CHỈ CHO NHẬP SỐ ***
                        $validation->setType(DataValidation::TYPE_DECIMAL);
                        $validation->setOperator(DataValidation::OPERATOR_GREATERTHANOREQUAL);
                        $validation->setFormula1(0); // Phải lớn hơn hoặc bằng 0
                        $validation->setErrorStyle(DataValidation::STYLE_STOP);
                        $validation->setErrorTitle('Lỗi định dạng');
                        $validation->setError('Vui lòng chỉ nhập số dương.');

                        // *** 3. HIỆN TOOLTIP KHI CLICK VÀO Ô ***
                        if (!empty($attribute->unit)) {
                            $validation->setShowInputMessage(true);
                            $validation->setPromptTitle('Lưu ý');
                            $validation->setPrompt("Nhập số (Đơn vị: {$attribute->unit})");
                        }
                    }

                    // Áp dụng validation cho cả cột
                    $sheet->setDataValidation($range, $validation);
                }
            },
        ];
    }
}