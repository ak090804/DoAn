<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PayOS\PayOS;
use App\Models\Order;
use App\Jobs\UpdateRecommendationsForOrder;

class PaymentController extends Controller
{
    // 1. Xử lý khi khách thanh toán thành công và quay lại web
    public function success(Request $request)
    {
        // PayOS sẽ trả về params trên URL: ?code=00&id=...&orderCode=...
        $orderCode = $request->input('orderCode');
        $status = $request->input('code');
        $paymentId = $request->input('id') ?: $request->input('paymentRequestId');

        if ($status == '00') {
            // First try: if an Order exists with this id (older behaviour), update and redirect
            $order = Order::find($orderCode);
            if ($order) {
                if ($order->status === 'pending') {
                    $order->status = 'paid';
                    $order->save();
                }
                return redirect()->route('client.home')->with('success', 'Thanh toán thành công! Đơn hàng đã được lưu.');
            }

            // Otherwise, this return likely references a SavedCart (we used savedCart id as orderCode)
            $savedCartId = $orderCode;
            $savedCart = \App\Models\SavedCart::find($savedCartId);
            if (!$savedCart) {
                return redirect()->route('client.home')->with('error', 'Giao dịch không tồn tại');
            }

            // If webhook already created the order, redirect to it
            $payos = $savedCart->items['_payos'] ?? null;
            $orderId = $payos['order_id'] ?? null;
            if ($orderId) {
                return redirect()->route('client.home')->with('success', 'Thanh toán thành công! Đơn hàng đã được lưu.');
            }

            // QUICK FIX: If PayOS returned status in URL (user redirected back) and it indicates PAID,
            // create the order immediately so the user doesn't wait for webhook.
            $returnStatus = strtolower((string) $request->input('status', ''));
            if ($returnStatus === 'paid' || $returnStatus === 'success' || $status == '00') {
                // Create order now (same logic as webhook)
                $items = $savedCart->items;
                $items['_payos']['status'] = 'paid';
                // also persist payment id from return if present
                if ($paymentId) {
                    $items['_payos']['payment_request_id'] = $paymentId;
                }
                $savedCart->items = $items;
                $savedCart->status = 'paid';
                $savedCart->save();

                $orderId = $items['_payos']['order_id'] ?? null;
                if (!$orderId) {
                    $customerData = $items['_customer'] ?? null;
                    $customerId = null;
                    if ($customerData) {
                        $customerModel = \App\Models\Customer::updateOrCreate(
                            ['user_id' => $customerData['user_id'] ?? null],
                            [
                                'name' => $customerData['name'] ?? null,
                                'phone' => $customerData['phone'] ?? null,
                                'address' => $customerData['address'] ?? null,
                                'user_id' => $customerData['user_id'] ?? null,
                            ]
                        );
                        $customerId = $customerModel->id;
                    }

                    $total = $savedCart->total_price ?? 0;
                    $order = \App\Models\Order::create([
                        'customer_id' => $customerId,
                        'user_id' => $customerData['user_id'] ?? null,
                        'total_price' => $total,
                        'discount_amount' => 0,
                        'khuyen_mai_id' => null,
                        'status' => 'paid',
                        'note' => $customerData['note'] ?? null,
                    ]);

                    foreach ($savedCart->items as $pvId => $it) {
                        if ($pvId === '_customer' || $pvId === '_payos')
                            continue;
                        $qty = intval($it['quantity'] ?? 0);
                        $price = floatval($it['price'] ?? 0);
                        $chargeQty = !empty($it['is_bogo']) ? (int) ceil($qty / 2) : $qty;

                        \App\Models\OrderItems::create([
                            'order_id' => $order->id,
                            'product_variant_id' => $pvId,
                            'quantity' => $qty,
                            'price' => $price,
                            'subtotal' => $price * $chargeQty
                        ]);

                        try {
                            $variant = \App\Models\ProductVariant::find($pvId);
                            if ($variant) {
                                $variant->quantity = max(0, $variant->quantity - $qty);
                                $variant->save();
                            }
                        } catch (\Exception $e) {
                            \Illuminate\Support\Facades\Log::error('Lỗi trừ tồn kho variant (return-immediate): ' . $pvId);
                        }
                    }

                    // link order id back into savedCart
                    $items = $savedCart->items;
                    $items['_payos']['order_id'] = $order->id;
                    $savedCart->items = $items;
                    $savedCart->save();

                    try {
                        UpdateRecommendationsForOrder::dispatch($order->id);
                    } catch (\Exception $e) {
                    }

                    return redirect()->route('client.home')->with('success', 'Thanh toán thành công! Đơn hàng đã được lưu.');
                }
                return redirect()->route('client.home')->with('success', 'Thanh toán thành công! Đơn hàng đã được lưu.');
            }

            // Otherwise try server-side check with PayOS API first (avoid waiting for webhook)
            try {
                $payOS = new PayOS(
                    env('PAYOS_CLIENT_ID'),
                    env('PAYOS_API_KEY'),
                    env('PAYOS_CHECKSUM_KEY')
                );

                // We used savedCart id as orderCode when creating the payment link
                $info = $payOS->getPaymentLinkInformation($savedCartId);

                $remoteStatus = $info['status'] ?? ($info['data']['status'] ?? null);
                $remoteStatusNormalized = strtolower((string) ($remoteStatus ?? ''));

                if (in_array($remoteStatusNormalized, ['paid', 'success', 'completed', 'settled'])) {
                    // Create order now (same logic as webhook)
                    $items = $savedCart->items;
                    $items['_payos']['status'] = 'paid';
                    $savedCart->items = $items;
                    $savedCart->status = 'paid';
                    $savedCart->save();

                    $orderId = $items['_payos']['order_id'] ?? null;
                    if (!$orderId) {
                        $customerData = $items['_customer'] ?? null;
                        $customerId = null;
                        if ($customerData) {
                            $customerModel = \App\Models\Customer::updateOrCreate(
                                ['user_id' => $customerData['user_id'] ?? null],
                                [
                                    'name' => $customerData['name'] ?? null,
                                    'phone' => $customerData['phone'] ?? null,
                                    'address' => $customerData['address'] ?? null,
                                    'user_id' => $customerData['user_id'] ?? null,
                                ]
                            );
                            $customerId = $customerModel->id;
                        }

                        $total = $savedCart->total_price ?? 0;
                        $order = \App\Models\Order::create([
                            'customer_id' => $customerId,
                            'user_id' => $customerData['user_id'] ?? null,
                            'total_price' => $total,
                            'discount_amount' => 0,
                            'khuyen_mai_id' => null,
                            'status' => 'paid',
                            'note' => $customerData['note'] ?? null,
                        ]);

                        foreach ($savedCart->items as $pvId => $it) {
                            if ($pvId === '_customer' || $pvId === '_payos')
                                continue;
                            $qty = intval($it['quantity'] ?? 0);
                            $price = floatval($it['price'] ?? 0);
                            $chargeQty = !empty($it['is_bogo']) ? (int) ceil($qty / 2) : $qty;

                            \App\Models\OrderItems::create([
                                'order_id' => $order->id,
                                'product_variant_id' => $pvId,
                                'quantity' => $qty,
                                'price' => $price,
                                'subtotal' => $price * $chargeQty
                            ]);

                            try {
                                $variant = \App\Models\ProductVariant::find($pvId);
                                if ($variant) {
                                    $variant->quantity = max(0, $variant->quantity - $qty);
                                    $variant->save();
                                }
                            } catch (\Exception $e) {
                                \Illuminate\Support\Facades\Log::error('Lỗi trừ tồn kho variant (return-check): ' . $pvId);
                            }
                        }

                        // link order id back into savedCart
                        $items = $savedCart->items;
                        $items['_payos']['order_id'] = $order->id;
                        $savedCart->items = $items;
                        $savedCart->save();

                        try {
                            UpdateRecommendationsForOrder::dispatch($order->id);
                        } catch (\Exception $e) {
                        }

                        return redirect()->route('client.home')->with('success', 'Thanh toán thành công! Đơn hàng đã được lưu.');
                    } else {
                        return redirect()->route('client.home')->with('success', 'Thanh toán thành công! Đơn hàng đã được lưu.');
                    }
                }

            } catch (\Throwable $th) {
                // If API call failed, we fallback to waiting view and let webhook handle it
                \Illuminate\Support\Facades\Log::warning('PayOS remote status check failed: ' . $th->getMessage());
            }

            // Otherwise show a waiting page that polls PayOS status until webhook creates the order
            return view('client.checkout.payment_wait', [
                'savedCartId' => $savedCartId,
                'paymentId' => $paymentId
            ]);
        }

        return redirect()->route('client.cart')->with('error', 'Thanh toán thất bại hoặc bị hủy.');
    }

