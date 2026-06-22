<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use App\Models\Product; 
use App\Models\Customer;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Dashboard extends Component
{
    public $dateRange = 'today';
    public $startDate;
    public $endDate;
    public $topProducts = [];
    public $darkMode = false;
    public $chartData = ['labels' => ['No Data'], 'values' => [0]];

    protected $listeners = [
        'refreshDashboard' => '$refresh',
        'dark-mode-changed' => 'updateDarkMode',
    ];

    public function mount()
    {
        $this->setDateRange();
        $this->checkDarkModePreference();
    }

    public function checkDarkModePreference()
    {
        if (session()->has('dark_mode')) {
            $this->darkMode = session('dark_mode');
        }
    }

    public function updatedDateRange()
    {
        $this->setDateRange();
    }

    public function updateDarkMode($darkMode)
    {
        $this->darkMode = $darkMode;
        $this->dispatch('update-chart', $this->chartData);
    }

    public function setDateRange()
    {
        $now = Carbon::now();

        switch ($this->dateRange) {
            case 'today':
                $this->startDate = Carbon::today()->startOfDay();
                $this->endDate = Carbon::today()->endOfDay();
                break;
            case 'week':
                $this->startDate = $now->copy()->startOfWeek(Carbon::SUNDAY);
                $this->endDate = $now->copy()->endOfWeek(Carbon::SATURDAY);
                break;
            case 'month':
                $this->startDate = $now->copy()->startOfMonth();
                $this->endDate = $now->copy()->endOfMonth();
                break;
            case 'year':
                $this->startDate = $now->copy()->startOfYear();
                $this->endDate = $now->copy()->endOfYear();
                break;
            default:
                $this->startDate = Carbon::today()->startOfDay();
                $this->endDate = Carbon::today()->endOfDay();
        }

        $this->loadChartData();
        $this->loadTopProducts();
        
        $this->dispatch('update-chart', $this->chartData);
    }

    public function loadChartData()
    {
        try {
            $driver = DB::connection()->getDriverName();
            
            // Today: hourly chart
            if ($this->dateRange === 'today') {
                // SQLite doesn't have HOUR() function, use strftime
                if ($driver === 'sqlite') {
                    $sales = Order::whereDate('created_at', Carbon::today())
                        ->select(
                            DB::raw("strftime('%H', created_at) as hour"),
                            DB::raw('COALESCE(SUM(total_amount), 0) as total')
                        )
                        ->groupBy('hour')
                        ->orderBy('hour')
                        ->get();
                } else {
                    // MySQL
                    $sales = Order::whereDate('created_at', Carbon::today())
                        ->select(
                            DB::raw('HOUR(created_at) as hour'),
                            DB::raw('COALESCE(SUM(total_amount), 0) as total')
                        )
                        ->groupBy('hour')
                        ->orderBy('hour')
                        ->get();
                }

                $labels = [];
                $values = [];

                for ($hour = 0; $hour < 24; $hour++) {
                    $labels[] = sprintf('%02d:00', $hour);
                    $hourData = $sales->firstWhere('hour', (string)$hour);
                    $values[] = $hourData ? (float) $hourData->total : 0;
                }

                $this->chartData = [
                    'labels' => $labels,
                    'values' => $values
                ];
            }
            // Week: daily chart Sun-Sat
            elseif ($this->dateRange === 'week') {
                // SQLite uses strftime for day of week
                if ($driver === 'sqlite') {
                    $sales = Order::whereBetween('created_at', [$this->startDate, $this->endDate])
                        ->select(
                            DB::raw("strftime('%Y-%m-%d', created_at) as date"),
                            DB::raw("strftime('%w', created_at) as day_of_week"),
                            DB::raw('COALESCE(SUM(total_amount), 0) as total')
                        )
                        ->groupBy('date', 'day_of_week')
                        ->orderBy('date')
                        ->get();
                } else {
                    // MySQL
                    $sales = Order::whereBetween('created_at', [$this->startDate, $this->endDate])
                        ->select(
                            DB::raw('DATE(created_at) as date'),
                            DB::raw('DAYOFWEEK(created_at) as day_of_week'),
                            DB::raw('COALESCE(SUM(total_amount), 0) as total')
                        )
                        ->groupBy('date', 'day_of_week')
                        ->orderBy('date')
                        ->get();
                }

                $labels = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
                $values = array_fill(0, 7, 0);

                foreach ($sales as $sale) {
                    // SQLite: 0=Sunday, 6=Saturday
                    // MySQL: 1=Sunday, 7=Saturday
                    $index = $driver === 'sqlite' ? (int)$sale->day_of_week : ((int)$sale->day_of_week - 1);
                    if (isset($values[$index])) {
                        $values[$index] = (float) $sale->total;
                    }
                }

                $this->chartData = [
                    'labels' => $labels,
                    'values' => $values
                ];
            }
            // Month / Year: daily chart
            else {
                $sales = Order::whereBetween('created_at', [$this->startDate, $this->endDate])
                    ->select(
                        DB::raw("strftime('%Y-%m-%d', created_at) as date"),
                        DB::raw('COALESCE(SUM(total_amount), 0) as total')
                    )
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get();

                if ($sales->isEmpty()) {
                    $this->chartData = [
                        'labels' => ['No Sales Data'],
                        'values' => [0]
                    ];
                    return;
                }

                $dates = [];
                $values = [];
                $currentDate = $this->startDate->copy();

                $salesByDate = $sales->keyBy(function ($item) {
                    return $item->date;
                });

                $maxPoints = 60;
                $totalDays = $this->startDate->diffInDays($this->endDate) + 1;
                $step = max(1, ceil($totalDays / $maxPoints));
                $dayCount = 0;

                while ($currentDate <= $this->endDate) {
                    if ($dayCount % $step == 0 || $dayCount == $totalDays - 1) {
                        $dateKey = $currentDate->format('Y-m-d');
                        $dates[] = $currentDate->format('M d');
                        $values[] = isset($salesByDate[$dateKey]) ? (float) $salesByDate[$dateKey]->total : 0;
                    }
                    $currentDate->addDay();
                    $dayCount++;
                }

                if (empty($dates)) {
                    $this->chartData = ['labels' => ['No Data'], 'values' => [0]];
                } else {
                    $this->chartData = [
                        'labels' => $dates,
                        'values' => $values
                    ];
                }
            }
            
            
        } catch (\Exception $e) {
            Log::error('Error loading chart data: ' . $e->getMessage());
        }
    }

    public function loadTopProducts()
    {
        try {
            $this->topProducts = DB::table('order_items')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->select(
                    'products.name',
                    'products.id',
                    DB::raw('COALESCE(SUM(order_items.quantity), 0) as total_quantity'),
                    DB::raw('COALESCE(SUM(order_items.total), 0) as total_sales')
                )
                ->whereBetween('orders.created_at', [$this->startDate, $this->endDate])
                ->groupBy('products.id', 'products.name')
                ->orderBy('total_sales', 'desc')
                ->limit(5)
                ->get();
                
        } catch (\Exception $e) {
            Log::error('Error loading top products: ' . $e->getMessage());
            $this->topProducts = collect([]);
        }
    }

    public function getStatsProperty()
    {
        $now = Carbon::now();

        try {
            return [
                'today_sales' => Order::whereDate('created_at', Carbon::today())->sum('total_amount') ?? 0,
                'week_sales' => Order::whereBetween('created_at', [$now->copy()->startOfWeek(Carbon::SUNDAY), $now->copy()->endOfWeek(Carbon::SATURDAY)])->sum('total_amount') ?? 0,
                'month_sales' => Order::whereMonth('created_at', $now->month)->whereYear('created_at', $now->year)->sum('total_amount') ?? 0,
                'total_products' => Product::count(),
                'total_customers' => Customer::count(),
                'low_stock_products' => Product::whereRaw('stock_quantity <= COALESCE(low_stock_threshold, 5)')->count(),
                'pending_orders' => Order::where('status', 'pending')->count(),
                'today_expenses' => Expense::whereDate('created_at', Carbon::today())->sum('amount') ?? 0,
                'total_revenue' => Order::sum('total_amount') ?? 0,
                'average_order_value' => Order::avg('total_amount') ?? 0,
            ];
        } catch (\Exception $e) {
            Log::error('Error loading stats: ' . $e->getMessage());
            return [
                'today_sales' => 0,
                'week_sales' => 0,
                'month_sales' => 0,
                'total_products' => 0,
                'total_customers' => 0,
                'low_stock_products' => 0,
                'pending_orders' => 0,
                'today_expenses' => 0,
                'total_revenue' => 0,
                'average_order_value' => 0,
            ];
        }
    }

    public function render()
    {
        return view('livewire.dashboard', [
            'stats' => $this->stats,
            'chartData' => $this->chartData,
            'topProducts' => $this->topProducts,
            'recentOrders' => Order::with('customer')
                ->latest()
                ->limit(10)
                ->get(),
            'recentCustomers' => Customer::latest()
                ->limit(5)
                ->get(),
        ])->layout('layouts.app');
    }
}