<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\Notification; 
use App\Models\Review;
class SellerOrderManager extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap'; 

    // --- FIX 2: GIỮ TRẠNG THÁI TRÊN URL ---
    // Cái này cực quan trọng. Nó giúp Livewire biết là mày đang ở Tab nào (VD: status=Shipping).
    // Khi mày bấm qua trang 2, nó sẽ nối thêm ?status=Shipping&page=2
    // Nếu không có dòng này, bấm trang 2 nó dễ bị reset về Pending lắm.
    protected $queryString = [
        'status' => ['except' => 'Pending'], // Không hiện lên URL nếu là Pending (mặc định)
    ];

    public $status = 'Pending';
    public $selectedOrders = [];
    public $selectAll = false;
    //reply review
    public $showReviewModal = false;
    public $orderForReview;
    public $replies = []; // Mảng để lưu các phản hồi, key là review_id
    public function mount()
    {
        $this->status = request()->query('status', 'Pending');
    }

    public function updateStatus($orderId, $newStatus)
    {
        $order = Order::where('seller_id', Auth::id())->findOrFail($orderId);
        if ($newStatus === 'Shipping' && $order->status === 'Pending') {
            $order->status = 'Shipping';
        } elseif ($newStatus === 'Delivered' && $order->status === 'Shipping') {
            $order->status = 'Delivered';
        } else {
            session()->flash('error', 'Không thể cập nhật trạng thái. Vui lòng kiểm tra trạng thái hiện tại.');
            return;
        }

        $order->save();
        $statusMessages = [
            'Shipping' => 'đang được vận chuyển',
            'Delivered' => 'đã được giao hàng'
        ];
        
        if (isset($statusMessages[$newStatus])) {
            Notification::create([
                'user_id' => $order->user_id,
                'type' => 'order_status_updated',
                'message' => "Đơn hàng #{$order->id} của bạn {$statusMessages[$newStatus]}",
                'is_read' => false,
            ]);
        }
        session()->flash('success', 'Cập nhật trạng thái thành công!');
        $this->resetPage();
        $this->dispatch('statusUpdated');
    }
    public function filterByStatus($newStatus)
    {
        $this->status = $newStatus;
        $this->resetPage(); // Reset phân trang về trang 1 khi đổi tab
    }
    public function bulkApprove()
    {
        if (empty($this->selectedOrders)) {
            session()->flash('error', 'Vui lòng chọn ít nhất một đơn hàng.');
            return;
        }

        $orders = Order::where('seller_id', Auth::id())->whereIn('id', $this->selectedOrders)->get();
        foreach ($orders as $order) {
            if ($order->status === 'Pending') {
                $order->status = 'Shipping';
                $order->save();
                 Notification::create([
                    'user_id' => $order->user_id,
                    'type' => 'order_status_updated',
                    'message' => "Đơn hàng #{$order->id} của bạn đã được xác nhận và đang được vận chuyển",
                    'is_read' => false,
                ]);
            }
        }

        $this->selectedOrders = [];
        $this->selectAll = false;
        session()->flash('success', 'Đã phê duyệt đơn hàng hàng loạt thành công!');
        $this->resetPage();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedOrders = Order::where('seller_id', Auth::id())
                ->where('status', 'Pending')
                ->where('status', $this->status)
                ->pluck('id')
                ->toArray();
        } else {
            $this->selectedOrders = [];
        }
    }
    //modalreview
    public function openReviewModal($orderId)
    {
        // Tải đơn hàng VỚI các review của CHÍNH NÓ
        // và tải thông tin 'buyer' và 'product' cho TỪNG review.
        $this->orderForReview = Order::with([
                'reviews.buyer', // Tải người viết review
                'reviews.product' // Tải sản phẩm được review
            ])
            ->where('seller_id', Auth::id()) 
            ->findOrFail($orderId);

        // Lấy tất cả review của đơn hàng này
        $allReviews = $this->orderForReview->reviews; // Đơn giản hơn nhiều!

        // Tải các phản hồi có sẵn vào mảng $replies
        if ($allReviews->isNotEmpty()) {
            $this->replies = $allReviews->pluck('reply', 'id')->toArray();
        } else {
            $this->replies = [];
        }

        $this->showReviewModal = true;
    }

    /**
     * Đóng Modal
     */
    public function closeReviewModal()
    {
        $this->showReviewModal = false;
        $this->orderForReview = null;
        $this->replies = [];
        session()->forget('reply_success'); // Xóa session message
        session()->forget('reply_error');
    }

    /**
     * Lưu phản hồi của Seller
     */
    public function submitReply($reviewId)
    {
        // Tải kèm 'buyer' và 'product' để lấy thông tin
        $review = Review::with('buyer', 'product')->find($reviewId); 
        
        // Kiểm tra quyền
        if ($review && $review->product->seller_id == Auth::id()) {
            
            $replyContent = $this->replies[$reviewId] ?? null;

            // 1. Cập nhật phản hồi vào bảng reviews
            $review->update([
                'reply' => $replyContent
            ]);

            session()->flash('reply_success', 'Đã lưu phản hồi cho đánh giá #' . $reviewId);

            // ==========================================================
            // BỔ SUNG: TẠO THÔNG BÁO CHO BUYER
            // (Sử dụng model Notification.php bạn đã cung cấp)
            // ==========================================================
           $sellerName = htmlspecialchars_decode(Auth::user()->name, ENT_QUOTES); 
            $productName = htmlspecialchars_decode($review->product->name, ENT_QUOTES);

            $separator = "||---REPLY---||"; 
            
            // BỎ DẤU '...' xung quanh tên
            $summary = "Người bán {$sellerName} đã phản hồi đánh giá của bạn cho sản phẩm {$productName}.";
            
            // Ghép tóm tắt và nội dung phản hồi
            $fullMessage = $summary . $separator . $replyContent;

            Notification::create([
                'user_id' => $review->buyer_id, 
                'type' => 'review_replied',
                'message' => $fullMessage, // <-- Lưu nội dung sạch
                'is_read' => false 
            ]);
            // ==========================================================

        } else {
            session()->flash('reply_error', 'Không thể lưu phản hồi. Đã có lỗi xảy ra.');
        }
    }
    public function render()
    {
        $seller = Auth::user();

        $pendingCount = Order::where('seller_id', $seller->id)->where('status', 'Pending')->count();
        $shippingCount = Order::where('seller_id', $seller->id)->where('status', 'Shipping')->count();
        $deliveredCount = Order::where('seller_id', $seller->id)->where('status', 'Delivered')->count();
        $completedCount = Order::where('seller_id', $seller->id)->where('status', 'Completed')->count();

        $orders = Order::where('seller_id', $seller->id)
            ->with('buyer', 'items.product')
            ->where('status', $this->status)
            ->latest()
            ->paginate(10);

        return view('seller.orders.seller-order-manager', [
            'orders' => $orders,
            'pendingCount' => $pendingCount,
            'shippingCount' => $shippingCount,
            'deliveredCount' => $deliveredCount,
            'completedCount' => $completedCount,
            'status' => $this->status,
        ]);
    }
}