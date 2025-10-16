<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;

class SellerOrderManager extends Component
{
    use WithPagination;

    public $status = 'Pending';
    public $selectedOrders = [];
    public $selectAll = false;

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
        session()->flash('success', 'Cập nhật trạng thái thành công!');
        $this->resetPage();
        $this->dispatch('statusUpdated');
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