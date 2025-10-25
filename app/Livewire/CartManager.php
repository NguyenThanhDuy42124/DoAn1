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
    }

    public function loadCart()
    {
        $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);
        $items = CartItem::with('product')->where('cart_id', $cart->id)->get();
        $items = CartItem::with('product.images')->where('cart_id', $cart->id)->get();
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

        // Selected mặc định: Tick hết nếu in_stock và quantity > 0
        $this->selectedItems = collect($items)->filter(function ($item) {
        return $item->stock_status === 'in_stock' && $item->quantity > 0;
    })->pluck('id')->toArray();
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
        if ($cartItem && $cartItem->cart->user_id === Auth::id()) {
            $cartItem->delete();
            $this->loadCart();
            $this->dispatch('success', 'Item removed!');
        }
    }

    public function getTotalPriceProperty()
    {
        $total = 0;
        foreach ($this->cartItems as $item) {
            if (in_array($item['id'], $this->selectedItems)) {  // Fix: $item['id'], không $items
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