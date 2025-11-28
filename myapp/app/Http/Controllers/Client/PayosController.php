<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SavedCart;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use App\Jobs\UpdateRecommendationsForOrder;

class PayosController extends Controller
{
    // Show QR page for a saved cart
    public function showQr($savedCartId)
    {
        $savedCart = SavedCart::find($savedCartId);
        if (!$savedCart) {
            return redirect()->route('client.cart')->with('error', 'Không tìm thấy giao dịch');
        }

        $payos = $savedCart->items['_payos'] ?? null;
        return view('client.checkout.payos_qr', ['savedCart' => $savedCart, 'payos' => $payos]);
    }

    // Poll payment status by payment_request_id
    public function status($paymentRequestId)
    {
        $all = SavedCart::all();
        foreach ($all as $s) {
            $meta = $s->items['_payos'] ?? null;
            if (!$meta)
                continue;

            $candidates = [
                $meta['payment_request_id'] ?? null,
                $meta['payment_link_id'] ?? null,
                $meta['payment_link_alt'] ?? null,
                $meta['paymentLinkId'] ?? null,
                $meta['id'] ?? null,
            ];

            foreach ($candidates as $cand) {
                if ($cand && (string) $cand === (string) $paymentRequestId) {
                    return response()->json([
                        'status' => $s->status ?? 'pending',
                        'saved_cart_id' => $s->id,
                        'order_id' => $meta['order_id'] ?? null,
                        'payos' => $meta
                    ]);
                }
            }
        }

        return response()->json(['status' => 'not_found'], 404);
    }

    // Webhook receiver from PayOS
    public function webhook(Request $request)
    {
        $payload = $request->all();
        Log::debug('PayOS Webhook payload', $payload);

        // Accept multiple possible id fields from PayOS webhook payloads
        $paymentId = $payload['data']['id'] ?? $payload['data']['paymentLinkId'] ?? ($payload['paymentRequestId'] ?? $payload['paymentLinkId'] ?? null);
        $status = $payload['data']['status'] ?? ($payload['status'] ?? null);

        if (!$paymentId) {
            return response()->json(['ok' => false, 'reason' => 'no_payment_id'], 400);
        }

        // find saved cart by scanning items and matching against multiple stored id keys
        $saved = null;
        $all = SavedCart::all();
        foreach ($all as $s) {
            $meta = $s->items['_payos'] ?? null;
            if (!$meta)
                continue;
            $candidates = [
                $meta['payment_request_id'] ?? null,
                $meta['payment_link_id'] ?? null,
                $meta['payment_link_alt'] ?? null,
                $meta['paymentLinkId'] ?? null,
                $meta['id'] ?? null,
            ];
            foreach ($candidates as $cand) {
                if ($cand && $paymentId && (string) $cand === (string) $paymentId) {
                    $saved = $s;
                    break 2;
                }
            }
        }

        if (!$saved) {
            Log::warning('PayOS webhook: savedCart not found for payment id', ['payment_id' => $paymentId]);
            return response()->json(['ok' => true]);
        }

        // If payment success, create Order from SavedCart if not already created
        if (in_array(strtolower($status), ['paid', 'success', 'completed'])) {
            // Avoid duplicate processing
            $items = $saved->items;
            $items['_payos']['status'] = 'paid';
            $saved->items = $items;
            $saved->status = 'paid';
            $saved->save();

            $orderId = $items['_payos']['order_id'] ?? null;
            if ($orderId) {
                $order = Order::find($orderId);
                if ($order) {
                    $order->status = 'paid';
                    $order->save();
                }
            } else {
                // Create Order now from saved cart
                $customerData = $items['_customer'] ?? null;
                $customerId = null;
                if ($customerData) {
                    // create or update Customer record
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

                $total = $saved->total_price ?? 0;
                $order = Order::create([
                    'customer_id' => $customerId,
                    'user_id' => $customerData['user_id'] ?? null,
                    'total_price' => $total,
                    'discount_amount' => 0,
                    'khuyen_mai_id' => null,
                    'status' => 'paid',
                    'note' => $customerData['note'] ?? null,
                ]);

                // create order items and decrement inventory
                foreach ($saved->items as $pvId => $it) {
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
                        Log::error('Lỗi trừ tồn kho variant (webhook): ' . $pvId);
                    }
                }

                // link order id back into savedCart
                $items = $saved->items;
                $items['_payos']['order_id'] = $order->id;
                $saved->items = $items;
                $saved->save();

                try {
                    UpdateRecommendationsForOrder::dispatch($order->id);
                } catch (\Exception $e) {
                }
            }
        } else {
            $items = $saved->items;
            $items['_payos']['status'] = $status ?? 'unknown';
            $saved->items = $items;
            $saved->save();
        }

        return response()->json(['ok' => true]);
    }
}
