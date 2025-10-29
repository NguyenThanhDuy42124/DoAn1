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
            if (!$session)
            {
                throw new NotFoundHttpException();
            }
            $customer = \Stripe\Customer::retrieve($session->customer);
            return view ('buyer.checkouts.success', compact('customer'));
        } catch (\Exception $e) {
            report($e);
            throw new NotFoundHttpException();
        }
    }

    public function cancel(Request $request)
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
        $sessionId = $request->query('session_id');

        if ($sessionId)
        {
            
            try {
                $session = \Stripe\Checkout\Session::retrieve($sessionId);
                if($session)
                {
                    $orders=Order::with('items.product')
                    ->where('session_id', $session->id)
                    ->where('status', 'Pending')
                    ->where('payment_status', 'unpaid')
                    ->get();
                    foreach($orders as $order)
                    {
                        $order->status = 'Cancelled';
                        $order->cancellation_reason = 'User cancelled payment';
                        $order->save(); 
                        foreach($order->items as $item)
                        {
                            if($item->product)
                            {
                                $item->product->stock += $item->quantity;
                                $item->product->save();
                            }
                        }
                    }
                    
                }
            } catch (\Exception $e)
            {
                report($e);
                return view('buyer.checkouts.cancel')->with('error', 'Không thể xác thực phiên hủy.');
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

        switch ($event->type) {
            case 'checkout.session.completed':
                $session = $event->data->object;

                $orders = Order::with('items.product')
                    ->where('session_id', $session->id)
                    ->where('payment_status', 'unpaid') // Chỉ xử lý đơn chưa thanh toán
                    ->get();
                
                foreach($orders as $order)
                {
                    // Đã được xử lý bởi một webhook call khác rồi thì bỏ qua
                    if($order->payment_status !== 'unpaid') {
                        continue;
                    }

                    $order->payment_status = 'paid';
                    // Tạm thời chưa lưu, chờ kiểm tra stock

                    $hasInsufficientStock = false;
                    $insufficientItems = [];

                    // 1. Vòng lặp KIỂM TRA stock trước
                    foreach($order->items as $item)
                    {
                        if(!$item->product || $item->product->stock < $item->quantity)
                        {
                            $hasInsufficientStock = true;
                            $insufficientItems[] = $item->product ? $item->product->name : 'Unknown Product';
                        }
                    }

                    // 2. Quyết định dựa trên kết quả kiểm tra
                    if ($hasInsufficientStock)
                    {
                        // Nếu hết hàng -> Hủy đơn và GHI LÝ DO
                        $order->status = 'Cancelled';
                        $order->cancellation_reason = 'Insufficient stock after payment: ' . implode(', ', $insufficientItems);
                        $order->save();
                        
                        // TODO:gọi API Stripe để refund đơn hàng này
                    }
                    else
                    {
                        // Nếu đủ hàng -> Trừ kho và xác nhận đơn
                        $order->status = 'Pending';
                        
                        foreach($order->items as $item)
                        {
                            // $item->product đã được load sẵn
                            $item->product->stock -= $item->quantity;
                            $item->product->save();
                        }

                        $order->save(); // Lưu đơn hàng sau khi đã trừ kho thành công

                        // Gửi notification cho seller
                        \App\Models\Notification::create([
                            'user_id' => $order->seller_id,
                            'type' => 'new_order',
                            'message' => "Bạn có đơn hàng mới #{$order->id} từ {$order->buyer_name} với tổng giá " . number_format($order->total_price) . " VND",
                            'is_read' => false,
                        ]);
                    }
                }
                break;

            default:
                echo 'Received unknown event type ' . $event->type;
        }

        return response('');

    }
}