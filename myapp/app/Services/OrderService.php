<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItems;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function getAllPaginated($perPage = 15, $filters = [])
    {
        $query = Order::with(['customer', 'employee', 'orderItems.product']);

        // Apply filters
        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->whereHas('customer', function($sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%");
                })->orWhereHas('employee', function($sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%");
                });
            });
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        // Apply sorting
        if (isset($filters['sort'])) {
            switch ($filters['sort']) {
                case 'date_desc':
                    $query->latest();
                    break;
                case 'date_asc':
                    $query->oldest();
                    break;
                case 'total_desc':
                    $query->orderBy('total_price', 'desc');
                    break;
                case 'total_asc':
                    $query->orderBy('total_price', 'asc');
                    break;
            }
        } else {
            $query->latest(); // default sort by latest
        }

        return $query->paginate($perPage);
    }

    public function find($id)
    {
        return Order::with(['customer', 'employee', 'orderItems.product'])->findOrFail($id);
    }

    public function create(array $data)
    {
        try {
            DB::beginTransaction();

            // Create order
            $order = Order::create([
                'customer_id' => $data['customer_id'],
                'employee_id' => $data['employee_id'] ?? null,
                'total_price' => 0, // will be updated after adding items
                'status' => $data['status'] ?? 'pending',
                'note' => $data['note'] ?? null,
            ]);

            // Add order items
            $total = 0;
            foreach ($data['items'] as $item) {
                $subtotal = $item['quantity'] * $item['price'];
                $total += $subtotal;

                OrderItems::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $subtotal,
                ]);
            }

            // Update order total
            $order->update(['total_price' => $total]);

            DB::commit();
            return $order;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update($id, array $data)
    {
        try {
            DB::beginTransaction();

            $order = Order::findOrFail($id);

            // Update order details
            $order->update([
                'employee_id' => $data['employee_id'] ?? $order->employee_id,
                'status' => $data['status'] ?? $order->status,
                'note' => $data['note'] ?? $order->note,
            ]);

            // If items are being updated
            if (isset($data['items'])) {
                // Remove existing items
                $order->orderItems()->delete();

                // Add new items
                $total = 0;
                foreach ($data['items'] as $item) {
                    $subtotal = $item['quantity'] * $item['price'];
                    $total += $subtotal;

                    OrderItems::create([
                        'order_id' => $order->id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        'subtotal' => $subtotal,
                    ]);
                }

                // Update order total
                $order->update(['total_price' => $total]);
            }

            DB::commit();
            return $order;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function delete($id)
    {
        $order = Order::findOrFail($id);
        return $order->delete();
    }

    public function updateStatus($id, $status)
    {
        $order = Order::findOrFail($id);
        return $order->update(['status' => $status]);
    }
}