<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ProductSalesController extends Controller
{
    /**
     * Get top 5 best-selling products
     */
    public function topSellingProducts()
    {
        $topProducts = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select(
                'products.id',
                'products.name',
                DB::raw('SUM(order_items.quantity) as total_sold')
            )
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $topProducts,
            'message' => 'Top 5 best-selling products'
        ]);
    }

    /**
     * Get top products (alternative using raw SQL)
     */
    public function topSellingProductsRaw()
    {
        $topProducts = DB::select("
            SELECT `products`.`id`, `products`.`name`, SUM(`order_items`.`quantity`) AS total_sold
            FROM `order_items`
            INNER JOIN `products` ON `order_items`.`product_id` = `products`.`id`
            GROUP BY `products`.`id`, `products`.`name`
            ORDER BY `total_sold` DESC
            LIMIT 5
        ");

        return response()->json([
            'success' => true,
            'data' => $topProducts,
            'message' => 'Top 5 best-selling products (raw SQL)'
        ]);
    }
}
