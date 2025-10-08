<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CartController extends Controller
{
    

    public function index()
    {
        $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);
    $cartItems = CartItem::with('product')->where('cart_id', $cart->id)->get();

    foreach ($cartItems as $item) {
        $productStock = $item->product->stock;

        if ($productStock == 0) {
            $item->stock_status = 'out_of_stock';
            $item->quantity=0;
        } elseif ($item->quantity > $productStock) {
            $item->quantity = $productStock; // auto chỉnh quantity về max stock
            $item->save();
            $item->stock_status = 'limited_stock';
        } else {
            $item->stock_status = 'in_stock';
        }
    }

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

   public function purchaseHistory()
{
    $user = Auth::user();
    
    // Fetch orders for the authenticated buyer
    $orders = Order::where('buyer_id', $user->id)
        ->with('items.product') // Assuming Order has a relationship to OrderItem and Product
        ->orderBy('created_at', 'desc')
        ->get();

    // Gộp theo session_id + seller_id
    $ordersGrouped = $orders->groupBy(function($order) {
        return $order->session_id . '-' . $order->seller_id;
    });
    
    // Truyền $ordersGrouped xuống view thay vì $orders
    return view('buyer.checkouts.purchase_history', compact('ordersGrouped'));
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

    foreach($cartItems as $item)
    {
        if($item->quantity > $item->product->stock)
        {
            return redirect()->route('buyer.carts.index')->with('error', 'Insufficient stock for '.$item->product->name . ' please update your cart.');
        }
    }

    $itemsBySeller = $cartItems->groupBy(fn($item)=>$item->product->seller_id);

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
                'unit_amount' => $item->price, // stripe tính theo cents
            ],
            'quantity' => $item->quantity,
        ];
    }

    \Stripe\Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

    $customer = \Stripe\Customer::create([
    'email' => Auth::user()->email,
    'name'  => Auth::user()->name,
]);
    $session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'], // fix lỗi hồi nãy
        'line_items' => $lineItems,
        'mode' => 'payment',
        'success_url' => route('buyer.checkouts.success', [], true)."?session_id={CHECKOUT_SESSION_ID}",
        'cancel_url' => route('buyer.checkouts.cancel', [], true),
        'customer' => $customer->id,
    ]);

    // Tạo order

    $user = Auth::user();   
    
    foreach($itemsBySeller as $sellerId => $sellerItems)
    {
        $orderTotal = $sellerItems->sum(fn($item)=>$item->price* $item->quantity);
        $order = Order::create([
        'buyer_id' => $user->id,
        'status' => 'unpaid',
        'total_price' => $orderTotal,
        'seller_id' => $sellerId,
        'buyer_name' => $user->name,
        'buyer_email' => $user->email,
        'buyer_phone' => $user->phoneNumber,
        'session_id' => $session->id,
        'shipping_address' => $user->address, // Allow override at checkout  
        // Other order details: total, items (via relationships), etc.
    ]);
     // Lưu từng item
        foreach ($sellerItems as $item) {
        $order->items()->create([
            'product_id' => $item->product_id,
            'quantity' => $item->quantity,
            'price' => $item->price,
        ]);
    }
    }
    

    return redirect($session->url);
}

public function success(Request $request)
{
    \Stripe\Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
    $sessionId = $request->get('session_id');

    try {
        $session = \Stripe\Checkout\Session::retrieve($sessionId);

        if (!$session) {
            throw new NotFoundHttpException;
        }

        $customer = \Stripe\Customer::retrieve($session->customer);

        $orders = Order::with('items.product')
                ->where('session_id', $session->id)
                ->get();
            foreach($orders as $order)
            {
                if($order && $order->status === 'unpaid')
            {
                $order->status='paid';
                $order->save();
                foreach($order->items as $item)
                {
                    $product = $item->product;
                    if($product)
                    {
                        if($product->stock < $item->quantity)
                        {
                            continue;
                        }
                        $product->stock -= $item->quantity;
                        $product->save();
                    }
                }
            }
            }
        

        return view('buyer.checkouts.success', compact('customer'));
    } catch (\Exception $e) {
        throw new NotFoundHttpException();
    }
}

public function cancel()
{
    return view('buyer.checkouts.cancel');
}

public function webhook()
{
    // This is your Stripe CLI webhook secret for testing your endpoint locally.
    $endpoint_secret = env('STRIPE_WEBHOOK_SECRET');
    $payload = @file_get_contents('php://input');
    $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
    $event = null;

    try {
        $event = \Stripe\Webhook::constructEvent(
            $payload,
            $sig_header,
            $endpoint_secret
        );
    } catch (\UnexpectedValueException $e) {
        // Invalid payload
        return response('', 400);
    } catch (\Stripe\Exception\SignatureVerificationException $e) {
        // Invalid signature
        return response('', 400);
    }

    // Handle the event
    switch ($event->type) {
        case 'checkout.session.completed':
            $session = $event->data->object;

            $orders = Order::with('items.product')
                ->where('session_id', $session->id)
                ->get();
            foreach($orders as $order)
            {
                if($order && $order->status === 'unpaid')
            {
                $order->status='paid';
                $order->save();
                foreach($order->items as $item)
                {
                    $product = $item->product;
                    if($product)
                    {
                        if($product->stock < $item->quantity)
                        {
                            continue;
                        }
                        $product->stock -= $item->quantity;
                        $product->save();
                    }
                }
            }
            }
            
            // chỗ này mày có thể gửi mail hoặc notification
            // Send email to customer
            // ...
            break;

        default:
            echo 'Received unknown event type ' . $event->type;
    }

    return response('');
}

}


