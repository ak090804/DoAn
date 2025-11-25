<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\KhuyenMai;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Get cart from session
     */
    private function getCart()
    {
        return session()->get('cart', []);
    }

    /**
     * Save cart to session
     */
    private function saveCart($cart)
    {
        session()->put('cart', $cart);
    }

    /**
     * Add product to cart (API)
     */
    public function addToCart(Request $request)
    {
        // Expecting product_variant_id from frontend (use variant id as cart key)
        $variantId = $request->input('product_id');
        $quantity = (int) $request->input('quantity', 1);

        // Find product variant
        $variant = ProductVariant::find($variantId);
        if (!$variant) {
            return response()->json([
                'success' => false,
                'message' => 'Sản phẩm không tồn tại'
            ], 404);
        }

        $price = $variant->price ?? 0;
        if ($price <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Sản phẩm không có giá'
            ], 404);
        }

        // Get current cart
        $cart = $this->getCart();

        // detect if this variant is part of a Buy-1-Get-1 promotion
        $isBogo = $this->isBogoVariant($variantId);

        // Add or update item in cart using variant id as key
        if (isset($cart[$variantId])) {
            $cart[$variantId]['quantity'] += $quantity;
            if ($isBogo) {
                $cart[$variantId]['is_bogo'] = true;
            }
        } else {
            $cart[$variantId] = [
                'id' => $variantId,
                'name' => $variant->product->name ?? $variant->name,
                'price' => $price,
                'image' => $variant->image ?? null,
                'quantity' => $quantity,
                'is_bogo' => $isBogo ? true : false
            ];
        }

        // Save cart
        $this->saveCart($cart);

        return response()->json([
            'success' => true,
            'message' => 'Đã thêm vào giỏ hàng',
            'cart_count' => count($cart),
            'cart_total' => $this->calculateTotal($cart)
        ]);
    }

    /**
     * View cart page
     */
    public function viewCart()
    {
        $cart = $this->getCart();
        $total = $this->calculateTotal($cart);

        return view('client.cart.index', [
            'cart' => $cart,
            'total' => $total
        ]);
    }

    /**
     * Update quantity in cart (API)
     */
    public function updateQuantity(Request $request)
    {
        $productId = $request->input('product_id');
        $quantity = $request->input('quantity', 1);

        $cart = $this->getCart();

        if (isset($cart[$productId])) {
            if ($quantity <= 0) {
                unset($cart[$productId]);
            } else {
                $cart[$productId]['quantity'] = $quantity;
            }
        }

        $this->saveCart($cart);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật giỏ hàng thành công',
            'cart_count' => count($cart),
            'cart_total' => $this->calculateTotal($cart)
        ]);
    }

    /**
     * Remove item from cart (API)
     */
    public function removeFromCart(Request $request)
    {
        $productId = $request->input('product_id');
        $cart = $this->getCart();

        if (isset($cart[$productId])) {
            unset($cart[$productId]);
        }

        $this->saveCart($cart);

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa khỏi giỏ hàng',
            'cart_count' => count($cart),
            'cart_total' => $this->calculateTotal($cart)
        ]);
    }

    /**
     * Get cart data (API)
     */
    public function getCartData()
    {
        $cart = $this->getCart();
        $total = $this->calculateTotal($cart);

        return response()->json([
            'success' => true,
            'cart' => $cart,
            'cart_count' => count($cart),
            'cart_total' => $total
        ]);
    }

    /**
     * Clear cart (API)
     */
    public function clearCart()
    {
        session()->forget('cart');

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa giỏ hàng',
            'cart_count' => 0,
            'cart_total' => 0
        ]);
    }

    /**
     * Check if user is logged in
     */
    public function checkLogin()
    {
        $isLoggedIn = session()->has('user_id');

        return response()->json([
            'isLoggedIn' => $isLoggedIn
        ]);
    }

    /**
     * Calculate cart total
     */
    private function calculateTotal($cart)
    {
        $total = 0;
        foreach ($cart as $item) {
            $qty = isset($item['quantity']) ? intval($item['quantity']) : 0;
            $price = isset($item['price']) ? floatval($item['price']) : 0;
            if (!empty($item['is_bogo'])) {
                // For B1G1: charge for ceil(qty/2)
                $chargeQty = (int) ceil($qty / 2);
            } else {
                $chargeQty = $qty;
            }
            $total += $price * $chargeQty;
        }
        return $total;
    }

    /**
     * Check if a product variant is in an active tang_sp promotion (Buy 1 Get 1)
     */
    private function isBogoVariant($variantId)
    {
        if (!$variantId)
            return false;
        $promos = KhuyenMai::active()->where('loai', 'tang_sp')->get();
        foreach ($promos as $p) {
            $data = $p->data ?? [];
            if (is_array($data) && isset($data['gift_product_variant_id']) && intval($data['gift_product_variant_id']) === intval($variantId)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Verify voucher code and return discount info (API)
     */
    public function verifyVoucher(Request $request)
    {
        $code = $request->input('code', '');
        $cartTotal = floatval($request->input('cart_total', 0));

        if (!$code) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng nhập mã voucher'
            ], 400);
        }

        // Look for voucher (order-level discount)
        $voucher = KhuyenMai::where('ma', $code)
            ->where('loai', 'giam_gia_hd')
            ->active()
            ->first();

        if (!$voucher) {
            return response()->json([
                'success' => false,
                'message' => 'Mã voucher không hợp lệ hoặc đã hết hạn'
            ], 404);
        }

        // Calculate discount amount
        $discountAmount = floatval($voucher->gia_tri ?? 0);
        if ($voucher->so_tien_giam_toi_da) {
            $discountAmount = min($discountAmount, floatval($voucher->so_tien_giam_toi_da));
        }

        // Check if discount <= 10% of cart total
        $discountPercent = $cartTotal > 0 ? ($discountAmount / $cartTotal) * 100 : 0;
        if ($discountPercent > 10) {
            return response()->json([
                'success' => false,
                'message' => 'Mã voucher không hợp lệ. Giảm giá không thể vượt quá 10% tổng tiền.'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Mã voucher hợp lệ',
            'discount_amount' => $discountAmount,
            'discount_percent' => round($discountPercent, 2),
            'voucher_name' => $voucher->ten,
            'voucher_code' => $code
        ]);
    }
}
