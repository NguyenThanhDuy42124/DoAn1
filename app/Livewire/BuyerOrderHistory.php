<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\Notification;

class BuyerOrderHistory extends Component
{
    use WithPagination;

    public $status = 'Pending';
    // ===============================================
    public $showReviewModal = false;
    public $productToReview;
    public $current_order_id;
    public $rating; // <-- Thuộc tính này sẽ được dùng cho cả 2 lần
    public $comment = '';
    public $alreadyReviewed = false;

    // ++ BỔ SUNG: Thuộc tính cho đánh giá/phản hồi bổ sung
    public $existingReview;
    public $additionalFeedback = '';
    // ===============================================

    // Các quy tắc validation
    protected $rules = [
        'rating' => 'required|integer|min:1|max:5', // <-- Giờ dùng chung
        'comment' => 'required|string|min:15|max:500',
        'additionalFeedback' => 'required|string|min:10|max:500', 
    ];

    protected $messages = [
        'rating.required' => 'Vui lòng chọn số sao đánh giá.', // <-- Giờ dùng chung
        'comment.required' => 'Vui lòng nhập nhận xét của bạn.',
        'comment.min' => 'Nhận xét phải có ít nhất 15 ký tự.',
        'comment.max' => 'Nhận xét không được vượt quá 500 ký tự.',
        // == Message mới ==
        'additionalFeedback.required' => 'Vui lòng nhập phản hồi bổ sung.',
        'additionalFeedback.min' => 'Phản hồi bổ sung phải có ít nhất 10 ký tự.',
        'additionalFeedback.max' => 'Phản hồi bổ sung không được vượt quá 500 ký tự.',
    ];
    
    // ===============================================
    // CÁC HÀM HIỆN CÓ (KHÔNG XÓA)
    // ===============================================

    public function mount()
    {
        $this->status = request()->query('status', 'Pending');
    }

    public function updateStatus($newStatus)
    {
        $this->status = $newStatus;
        $this->resetPage();
    }

    // Action: Hủy đơn
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

    // Action: Xác nhận nhận hàng
    public function confirmOrder($orderId)
    {
        $order = Order::where('user_id', Auth::id())->findOrFail($orderId);

        if ($order->status === 'Delivered') {
            $order->status = 'Completed';
            $order->save();
            session()->flash('success', 'Đã xác nhận nhận hàng!');
        } else {
            session()->flash('error', 'Không thể xác nhận đơn hàng này.');
        }

        $this->resetPage();
        $this->dispatch('orderUpdated');
    }

    // Action: Yêu cầu trả hàng
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

    // ===============================================
    // LOGIC MODAL ĐÁNH GIÁ (ĐÃ CẬP NHẬT)
    // ===============================================

    /**
     * Mở Modal Đánh giá (ĐÃ SỬA LỖI)
     * Hàm này giữ nguyên như phiên bản đã sửa lỗi ở lượt trước.
     */
    public function openReviewModal($productId, $orderId)
    {
        $this->productToReview = Product::with('images')->find($productId); 
        $this->current_order_id = $orderId;
        
        $this->resetValidation();
        $this->resetReviewFields();
        

        // ==========================================================
        // SỬA LỖI: Khôi phục lại điều kiện ->where('order_id', $orderId)
        // ==========================================================
        $this->existingReview = Review::where('buyer_id', Auth::id())
            ->where('product_id', $productId)
            ->where('order_id', $orderId) // <-- ĐIỀU KIỆN QUAN TRỌNG
            ->first();
        // ==========================================================

        if ($this->existingReview) {
            // Nếu đã tồn tại đánh giá, tải dữ liệu cũ vào
            $this->alreadyReviewed = true;
            $this->rating = $this->existingReview->rating; // <-- Tải sao cũ
            $this->comment = $this->existingReview->comment;
            $this->additionalFeedback = $this->existingReview->buyer_additional_feedback ?? '';
        } else {
            // Nếu chưa, đảm bảo các trường đã reset
            $this->alreadyReviewed = false;
        }

        $this->showReviewModal = true;
    }

    /**
     * Đóng Modal (Giữ nguyên)
     */
    public function closeReviewModal()
    {
        $this->showReviewModal = false;
        $this->resetReviewFields();
        $this->resetValidation();
        session()->forget('error_modal');
    }

