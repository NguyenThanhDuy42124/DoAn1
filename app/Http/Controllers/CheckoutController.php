<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CheckoutController extends Controller
{
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
        'user_id' => $user->id,
        'status' => 'Pending',
        'payment_status' => 'unpaid',
        'product_id' => $sellerItems->first()->product_id,
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
                if($order && $order->payment_status === 'unpaid')
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
                if($order && $order->payment_status === 'unpaid')
            {
                $order->payment_status='paid';
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
