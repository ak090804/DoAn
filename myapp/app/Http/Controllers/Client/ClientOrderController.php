<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Customer;
use App\Services\OrderService;
use Illuminate\Support\Facades\Log;

class ClientOrderController extends Controller
{
    // List orders for the logged-in customer's user
    public function index(Request $request)
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return redirect()->route('login');
        }

        $customer = Customer::where('user_id', $userId)->first();
        if (!$customer) {
            return view('client.orders.index', ['orders' => collect(), 'customer' => null]);
        }

        $orders = Order::with('orderItems.productVariant.product')
            ->where('customer_id', $customer->id)
            ->latest()
            ->paginate(10);

        return view('client.orders.index', compact('orders', 'customer'));
    }

    // Show order detail if it belongs to the logged-in customer
    public function show($id)
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return redirect()->route('login');
        }

        $customer = Customer::where('user_id', $userId)->first();
        if (!$customer) {
            return redirect()->route('client.home')->with('error', 'Không tìm thấy khách hàng');
        }

        $order = Order::with('orderItems.productVariant.product')->find($id);
        if (!$order || $order->customer_id != $customer->id) {
            return redirect()->route('client.orders.index')->with('error', 'Đơn hàng không tồn tại');
        }

        return view('client.orders.show', compact('order'));
    }

    /**
     * Allow customer to update order status for limited transitions:
     * - pending -> cancelled (customer cancel)
     * - shipping -> completed (customer confirms receipt)
     */
    public function updateStatus(Request $request, $id)
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return redirect()->route('login');
        }

        $customer = Customer::where('user_id', $userId)->first();
        if (!$customer) {
            return redirect()->route('client.home')->with('error', 'Không tìm thấy khách hàng');
        }

        $order = Order::find($id);
        if (!$order || $order->customer_id != $customer->id) {
            return redirect()->route('client.orders.index')->with('error', 'Đơn hàng không tồn tại');
        }

        $requested = $request->input('status');

        // Allowed transitions by customer
        $allowed = [];
        if ($order->status == 'pending') {
            $allowed[] = 'cancelled';
        }
        if ($order->status == 'shipping') {
            $allowed[] = 'completed';
        }

        if (!in_array($requested, $allowed)) {
            return redirect()->route('client.orders.show', $order->id)->with('error', 'Không thể cập nhật trạng thái đơn hàng.');
        }

        try {
            $service = new OrderService();
            $service->updateStatus($order->id, $requested);
            return redirect()->route('client.orders.show', $order->id)->with('success', 'Cập nhật trạng thái thành công.');
        } catch (\Exception $e) {
            Log::error('Customer updateStatus error: ' . $e->getMessage());
            return redirect()->route('client.orders.show', $order->id)->with('error', 'Lỗi khi cập nhật trạng thái.');
        }
    }
}