    /**
     * Gửi Đánh giá (GỐC - Giữ nguyên)
     */
    public function submitReview()
    {
        $this->validate([
            'rating' => $this->rules['rating'],
            'comment' => $this->rules['comment'],
        ]);

        if (!$this->productToReview || $this->alreadyReviewed) {
            return;
        }

        $newReview = Review::create([
            'buyer_id' => Auth::id(),
            'product_id' => $this->productToReview->id,
            'order_id' => $this->current_order_id,
            'rating' => $this->rating,
            'comment' => $this->comment,
        ]);

        // Logic tạo Notification cho Seller (Giữ nguyên)
        $buyerName = htmlspecialchars_decode(Auth::user()->name, ENT_QUOTES);
        $productName = htmlspecialchars_decode($this->productToReview->name, ENT_QUOTES);
        $sellerId = $this->productToReview->seller_id;
        $reviewContent = $this->comment; 

        $separator = "||---REVIEW---||";
        $summary = "Người mua {$buyerName} đã đánh giá {$newReview->rating} sao cho sản phẩm {$productName} của bạn.";
        $fullMessage = $summary . $separator . $reviewContent;

        Notification::create([
            'user_id' => $sellerId,
            'type' => 'new_review',
            'message' => $fullMessage,
            'is_read' => false 
        ]);
        
        $this->closeReviewModal();
        session()->flash('success', 'Cảm ơn bạn đã đánh giá sản phẩm!');
    }

    /**
     * =================================================================
     * HÀM MỚI: Gửi Phản hồi Bổ sung (ĐÃ CẬP NHẬT THEO YÊU CẦU)
     * =================================================================
     */
    public function submitAdditionalFeedback()
    {
        // 1. Validate cả rating VÀ phản hồi bổ sung
        $this->validate([
            'rating' => $this->rules['rating'], // <-- VALIDATE RATING
            'additionalFeedback' => $this->rules['additionalFeedback']
        ]);

        if (!$this->existingReview) {
            session()->flash('error_modal', 'Không tìm thấy đánh giá gốc.');
            return;
        }

        if (empty($this->existingReview->reply)) {
            session()->flash('error_modal', 'Bạn chỉ có thể phản hồi sau khi người bán đã trả lời.');
            return;
        }

        if (!empty($this->existingReview->buyer_additional_feedback)) {
            session()->flash('error_modal', 'Bạn đã gửi phản hồi bổ sung rồi.');
            return;
        }

        // 2. Cập nhật đánh giá gốc (bao gồm cả RATING)
        $this->existingReview->update([
            'rating' => $this->rating, // <-- LƯU RATING MỚI
            'buyer_additional_feedback' => $this->additionalFeedback
        ]);

        // ==========================================================
        // 3. TẠO THÔNG BÁO CHO SELLER VỀ VIỆC CẬP NHẬT
        // ==========================================================
        try {
            $buyerName = htmlspecialchars_decode(Auth::user()->name, ENT_QUOTES);
            $productName = htmlspecialchars_decode($this->productToReview->name, ENT_QUOTES);
            $sellerId = $this->productToReview->seller_id;
            $additionalContent = $this->additionalFeedback; 
            $newRating = $this->rating; // Lấy rating mới từ thuộc tính

            $separator = "||---REVIEW---||";
            $summary = "Người mua {$buyerName} đã CẬP NHẬT đánh giá thành {$newRating} sao cho sản phẩm {$productName} của bạn.";
            
            $fullMessage = $summary . $separator . $additionalContent;

            Notification::create([
                'user_id' => $sellerId, // Gửi cho Seller
                'type' => 'review_updated', // <-- Loại thông báo mới
                'message' => $fullMessage,
                'is_read' => false 
            ]);
        } catch (\Exception $e) {
            // Ghi log lỗi nếu có, nhưng vẫn báo thành công cho người dùng
            \Log::error('Failed to create updated review notification: ' . $e->getMessage());
        }
        // ==========================================================

        session()->flash('success', 'Đã cập nhật đánh giá thành công!');
        $this->closeReviewModal();
    }


    /**
     * Reset các trường của form (Giữ nguyên)
     */
    private function resetReviewFields()
    {
        $this->rating = null;
        $this->comment = '';
        $this->alreadyReviewed = false;
        $this->existingReview = null;
        $this->additionalFeedback = '';
    }

    /**
     * Render (Giữ nguyên)
     */
    public function render()
    {
        $buyer = Auth::user();

        // Counts (Giữ nguyên)
        $pendingCount = Order::where('user_id', $buyer->id)->where('status', 'Pending')->count();
        $shippingCount = Order::where('user_id', $buyer->id)->where('status', 'Shipping')->count();
        $deliveredCount = Order::where('user_id', $buyer->id)->whereIn('status', ['Delivered', 'Completed'])->count();
        $cancelledCount = Order::where('user_id', $buyer->id)->where('status', 'Cancelled')->count();
        $returnedCount = Order::where('user_id', $buyer->id)->where('status', 'Returned')->count();
        
        // Query (Giữ nguyên)
        $query = Order::where('user_id', $buyer->id)
            ->with([
                'items.product',
                'reviews' // Tải các review CỦA ĐƠN HÀNG NÀY
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