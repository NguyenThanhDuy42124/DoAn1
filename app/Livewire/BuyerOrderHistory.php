<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\Product; // <-- BỔ SUNG
use App\Models\Review;  // <-- BỔ SUNG
class BuyerOrderHistory extends Component
{
    use WithPagination;

    public $status = 'Pending';  // Default tab
    // ===============================================
    public $showReviewModal = false;
    public $productToReview;
    public $current_order_id;
    public $rating;
    public $comment = '';
    public $alreadyReviewed = false;

    // Các quy tắc validation
    protected $rules = [
        'rating' => 'required|integer|min:1|max:5',
        'comment' => 'required|string|min:15|max:500',
    ];

    protected $messages = [
        'rating.required' => 'Vui lòng chọn số sao đánh giá.',
        'comment.required' => 'Vui lòng nhập nhận xét của bạn.',
        'comment.min' => 'Nhận xét phải có ít nhất 15 ký tự.',
        'comment.max' => 'Nhận xét không được vượt quá 500 ký tự.',
    ];
    // ===============================================
    public function mount()
    {
        $this->status = request()->query('status', 'Pending');
    }
    public function updateStatus($newStatus)
    {
        $this->status = $newStatus;
        
        // Nếu bạn đang dùng $queryString, Livewire sẽ tự động cập nhật URL
        // Nếu không, bạn chỉ cần gán lại $status là đủ.
        
        // Bạn không cần làm gì thêm ở đây. 
        // Livewire sẽ tự động gọi lại hàm render() với $status mới.
    }
    // Action: Hủy đơn (Pending -> Cancelled)
    public function cancelOrder($orderId)
    {
        $order = Order::where('user_id', Auth::id())->findOrFail($orderId);

        if ($order->status === 'Pending') {
            $order->status = 'Cancelled';
            $order->save();
            session()->flash('success', 'Đơn hàng đã được hủy thành công!');
        } else {
            session()->flash('error', 'Không thể hủy đơn hàng này.');
        }

        $this->resetPage();
        $this->dispatch('orderUpdated');
    }

    // Action: Xác nhận nhận hàng (Delivered -> Completed, giả sử)
    public function confirmOrder($orderId)
    {
        $order = Order::where('user_id', Auth::id())->findOrFail($orderId);

        if ($order->status === 'Delivered') {
            $order->status = 'Completed';  // Hoặc logic mày muốn
            $order->save();
            session()->flash('success', 'Đã xác nhận nhận hàng!');
        } else {
            session()->flash('error', 'Không thể xác nhận đơn hàng này.');
        }

        $this->resetPage();
        $this->dispatch('orderUpdated');
    }

    // Action: Yêu cầu trả hàng (Delivered -> Returned)
    public function returnOrder($orderId)
    {
        $order = Order::where('user_id', Auth::id())->findOrFail($orderId);

        if ($order->status === 'Delivered') {
            $order->status = 'Returned';
            $order->save();
            session()->flash('success', 'Yêu cầu trả hàng đã được gửi!');
        } else {
            session()->flash('error', 'Không thể yêu cầu trả hàng này.');
        }

        $this->resetPage();
        $this->dispatch('orderUpdated');
    }
    /**
     * Mở Modal Đánh giá
     */
    public function openReviewModal($productId, $orderId)
    {
        $this->productToReview = Product::find($productId);
        $this->current_order_id = $orderId;
        
        // Kiểm tra xem đã đánh giá sản phẩm này chưa
        $existingReview = Review::where('buyer_id', Auth::id())
            ->where('product_id', $productId)
            // ->where('order_id', $orderId) // Bật nếu bạn có cột order_id
            ->first();

        if ($existingReview) {
            $this->alreadyReviewed = true;
        } else {
            $this->alreadyReviewed = false;
        }

        $this->resetValidation();
        $this->resetReviewFields(); // Reset field mỗi lần mở
        $this->showReviewModal = true;
    }

    /**
     * Đóng Modal
     */
    public function closeReviewModal()
    {
        $this->showReviewModal = false;
        $this->resetReviewFields();
        $this->resetValidation();
    }

    /**
     * Gửi Đánh giá
     */
    public function submitReview()
    {
        $this->validate(); // Validate các $rules ở trên

        if (!$this->productToReview || $this->alreadyReviewed) {
            return; // Ngăn chặn nếu có lỗi
        }

        Review::create([
            'buyer_id' => Auth::id(),
            'product_id' => $this->productToReview->id,
            'rating' => $this->rating,
            'comment' => $this->comment,
            // 'order_id' => $this->current_order_id, // Gán nếu bạn có cột này
        ]);

        $this->closeReviewModal();
        session()->flash('success', 'Cảm ơn bạn đã đánh giá sản phẩm!');
    }

    /**
     * Reset các trường của form
     */
    private function resetReviewFields()
    {
        $this->rating = null;
        $this->comment = '';
        $this->alreadyReviewed = false;
    }
    public function render()
    {
        $buyer = Auth::user();

        // Counts riêng (tất cả orders của buyer, không filtered)
        $pendingCount = Order::where('user_id', $buyer->id)->where('status', 'Pending')->count();
        $shippingCount = Order::where('user_id', $buyer->id)->where('status', 'Shipping')->count();
        $deliveredCount = Order::where('user_id', $buyer->id)->whereIn('status', ['Delivered', 'Completed'])->count();
        $cancelledCount = Order::where('user_id', $buyer->id)->where('status', 'Cancelled')->count();
        $returnedCount = Order::where('user_id', $buyer->id)->where('status', 'Returned')->count();
        
        // $query = Order::where('user_id', $buyer->id)->with('items.product'); // <-- DÒNG CŨ CỦA BẠN

        // THAY THẾ BẰNG DÒNG MỚI: Tải các đánh giá của SẢN PHẨM, nhưng CHỈ của buyer hiện tại
        $query = Order::where('user_id', $buyer->id)
            ->with([
                'items.product' => function ($query) {
                    $query->with(['reviews' => function ($q) {
                        $q->where('buyer_id', Auth::id());
                    }]);
                }
            ]);

        if($this->status === 'Delivered')
        {
            $query->whereIn('status', ['Delivered', 'Completed']);
        }else
        {
            $query->where('status', $this->status);
        }
        $orders = $query->latest()->paginate(10);

        return view('buyer.orders.buyer-order-history', [
            'orders' => $orders,
            'pendingCount' => $pendingCount,
            'shippingCount' => $shippingCount,
            'deliveredCount' => $deliveredCount,
            'cancelledCount' => $cancelledCount,
            'returnedCount' => $returnedCount,
            'status' => $this->status,
        ]);
    }
}