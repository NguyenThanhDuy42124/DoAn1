<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductImportErrorExport implements FromArray, WithHeadings, WithStyles, ShouldAutoSize
{
    protected $errorRows;

    // Nhận dữ liệu lỗi từ Controller truyền sang
    public function __construct(array $errorRows)
    {
        $this->errorRows = $errorRows;
    }

    public function headings(): array
    {
        // Lấy danh sách key (header) từ dòng lỗi đầu tiên để làm tiêu đề
        if (empty($this->errorRows)) {
            return [];
        }

        // Lấy các key của dòng dữ liệu gốc (name, price,...)
        $headers = array_keys($this->errorRows[0]['data']);
        
        // Thêm cột cuối cùng là thông báo lỗi
        $headers[] = 'NGUYÊN NHÂN LỖI (CẦN SỬA)';

        return $headers;
    }

    public function array(): array
    {
        $exportData = [];

        foreach ($this->errorRows as $row) {
            // Lấy dữ liệu dòng gốc
            $rowData = array_values($row['data']);
            // Nối thêm lý do lỗi vào cuối dòng
            $rowData[] = $row['error'];
            
            $exportData[] = $rowData;
        }

        return $exportData;
    }

    public function styles(Worksheet $sheet)
    {
        // Lấy cột cuối cùng (cột Lỗi) để tô màu đỏ cảnh báo
        $lastColumn = $sheet->getHighestColumn();
        
        return [
            // Style cho dòng Header (dòng 1)
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'FF0000']] // Nền đỏ header
            ],
            // Style cho cột Lỗi (Cột cuối cùng)
            $lastColumn => [
                'font' => ['color' => ['rgb' => 'FF0000'], 'bold' => true],
            ],
        ];
    }
}