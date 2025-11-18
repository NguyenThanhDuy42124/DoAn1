<?php

namespace App\Livewire\Seller;

use Livewire\Component;
use App\Models\Order;
use Carbon\Carbon;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\Auth;

class ExportReportForm extends Component
{
    public $start_date;
    public $end_date;

    // (Phần rules và messages giữ nguyên, không đổi)
    protected $rules = [
        'start_date' => 'nullable|date',
        'end_date'   => 'nullable|date|after_or_equal:start_date',
    ];

    protected $messages = [
        'end_date.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu.',
    ];

    // (Phần updated và validateDateRangeLimit giữ nguyên, không đổi)
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
        $this->validateDateRangeLimit();
    }

    private function validateDateRangeLimit()
    {
        $this->resetErrorBag('date_range');

        if ($this->start_date && $this->end_date) {
            $start = Carbon::parse($this->start_date);
            $end = Carbon::parse($this->end_date);
            $diffInDays = $end->diffInDays($start);

            if ($diffInDays >= 15) {
                $this->addError('date_range', 'Lỗi: Chỉ được xuất báo cáo trong khoảng 15 ngày hoặc ít hơn.');
            }
        }
    }

    /**
     * HÀM MỚI: Tách logic truy vấn ra riêng
     * Hàm này sẽ build query, nhưng CHƯA thực thi
     */
    private function getOrderQuery()
    {
        // Luôn lọc theo seller và trạng thái 'paid'
        $query = Order::query()
            ->where('seller_id', Auth::id())
            ->where('payment_status', 'paid');

        if ($this->start_date && $this->end_date) {
            // Logic 1: Lọc theo khoảng ngày (nếu có)
            $query->whereBetween('created_at', [
                Carbon::parse($this->start_date)->startOfDay(),
                Carbon::parse($this->end_date)->endOfDay()
            ]);
        } else {
            // Logic 2: Lấy 15 item mới nhất (nếu không chọn ngày)
            $query->latest()->take(15);
        }

        return $query;
    }

    /**
     * Hàm chính xử lý việc xuất Excel
     */
    public function exportExcel()
    {
        // Chạy validation
        $this->validate();
        $this->validateDateRangeLimit();

        if ($this->getErrorBag()->isNotEmpty()) {
            return;
        }

        // === BẮT ĐẦU TRUY VẤN DỮ LIỆU ===
        // Lấy dữ liệu từ hàm query chung
        $orders = $this->getOrderQuery()->get();

        // (Tùy chọn) Format dữ liệu cho Excel
        $exportData = $orders->map(function ($order) {
            return [
                'Mã đơn hàng'   => $order->id,
                'Ngày đặt'        => $order->created_at->format('d/m/Y H:i'),
                'Tổng tiền'       => $order->total_price, // FastExcel sẽ tự hiểu đây là số
                'Trạng thái'      => ucfirst($order->status),
                // Thêm các cột khác nếu bạn muốn
                // 'Tên khách hàng' => $order->customer->name,
            ];
        });

        // === KẾT THÚC TRUY VẤN DỮ LIỆU ===

        $filename = 'bao-cao-doanh-thu-' . now()->format('Y-m-d') . '.xlsx';

        // Trả về file download
        return (new FastExcel($exportData))->download($filename);
    }

    /**
     * HÀM RENDER (Cập nhật)
     * Hàm này chạy mỗi khi component được tải hoặc cập nhật.
     * Nó sẽ lấy dữ liệu và truyền cho view.
     */
    public function render()
    {
        // Lấy dữ liệu cho bảng preview
        $orders = $this->getOrderQuery()->get();

        // Trả về view, kèm theo biến $orders
        return view('livewire.seller.export-report-form', [
            'orders' => $orders
        ]);
    }
}
