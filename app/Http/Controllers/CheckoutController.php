<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Voucher;
// use App\Models\VoucherUsage; // <-- XÓA: Không cần model này nữa
use App\Models\InventoryTransaction;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Checkout\Session;
use Stripe\PaymentIntent;
use Stripe\Refund;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CheckoutController extends Controller
{
    // 1. TRANG REVIEW (Giữ nguyên)
    public function review()
    {
        if (!Auth::check()) return redirect()->route('login');

        // Hàm này sẽ tính toán lại mọi thứ mỗi khi F5 trang
        $data = $this->calculateOrderData(Auth::user());

        if (isset($data['error'])) {
            return redirect()->route('buyer.carts.index')->with('error', $data['error']);
        }

        return view('buyer.checkouts.review', [
            'ordersBySeller' => $data['ordersBySeller'],
            'grandTotal' => $data['grandTotal'],
            'shippingAddress' => Auth::user()->address ?? 'Chưa cập nhật',
            'user' => Auth::user()
        ]);
    }

    // 2. ÁP DỤNG VOUCHER (Đã sửa: Bỏ check lịch sử dùng)
    public function applyVoucher(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'seller_id' => 'required|integer'
        ]);

        $code = strtoupper(trim($request->code));
        $sellerId = $request->seller_id;

        // Tìm voucher còn hiệu lực (active) của shop đó
        $voucher = Voucher::where('code', $code)
            ->where('seller_id', $sellerId)
            ->where('is_active', true)
            ->first();

        if (!$voucher) {
            return back()->with('error', 'Mã giảm giá không tồn tại ở shop này.');
        }

        // Helper isValid() trong Model Voucher giờ chỉ cần check:
        // 1. Thời gian (Hết hạn chưa?)
        // 2. Số lượng tổng (Hết lượt chưa?)
        if (!$voucher->isValid()) {
            return back()->with('error', 'Mã giảm giá đã hết hạn hoặc hết số lượng.');
        }

        // --- ĐÃ XÓA ĐOẠN CHECK USER_ID ĐÃ DÙNG CHƯA ---

        // Lưu vào Session
        $appliedVouchers = session('applied_vouchers', []);
        $appliedVouchers[$sellerId] = $code;
        session(['applied_vouchers' => $appliedVouchers]);

        // Reload lại trang (back) để hàm review() tính toán lại giá
        return back()->with('success', 'Đã áp mã ' . $code);
    }

    // 3. GỬ VOUCHER (Giữ nguyên)
    public function removeVoucher(Request $request)
    {
        $sellerId = $request->seller_id;
        $appliedVouchers = session('applied_vouchers', []);

        if (isset($appliedVouchers[$sellerId])) {
            unset($appliedVouchers[$sellerId]);
            session(['applied_vouchers' => $appliedVouchers]);
        }

        return back()->with('success', 'Đã gỡ mã.');
    }

    // 4. PROCESS PAYMENT (Giữ nguyên logic, chỉ cập nhật metadata)
    public function processPayment()
    {
        if (!Auth::check()) return redirect()->route('login');
        $user = Auth::user();
        $data = $this->calculateOrderData($user);

        if (isset($data['error'])) return redirect()->route('buyer.carts.index')->with('error', $data['error']);

        $ordersBySeller = $data['ordersBySeller'];
        $selectedCartItemIds = $data['selectedCartItemIds'];

        $lineItems = [];
        $metadataVouchers = [];

        foreach ($ordersBySeller as $sellerId => $group) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'vnd',
                    'product_data' => [
                        'name' => "Đơn hàng từ shop: " . $group['seller_name'],
                        'description' => "Gồm " . count($group['items']) . " sản phẩm",
                    ],
                    'unit_amount' => $group['final_total'],
                ],
                'quantity' => 1,
            ];

            // Lưu voucher vào metadata để webhook xử lý
            if ($group['voucher']) {
                $metadataVouchers[$sellerId] = [
                    'voucher_id' => $group['voucher']->id,
                    'discount_amount' => $group['discount_amount'],
                ];
            }
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
                'selected_cart_item_ids' => json_encode($selectedCartItemIds),
                'buyer_name' => $user->name,
                'buyer_email' => $user->email,
                'buyer_phone' => $user->phoneNumber ?? 'N/A',
                'shipping_address' => $user->address ?? 'Chưa cung cấp',
                'applied_vouchers_json' => json_encode($metadataVouchers),
            ]
        ]);

        return redirect($checkoutSession->url);
    }

    // 5. HELPER TÍNH TOÁN (Quan trọng: Logic tính giá nằm ở đây)
    private function calculateOrderData($user)
    {
        // ... (Phần lấy CartItems giữ nguyên như cũ) ...
        $selectedCartItemIds = session('selected_cart_items', []);
        if (empty($selectedCartItemIds)) return ['error' => 'Chưa chọn sản phẩm.'];

        $cart = Cart::firstOrCreate(['user_id' => $user->id]);
        $cartItems = CartItem::with(['product.seller', 'product.images']) // <--- Thêm cái này
            ->where('cart_id', $cart->id)
            ->whereIn('id', $selectedCartItemIds)->get();

        if ($cartItems->isEmpty()) return ['error' => 'Giỏ hàng lỗi.'];

        // Gom nhóm
        $ordersBySeller = [];
        $appliedVouchersSession = session('applied_vouchers', []);

        foreach ($cartItems as $item) {
            // Check stock
            $item->product->refresh();
            if ($item->quantity > $item->product->stock) return ['error' => "Hết hàng: {$item->product->name}"];

            $sellerId = $item->product->seller_id;
            if (!isset($ordersBySeller[$sellerId])) {
                $ordersBySeller[$sellerId] = [
                    'seller_id' => $sellerId,
                    'seller_name' => $item->product->seller->name ?? 'Shop',
                    'items' => [],
                    'subtotal' => 0,
                    'discount_amount' => 0,
                    'final_total' => 0,
                    'voucher' => null,
                    'voucher_error' => null,
                ];
            }
            $ordersBySeller[$sellerId]['items'][] = $item;
            $ordersBySeller[$sellerId]['subtotal'] += $item->price * $item->quantity;
        }

        $grandTotal = 0;

        // Tính toán Voucher
        foreach ($ordersBySeller as $sellerId => &$group) {
            $subtotal = $group['subtotal'];
            $group['available_vouchers'] = Voucher::where('seller_id', $sellerId)
                ->where('is_active', true)
                ->get()
                ->filter(function ($v) use ($subtotal) {
                    return $v->isValid() && $subtotal >= $v->min_order_value;
                })
                ->values();

            if (isset($appliedVouchersSession[$sellerId])) {
                $code = $appliedVouchersSession[$sellerId];
                // Tìm voucher active (không cần check used_count của user nữa)
                $voucher = Voucher::where('code', $code)->where('seller_id', $sellerId)->first();

                // ... bên trong vòng lặp foreach ($ordersBySeller ...)

                if ($voucher && $voucher->isValid()) {
                    if ($subtotal >= $voucher->min_order_value) {

                        // 1. Tính toán giá trị giảm sơ bộ
                        $discount = 0;
                        $voucherValue = (float)$voucher->value; // Ép kiểu float để tính toán

                        if ($voucher->type === 'fixed') {
                            $discount = $voucherValue;
                        } elseif ($voucher->type === 'percent') {
                            $discount = ($subtotal * $voucherValue) / 100;
                        }

                        // 2. Kiểm tra giảm tối đa (QUAN TRỌNG: Phải check > 0)
                        $maxDiscount = (float)$voucher->max_discount_amount;

                        if (
                            $voucher->type === 'percent'
                            && $maxDiscount > 0  // <--- THÊM DÒNG NÀY ĐỂ FIX LỖI 0đ
                            && $discount > $maxDiscount
                        ) {

                            $discount = $maxDiscount;
                        }

                        // 3. Đảm bảo không giảm quá tiền hàng
                        if ($discount > $subtotal) {
                            $discount = $subtotal;
                        }

                        $group['discount_amount'] = $discount;
                        $group['voucher'] = $voucher;
                    } else {
                        $group['voucher_error'] = "Đơn tối thiểu " . number_format($voucher->min_order_value) . "đ";
                    }
                }
            }

            $group['final_total'] = $subtotal - $group['discount_amount'];
            $grandTotal += $group['final_total'];
        }

        return [
            'ordersBySeller' => $ordersBySeller,
            'grandTotal' => $grandTotal,
            'selectedCartItemIds' => $selectedCartItemIds
        ];
    }

    // 6. WEBHOOK (Đã sửa: Bỏ lưu voucher_usage)
    public function webhook()
    {
        $endpoint_secret = env('STRIPE_WEBHOOK_SECRET');
        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event = null;

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
        } catch (\Exception $e) {
            return response('', 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
            if (Order::where('session_id', $session->id)->exists()) return response('Handled', 200);

            $metadata = $session->metadata;
            $userId = $metadata->user_id;
            $selectedCartItemIds = json_decode($metadata->selected_cart_item_ids, true);
            $appliedVouchersInfo = json_decode($metadata->applied_vouchers_json ?? '[]', true);

            $cartItems = CartItem::with('product')->whereIn('id', $selectedCartItemIds)->get();

            DB::beginTransaction();
            try {
                $hasInsufficientStock = false;
                foreach ($cartItems as $item) {
                    $item->product->refresh();
                    if ($item->product->stock < $item->quantity) $hasInsufficientStock = true;
                }

                $itemsBySeller = $cartItems->groupBy('product.seller_id');

                // =========================
                // THÊM ĐOẠN LẤY CHARGE ID
                // =========================
                Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
                $paymentIntentId = $session->payment_intent ?? null;
                $chargeId = null;
                $fee = 0;

                if ($paymentIntentId) {
                    try {
                        $pi = \Stripe\PaymentIntent::retrieve($paymentIntentId);
                        $chargeId = $pi->charges->data[0]->id ?? null;
                        $charge = \Stripe\Charge::retrieve($chargeId);
                        $balance = \Stripe\BalanceTransaction::retrieve($charge->balance_transaction);
                        $fee = $balance->fee / 100;
                    } catch (\Throwable $e) {
                        \Log::warning('Could not retrieve PaymentIntent: ' . $e->getMessage());
                    }
                }

                // =========================
                // KẾT THÚC ĐOẠN THÊM
                // =========================

                foreach ($itemsBySeller as $sellerId => $sellerItems) {
                    $subtotal = $sellerItems->sum(fn($item) => $item->price * $item->quantity);

                    // Voucher info
                    $voucherInfo = $appliedVouchersInfo[$sellerId] ?? null;
                    $discountAmount = $voucherInfo ? $voucherInfo['discount_amount'] : 0;
                    $voucherId = $voucherInfo ? $voucherInfo['voucher_id'] : null;
                    $totalPrice = $subtotal - $discountAmount;

                    $order = Order::create([
                        'user_id' => $userId,
                        'seller_id' => $sellerId,
                        'status' => $hasInsufficientStock ? 'Cancelled' : 'Pending',
                        'payment_status' => 'paid',
                        'session_id' => $session->id,
                        'transaction_id' => $paymentIntentId,
                        'charge_id' => $chargeId, // <-- thêm
                        'transaction_fee' => $fee, // <-- thêm (nullable)
                        'subtotal' => $subtotal,
                        'discount_amount' => $discountAmount,
                        'voucher_id' => $voucherId,
                        'total_price' => $totalPrice,
                        'buyer_name' => $metadata->buyer_name,
                        'buyer_email' => $metadata->buyer_email,
                        'buyer_phone' => $metadata->buyer_phone,
                        'shipping_address' => $metadata->shipping_address,
                    ]);

                    foreach ($sellerItems as $item) {
                        $order->items()->create([
                            'product_id' => $item->product_id,
                            'quantity' => $item->quantity,
                            'price' => $item->price,
                        ]);

                        if (!$hasInsufficientStock) {
                            $item->product->decrement('stock', $item->quantity);
                            InventoryTransaction::create([
                                'product_id' => $item->product_id,
                                'seller_id' => $sellerId,
                                'transaction_type' => 'sale',
                                'quantity' => $item->quantity,
                                'notes' => "Đơn hàng #{$order->id}"
                            ]);
                        }
                    }

                    // *** CHỈ CỘNG SỐ LƯỢNG ĐÃ DÙNG CỦA VOUCHER (Không lưu ai dùng) ***
                    if (!$hasInsufficientStock && $voucherId) {
                        Voucher::where('id', $voucherId)->increment('used_count');
                    }

                    if (!$hasInsufficientStock) {
                        Notification::create([
                            'user_id' => $sellerId,
                            'type' => 'new_order',
                            'message' => "Đơn hàng #{$order->id}",
                            'is_read' => false,
                        ]);
                        // =================================================================
                        // 🔴 BẮT ĐẦU XỬ LÝ VÍ HỆ THỐNG (MÔ HÌNH GIỮ TIỀN - ESCROW)
                        // =================================================================

                        // 1. Tìm ví trung gian (System Wallet) - ID 12
                        // Lưu ý: Phải chắc chắn trong Database bảng wallets đã có dòng user_id = 99 nha!
                        $systemWallet = \App\Models\Wallet::where('user_id', 12)->lockForUpdate()->first();

                        if ($systemWallet) {
                            // Cộng TOÀN BỘ tiền vào ví trung gian
                            $systemWallet->balance += $totalPrice;
                            $systemWallet->save();

                            // Ghi lịch sử: Tiền đang tạm giữ
                            \App\Models\Transaction::create([
                                'wallet_id' => $systemWallet->id,
                                'amount' => $totalPrice,
                                'type' => 'deposit',
                                'description' => "Tạm giữ tiền đơn hàng #{$order->id} (Chờ hoàn thành)",
                                'reference_id' => $order->id
                            ]);
                        } else {
                            // Log lỗi để biết đường mà sửa nếu quên tạo ví cho thằng ID 99
                            \Log::error("Không tìm thấy ví hệ thống cho User ID 99");
                        }

                        // 🔴 CẤM: Không cộng tiền Seller ở đây
                        // 🔴 CẤM: Không cộng tiền Admin ở đây (Xóa luôn đoạn Admin phía dưới đi)

                        // =================================================================
                        // 🔴 KẾT THÚC XỬ LÝ VÍ
                        // =================================================================
                    }
                }



                if ($hasInsufficientStock) {
                    Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
                    Refund::create(['payment_intent' => $session->payment_intent]);
                }

                CartItem::whereIn('id', $selectedCartItemIds)->delete();
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error($e->getMessage());
                return response('Error', 500);
            }
        }
        return response('');
    }

    // Cancel & Success giữ nguyên...
    public function cancel(Request $request)
    {
        return view('buyer.checkouts.cancel');
    }
    public function success(Request $request)
    {
        return view('buyer.checkouts.success');
    }
}
