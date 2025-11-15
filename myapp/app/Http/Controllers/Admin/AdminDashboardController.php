<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use App\Services\ProductVariantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    protected $orderService;
    protected $productVariantService;

    // Inject cả OrderService và ProductVariantService
    public function __construct(OrderService $orderService, ProductVariantService $productVariantService)
    {
        $this->orderService = $orderService;
        $this->productVariantService = $productVariantService;
    }

    /**
     * Hiển thị trang dashboard (thống kê tổng quan)
     */
    public function index(Request $request)
    {
        // Nhận tháng cần lọc (mặc định là tháng hiện tại)
        $month = $request->input('month', now()->format('Y-m'));

        // Lấy năm và tháng
        $year = Carbon::parse($month)->year;
        $monthNumber = Carbon::parse($month)->month;

        // Thống kê số đơn hàng, doanh thu theo tháng
        $stats = DB::table('orders')
            ->selectRaw('COUNT(*) as total_orders, SUM(total_price) as total_revenue')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $monthNumber)
            ->first();

        // Thống kê doanh thu theo ngày trong tháng (vẽ biểu đồ)
        $dailyRevenue = DB::table('orders')
            ->selectRaw('DAY(created_at) as day, SUM(total_price) as revenue')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $monthNumber)
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        // Top 5 sản phẩm bán chạy (theo tổng số lượng) thông qua service
        $topProducts = $this->productVariantService->getTopSellingProducts();

        // Dữ liệu tổng quan
        $totalOrders = $stats->total_orders ?? 0;
        $totalRevenue = $stats->total_revenue ?? 0;

        return view('admin.dashboard', compact(
            'month',
            'totalOrders',
            'totalRevenue',
            'dailyRevenue',
            'topProducts'
        ));
    }
}
