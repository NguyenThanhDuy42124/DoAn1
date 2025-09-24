<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartItemController extends Controller
{
    public function edit($id)
    {
        $cartItem = CartItem::with('product')
            ->where('id', $id)
            ->whereHas('cart', fn($q) => $q->where('user_id', Auth::id()))
            ->firstOrFail();

        return view('buyer.carts.edit-item', compact('cartItem'));
    }

    public function update(Request $request, $id)
    {
        $cartItem = CartItem::with('product')
            ->where('id', $id)
            ->whereHas('cart', fn($q) => $q->where('user_id', Auth::id()))
            ->firstOrFail();

        $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $cartItem->product->stock,
        ]);

        $cartItem->quantity = $request->quantity;
        $cartItem->save();

        return redirect()->route('buyer.carts.index')
            ->with('success', 'Cart item updated successfully.');
    }

    public function destroy($id)
    {
        $item = CartItem::where('id', $id)
            ->whereHas('cart', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->firstOrFail();

        $item->delete();

        return redirect()->route('buyer.carts.index')
            ->with('success', 'Sản phẩm đã được xoá khỏi giỏ hàng.');
    }
}
