<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
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

        // Add or update item in cart using variant id as key
        if (isset($cart[$variantId])) {
            $cart[$variantId]['quantity'] += $quantity;
        } else {
            $cart[$variantId] = [
                'id' => $variantId,
                'name' => $variant->product->name ?? $variant->name,
                'price' => $price,
                'image' => $variant->image ?? null,
                'quantity' => $quantity
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
            $total += $item['price'] * $item['quantity'];
        }
        return $total;
    }
}