    // 2. Xử lý khi khách bấm nút "Hủy" trên PayOS
    public function cancel(Request $request)
    {
        $orderCode = $request->input('orderCode');
        // Có thể cập nhật trạng thái đơn hàng là 'canceled' nếu muốn
        return redirect()->route('client.cart')->with('error', 'Bạn đã hủy thanh toán.');
    }

    // 3. Webhook: PayOS gọi ngầm vào đây để báo trạng thái (Quan trọng nhất)
    public function webhook(Request $request)
    {
        $payOS = new PayOS(
            env('PAYOS_CLIENT_ID'),
            env('PAYOS_API_KEY'),
            env('PAYOS_CHECKSUM_KEY')
        );

        try {
            // Lấy body gửi lên
            $body = $request->getContent();
            $webhookData = json_decode($body, true);

            // Xác thực dữ liệu webhook để tránh giả mạo
            $payOS->verifyPaymentWebhookData($webhookData);

            // Nếu xác thực OK, xử lý logic
            $data = $webhookData['data'];
            $orderCode = $data['orderCode'];

            // Kiểm tra mã lỗi (00 là thành công)
            if ($data['code'] == '00') {
                $order = Order::find($orderCode);
                if ($order) {
                    // Cập nhật trạng thái đã thanh toán
                    $order->status = 'paid';
                    // Có thể lưu thêm mã giao dịch PayOS vào DB nếu cần
                    // $order->transaction_id = $data['paymentLinkId'];
                    $order->save();
                }
            }

            return response()->json(["success" => true]);

        } catch (\Throwable $th) {
            return response()->json(["success" => false, "message" => $th->getMessage()]);
        }
    }
}