<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DailySalesExport;
use App\Exports\TopProductsExport;

class StatisticController extends Controller
{
    public function index()
    {
        $from = Carbon::now()->subDays(6)->startOfDay();
        $to = Carbon::now()->endOfDay();

        $dailyStats = $this->getDailyStats($from, $to);
        $topProducts = $this->getTopProducts($from, $to);
        $kpis = $this->getKPIs($from, $to);
        $topCustomers = $this->getTopCustomers($from, $to);
        $orderStats = $this->getOrderStats($from, $to);

        return view('project_1.admin.home', compact('dailyStats', 'topProducts', 'kpis', 'topCustomers', 'orderStats'));
    }

    public function filter(Request $request)
    {
        $from = Carbon::parse($request->input('from'))->startOfDay();
        $to = Carbon::parse($request->input('to'))->endOfDay();

        $dailyStats = $this->getDailyStats($from, $to);
        $topProducts = $this->getTopProducts($from, $to);
        $kpis = $this->getKPIs($from, $to);
        $topCustomers = $this->getTopCustomers($from, $to);
        $orderStats = $this->getOrderStats($from, $to);

        return view('project_1.admin.home', compact('dailyStats', 'topProducts', 'kpis', 'topCustomers', 'orderStats'));
    }

    public function exportExcel(Request $request)
    {
        $from = $request->has('from')
            ? Carbon::parse($request->input('from'))->startOfDay()
            : Carbon::now()->subDays(6)->startOfDay();

        $to = $request->has('to')
            ? Carbon::parse($request->input('to'))->endOfDay()
            : Carbon::now()->endOfDay();

        $dailyStats = $this->getDailyStats($from, $to);
        $topProducts = $this->getTopProducts($from, $to);

        return Excel::download(new class($dailyStats, $topProducts) implements \Maatwebsite\Excel\Concerns\WithMultipleSheets {
            protected $dailyStats, $topProducts;

            public function __construct($dailyStats, $topProducts)
            {
                $this->dailyStats = $dailyStats;
                $this->topProducts = $topProducts;
            }

            public function sheets(): array
            {
                return [
                    new DailySalesExport($this->dailyStats),
                    new TopProductsExport($this->topProducts),
                ];
            }
        }, 'thong_ke_ban_hang.xlsx');
    }

    // ==================== Helper Functions ====================

    private function getDailyStats($from, $to)
    {
        $orders = Order::with('items')
            ->whereBetween('created_at', [$from, $to])
            ->get()
            ->groupBy(function ($order) {
                return $order->created_at->format('Y-m-d');
            });

        $dailyStats = [];
        $period = Carbon::parse($from)->daysUntil($to);

        foreach ($period as $date) {
            $formattedDate = $date->format('Y-m-d');
            $dayOrders = $orders[$formattedDate] ?? collect();

            $dailyStats[] = [
                'date' => $formattedDate,
                'total_orders' => $dayOrders->count(),
                'total_products' => $dayOrders->flatMap->items->sum('quantity'),
                'total_revenue' => $dayOrders->sum('total_price'),
            ];
        }

        return $dailyStats;
    }

    private function getTopProducts($from, $to)
    {
        return DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereBetween('orders.created_at', [$from, $to])
            ->select(
                'order_items.product_id',
                'order_items.product_name',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.total_price) as total_revenue')
            )
            ->groupBy('order_items.product_id', 'order_items.product_name')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();
    }

    private function getKPIs($from, $to)
    {
        $orders = Order::whereBetween('created_at', [$from, $to])->get();
        
        return [
            'total_revenue' => $orders->sum('total_price'),
            'total_orders' => $orders->count(),
            'total_customers' => $orders->pluck('name')->unique()->count(),
            'avg_order_value' => $orders->count() > 0 ? $orders->sum('total_price') / $orders->count() : 0,
            'total_products_sold' => $orders->flatMap->items->sum('quantity'),
        ];
    }

    private function getTopCustomers($from, $to)
    {
        return DB::table('orders')
            ->whereBetween('created_at', [$from, $to])
            ->select(
                DB::raw("CONCAT(name, ' - ', phone) as customer_info"),
                'name',
                'phone',
                'address',
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(total_price) as total_spent')
            )
            ->groupBy('name', 'phone', 'address')
            ->orderByDesc('total_spent')
            ->limit(10)
            ->get();
    }

    private function getOrderStats($from, $to)
    {
        $orders = Order::whereBetween('created_at', [$from, $to])->get();
        
        return [
            'completed' => $orders->where('status', 'Đã thanh toán')->count(),
            'pending' => $orders->where('status', 'Chưa thanh toán')->count(),
            'approved' => $orders->where('status', 'Đã duyệt')->count(),
            'failed' => $orders->where('status', 'Thanh toán thất bại')->count(),
        ];
    }
}
