<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    

    public function index()
    {
        $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);
        $cartItems = CartItem::with('product')->where('cart_id', $cart->id)->get();
        return view('buyer.carts.index', compact('cartItems'));
    }

    public function store(Request $request)
    {
        
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);
        if ($product->stock < $request->quantity) {
            return redirect()->route('buyer.carts.index')
                ->with('error', 'Insufficient stock for ' . $product->name);
        }

        $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);
        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $request->product_id)
            ->first();

        if ($cartItem) {
            $cartItem->quantity += $request->quantity;
            $cartItem->save();
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
                'price' => $product->price,
            ]);
        }

        return redirect()->route('buyer.carts.index')
            ->with('success', 'Product added to cart successfully.');
    }

    public function edit($id)
    {
        $cart = Cart::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $cartItems = CartItem::with('product')->where('cart_id', $cart->id)->get();
        return view('buyer.carts.edit', compact('cart', 'cartItems'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'cart_item_id' => 'required|exists:cart_items,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = Cart::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $cartItem = CartItem::where('id', $request->cart_item_id)
            ->where('cart_id', $cart->id)
            ->firstOrFail();

        $product = Product::findOrFail($cartItem->product_id);
        if ($product->stock < $request->quantity) {
            return redirect()->route('buyer.carts.edit', $cart->id)
                ->with('error', 'Insufficient stock for ' . $product->name);
        }

        $cartItem->quantity = $request->quantity;
        $cartItem->save();

        return redirect()->route('buyer.carts.index')
            ->with('success', 'Cart updated successfully.');
    }

public function checkout()
{
    $cart = Cart::where('user_id', Auth::id())->firstOrFail();
    $cartItems = CartItem::with('product')->where('cart_id', $cart->id)->get();

    if ($cartItems->isEmpty()) {
        return redirect()->route('buyer.carts.index')
            ->with('error', 'Giỏ hàng trống, không thể checkout.');
    }

    $lineItems = [];
    $totalPrice = 0;

    foreach ($cartItems as $item) {
        $totalPrice += $item->price * $item->quantity;

        $lineItems[] = [
            'price_data' => [
                'currency' => 'vnd', // đổi sang vnd nếu Stripe account của mày support
                'product_data' => [
                    'name' => $item->product->name,
                ],
                'unit_amount' => $item->price * 100, // stripe tính theo cents
            ],
            'quantity' => $item->quantity,
        ];
    }

    \Stripe\Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

    $session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'], // fix lỗi hồi nãy
        'line_items' => $lineItems,
        'mode' => 'payment',
        'success_url' => route('pages.checkouts.success', [], true),
        'cancel_url' => route('pages.checkouts.cancel', [], true),
    ]);

    // Tạo order
    $order = new Order();
    $order->buyer_id = Auth::id();
    $order->status = 'unpaid';
    $order->total_price = $totalPrice;
    $order->session_id = $session->id;
    $order->save();

    // Lưu từng item
    foreach ($cartItems as $item) {
        $order->items()->create([
            'product_id' => $item->product_id,
            'quantity' => $item->quantity,
            'price' => $item->price,
        ]);
    }

    return redirect($session->url);
}

public function success()
{
  return view('pages.checkouts.success');
}
public function cancel()
{
return view('pages.checkouts.cancel');
}




}