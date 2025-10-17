<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use App\Models\CartItem;

class OrderController extends Controller
{
    public function purchaseHistory()
    {
        $user = Auth::user();
        
        $orders = Order::where('user_id', $user->id) // Sử dụng user_id thay vì buyer_id
            ->with('items.product')
            ->orderBy('created_at', 'desc')
            ->get();

        $ordersGrouped = $orders->groupBy(function ($order) {
            return $order->session_id . '-' . $order->seller_id;
        });
        
        return view('buyer.checkouts.purchase_history', compact('ordersGrouped'));
    }

    public function repurchase(Order $order)
    {
        if ($order->user_id !== Auth::id())
        {
            abort(403);
        }

        $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);

        $addedProducts = [];
        $failedProducts = [];
        foreach($order -> items as $item)
        {
            $product = $item->product;
            if(!$product)
            {
                $failedProducts[] = "Sản phẩm '{$item->product_name}' không còn tồn tại.";
                continue;
            }

            if($product->stock < $item->quantity)
            {
                $failedProducts[] = "Sản phẩm '{$product->name}' không đủ số lượng (cần {$item->quantity}, còn {$product->stock}).";
                continue;
            }

            $cartItem = CartItem::where('cart_id', $cart->id)
                ->where('product_id', $product->id)
                ->first();

            if($cartItem)
            {
                $cartItem->quantity += $item->quantity;
                $cartItem->save();
            }else
            {
                CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $product->id,
                    'quantity' => $item->quantity,
                    'price' => $product->price,
                ]);
            }
            $addedProducts[] = $product->name;
        }

        $successMessage = !empty($addedProducts) ? 'Đã thêm ' .implode(', ', $addedProducts) . ' vào giỏ.' : '';
        $errorMessage = !empty($failedProducts) ? 'Không thể thêm: ' . implode('; ', $failedProducts) : '';

        $message = trim($successMessage . '' . $errorMessage);

        return redirect()->route('buyer.carts.index')->with('status', $message);
    }

}
