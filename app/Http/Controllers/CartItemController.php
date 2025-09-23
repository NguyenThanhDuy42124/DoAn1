<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CartItem;

class CartItemController extends Controller
{
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
