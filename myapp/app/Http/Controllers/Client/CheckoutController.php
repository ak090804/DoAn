<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItems;
use App\Models\SavedCart;
use App\Models\Customer;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use App\Models\KhuyenMai;
use App\Jobs\UpdateRecommendationsForOrder;
use PayOS\PayOS;
use Illuminate\Support\Facades\Log; // Thêm Log để ghi lỗi

class CheckoutController extends Controller
{
    /**
     * Show checkout page
     */
    public function show()
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return redirect()->route('login');
        }

        $cart = session()->get('cart', []);
        if (count($cart) === 0) {
            return redirect()->route('client.cart')->with('message', 'Giỏ hàng trống');
        }

        $user = User::find($userId);
        if (!$user) {
            return redirect()->route('login');
        }

        $total = 0;
        foreach ($cart as $item) {
            $qty = isset($item['quantity']) ? intval($item['quantity']) : 0;
            $price = isset($item['price']) ? floatval($item['price']) : 0;
            $chargeQty = $qty;
            if (!empty($item['is_bogo'])) {
                $chargeQty = (int) ceil($qty / 2);
            }
            $total += $price * $chargeQty;
        }

        $allVouchers = KhuyenMai::active()->where('loai', 'giam_gia_hd')->where('is_private', false)->get();
        $vouchers = $allVouchers->filter(function ($v) use ($total) {
            $discount = floatval($v->gia_tri ?? 0);
            if ($v->so_tien_giam_toi_da) {
                $discount = min($discount, floatval($v->so_tien_giam_toi_da));
            }
            $percent = $total > 0 ? ($discount / $total) * 100 : 0;
            return $percent <= 10;
        });

        return view('client.checkout.index', [
            'cart' => $cart,
            'total' => $total,
            'user' => $user,
            'vouchers' => $vouchers
        ]);
    }

    /**
     * Store order (xử lý thanh toán)
     */
    public function store(Request $request)
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Vui lòng đăng nhập'], 401);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'note' => 'nullable|string|max:1000'
        ]);

        $cart = session()->get('cart', []);
        if (count($cart) === 0) {
            return response()->json(['success' => false, 'message' => 'Giỏ hàng trống'], 400);
        }

        // Bắt đầu khối Try lớn bao trùm toàn bộ logic
        try {
            // 1. Tính toán tổng tiền
            $totalPrice = 0;
            foreach ($cart as $item) {
                $qty = isset($item['quantity']) ? intval($item['quantity']) : 0;
                $price = isset($item['price']) ? floatval($item['price']) : 0;
                $chargeQty = $qty;
                if (!empty($item['is_bogo'])) {
                    $chargeQty = (int) ceil($qty / 2);
                }
                $totalPrice += $price * $chargeQty;
            }

            // 2. Lưu SavedCart
            $savedCart = SavedCart::create([
                'user_id' => $userId,
                'session_id' => session()->getId(),
                'items' => $cart,
                'total_price' => $totalPrice,
                'status' => 'pending'
            ]);

            // 3. Tạo/Update Customer
            $customer = Customer::updateOrCreate(
                ['user_id' => $userId],
                [
                    'name' => $request->name,
                    'phone' => $request->phone,
                    'address' => $request->address ?? null,
                    'user_id' => $userId
                ]
            );

            // 4. Xử lý Voucher
            $discountAmount = 0;
            $khuyenMaiId = null;
            if ($request->filled('voucher_code') || $request->filled('voucher_manual')) {
                $code = $request->input('voucher_code') ?: $request->input('voucher_manual');
                $promo = KhuyenMai::where('ma', $code)->where('loai', 'giam_gia_hd')->active()->first();
                if ($promo) {
                    $discountAmount = floatval($promo->gia_tri ?? 0);
                    if ($promo->so_tien_giam_toi_da) {
                        $discountAmount = min($discountAmount, floatval($promo->so_tien_giam_toi_da));
                    }
                    $khuyenMaiId = $promo->id;
                }
            }
            $finalTotal = max(0, $totalPrice - $discountAmount);

            $paymentMethod = $request->input('payment_method');

            // If payment method is PayOS (bank transfer), DO NOT create the Order yet.
            // Save customer info into the SavedCart items so webhook can create the Order later.
            if ($paymentMethod === 'payos') {
                $items = $savedCart->items ?? [];
                $items['_customer'] = [
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'address' => $request->address ?? null,
                    'note' => $request->note ?? null,
                    'user_id' => $userId,
                    'voucher_code' => $request->input('voucher_code') ?: $request->input('voucher_manual') ?: null,
                ];
                $savedCart->items = $items;
                $savedCart->status = 'pending_payment';
                $savedCart->save();

                // Clear session cart for user (we stored snapshot in savedCart)
                session()->forget('cart');

            } else {
                // 5. Tạo Order 
                $order = Order::create([
                    'customer_id' => $customer->id,
                    'user_id' => $userId,
                    'total_price' => $finalTotal,
                    'discount_amount' => $discountAmount,
                    'khuyen_mai_id' => $khuyenMaiId,
                    'status' => 'pending',
                    'note' => $request->note ?? null
                ]);

                // 6. Lưu OrderItems 
                foreach ($cart as $productVariantId => $item) {
                    $qty = intval($item['quantity'] ?? 0);
                    $price = floatval($item['price'] ?? 0);
                    $chargeQty = !empty($item['is_bogo']) ? (int) ceil($qty / 2) : $qty;

                    OrderItems::create([
                        'order_id' => $order->id,
                        'product_variant_id' => $productVariantId,
                        'quantity' => $qty,
                        'price' => $price,
                        'subtotal' => $price * $chargeQty
                    ]);

                    try {
                        $variant = ProductVariant::find($productVariantId);
                        if ($variant) {
                            $variant->quantity = max(0, $variant->quantity - $qty);
                            $variant->save();
                        }
                    } catch (\Exception $e) {
                        Log::error('Lỗi trừ tồn kho variant: ' . $productVariantId);
                    }
                }

                if (isset($savedCart)) {
                    $savedCart->status = 'ordered';
                    $savedCart->save();
                }

                try {
                    UpdateRecommendationsForOrder::dispatch($order->id);
                } catch (\Exception $e) {
                }

                session()->forget('cart');
            }

            // --- TÍCH HỢP PAYOS (Có Debug) ---
            if ($request->input('payment_method') === 'payos') {

                // Kiểm tra đơn 0 đồng
                if ($finalTotal <= 0) {
                    $order->status = 'paid';
                    $order->save();
                    return response()->json([
                        'success' => true,
                        'message' => 'Đơn hàng 0đ thanh toán thành công!',
                        'redirect' => route('client.orderConfirm', $order->id)
                    ]);
                }

                // Lấy cấu hình từ config (Không dùng env trực tiếp)
                $clientId = config('services.payos.client_id');
                $apiKey = config('services.payos.api_key');
                $checksumKey = config('services.payos.checksum_key');

                // DEBUG: Kiểm tra xem có lấy được key không
                if (empty($clientId) || empty($apiKey) || empty($checksumKey)) {
                    Log::error("PayOS Config Missing:", [
                        'client_id' => $clientId,
                        'api_key' => $apiKey,
                        'checksum_key' => $checksumKey
                    ]);

                    return response()->json([
                        'success' => false,
                        'message' => 'Lỗi Server: Chưa nhận cấu hình PayOS. Hãy chạy "php artisan config:clear"'
                    ], 500);
                }

                $payOS = new PayOS($clientId, $apiKey, $checksumKey);

                // Use savedCart id as orderCode for deferred payment scenarios
                $orderCode = intval($savedCart->id);
                $amount = intval($finalTotal);

                $data = [
                    "orderCode" => $orderCode,
                    "amount" => $amount,
                    "description" => "Don hang " . $orderCode,
                    "items" => [],
                    "returnUrl" => route('payment.success'),
                    "cancelUrl" => route('payment.cancel')
                ];

                try {
                    // Log request payload to help debug SDK issues
                    try {
                        Log::debug('PayOS request payload', ['data' => $data]);
                    } catch (\Throwable $_logEx) {
                        // ignore logging errors
                    }

                    $response = $payOS->createPaymentLink($data);

                    // Log raw response for debugging
                    try {
                        Log::debug('PayOS raw response', ['response' => $response]);
                    } catch (\Throwable $_logEx) {
                        // ignore logging errors
                    }

                    // Normalize response safely: support array, object, or direct URL string
                    $checkoutUrl = null;
                    if (is_array($response) && isset($response['checkoutUrl'])) {
                        $checkoutUrl = $response['checkoutUrl'];
                    } elseif (is_object($response) && isset($response->checkoutUrl)) {
                        $checkoutUrl = $response->checkoutUrl;
                    } elseif (is_string($response) && filter_var($response, FILTER_VALIDATE_URL)) {
                        $checkoutUrl = $response;
                    } elseif (is_array($response) && isset($response['data']['checkoutUrl'])) {
                        // Some SDKs wrap payload in data
                        $checkoutUrl = $response['data']['checkoutUrl'];
                    }

                    // Save PayOS meta into SavedCart->items under _payos key so we can show QR and handle webhook
                    // Normalize and persist multiple possible ID keys returned by PayOS
                    $payment_request_id = $response['id'] ?? ($response['paymentRequestId'] ?? ($response['data']['id'] ?? null));
                    $payment_link_id = $response['paymentLinkId'] ?? ($response['data']['paymentLinkId'] ?? null);
                    $payment_link_alt = $response['data']['paymentLinkId'] ?? ($response['data']['paymentLink'] ?? null);

                    $payosMeta = [
                        'checkout_url' => $checkoutUrl ?? null,
                        'payment_request_id' => $payment_request_id,
                        'payment_link_id' => $payment_link_id,
                        'payment_link_alt' => $payment_link_alt,
                        'qr_image' => $response['qrImage'] ?? ($response['data']['qrImage'] ?? null),
                        'qr_text' => $response['qr'] ?? ($response['data']['qr'] ?? ($response['qrCode'] ?? ($response['data']['qrCode'] ?? null)))
                    ];

                    // attach savedCart id so webhook can find the savedCart and create the order later
                    $payosMeta['saved_cart_id'] = $savedCart->id ?? null;

                    $items = $savedCart->items ?? [];
                    $items['_payos'] = $payosMeta;
                    $savedCart->items = $items;
                    $savedCart->save();

                    // If PayOS provided a QR directly, return it so the client can display
                    if (!empty($payosMeta['qr_image']) || !empty($payosMeta['qr_text'])) {
                        return response()->json([
                            'success' => true,
                            'message' => 'QR sẵn sàng',
                            'qr_image' => $payosMeta['qr_image'],
                            'qr_text' => $payosMeta['qr_text'],
                            'payment_request_id' => $payosMeta['payment_request_id'],
                            'saved_cart_id' => $payosMeta['saved_cart_id'] ?? $savedCart->id,
                        ]);
                    }

                    if (empty($checkoutUrl)) {
                        Log::error('PayOS returned unexpected response (no checkoutUrl)', ['response' => $response]);
                        throw new \Exception("Không có checkoutUrl trong phản hồi PayOS");
                    }

                    return response()->json([
                        'success' => true,
                        'message' => 'Đang chuyển tới cổng thanh toán...',
                        'redirect' => $checkoutUrl
                    ]);
                } catch (\Throwable $th) {
                    Log::error("PayOS Error: " . $th->getMessage(), ['exception' => $th]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Lỗi tạo cổng thanh toán: ' . $th->getMessage()
                    ], 500);
                }
            }
            // --- HẾT PHẦN PAYOS ---

            // Return mặc định cho COD
            return response()->json([
                'success' => true,
                'message' => 'Đơn hàng COD tạo thành công',
                'order_id' => $order->id,
                'redirect' => route('client.orderConfirm', $order->id)
            ]);

        } catch (\Exception $e) {
            // Đây là Catch của khối Try lớn nhất
            Log::error("Checkout Error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi hệ thống: ' . $e->getMessage()
            ], 500);
        }
    } // Kết thúc hàm store

    /**
     * Order confirmation page
     */
    public function confirm($orderId)
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return redirect()->route('login');
        }

        $order = Order::with('orderItems.productVariant.product')->find($orderId);

        if (!$order) {
            return redirect()->route('client.home')->with('error', 'Đơn hàng không tồn tại');
        }

        if ($order->user_id != $userId) {
            return redirect()->route('client.home')->with('error', 'Bạn không có quyền xem đơn hàng này');
        }

        return view('client.checkout.confirm', [
            'order' => $order
        ]);
    }
}