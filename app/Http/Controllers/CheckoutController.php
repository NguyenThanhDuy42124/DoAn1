<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Notifications\NewOrderNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Checkout\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CheckoutController extends Controller
{
  
    public function checkout()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để thanh toán.');
        }

        $user = Auth::user();
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);

        $selectedCartItemIds = session('selected_cart_items', []);
        if (empty($selectedCartItemIds)) {
            return redirect()->route('buyer.carts.index')
                ->with('error', 'Không có sản phẩm nào được chọn để thanh toán.');
        }

        $cartItems = CartItem::with('product')
            ->where('cart_id', $cart->id)
            ->whereIn('id', $selectedCartItemIds)
            ->get();

        if ($cartItems->isEmpty()) {
            session()->forget('selected_cart_items');
            return redirect()->route('buyer.carts.index')
                ->with('error', 'Giỏ hàng trống hoặc sản phẩm không hợp lệ.');
        }

        foreach ($cartItems as $item) {
            if ($item->quantity > $item->product->stock || $item->product->stock <= 0) {
                session()->forget('selected_cart_items');
                return redirect()->route('buyer.carts.index')
                    ->with('error', 'Số lượng tồn kho không đủ cho: ' . $item->product->name);
            }
        }

        $itemsBySeller = $cartItems->groupBy(function ($item) {
    return $item->product->seller_id;
});

        $lineItems = [];
        foreach ($cartItems as $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'vnd',
                    'product_data' => ['name' => $item->product->name],
                    'unit_amount' => $item->price,
                ],
                'quantity' => $item->quantity,
            ];
        }

        Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

        $stripeCustomer = Customer::create([
            'email' => $user->email,
            'name' => $user->name,
        ]);

        $checkoutSession = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => route('buyer.checkouts.success', [], true) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('buyer.checkouts.cancel', [], true) . '?session_id={CHECKOUT_SESSION_ID}',
            'customer' => $stripeCustomer->id,
        ]);


        foreach ($itemsBySeller as $sellerId => $sellerItems) {
            $orderTotal = $sellerItems->sum(fn($item) => $item->price * $item->quantity);

            $order = Order::create([
                'user_id' => $user->id,
                'status' => 'Pending',
                'payment_status' => 'unpaid',
                'total_price' => $orderTotal,
                'seller_id' => $sellerId,
                'buyer_name' => $user->name,
                'buyer_email' => $user->email,
                'buyer_phone' => $user->phoneNumber ?? 'N/A',
                'session_id' => $checkoutSession->id,
                'shipping_address' => $user->address ?? 'Chưa cung cấp',
            ]);

            foreach ($sellerItems as $item) {
                $order->items()->create([
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                ]);
            }
        }

        session()->forget('selected_cart_items');

        return redirect($checkoutSession->url);
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
                    $order->payment_status='paid';
                    $order->save();
                    $hasInsufficientStock = false;

                    foreach($order->items as $item)
                    {
                        $product = $item->product;
                        if($product)
                        {
                            if($product->stock < $item->quantity)
                            {
                                $hasInsufficientStock = true;
                            }
                            $product->stock -= $item->quantity;
                            $product->save();
                        }
                    }

                    if ($hasInsufficientStock)
                    {
                        $order->status = 'Cancelled';
                        $order->cancellation_reason = 'Insuffcient stock after payment';
                        $order->save();
                    }
                    else
                    {
                        $order->status = 'Pending';
                        $order->save();
                    }
                }
                }
            

            return view('buyer.checkouts.success', compact('customer'));
        } catch (\Exception $e) {
            throw new NotFoundHttpException();
        }
    }

    public function cancel(Request $request)
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
        $sessionId = $request->query('session_id');

        if ($sessionId)
        {
            $session = \Stripe\Checkout\Session::retrieve($sessionId);
            if($session)
            {
                $orders = Order::where('session_id', $session->id)
                    ->where('status', 'Pending')
                    ->where('payment_status', 'unpaid')
                    ->get();
                
                    foreach($orders as $order)
                    {
                        $order->status = 'Cancelled';
                        $order->cancellation_reason = 'User cancelled payment';
                        $order->save();
                    }

                    foreach ($order->items as $item)
                    {
                        $product = $item->product;
                        if($product)
                        {
                            $product->stock += $item->quantity;
                            $product->save();
                        }
                    }
            }
        }

        return view('buyer.checkouts.cancel')->with('success', 'Đơn hàng đã bị hủy.');
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