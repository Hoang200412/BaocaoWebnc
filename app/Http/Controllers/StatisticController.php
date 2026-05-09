<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DailySalesExport;
use App\Exports\TopProductsExport;

class StatisticController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->filled('from')
            ? Carbon::parse($request->input('from'))->startOfDay()
            : Carbon::now()->subDays(29)->startOfDay();

        $to = $request->filled('to')
            ? Carbon::parse($request->input('to'))->endOfDay()
            : Carbon::now()->endOfDay();

        $overview = $this->getOverviewStats();
        $periodStats = $this->getPeriodStats($from, $to);
        $lowStockBooks = $this->getLowStockBooks();
        $orderStatusStats = $this->getOrderStatusStats($from, $to);
        $revenueByDay = $this->getRevenueByDay($from, $to);
        $revenueByMonth = $this->getRevenueByMonth();
        $revenueByYear = $this->getRevenueByYear();
        $topProducts = $this->getTopProducts($from, $to);
        $topCategories = $this->getTopCategories($from, $to);
        $customerGrowth = $this->getCustomerGrowth();

        return view(
            'project_1.admin.home',
            compact(
                'from',
                'to',
                'overview',
                'periodStats',
                'lowStockBooks',
                'orderStatusStats',
                'revenueByDay',
                'revenueByMonth',
                'revenueByYear',
                'topProducts',
                'topCategories',
                'customerGrowth'
            )
        );
    }

    public function filter(Request $request)
    {
        return $this->index($request);
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

    private function getOverviewStats(): array
    {
        $todayStart = Carbon::today();
        $todayEnd = Carbon::today()->endOfDay();

        return [
            'total_books' => Product::count(),
            'total_orders' => Order::count(),
            'total_customers' => User::where('type', User::type_member)->count(),
            'total_revenue' => (float) Order::where('payment_status', Order::PAYMENT_STATUS_SUCCESS)->sum('total_price'),
            'today_revenue' => (float) Order::whereBetween('created_at', [$todayStart, $todayEnd])
                ->where('payment_status', Order::PAYMENT_STATUS_SUCCESS)
                ->sum('total_price'),
            'today_orders' => Order::whereBetween('created_at', [$todayStart, $todayEnd])->count(),
        ];
    }

    private function getPeriodStats(Carbon $from, Carbon $to): array
    {
        $orders = Order::with('items')->whereBetween('created_at', [$from, $to])->get();

        return [
            'orders' => $orders->count(),
            'revenue' => (float) $orders
                ->where('payment_status', Order::PAYMENT_STATUS_SUCCESS)
                ->sum('total_price'),
            'products_sold' => (int) $orders->flatMap->items->sum('quantity'),
            'avg_order_value' => $orders->count() > 0 ? (float) $orders->avg('total_price') : 0,
        ];
    }

    private function getLowStockBooks(int $threshold = 10)
    {
        return Product::query()
            ->select('id', 'name', 'quantity', 'price')
            ->where('quantity', '<=', $threshold)
            ->orderBy('quantity')
            ->limit(8)
            ->get();
    }

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
            ->where('orders.payment_status', Order::PAYMENT_STATUS_SUCCESS)
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

    private function getTopCategories($from, $to)
    {
        return DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->whereBetween('orders.created_at', [$from, $to])
            ->where('orders.payment_status', Order::PAYMENT_STATUS_SUCCESS)
            ->select(
                'categories.id',
                'categories.name',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.total_price) as total_revenue')
            )
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();
    }

    private function getRevenueByDay(Carbon $from, Carbon $to): array
    {
        $rows = Order::query()
            ->whereBetween('created_at', [$from, $to])
            ->where('payment_status', Order::PAYMENT_STATUS_SUCCESS)
            ->selectRaw('DATE(created_at) as date_key, SUM(total_price) as total_revenue')
            ->groupBy('date_key')
            ->orderBy('date_key')
            ->get()
            ->keyBy('date_key');

        $labels = [];
        $values = [];
        $cursor = $from->copy()->startOfDay();
        $end = $to->copy()->startOfDay();

        while ($cursor <= $end) {
            $key = $cursor->format('Y-m-d');
            $labels[] = $cursor->format('d/m');
            $values[] = isset($rows[$key]) ? (float) $rows[$key]->total_revenue : 0;
            $cursor->addDay();
        }

        return ['labels' => $labels, 'values' => $values];
    }

    private function getRevenueByMonth(): array
    {
        $from = Carbon::now()->subMonths(11)->startOfMonth();
        $to = Carbon::now()->endOfMonth();

        $rows = Order::query()
            ->whereBetween('created_at', [$from, $to])
            ->where('payment_status', Order::PAYMENT_STATUS_SUCCESS)
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month_key, SUM(total_price) as total_revenue")
            ->groupBy('month_key')
            ->orderBy('month_key')
            ->get()
            ->keyBy('month_key');

        $labels = [];
        $values = [];
        $cursor = $from->copy();

        while ($cursor <= $to) {
            $key = $cursor->format('Y-m');
            $labels[] = $cursor->format('m/Y');
            $values[] = isset($rows[$key]) ? (float) $rows[$key]->total_revenue : 0;
            $cursor->addMonth();
        }

        return ['labels' => $labels, 'values' => $values];
    }

    private function getRevenueByYear(): array
    {
        $from = Carbon::now()->subYears(4)->startOfYear();
        $to = Carbon::now()->endOfYear();

        $rows = Order::query()
            ->whereBetween('created_at', [$from, $to])
            ->where('payment_status', Order::PAYMENT_STATUS_SUCCESS)
            ->selectRaw('YEAR(created_at) as year_key, SUM(total_price) as total_revenue')
            ->groupBy('year_key')
            ->orderBy('year_key')
            ->get()
            ->keyBy('year_key');

        $labels = [];
        $values = [];
        $startYear = (int) $from->format('Y');
        $endYear = (int) $to->format('Y');

        for ($year = $startYear; $year <= $endYear; $year++) {
            $labels[] = (string) $year;
            $values[] = isset($rows[$year]) ? (float) $rows[$year]->total_revenue : 0;
        }

        return ['labels' => $labels, 'values' => $values];
    }

    private function getOrderStatusStats(Carbon $from, Carbon $to): array
    {
        $grouped = Order::query()
            ->whereBetween('created_at', [$from, $to])
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $labels = Order::STATUS_OPTIONS;
        $values = collect($labels)->map(function ($status) use ($grouped) {
            return (int) ($grouped[$status] ?? 0);
        })->toArray();

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }

    private function getCustomerGrowth(): array
    {
        $from = Carbon::now()->subMonths(11)->startOfMonth();
        $to = Carbon::now()->endOfMonth();

        $rows = User::query()
            ->where('type', User::type_member)
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month_key, COUNT(*) as total")
            ->groupBy('month_key')
            ->orderBy('month_key')
            ->get()
            ->keyBy('month_key');

        $labels = [];
        $newUsers = [];
        $cumulative = [];
        $runningTotal = (int) User::query()
            ->where('type', User::type_member)
            ->where('created_at', '<', $from)
            ->count();

        $cursor = $from->copy();
        while ($cursor <= $to) {
            $key = $cursor->format('Y-m');
            $count = isset($rows[$key]) ? (int) $rows[$key]->total : 0;
            $runningTotal += $count;

            $labels[] = $cursor->format('m/Y');
            $newUsers[] = $count;
            $cumulative[] = $runningTotal;

            $cursor->addMonth();
        }

        return [
            'labels' => $labels,
            'new_users' => $newUsers,
            'cumulative' => $cumulative,
        ];
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
            ->where('payment_status', Order::PAYMENT_STATUS_SUCCESS)
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
            'completed' => $orders->where('payment_status', Order::PAYMENT_STATUS_SUCCESS)->count(),
            'pending' => $orders->where('payment_status', Order::PAYMENT_STATUS_PENDING)->count(),
            'approved' => $orders->where('status', Order::STATUS_APPROVED)->count(),
            'failed' => $orders->where('payment_status', Order::PAYMENT_STATUS_FAILED)->count(),
        ];
    }
}
