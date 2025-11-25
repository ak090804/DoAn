<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItems;
use App\Models\SavedCart;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Models\KhuyenMai;

class CheckoutController extends Controller
{
    /**
     * Show checkout page
     */
    public function show()
    {
        // Kiểm tra đăng nhập
        $userId = session()->get('user_id');
        if (!$userId) {
            return redirect()->route('login');
        }

        // Lấy giỏ hàng từ session
        $cart = session()->get('cart', []);
        if (count($cart) === 0) {
            return redirect()->route('client.cart')->with('message', 'Giỏ hàng trống');
        }

        // Lấy thông tin người dùng
        $user = User::find($userId);
        if (!$user) {
            return redirect()->route('login');
        }

        // Tính tổng (bao gồm B1G1 nếu có)
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

        // Load available order-level (giam_gia_hd) public promotions and filter by 10% rule
        $allVouchers = KhuyenMai::active()->where('loai', 'giam_gia_hd')->where('is_private', false)->get();

        // Filter vouchers: only show if discount <= 10% of total
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
        // Kiểm tra đăng nhập
        $userId = session()->get('user_id');
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập'
            ], 401);
        }

        // Validate
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'note' => 'nullable|string|max:1000'
        ]);

        // Lấy giỏ hàng
        $cart = session()->get('cart', []);
        if (count($cart) === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Giỏ hàng trống'
            ], 400);
        }

        try {
            // Tính tổng tiền (áp dụng B1G1 nếu có)
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

            // Lưu snapshot giỏ hàng vào DB (SavedCart) trước khi tạo đơn
            $savedCart = SavedCart::create([
                'user_id' => $userId,
                'session_id' => session()->getId(),
                'items' => $cart,
                'total_price' => $totalPrice,
                'status' => 'pending'
            ]);

            // Tạo hoặc cập nhật thông tin Customer liên kết với user hiện tại
            $customer = \App\Models\Customer::updateOrCreate(
                ['user_id' => $userId],
                [
                    'name' => $validated['name'],
                    'phone' => $validated['phone'],
                    'address' => $validated['address'] ?? null,
                    'user_id' => $userId
                ]
            );

            // Apply voucher if provided
            $discountAmount = 0;
            $khuyenMaiId = null;
            if ($request->filled('voucher_code') || $request->filled('voucher_manual')) {
                $code = $request->input('voucher_code') ?: $request->input('voucher_manual');
                $promo = KhuyenMai::where('ma', $code)->where('loai', 'giam_gia_hd')->active()->first();
                if ($promo) {
                    // Treat gia_tri as fixed discount amount; cap with so_tien_giam_toi_da if set
                    $discountAmount = floatval($promo->gia_tri ?? 0);
                    if ($promo->so_tien_giam_toi_da) {
                        $discountAmount = min($discountAmount, floatval($promo->so_tien_giam_toi_da));
                    }
                    $khuyenMaiId = $promo->id;
                }
            }

            $finalTotal = max(0, $totalPrice - $discountAmount);

            // Tạo đơn hàng (tham chiếu tới customer_id)
            $order = Order::create([
                'customer_id' => $customer->id,
                'total_price' => $finalTotal,
                'discount_amount' => $discountAmount,
                'khuyen_mai_id' => $khuyenMaiId,
                'status' => 'pending',
                'note' => $validated['note'] ?? null
            ]);

            // Tạo chi tiết đơn hàng
            foreach ($cart as $productVariantId => $item) {
                // determine charged subtotal considering B1G1
                $qty = isset($item['quantity']) ? intval($item['quantity']) : 0;
                $price = isset($item['price']) ? floatval($item['price']) : 0;
                $chargeQty = $qty;
                if (!empty($item['is_bogo'])) {
                    $chargeQty = (int) ceil($qty / 2);
                }
                // Save using product_variant_id (matches OrderItems model)
                OrderItems::create([
                    'order_id' => $order->id,
                    'product_variant_id' => $productVariantId,
                    'quantity' => $qty,
                    'price' => $price,
                    'subtotal' => $price * $chargeQty
                ]);
            }

            // Xóa giỏ hàng
            // Đánh dấu saved cart đã được chuyển thành đơn hàng (nếu muốn)
            try {
                if (isset($savedCart)) {
                    $savedCart->status = 'ordered';
                    $savedCart->save();
                }
            } catch (\Exception $e) {
                // không block nếu việc cập nhật trạng thái saved cart lỗi
            }

            session()->forget('cart');

            return response()->json([
                'success' => true,
                'message' => 'Đơn hàng được tạo thành công',
                'order_id' => $order->id,
                'redirect' => route('client.orderConfirm', $order->id)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi tạo đơn hàng: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Order confirmation page
     */
    public function confirm($orderId)
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return redirect()->route('login');
        }

        // eager load productVariant and the related product to show names/prices
        $order = Order::with('orderItems.productVariant.product')->find($orderId);
        if (!$order || $order->user_id != $userId) {
            return redirect()->route('client.home')->with('error', 'Đơn hàng không tồn tại');
        }

        return view('client.checkout.confirm', [
            'order' => $order
        ]);
    }
}
