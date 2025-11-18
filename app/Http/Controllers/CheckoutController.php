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
use App\Models\Notification;
use Stripe\Refund;
use App\Models\InventoryTransaction;

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
            return redirect()->route('buyer.carts.index')
                ->with('error', 'Số lượng tồn kho không đủ cho: ' . $item->product->name);
        }
    }

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
        'metadata' => [
            'user_id' => $user->id,
            // Lưu ID của các cart items đã chọn
            'selected_cart_item_ids' => json_encode($selectedCartItemIds),
            'buyer_name' => $user->name,
            'buyer_email' => $user->email,
            'buyer_phone' => $user->phoneNumber ?? 'N/A',
            'shipping_address' => $user->address ?? 'Chưa cung cấp',
        ]
    ]);

    
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
        return view('buyer.checkouts.cancel')->with('success', 'Thanh toán đã bị hủy. Giỏ hàng của bạn vẫn được giữ nguyên.');
    }

    public function webhook()
{
    $endpoint_secret = env('STRIPE_WEBHOOK_SECRET');
    $payload = @file_get_contents('php://input');
    $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
    $event = null;

    try {
        $event = \Stripe\Webhook::constructEvent(
            $payload, $sig_header, $endpoint_secret
        );
    } catch (\UnexpectedValueException $e) {
        return response('', 400);
    } catch (\Stripe\Exception\SignatureVerificationException $e) {
        return response('', 400);
    }

    switch ($event->type) {
        case 'checkout.session.completed':
            $session = $event->data->object;

            // *** SỬA ĐỔI: Chống xử lý trùng lặp ***
            // (Kiểm tra xem session này đã được xử lý chưa)
            if (Order::where('session_id', $session->id)->exists()) {
                return response('Webhook Handled', 200);
            }
            
            // *** SỬA ĐỔI: Lấy metadata ra ***
            $metadata = $session->metadata;
            $userId = $metadata->user_id;
            $selectedCartItemIds = json_decode($metadata->selected_cart_item_ids, true);
            
            // Lấy lại các cart items từ metadata
            $cartItems = CartItem::with('product')
                ->whereIn('id', $selectedCartItemIds)
                ->get();
            
            DB::beginTransaction();
            try 
            {
                // *** SỬA ĐỔI: Logic kiểm tra stock (Final check) ***
                $hasInsufficientStock = false;
                $insufficientItems = [];
                foreach($cartItems as $item)
                {
                    // Phải tải lại (refresh) để lấy stock mới nhất
                    $item->product->refresh(); 
                    if(!$item->product || $item->product->stock < $item->quantity)
                    {
                        $hasInsufficientStock = true;
                        $insufficientItems[] = $item->product ? $item->product->name : 'Unknown Product';
                    }
                }

                // Group theo seller
                $itemsBySeller = $cartItems->groupBy('product.seller_id');

                // *** SỬA ĐỔI: Bắt đầu tạo Order TỪ ĐÂY ***
                foreach ($itemsBySeller as $sellerId => $sellerItems) 
                {
                    $orderTotal = $sellerItems->sum(fn($item) => $item->price * $item->quantity);

                    // Tạo đơn hàng
                    $order = Order::create([
                        'user_id' => $userId,
                        'seller_id' => $sellerId,
                        'total_price' => $orderTotal,
                        'session_id' => $session->id, // <-- Dùng để chống trùng lặp
                        'payment_status' => 'paid', // <-- Luôn luôn là paid
                        'buyer_name' => $metadata->buyer_name,
                        'buyer_email' => $metadata->buyer_email,
                        'buyer_phone' => $metadata->buyer_phone,
                        'shipping_address' => $metadata->shipping_address,
                        
                        // Quyết định trạng thái đơn hàng dựa trên stock
                        'status' => $hasInsufficientStock ? 'Cancelled' : 'Pending',
                        'cancellation_reason' => $hasInsufficientStock 
                            ? 'Insufficient stock after payment: ' . implode(', ', $insufficientItems) 
                            : null,
                    ]);
                    
                    // Tạo OrderItems
                    foreach ($sellerItems as $item) {
                        $order->items()->create([
                            'product_id' => $item->product_id,
                            'quantity' => $item->quantity,
                            'price' => $item->price,
                        ]);
                        
                        // *** CHỈ TRỪ KHO KHI ĐỦ HÀNG ***
                        if (!$hasInsufficientStock) {
            
                        // 1. TRỪ KHO VẬT LÝ (Update 'stock' column)
                        $item->product->decrement('stock', $item->quantity);
                        
                        // 2. GHI LOG GIAO DỊCH (Insert into inventory_transactions)
                        InventoryTransaction::create([
                            'product_id' => $item->product_id,
                            'seller_id' => $sellerId, // Lấy từ vòng lặp ngoài
                            'transaction_type' => 'sale', // Loại giao dịch: BÁN HÀNG
                            'quantity' => $item->quantity, 
                            'notes' => "Xuất kho bán hàng tự động cho đơn hàng #" . $order->id,
                        ]);
                    }
                    }

                    // Gửi thông báo cho seller
                    if (!$hasInsufficientStock) {
                        Notification::create([
                            'user_id' => $order->seller_id,
                            'type' => 'new_order',
                            'message' => "Bạn có đơn hàng mới #{$order->id} từ {$order->buyer_name} với tổng giá " . number_format($order->total_price) . " VND",
                            'is_read' => false,
                        ]);
                    }
                } // Kết thúc vòng lặp seller

                // Nếu thiếu hàng, thực hiện Refund
                if ($hasInsufficientStock) {
                    Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
                    Refund::create([
                        'payment_intent' => $session->payment_intent,
                        'reason' => 'requested_by_customer', // hoặc 'other'
                        'metadata' => [
                            'reason' => 'Insufficient stock after payment',
                            'session_id' => $session->id,
                        ]
                    ]);
                }

                CartItem::whereIn('id', $selectedCartItemIds)->delete();

                DB::commit();

            } catch (\Exception $e) {
                DB::rollBack();
                // Ghi log lỗi nghiêm trọng
                \Log::error('Stripe Webhook Error: ' . $e->getMessage(), ['session_id' => $session->id]);
                // Trả về lỗi 500 để Stripe thử lại
                return response('Webhook Error', 500);
            }
            break;

        default:
            echo 'Received unknown event type ' . $event->type;
    }

    return response('');
}
}