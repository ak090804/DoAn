<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItems;
use App\Models\SavedCart;
use App\Models\Customer;
use Illuminate\Http\Request;

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

        // Tính tổng
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return view('client.checkout.index', [
            'cart' => $cart,
            'total' => $total,
            'user' => $user
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
            // Tính tổng tiền
            $totalPrice = 0;
            foreach ($cart as $item) {
                $totalPrice += $item['price'] * $item['quantity'];
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

            // Tạo đơn hàng (tham chiếu tới customer_id)
            $order = Order::create([
                'customer_id' => $customer->id,
                'total_price' => $totalPrice,
                'status' => 'pending',
                'note' => $validated['note'] ?? null
            ]);

            // Tạo chi tiết đơn hàng
            foreach ($cart as $productVariantId => $item) {
                // Save using product_variant_id (matches OrderItems model)
                OrderItems::create([
                    'order_id' => $order->id,
                    'product_variant_id' => $productVariantId,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['price'] * $item['quantity']
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
