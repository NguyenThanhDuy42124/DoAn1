<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;  // Import đúng cho redirect

class CartManager extends Component
{
    public $cartItems = [];
    public $selectedItems = [];
    public $quantities = [];

    public function mount()
    {
        $this->loadCart();
        $this->initializeSelections();
    
    }

    public function initializeSelections()
    {
        $latestItemData = collect($this->cartItems)
                            ->sortByDesc('updated_at') // Sắp giảm dần (mới nhất -> cũ)
                            ->first();

        if ($latestItemData) {
            // Cần lấy 'stock_status' từ $this->cartItems (array)
            // (Đảm bảo $latestItemData là array chứa 'stock_status')

            // Chỉ chọn nếu item mới nhất còn hàng
            if ($latestItemData['stock_status'] === 'in_stock') {
                // Tự động chọn item mới nhất (cập nhật gần nhất)
                $this->selectedItems = [(string)$latestItemData['id']];
            } else {
                // Nếu item mới nhất hết hàng, không chọn gì
                $this->selectedItems = [];
            }
        } else {
            // Nếu giỏ hàng trống, không chọn gì
            $this->selectedItems = [];
        }
    }
    public function loadCart()
    {
        $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);
        $items = CartItem::with('product.images')
                           ->where('cart_id', $cart->id)
                           ->orderBy('created_at', 'desc')
                           ->get();
        // Convert to array và add stock_status để view dùng
        $this->cartItems = $items->map(function ($item) {
            $maxStock = $item->product->stock;
            $displayQty = min($item->quantity, $maxStock);

            if ($item->quantity > $maxStock) {
                $item->quantity = $maxStock;
                $item->save();
            }

            $item->stock_status = ($maxStock == 0) ? 'out_of_stock' : ($item->quantity > $maxStock ? 'limited_stock' : 'in_stock');

            // Init quantities
            $this->quantities[$item->id] = $displayQty;

            return $item->toArray();  // Bao gồm stock_status
        })->toArray();

    }

    public function updatedQuantities($value, $key)  // Hook khi quantity thay đổi
    {
        $cartItemId = $key;
        $newQty = (int)$value;

        if ($newQty < 1) {
            $newQty = 1;
            $this->quantities[$cartItemId] = 1;
        }

        $cartItem = CartItem::find($cartItemId);  // Fix: CartItem, không CarItem
        if ($cartItem && $cartItem->cart->user_id === Auth::id()) {  // Fix biến $cartItem
            $product = $cartItem->product;
            if ($newQty > $product->stock) {
                $this->dispatch('error', 'Insufficient stock for ' . $product->name);
                $this->quantities[$cartItemId] = $product->stock;
                return;  // Dừng, không save
            }

            $cartItem->quantity = $newQty;
            $cartItem->save();
            $this->dispatch('success', 'Quantity updated!');
        }

        $this->loadCart();  // Refresh data để update total/stock_status
    }

    public function removeItem($cartItemId)
    {
        $cartItem = CartItem::find($cartItemId);
        $cartItem = CartItem::find($cartItemId);
        if ($cartItem && $cartItem->cart->user_id === Auth::id()) {
            
            // 1. Vẫn xóa khỏi Database
            $cartItem->delete();

            // 2. Xóa khỏi mảng $cartItems (thay vì loadCart)
            // Lọc ra tất cả item KHÔNG có id = $cartItemId
            $this->cartItems = array_values(
                array_filter($this->cartItems, function ($item) use ($cartItemId) {
                    return $item['id'] != $cartItemId;
                })
            );

            // 3. Xóa khỏi mảng $selectedItems (đề phòng item bị xóa đang được chọn)
            $this->selectedItems = array_values(
                array_filter($this->selectedItems, function ($id) use ($cartItemId) {
                    return $id != $cartItemId;
                })
            );

            // 4. BỎ $this->loadCart();
            // $this->loadCart(); 

            // 5. Vẫn dispatch event
            $this->dispatch('success', 'Item removed!');

            // Livewire sẽ tự động render lại và tự gọi getTotalPriceProperty
        }
    }

    public function getTotalPriceProperty()
    {
        $total = 0;
        foreach ($this->cartItems as $item) {
            if (in_array((string)$item['id'], $this->selectedItems)) {  // Fix: $item['id'], không $items
                $qty = $this->quantities[$item['id']] ?? 0;
                $total += $item['price'] * $qty;
            }
        }
        return $total;
    }

    public function checkout()
    {
        session(['selected_cart_items' => $this->selectedItems]);

        // Fix: Dùng $this->redirect() trong Livewire 3
        $this->redirect(route('buyer.checkouts.checkout'));
    }

    public function render()
    {
        return view('buyer.carts.cart-manager')->layout('layouts.app');  // Đảm bảo path view đúng
    }
}