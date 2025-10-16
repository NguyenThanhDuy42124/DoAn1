<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;

class BuyerOrderHistory extends Component
{
    use WithPagination;

    public $status = 'Pending';  // Default tab

    public function mount()
    {
        $this->status = request()->query('status', 'Pending');
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

    public function render()
    {
        $buyer = Auth::user();

        // Counts riêng (tất cả orders của buyer, không filtered)
        $pendingCount = Order::where('user_id', $buyer->id)->where('status', 'Pending')->count();
        $shippingCount = Order::where('user_id', $buyer->id)->where('status', 'Shipping')->count();
        $deliveredCount = Order::where('user_id', $buyer->id)->where('status', 'Delivered')->count();
        $cancelledCount = Order::where('user_id', $buyer->id)->where('status', 'Cancelled')->count();
        $returnedCount = Order::where('user_id', $buyer->id)->where('status', 'Returned')->count();

        // Orders paginated, with items
        $orders = Order::where('user_id', $buyer->id)
            ->with('items.product')
            ->where('status', $this->status)
            ->latest()  // Mới nhất top, đổi ASC nếu cần
            ->paginate(10);

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