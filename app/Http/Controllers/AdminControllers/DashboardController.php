<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use App\Models\ProductVariant;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    private function getDateRange($period, $request)
    {
        $endDate = Carbon::now()->endOfDay();
        
        if ($period == 'custom') {
            $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date'))->startOfDay() : Carbon::now()->subDays(30)->startOfDay();
            $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : Carbon::now()->endOfDay();
            $periodLabel = 'Từ ' . $startDate->format('d/m/Y') . ' - ' . $endDate->format('d/m/Y');
        } else {
            switch ($period) {
                case 'today':
                    $startDate = Carbon::now()->startOfDay();
                    $periodLabel = 'Hôm nay'; break;
                case 'yesterday':
                    $startDate = Carbon::now()->subDay()->startOfDay();
                    $endDate = Carbon::now()->subDay()->endOfDay();
                    $periodLabel = 'Hôm qua'; break;
                case '7days':
                    $startDate = Carbon::now()->subDays(6)->startOfDay();
                    $periodLabel = '7 ngày qua'; break;
                case 'this_month':
                    $startDate = Carbon::now()->startOfMonth();
                    $periodLabel = 'Tháng này'; break;
                case 'last_month':
                    $startDate = Carbon::now()->subMonth()->startOfMonth();
                    $endDate = Carbon::now()->subMonth()->endOfMonth();
                    $periodLabel = 'Tháng trước'; break;
                case '30days': default:
                    $startDate = Carbon::now()->subDays(29)->startOfDay();
                    $periodLabel = '30 ngày qua'; break;
            }
        }
        
        return [$startDate, $endDate, $periodLabel];
    }

    // ==========================================
    // 1. DASHBOARD DOANH THU & BÁN HÀNG
    // ==========================================
    public function index(Request $request)
    {
        $period = $request->input('period', '30days');
        [$startDate, $endDate, $periodLabel] = $this->getDateRange($period, $request);

        // Cache Key Generator
        $cacheKey = "dashboard_revenue_{$period}_{$startDate->format('Ymd')}_{$endDate->format('Ymd')}";

        $data = Cache::remember($cacheKey, 600, function () use ($startDate, $endDate) {
            $diffInDays = $startDate->diffInDays($endDate) + 1;
            $prevStartDate = (clone $startDate)->subDays($diffInDays);
            $prevEndDate = (clone $startDate)->subSecond();

            // Tổng doanh thu (Chỉ tính đơn thành công)
            $totalRevenue = Order::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', Order::STATUS_RECEIVED)->sum('total_amount');
            
            $prevTotalRevenue = Order::whereBetween('created_at', [$prevStartDate, $prevEndDate])
                ->where('status', Order::STATUS_RECEIVED)->sum('total_amount');

            $revenueGrowth = $prevTotalRevenue > 0 ? (($totalRevenue - $prevTotalRevenue) / $prevTotalRevenue) * 100 : ($totalRevenue > 0 ? 100 : 0);

            // Số đơn thành công
            $totalOrders = Order::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', Order::STATUS_RECEIVED)->count();
            
            $prevTotalOrders = Order::whereBetween('created_at', [$prevStartDate, $prevEndDate])
                ->where('status', Order::STATUS_RECEIVED)->count();

            $ordersGrowth = $prevTotalOrders > 0 ? (($totalOrders - $prevTotalOrders) / $prevTotalOrders) * 100 : ($totalOrders > 0 ? 100 : 0);
            
            $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;
            $totalDiscount = Order::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', Order::STATUS_RECEIVED)->sum('discount_amount');

            // Biểu đồ Mixed
            $revenueByDate = Order::select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('SUM(total_amount) as total'),
                    DB::raw('COUNT(*) as total_orders')
                )->whereBetween('created_at', [$startDate, $endDate])
                ->where('status', Order::STATUS_RECEIVED)
                ->groupBy('date')->orderBy('date', 'ASC')->get()->keyBy('date');

            $chartDates = []; $chartRevenues = []; $chartOrders = [];
            $currentDate = clone $startDate;
            while ($currentDate <= $endDate) {
                $ds = $currentDate->format('Y-m-d');
                $chartDates[] = $currentDate->format('d/m');
                $chartRevenues[] = isset($revenueByDate[$ds]) ? $revenueByDate[$ds]->total : 0;
                $chartOrders[] = isset($revenueByDate[$ds]) ? $revenueByDate[$ds]->total_orders : 0;
                $currentDate->addDay();
            }

            // Top Sản Phẩm Bán Chạy (Có Tồn kho & Tỷ suất lợi nhuận)
            $topProducts = Product::join('order_items', 'products.id', '=', 'order_items.product_id')
                ->join('orders', 'orders.id', '=', 'order_items.order_id')
                ->whereBetween('orders.created_at', [$startDate, $endDate])
                ->where('orders.status', Order::STATUS_RECEIVED)
                ->select(
                    'products.id', 'products.name', 'products.thumbnail', 
                    DB::raw('SUM(order_items.quantity) as total_sold'),
                    DB::raw('SUM(order_items.line_total) as total_revenue')
                )
                ->groupBy('products.id', 'products.name', 'products.thumbnail')
                ->orderByDesc('total_revenue')
                ->limit(5)->get();

            // Giao dịch gần đây (Eager Loading User)
            $recentOrders = Order::with('user')->orderByDesc('created_at')->limit(6)->get();

            // Doanh thu theo phương thức thanh toán
            $paymentStats = Order::select('payment_method', DB::raw('SUM(total_amount) as total'))
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('status', Order::STATUS_RECEIVED)
                ->groupBy('payment_method')->get();
                
            $paymentLabels = []; $paymentData = [];
            $paymentMap = ['cod' => 'Nhận hàng', 'vnpay' => 'VNPay', 'momo' => 'Ví Momo', 'wallet' => 'Ví hệ thống'];
            foreach ($paymentStats as $stat) {
                $paymentLabels[] = $paymentMap[$stat->payment_method] ?? $stat->payment_method;
                $paymentData[] = $stat->total;
            }

            return compact(
                'totalRevenue', 'revenueGrowth', 'totalOrders', 'ordersGrowth',
                'avgOrderValue', 'totalDiscount',
                'chartDates', 'chartRevenues', 'chartOrders',
                'paymentLabels', 'paymentData',
                'topProducts', 'recentOrders'
            );
        });

        $customStartDate = $startDate->format('Y-m-d');
        $customEndDate = $endDate->format('Y-m-d');

        return view('admin.dashboard.index', array_merge($data, compact(
            'period', 'periodLabel', 'customStartDate', 'customEndDate'
        )));
    }

    // ==========================================
    // 2. DASHBOARD ĐIỀU PHỐI ĐƠN HÀNG
    // ==========================================
    public function orderStats(Request $request)
    {
        $period = $request->input('period', '30days');
        [$startDate, $endDate, $periodLabel] = $this->getDateRange($period, $request);

        $orderStatuses = Order::select('status', DB::raw('count(*) as total'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('status')->get();
            
        $statusGroups = ['Chờ xác nhận' => 0, 'Đang giao' => 0, 'Thành công' => 0, 'Đã hủy' => 0];

        foreach ($orderStatuses as $os) {
            if (in_array($os->status, [Order::STATUS_PENDING, Order::STATUS_READY_TO_PICK, Order::STATUS_PICKING])) {
                $statusGroups['Chờ xác nhận'] += $os->total;
            } elseif (in_array($os->status, [Order::STATUS_TRANSPORTING, Order::STATUS_DELIVERING])) {
                $statusGroups['Đang giao'] += $os->total;
            } elseif (in_array($os->status, [Order::STATUS_DELIVERED, Order::STATUS_RECEIVED])) {
                $statusGroups['Thành công'] += $os->total;
            } elseif (in_array($os->status, [Order::STATUS_CANCELLED, Order::STATUS_CANCEL, Order::STATUS_DELIVERY_FAIL, Order::STATUS_RETURNED])) {
                $statusGroups['Đã hủy'] += $os->total;
            }
        }

        // Lý do hủy đơn
        $cancelReasons = Order::whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('status', [Order::STATUS_CANCELLED, Order::STATUS_CANCEL, Order::STATUS_RETURNED])
            ->select('cancellation_reason', DB::raw('COUNT(*) as total'))
            ->groupBy('cancellation_reason')
            ->orderByDesc('total')
            ->limit(5)->get();

        $statusLabels = array_keys($statusGroups);
        $statusCounts = array_values($statusGroups);
        $statusColors = ['#f59e0b', '#3b82f6', '#10b981', '#ef4444'];

        $latestOrders = Order::with('user')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get()
            ->map(function($order) {
                return [
                    'id' => $order->id,
                    'order_code' => $order->order_code,
                    'customer_name' => $order->customer_name ?? ($order->user->name ?? 'Khách lẻ'),
                    'total_amount' => number_format($order->total_amount, 0, ',', '.') . ' ₫',
                    'payment_method' => strtoupper($order->payment_method),
                    'payment_status' => $order->payment_status,
                    'status' => $order->status,
                    'status_label' => Order::statusLabels()[$order->status] ?? $order->status,
                    'created_at' => $order->created_at->format('H:i d/m'),
                    'time_ago' => $order->created_at->diffForHumans()
                ];
            });

        $customStartDate = $startDate->format('Y-m-d');
        $customEndDate = $endDate->format('Y-m-d');

        if ($request->ajax()) {
            return response()->json([
                'statusCounts' => $statusCounts,
                'pipeline' => [
                    'pending' => $statusGroups['Chờ xác nhận'],
                    'processing' => $statusGroups['Đang giao'],
                    'completed' => $statusGroups['Thành công'],
                    'cancelled' => $statusGroups['Đã hủy']
                ],
                'latestOrders' => $latestOrders,
                'cancelReasons' => $cancelReasons
            ]);
        }

        return view('admin.dashboard.oder', compact(
            'period', 'periodLabel', 'customStartDate', 'customEndDate',
            'statusLabels', 'statusCounts', 'statusColors', 'statusGroups', 'latestOrders', 'cancelReasons'
        ));
    }

    // ==========================================
    // 3. DASHBOARD PHÂN TÍCH NGƯỜI DÙNG
    // ==========================================
    public function userStats(Request $request)
    {
        $period = $request->input('period', '30days');
        [$startDate, $endDate, $periodLabel] = $this->getDateRange($period, $request);

        $diffInDays = $startDate->diffInDays($endDate) + 1;
        $prevStart = (clone $startDate)->subDays($diffInDays);
        $prevEnd = (clone $startDate)->subSecond();

        $totalUsers = User::whereHas('role', fn($q) => $q->where('name', 'user'))->count();
        
        $newUsers = User::whereHas('role', fn($q) => $q->where('name', 'user'))
                        ->whereBetween('created_at', [$startDate, $endDate])->count();
        $prevNewUsers = User::whereHas('role', fn($q) => $q->where('name', 'user'))
                            ->whereBetween('created_at', [$prevStart, $prevEnd])->count();
        $newUsersGrowth = $prevNewUsers > 0 ? (($newUsers - $prevNewUsers) / $prevNewUsers * 100) : ($newUsers > 0 ? 100 : 0);

        // Buyer Rate
        $usersWithOrders = User::whereHas('role', fn($q) => $q->where('name', 'user'))->whereHas('orders')->count();
        $buyerRate = $totalUsers > 0 ? round($usersWithOrders / $totalUsers * 100, 1) : 0;

        // Tỷ lệ quay lại mua hàng (Retention > 24h)
        $returnBuyersCount = DB::table('orders')
            ->where('status', Order::STATUS_RECEIVED)
            ->select('user_id')
            ->groupBy('user_id')
            ->having(DB::raw('TIMESTAMPDIFF(HOUR, MIN(created_at), MAX(created_at))'), '>', 24)
            ->get()->count();
            
        $completedBuyers = Order::where('status', Order::STATUS_RECEIVED)->distinct('user_id')->count('user_id');
        $retentionRate = $completedBuyers > 0 ? round($returnBuyersCount / $completedBuyers * 100, 1) : 0;

        $userGrowthData = User::whereHas('role', fn($q) => $q->where('name', 'user'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')->orderBy('date')->get()->keyBy('date');

        $signupDates = []; $signupCounts = [];
        $cur = clone $startDate;
        while ($cur <= $endDate) {
            $ds = $cur->format('Y-m-d');
            $signupDates[] = $cur->format('d/m');
            $signupCounts[] = isset($userGrowthData[$ds]) ? $userGrowthData[$ds]->total : 0;
            $cur->addDay();
        }

        // Khách VIP + Eager Loading + Days since joined
        $topBuyers = User::whereHas('role', fn($q) => $q->where('name', 'user'))
            ->withSum(['orders as total_spent' => function($query) use ($startDate, $endDate) {
                $query->where('status', Order::STATUS_RECEIVED)
                      ->whereBetween('created_at', [$startDate, $endDate]);
            }], 'total_amount')
            ->withCount(['orders as total_orders' => function($query) use ($startDate, $endDate) {
                $query->where('status', Order::STATUS_RECEIVED)
                      ->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->having('total_spent', '>', 0)
            ->orderByDesc('total_spent')
            ->limit(10)->get()->map(function($user) {
                $user->days_since_joined = $user->created_at->diffInDays(now());
                return $user;
            });

        $newRegisteredUsers = User::whereHas('role', fn($q) => $q->where('name', 'user'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderByDesc('created_at')
            ->limit(6)->get();

        $customStartDate = $startDate->format('Y-m-d');
        $customEndDate = $endDate->format('Y-m-d');

        return view('admin.dashboard.user', compact(
            'period', 'periodLabel', 'customStartDate', 'customEndDate',
            'totalUsers', 'newUsers', 'newUsersGrowth', 'buyerRate', 'retentionRate', 'returnBuyersCount',
            'signupDates', 'signupCounts', 'topBuyers', 'newRegisteredUsers'
        ));
    }
}
