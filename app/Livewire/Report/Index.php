<?php

namespace App\Livewire\Report;

use App\Models\BusinessSetting;
use App\Models\Category;
use Livewire\Component;
use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\Purchase;
use App\Models\OrderItem;
use App\Models\PurchaseItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PDF;

class Index extends Component
{
    public $reportType = 'sales'; // sales, purchases, inventory, financial, customer, supplier, tax, profit
    public $dateRange = 'today';
    public $startDate;
    public $endDate;
    public $groupBy = 'day'; // day, week, month, year
    public $summary = [];
    public $details = [];

    // Filters
    public $customerId = '';
    public $supplierId = '';
    public $categoryId = '';
    public $productId = '';
    public $paymentMethod = '';
    public $status = '';
    public $userId = '';

    public $business_name;
    public $business_address;
    public $business_phone;
    public $business_email;
    public $receipt_footer;

    // Export options
    public $exportFormat = 'pdf'; // pdf, csv

    protected $queryString = [
        'reportType' => ['except' => 'sales'],
        'dateRange' => ['except' => 'today'],
        'groupBy' => ['except' => 'day'],
    ];

    public function mount($type = null)
    {
        $settings = BusinessSetting::getSettings();
        $this->business_name = $settings->business_name;
        $this->business_address = $settings->business_address;
        $this->business_phone = $settings->business_phone;
        $this->business_email = $settings->business_email;
        $this->receipt_footer = $settings->receipt_footer;

        if ($type) {
            $this->reportType = $type;
        }
        $this->setDateRange();
        $this->loadReport();
    }

    public function setDateRange()
    {
        switch ($this->dateRange) {
            case 'today':
                $this->startDate = Carbon::today()->startOfDay();
                $this->endDate   = Carbon::today()->endOfDay();
                break;

            case 'yesterday':
                $this->startDate = Carbon::yesterday()->startOfDay();
                $this->endDate   = Carbon::yesterday()->endOfDay();
                break;

            case 'week':
                $this->startDate = Carbon::now()->startOfWeek()->startOfDay();
                $this->endDate   = Carbon::now()->endOfWeek()->endOfDay();
                break;

            case 'month':
                $this->startDate = Carbon::now()->startOfMonth()->startOfDay();
                $this->endDate   = Carbon::now()->endOfMonth()->endOfDay();
                break;

            case 'quarter':
                $this->startDate = Carbon::now()->startOfQuarter()->startOfDay();
                $this->endDate   = Carbon::now()->endOfQuarter()->endOfDay();
                break;

            case 'year':
                $this->startDate = Carbon::now()->startOfYear()->startOfDay();
                $this->endDate   = Carbon::now()->endOfYear()->endOfDay();
                break;

            case 'custom':
                if (!$this->startDate) {
                    $this->startDate = Carbon::now()->subDays(30);
                }
                if (!$this->endDate) {
                    $this->endDate = Carbon::now();
                }

                $this->startDate = Carbon::parse($this->startDate)->startOfDay();
                $this->endDate   = Carbon::parse($this->endDate)->endOfDay();
                break;

            default:
                $this->startDate = Carbon::today()->startOfDay();
                $this->endDate   = Carbon::today()->endOfDay();
        }
    }

    public function updatedDateRange()
    {
        $this->setDateRange();
        $this->loadReport();
    }

    public function updatedReportType()
    {
        $this->reset(['customerId', 'supplierId', 'categoryId', 'productId', 'paymentMethod', 'status', 'userId']);
        $this->loadReport();
    }

    public function updatedGroupBy()
    {
        $this->loadReport();
    }

    public function updatedStartDate()
    {
        if ($this->dateRange === 'custom') {
            $this->loadReport();
        }
    }

    public function updatedEndDate()
    {
        if ($this->dateRange === 'custom') {
            $this->loadReport();
        }
    }

    // Filter update methods
    public function updatedCustomerId()
    {
        $this->loadReport();
    }

    public function updatedSupplierId()
    {
        $this->loadReport();
    }

    public function updatedCategoryId()
    {
        $this->loadReport();
    }

    public function updatedProductId()
    {
        $this->loadReport();
    }

    public function updatedPaymentMethod()
    {
        $this->loadReport();
    }

    public function updatedStatus()
    {
        $this->loadReport();
    }

    public function updatedUserId()
    {
        $this->loadReport();
    }

    public function loadReport()
    {
        switch ($this->reportType) {
            case 'sales':
                $this->loadSalesReport();
                break;
            case 'purchases':
                $this->loadPurchasesReport();
                break;
            case 'inventory':
                $this->loadInventoryReport();
                break;
            case 'financial':
                $this->loadFinancialReport();
                break;
            case 'customer':
                $this->loadCustomerReport();
                break;
            case 'supplier':
                $this->loadSupplierReport();
                break;
            case 'tax':
                $this->loadTaxReport();
                break;
            case 'profit':
                $this->loadProfitReport();
                break;
        }
    }

    protected function loadSalesReport()
    {
        $query = Order::whereBetween('order_date', [$this->startDate, $this->endDate])
            ->where('status', '!=', 'cancelled');

        logger('startDate: ' . $this->startDate . ' endDate: ' . $this->endDate);
        // Apply filters
        if ($this->customerId) {
            $query->where('customer_id', $this->customerId);
        }

        if ($this->paymentMethod) {
            $query->whereHas('payments', function ($q) {
                $q->whereHas('method', function ($sq) {
                    $sq->where('slug', $this->paymentMethod);
                });
            });
        }

        if ($this->userId) {
            $query->where('created_by', $this->userId);
        }

        // Sales Summary
        $this->summary = [
            'total_orders' => $query->count(),
            'total_revenue' => $query->sum('total_amount'),
            'total_tax' => $query->sum('tax_amount'),
            'total_discount' => $query->sum('discount_amount'),
            'average_order' => $query->avg('total_amount'),
            'cash_payments' => Payment::whereBetween('created_at', [$this->startDate, $this->endDate])
                ->where('payment_type', 'sale')
                ->whereHas('method', fn($q) => $q->where('slug', 'cash'))
                ->sum('amount'),
            'card_payments' => Payment::whereBetween('created_at', [$this->startDate, $this->endDate])
                ->where('payment_type', 'sale')
                ->whereHas('method', fn($q) => $q->where('slug', 'credit-card'))
                ->sum('amount'),
            'bank_payments' => Payment::whereBetween('created_at', [$this->startDate, $this->endDate])
                ->where('payment_type', 'sale')
                ->whereHas('method', fn($q) => $q->where('slug', 'bank-transfer'))
                ->sum('amount'),
        ];

        // Apply filters to details queries
        $detailsQuery = Order::whereBetween('order_date', [$this->startDate, $this->endDate])
            ->where('status', '!=', 'cancelled');

        if ($this->customerId) {
            $detailsQuery->where('customer_id', $this->customerId);
        }

        if ($this->userId) {
            $detailsQuery->where('created_by', $this->userId);
        }

        // Top Products
        $topProductsQuery = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereBetween('orders.order_date', [$this->startDate, $this->endDate])
            ->where('orders.status', '!=', 'cancelled');

        if ($this->customerId) {
            $topProductsQuery->where('orders.customer_id', $this->customerId);
        }

        if ($this->userId) {
            $topProductsQuery->where('orders.created_by', $this->userId);
        }

        if ($this->categoryId) {
            $topProductsQuery->where('products.category_id', $this->categoryId);
        }

        if ($this->productId) {
            $topProductsQuery->where('products.id', $this->productId);
        }

        $this->details['top_products'] = $topProductsQuery->select(
            'products.id',
            'products.name',
            'products.sku',
            DB::raw('SUM(order_items.quantity) as total_quantity'),
            DB::raw('SUM(order_items.total) as total_sales')
        )
        ->groupBy('products.id', 'products.name', 'products.sku')
        ->orderBy('total_sales', 'desc')
        ->limit(10)
        ->get();

        // Sales by Category
        $byCategoryQuery = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->whereBetween('orders.order_date', [$this->startDate, $this->endDate])
            ->where('orders.status', '!=', 'cancelled');

        if ($this->customerId) {
            $byCategoryQuery->where('orders.customer_id', $this->customerId);
        }

        if ($this->userId) {
            $byCategoryQuery->where('orders.created_by', $this->userId);
        }

        if ($this->productId) {
            $byCategoryQuery->where('products.id', $this->productId);
        }

        $this->details['by_category'] = $byCategoryQuery->select(
            'categories.id',
            'categories.name',
            DB::raw('SUM(order_items.quantity) as total_quantity'),
            DB::raw('SUM(order_items.total) as total_sales')
        )
        ->groupBy('categories.id', 'categories.name')
        ->orderBy('total_sales', 'desc')
        ->get();

        // Daily Sales Breakdown
        $dailyQuery = Order::whereBetween('order_date', [$this->startDate, $this->endDate])
            ->where('status', '!=', 'cancelled');

        if ($this->customerId) {
            $dailyQuery->where('customer_id', $this->customerId);
        }

        if ($this->userId) {
            $dailyQuery->where('created_by', $this->userId);
        }

        $this->details['daily'] = $dailyQuery->select(
            DB::raw('DATE(order_date) as date'),
            DB::raw('COUNT(*) as order_count'),
            DB::raw('SUM(total_amount) as revenue'),
            DB::raw('SUM(discount_amount) as discount'),
            DB::raw('SUM(tax_amount) as tax')
        )
        ->groupBy('date')
        ->orderBy('date', 'desc')
        ->limit(30)
        ->get();
    }

    protected function loadPurchasesReport()
    {
        $query = Purchase::whereBetween('created_at', [$this->startDate, $this->endDate])
            ->where('status', '!=', 'cancelled');

        // Apply filters
        if ($this->supplierId) {
            $query->where('supplier_id', $this->supplierId);
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        if ($this->userId) {
            $query->where('created_by', $this->userId);
        }

        $this->summary = [
            'total_purchases' => $query->count(),
            'total_cost' => $query->sum('total_amount'),
            'total_tax' => $query->sum('tax_amount'),
            'total_discount' => $query->sum('discount_amount'),
            'average_purchase' => $query->avg('total_amount'),
            'pending_purchases' => Purchase::whereBetween('created_at', [$this->startDate, $this->endDate])
                ->where('status', 'ordered')
                ->when($this->supplierId, fn($q) => $q->where('supplier_id', $this->supplierId))
                ->count(),
            'received_purchases' => Purchase::whereBetween('created_at', [$this->startDate, $this->endDate])
                ->where('status', 'received')
                ->when($this->supplierId, fn($q) => $q->where('supplier_id', $this->supplierId))
                ->count(),
        ];

        // Top Products Purchased
        $topProductsQuery = PurchaseItem::join('purchases', 'purchase_items.purchase_id', '=', 'purchases.id')
            ->join('products', 'purchase_items.product_id', '=', 'products.id')
            ->whereBetween('purchases.created_at', [$this->startDate, $this->endDate])
            ->where('purchases.status', '!=', 'cancelled');

        if ($this->supplierId) {
            $topProductsQuery->where('purchases.supplier_id', $this->supplierId);
        }

        if ($this->categoryId) {
            $topProductsQuery->where('products.category_id', $this->categoryId);
        }

        if ($this->productId) {
            $topProductsQuery->where('products.id', $this->productId);
        }

        $this->details['top_products'] = $topProductsQuery->select(
            'products.id',
            'products.name',
            'products.sku',
            DB::raw('SUM(purchase_items.quantity) as total_quantity'),
            DB::raw('SUM(purchase_items.total_cost) as total_cost')
        )
        ->groupBy('products.id', 'products.name', 'products.sku')
        ->orderBy('total_cost', 'desc')
        ->limit(10)
        ->get();

        // Purchases by Supplier
        $bySupplierQuery = Purchase::whereBetween('created_at', [$this->startDate, $this->endDate])
            ->where('status', '!=', 'cancelled')
            ->with('supplier');

        if ($this->supplierId) {
            $bySupplierQuery->where('supplier_id', $this->supplierId);
        }

        $this->details['by_supplier'] = $bySupplierQuery->select(
            'supplier_id',
            DB::raw('COUNT(*) as purchase_count'),
            DB::raw('SUM(total_amount) as total_cost')
        )
        ->groupBy('supplier_id')
        ->orderBy('total_cost', 'desc')
        ->get();
    }

    protected function loadInventoryReport()
    {
        // Apply date filters to inventory - get products with stock movements in date range
        $query = Product::query();

        // Apply filters
        if ($this->categoryId) {
            $query->where('category_id', $this->categoryId);
        }

        if ($this->productId) {
            $query->where('id', $this->productId);
        }

        if ($this->status === 'low_stock') {
            $query->whereRaw('stock_quantity <= low_stock_threshold');
        } elseif ($this->status === 'out_of_stock') {
            $query->where('stock_quantity', '<=', 0);
        } elseif ($this->status === 'in_stock') {
            $query->where('stock_quantity', '>', 0);
        }

        // Get products with their stock movements within date range
        $productIds = $query->pluck('id')->toArray();

        // Calculate stock movements within date range
        $stockIn = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereIn('order_items.product_id', $productIds)
            ->whereBetween('orders.order_date', [$this->startDate, $this->endDate])
            ->where('orders.status', '!=', 'cancelled')
            ->select('order_items.product_id', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->groupBy('order_items.product_id')
            ->get()
            ->keyBy('product_id');

        $stockPurchased = PurchaseItem::join('purchases', 'purchase_items.purchase_id', '=', 'purchases.id')
            ->whereIn('purchase_items.product_id', $productIds)
            ->whereBetween('purchases.created_at', [$this->startDate, $this->endDate])
            ->where('purchases.status', '!=', 'cancelled')
            ->select('purchase_items.product_id', DB::raw('SUM(purchase_items.quantity) as total_purchased'))
            ->groupBy('purchase_items.product_id')
            ->get()
            ->keyBy('product_id');

        // Summary with date range context
        $totalProducts = $query->count();
        $totalValue = $query->sum(DB::raw('stock_quantity * purchase_price'));
        $totalRetailValue = $query->sum(DB::raw('stock_quantity * selling_price'));
        $potentialProfit = $query->sum(DB::raw('stock_quantity * (selling_price - purchase_price)'));
        $lowStockCount = $query->clone()->whereRaw('stock_quantity <= low_stock_threshold')->count();
        $outOfStock = $query->clone()->where('stock_quantity', '<=', 0)->count();
        $inStock = $query->clone()->where('stock_quantity', '>', 0)->count();

        // Calculate total sold and purchased within date range
        $totalSold = $stockIn->sum('total_sold');
        $totalPurchased = $stockPurchased->sum('total_purchased');

        $this->summary = [
            'total_products' => $totalProducts,
            'total_value' => $totalValue,
            'total_retail_value' => $totalRetailValue,
            'potential_profit' => $potentialProfit,
            'low_stock_count' => $lowStockCount,
            'out_of_stock' => $outOfStock,
            'in_stock' => $inStock,
            'total_sold_in_period' => $totalSold,
            'total_purchased_in_period' => $totalPurchased,
        ];

        // Low Stock Products with filters
        $lowStockQuery = Product::with(['category', 'brand'])
            ->whereRaw('stock_quantity <= low_stock_threshold');

        if ($this->categoryId) {
            $lowStockQuery->where('category_id', $this->categoryId);
        }

        $this->details['low_stock'] = $lowStockQuery->orderByRaw('(stock_quantity / low_stock_threshold) ASC')
            ->limit(20)
            ->get();

        // By Category with date range context
        $byCategoryQuery = Product::select(
            'categories.id',
            'categories.name',
            DB::raw('COUNT(*) as product_count'),
            DB::raw('SUM(stock_quantity) as total_stock'),
            DB::raw('SUM(stock_quantity * purchase_price) as stock_value')
        )
        ->join('categories', 'products.category_id', '=', 'categories.id');

        if ($this->categoryId) {
            $byCategoryQuery->where('categories.id', $this->categoryId);
        }

        $this->details['by_category'] = $byCategoryQuery->groupBy('categories.id', 'categories.name')
            ->orderBy('stock_value', 'desc')
            ->get();

        // Add stock movement summary per category
        foreach ($this->details['by_category'] as $category) {
            $categoryProductIds = Product::where('category_id', $category->id)->pluck('id')->toArray();
            
            $categorySold = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
                ->whereIn('order_items.product_id', $categoryProductIds)
                ->whereBetween('orders.order_date', [$this->startDate, $this->endDate])
                ->where('orders.status', '!=', 'cancelled')
                ->sum('order_items.quantity');
                
            $categoryPurchased = PurchaseItem::join('purchases', 'purchase_items.purchase_id', '=', 'purchases.id')
                ->whereIn('purchase_items.product_id', $categoryProductIds)
                ->whereBetween('purchases.created_at', [$this->startDate, $this->endDate])
                ->where('purchases.status', '!=', 'cancelled')
                ->sum('purchase_items.quantity');
                
            $category->sold_in_period = $categorySold;
            $category->purchased_in_period = $categoryPurchased;
        }

        // By Brand
        $this->details['by_brand'] = Product::select(
            'brands.id',
            'brands.name',
            DB::raw('COUNT(*) as product_count'),
            DB::raw('SUM(stock_quantity) as total_stock'),
            DB::raw('SUM(stock_quantity * purchase_price) as stock_value')
        )
        ->join('brands', 'products.brand_id', '=', 'brands.id')
        ->when($this->categoryId, fn($q) => $q->where('products.category_id', $this->categoryId))
        ->groupBy('brands.id', 'brands.name')
        ->orderBy('stock_value', 'desc')
        ->get();

        // Top Products with date range context
        $topProductsQuery = Product::with(['category', 'brand'])
            ->orderByRaw('stock_quantity * purchase_price DESC')
            ->limit(10);

        if ($this->categoryId) {
            $topProductsQuery->where('category_id', $this->categoryId);
        }

        $topProducts = $topProductsQuery->get();
        
        // Add movement data to top products
        foreach ($topProducts as $product) {
            $product->sold_in_period = $stockIn[$product->id]->total_sold ?? 0;
            $product->purchased_in_period = $stockPurchased[$product->id]->total_purchased ?? 0;
            $product->net_movement = ($product->purchased_in_period ?? 0) - ($product->sold_in_period ?? 0);
        }
        
        $this->details['top_products'] = $topProducts;
    }

    protected function loadFinancialReport()
    {
        $revenueQuery = Order::whereBetween('order_date', [$this->startDate, $this->endDate])
            ->where('status', 'completed');

        if ($this->customerId) {
            $revenueQuery->where('customer_id', $this->customerId);
        }

        if ($this->userId) {
            $revenueQuery->where('created_by', $this->userId);
        }

        $revenue = $revenueQuery->sum('total_amount');

        $expensesQuery = Expense::whereBetween('created_at', [$this->startDate, $this->endDate]);

        if ($this->categoryId) {
            $expensesQuery->where('category_id', $this->categoryId);
        }

        $expenses = $expensesQuery->sum('amount');

        $purchasesQuery = Purchase::whereBetween('created_at', [$this->startDate, $this->endDate])
            ->where('status', 'received');

        if ($this->supplierId) {
            $purchasesQuery->where('supplier_id', $this->supplierId);
        }

        $purchases = $purchasesQuery->sum('total_amount');

        $refundsQuery = Payment::whereBetween('created_at', [$this->startDate, $this->endDate])
            ->where('payment_type', 'refund')
            ->where('amount', '<', 0);

        if ($this->customerId) {
            $refundsQuery->whereHas('order', fn($q) => $q->where('customer_id', $this->customerId));
        }

        $refunds = $refundsQuery->sum('amount');

        $taxCollectedQuery = Order::whereBetween('order_date', [$this->startDate, $this->endDate])
            ->where('status', 'completed');

        if ($this->customerId) {
            $taxCollectedQuery->where('customer_id', $this->customerId);
        }

        $taxCollected = $taxCollectedQuery->sum('tax_amount');

        $taxPaidQuery = Purchase::whereBetween('created_at', [$this->startDate, $this->endDate])
            ->where('status', 'received');

        if ($this->supplierId) {
            $taxPaidQuery->where('supplier_id', $this->supplierId);
        }

        $taxPaid = $taxPaidQuery->sum('tax_amount');

        $this->summary = [
            'revenue' => $revenue,
            'expenses' => $expenses,
            'purchases' => $purchases,
            'refunds' => abs($refunds),
            'net_income' => $revenue - $purchases - $expenses - abs($refunds),
            'gross_profit' => $revenue - $purchases,
            'profit_margin' => $revenue > 0 ? (($revenue - $purchases) / $revenue) * 100 : 0,
            'tax_collected' => $taxCollected,
            'tax_paid' => $taxPaid,
            'tax_due' => $taxCollected - $taxPaid,
        ];
    }

    protected function loadCustomerReport()
    {
        // Apply date range to customer report
        $query = Customer::query();

        if ($this->status !== '') {
            $query->where('is_active', $this->status);
        }

        if ($this->customerId) {
            $query->where('id', $this->customerId);
        }

        $customers = $query->get();
        $customerIds = $customers->pluck('id')->toArray();

        // Get customer orders within date range
        $customerOrders = Order::whereIn('customer_id', $customerIds)
            ->whereBetween('order_date', [$this->startDate, $this->endDate])
            ->where('status', '!=', 'cancelled')
            ->get();

        $customerOrdersCompleted = $customerOrders->where('status', 'completed');

        $totalOrders = $customerOrders->count();
        $totalSpent = $customerOrdersCompleted->sum('total_amount');
        $uniqueCustomers = $customerOrders->unique('customer_id')->count();

        $this->summary = [
            'total_customers' => $query->count(),
            'active_customers' => Customer::where('is_active', true)->count(),
            'inactive_customers' => Customer::where('is_active', false)->count(),
            //'customers_with_credit' => Customer::where('current_balance', '>', 0)->count(),
            //'total_credit' => Customer::sum('current_balance'),
            //'average_credit' => Customer::where('current_balance', '>', 0)->avg('current_balance'),
            'total_orders' => $totalOrders,
            'total_spent' => $totalSpent,
            'average_spent_per_customer' => $uniqueCustomers > 0 ? $totalSpent / $uniqueCustomers : 0,
            'customers_with_orders' => $uniqueCustomers,
        ];

        // Top customers by spending in date range
        $topCustomersQuery = Customer::withSum(['orders' => function ($q) {
            $q->where('status', 'completed')
                ->whereBetween('created_at', [$this->startDate, $this->endDate]);
        }], 'total_amount');

        if ($this->customerId) {
            $topCustomersQuery->where('id', $this->customerId);
        }

        $this->details['top_customers'] = $topCustomersQuery->orderBy('orders_sum_total_amount', 'desc')
            ->limit(10)
            ->get()
            ->filter(function ($customer) {
                return $customer->orders_sum_total_amount > 0;
            });

        // Customers with highest balances
        $balanceQuery = Customer::where('current_balance', '>', 0)
            ->orderBy('current_balance', 'desc')
            ->limit(10);

        if ($this->customerId) {
            $balanceQuery->where('id', $this->customerId);
        }

        $this->details['customers_with_balance'] = $balanceQuery->get();

        // Customer order history within date range
        $this->details['order_history'] = Order::whereIn('customer_id', $customerIds)
            ->whereBetween('order_date', [$this->startDate, $this->endDate])
            ->where('status', '!=', 'cancelled')
            ->select(
                'customer_id',
                DB::raw('COUNT(*) as order_count'),
                DB::raw('SUM(total_amount) as total_spent'),
                DB::raw('AVG(total_amount) as average_order_value')
            )
            ->groupBy('customer_id')
            ->with('customer')
            ->orderBy('total_spent', 'desc')
            ->limit(20)
            ->get();
    }

    protected function loadSupplierReport()
    {
        // Apply date range to supplier report
        $purchaseQuery = Purchase::where('status', 'received')
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->when($this->supplierId, fn($q) => $q->where('supplier_id', $this->supplierId));

        $supplierIds = $purchaseQuery->distinct()->pluck('supplier_id')->toArray();

        $this->summary = [
            'total_suppliers' => Supplier::count(),
            'active_suppliers' => Supplier::where('is_active', true)->count(),
            'inactive_suppliers' => Supplier::where('is_active', false)->count(),
            'total_purchases' => $purchaseQuery->count(),
            'total_spent' => $purchaseQuery->sum('total_amount'),
            'average_purchase' => $purchaseQuery->avg('total_amount') ?? 0,
            'pending_purchases' => Purchase::where('status', 'ordered')
                ->whereBetween('created_at', [$this->startDate, $this->endDate])
                ->when($this->supplierId, fn($q) => $q->where('supplier_id', $this->supplierId))
                ->sum('total_amount'),
            'active_suppliers_in_period' => count(array_unique($supplierIds)),
        ];

        // Top suppliers by purchase volume in date range
        $topSuppliersQuery = Supplier::withCount(['purchases' => function ($q) {
            $q->where('status', 'received')
                ->whereBetween('created_at', [$this->startDate, $this->endDate]);
        }])
        ->withSum(['purchases' => function ($q) {
            $q->where('status', 'received')
                ->whereBetween('created_at', [$this->startDate, $this->endDate]);
        }], 'total_amount');

        if ($this->supplierId) {
            $topSuppliersQuery->where('id', $this->supplierId);
        }

        $this->details['top_suppliers'] = $topSuppliersQuery->orderBy('purchases_sum_total_amount', 'desc')
            ->limit(10)
            ->get()
            ->filter(function ($supplier) {
                return $supplier->purchases_sum_total_amount > 0;
            });

        // Purchases by supplier over time
        $bySupplierQuery = Purchase::whereBetween('created_at', [$this->startDate, $this->endDate])
            ->where('status', 'received')
            ->with('supplier');

        if ($this->supplierId) {
            $bySupplierQuery->where('supplier_id', $this->supplierId);
        }

        $this->details['by_supplier'] = $bySupplierQuery->select(
            'supplier_id',
            DB::raw('COUNT(*) as purchase_count'),
            DB::raw('SUM(total_amount) as total_spent'),
            DB::raw('AVG(total_amount) as average_purchase')
        )
        ->groupBy('supplier_id')
        ->orderBy('total_spent', 'desc')
        ->limit(10)
        ->get();

        // Purchases trend by date
        $this->details['purchase_trend'] = Purchase::whereBetween('created_at', [$this->startDate, $this->endDate])
            ->where('status', 'received')
            ->when($this->supplierId, fn($q) => $q->where('supplier_id', $this->supplierId))
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total_amount) as total')
            )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();
    }

    protected function loadTaxReport()
    {
        $salesTaxQuery = Order::whereBetween('order_date', [$this->startDate, $this->endDate])
            ->where('status', 'completed');

        if ($this->customerId) {
            $salesTaxQuery->where('customer_id', $this->customerId);
        }

        if ($this->userId) {
            $salesTaxQuery->where('created_by', $this->userId);
        }

        $salesTax = $salesTaxQuery->select(
            DB::raw('SUM(tax_amount) as total_tax'),
            DB::raw('COUNT(*) as transaction_count')
        )
        ->first();

        $purchaseTaxQuery = Purchase::whereBetween('created_at', [$this->startDate, $this->endDate])
            ->where('status', 'received');

        if ($this->supplierId) {
            $purchaseTaxQuery->where('supplier_id', $this->supplierId);
        }

        $purchaseTax = $purchaseTaxQuery->select(
            DB::raw('SUM(tax_amount) as total_tax'),
            DB::raw('COUNT(*) as transaction_count')
        )
        ->first();

        $this->summary = [
            'sales_tax_collected' => $salesTax->total_tax ?? 0,
            'sales_transactions' => $salesTax->transaction_count ?? 0,
            'purchase_tax_paid' => $purchaseTax->total_tax ?? 0,
            'purchase_transactions' => $purchaseTax->transaction_count ?? 0,
            'net_tax_payable' => ($salesTax->total_tax ?? 0) - ($purchaseTax->total_tax ?? 0),
        ];

        // Tax by product category
        $byCategoryQuery = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->whereBetween('orders.order_date', [$this->startDate, $this->endDate])
            ->where('orders.status', 'completed');

        if ($this->customerId) {
            $byCategoryQuery->where('orders.customer_id', $this->customerId);
        }

        if ($this->categoryId) {
            $byCategoryQuery->where('categories.id', $this->categoryId);
        }

        if ($this->productId) {
            $byCategoryQuery->where('products.id', $this->productId);
        }

        $this->details['by_category'] = $byCategoryQuery->select(
            'categories.name',
            DB::raw('SUM(order_items.tax_amount) as total_tax'),
            DB::raw('SUM(order_items.total) as total_sales')
        )
        ->groupBy('categories.id', 'categories.name')
        ->orderBy('total_tax', 'desc')
        ->get();

        // Tax trend by date
        $this->details['tax_trend'] = Order::whereBetween('order_date', [$this->startDate, $this->endDate])
            ->where('status', 'completed')
            ->when($this->customerId, fn($q) => $q->where('customer_id', $this->customerId))
            ->select(
                DB::raw('DATE(order_date) as date'),
                DB::raw('SUM(tax_amount) as tax_collected'),
                DB::raw('COUNT(*) as transaction_count')
            )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();
    }

    protected function loadProfitReport()
    {
        // Get all completed orders in date range
        $ordersQuery = Order::whereBetween('order_date', [$this->startDate, $this->endDate])
            ->where('status', 'completed')
            ->with('items');

        if ($this->customerId) {
            $ordersQuery->where('customer_id', $this->customerId);
        }

        if ($this->userId) {
            $ordersQuery->where('created_by', $this->userId);
        }

        $orders = $ordersQuery->get();

        $totalRevenue = $orders->sum('total_amount');
        $totalDiscount = $orders->sum('discount_amount');
        $totalTax = $orders->sum('tax_amount');

        // Calculate cost of goods sold
        $cogs = 0;
        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                // Use average purchase price or actual cost if available
                $product = Product::find($item->product_id);
                if ($product) {
                    $cogs += $product->purchase_price * $item->quantity;
                }
            }
        }

        // Get expenses
        $expensesQuery = Expense::whereBetween('created_at', [$this->startDate, $this->endDate]);

        if ($this->categoryId) {
            $expensesQuery->where('category_id', $this->categoryId);
        }

        $expenses = $expensesQuery->sum('amount');

        $grossProfit = $totalRevenue - $cogs;
        $netProfit = $grossProfit - $expenses;

        $this->summary = [
            'total_revenue' => $totalRevenue,
            'total_discount' => $totalDiscount,
            'total_tax' => $totalTax,
            'cost_of_goods_sold' => $cogs,
            'gross_profit' => $grossProfit,
            'expenses' => $expenses,
            'net_profit' => $netProfit,
            'gross_margin' => $totalRevenue > 0 ? ($grossProfit / $totalRevenue) * 100 : 0,
            'net_margin' => $totalRevenue > 0 ? ($netProfit / $totalRevenue) * 100 : 0,
        ];

        // Profit by product
        $byProductQuery = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereBetween('orders.order_date', [$this->startDate, $this->endDate])
            ->where('orders.status', 'completed');

        if ($this->customerId) {
            $byProductQuery->where('orders.customer_id', $this->customerId);
        }

        if ($this->categoryId) {
            $byProductQuery->where('products.category_id', $this->categoryId);
        }

        if ($this->productId) {
            $byProductQuery->where('products.id', $this->productId);
        }

        $this->details['by_product'] = $byProductQuery->select(
            'products.id',
            'products.name',
            'products.sku',
            DB::raw('SUM(order_items.quantity) as quantity_sold'),
            DB::raw('SUM(order_items.total) as revenue'),
            DB::raw('SUM(order_items.quantity * products.purchase_price) as cost'),
            DB::raw('SUM(order_items.total - (order_items.quantity * products.purchase_price)) as profit')
        )
        ->groupBy('products.id', 'products.name', 'products.sku')
        ->orderBy('profit', 'desc')
        ->limit(10)
        ->get();

        // Profit by category
        $byCategoryQuery = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->whereBetween('orders.order_date', [$this->startDate, $this->endDate])
            ->where('orders.status', 'completed');

        if ($this->customerId) {
            $byCategoryQuery->where('orders.customer_id', $this->customerId);
        }

        if ($this->categoryId) {
            $byCategoryQuery->where('categories.id', $this->categoryId);
        }

        if ($this->productId) {
            $byCategoryQuery->where('products.id', $this->productId);
        }

        $this->details['by_category'] = $byCategoryQuery->select(
            'categories.id',
            'categories.name',
            DB::raw('SUM(order_items.total) as revenue'),
            DB::raw('SUM(order_items.quantity * products.purchase_price) as cost'),
            DB::raw('SUM(order_items.total - (order_items.quantity * products.purchase_price)) as profit')
        )
        ->groupBy('categories.id', 'categories.name')
        ->orderBy('profit', 'desc')
        ->get();

        // Profit trend by date
        $this->details['profit_trend'] = Order::whereBetween('order_date', [$this->startDate, $this->endDate])
            ->where('status', 'completed')
            ->when($this->customerId, fn($q) => $q->where('customer_id', $this->customerId))
            ->select(
                DB::raw('DATE(order_date) as date'),
                DB::raw('SUM(total_amount) as revenue'),
                DB::raw('COUNT(*) as order_count')
            )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->map(function ($item) {
                // Calculate approximate profit per day based on average margin
                $item->profit = $item->revenue * 0.2; // Assuming 20% average margin
                return $item;
            });
    }

    protected function getDateGroupQuery($field)
    {
        switch ($this->groupBy) {
            case 'hour':
                return DB::raw("DATE_FORMAT($field, '%Y-%m-%d %H:00') as date");
            case 'day':
                return DB::raw("DATE($field) as date");
            case 'week':
                return DB::raw("DATE_FORMAT($field, '%Y-%u') as date");
            case 'month':
                return DB::raw("DATE_FORMAT($field, '%Y-%m') as date");
            case 'year':
                return DB::raw("YEAR($field) as date");
            default:
                return DB::raw("DATE($field) as date");
        }
    }

    public function exportReport($format = null)
    {
        // Set the format if provided
        if ($format) {
            $this->exportFormat = $format;
        }
        $this->loadReport();
        $data = [
            'reportType' => $this->reportType,
            'startDate' => $this->startDate instanceof Carbon ? $this->startDate->format('Y-m-d') : $this->startDate,
            'endDate' => $this->endDate instanceof Carbon ? $this->endDate->format('Y-m-d') : $this->endDate,
            'summary' => $this->summary,
            'details' => $this->details,
            'business_name' => $this->business_name,
            'business_address' => $this->business_address,
            'business_phone' => $this->business_phone,
            'business_email' => $this->business_email,
            'receipt_footer' => $this->receipt_footer,
        ];

        // Log activity
        logActivity(
            'exported_report',
            new BusinessSetting(),
            [],
            [
                'report_type' => $this->reportType,
                'format' => $this->exportFormat,
                'date_range' => $this->dateRange,
                'start_date' => $this->startDate instanceof Carbon ? $this->startDate->format('Y-m-d') : $this->startDate,
                'end_date' => $this->endDate instanceof Carbon ? $this->endDate->format('Y-m-d') : $this->endDate,
                'filters' => [
                    'customer_id' => $this->customerId,
                    'supplier_id' => $this->supplierId,
                    'category_id' => $this->categoryId,
                    'product_id' => $this->productId,
                    'payment_method' => $this->paymentMethod,
                    'status' => $this->status,
                    'user_id' => $this->userId,
                ]
            ]
        );

        if ($this->exportFormat === 'pdf') {
            return $this->exportToPdf($data);
        } else {
            return $this->exportToCsv($data);
        }
    }

    protected function exportToPdf($data)
    {
        try {
            $data['business_name'] = $this->business_name;
            $data['business_address'] = $this->business_address;
            $data['business_phone'] = $this->business_phone;
            $data['business_email'] = $this->business_email;
            $data['receipt_footer'] = $this->receipt_footer;

            $pdf = PDF::loadView('reports.pdf.' . $this->reportType, $data);

            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->output();
            }, $this->reportType . '-report-' . now()->format('Y-m-d') . '.pdf');
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'message' => 'Error generating PDF: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    protected function exportToCsv($data)
    {
        try {
            $filename = $this->reportType . '-report-' . now()->format('Y-m-d') . '.csv';
            $handle = fopen('php://memory', 'r+');

            // Add report header
            fputcsv($handle, ['Report Type', ucfirst($this->reportType) . ' Report']);
            fputcsv($handle, ['Period', date('Y-m-d', strtotime($this->startDate)) . ' to ' . date('Y-m-d', strtotime($this->endDate))]);
            fputcsv($handle, []); // Empty line

            // Add summary
            fputcsv($handle, ['SUMMARY']);
            fputcsv($handle, ['Metric', 'Value']);
            foreach ($data['summary'] as $key => $value) {
                $formattedValue = is_numeric($value) ? number_format($value, 2) : $value;
                fputcsv($handle, [ucwords(str_replace('_', ' ', $key)), $formattedValue]);
            }
            fputcsv($handle, []); // Empty line

            // Add products if available
            if (!empty($data['details']['top_products'])) {
                fputcsv($handle, ['TOP SELLING PRODUCTS']);
                fputcsv($handle, ['Product', 'SKU', 'Quantity', 'Sales ($)']);
                foreach ($data['details']['top_products'] as $product) {
                    fputcsv($handle, [
                        $product->name,
                        $product->sku,
                        number_format($product->total_quantity),
                        number_format($product->total_sales, 2)
                    ]);
                }
                fputcsv($handle, []); // Empty line
            }

            // Add categories if available
            if (!empty($data['details']['by_category'])) {
                fputcsv($handle, ['SALES BY CATEGORY']);
                fputcsv($handle, ['Category', 'Quantity', 'Sales ($)', 'Percentage']);

                $totalSales = collect($data['details']['by_category'])->sum('total_sales');

                foreach ($data['details']['by_category'] as $category) {
                    $percentage = $totalSales > 0 ? ($category->total_sales / $totalSales) * 100 : 0;
                    fputcsv($handle, [
                        $category->name,
                        number_format($category->total_quantity),
                        number_format($category->total_sales, 2),
                        number_format($percentage, 1) . '%'
                    ]);
                }
                fputcsv($handle, []); // Empty line
            }

            // Add daily breakdown if available
            if (!empty($data['details']['daily'])) {
                fputcsv($handle, ['DAILY BREAKDOWN']);
                fputcsv($handle, ['Date', 'Orders', 'Revenue ($)', 'Tax ($)', 'Discount ($)']);
                foreach ($data['details']['daily'] as $day) {
                    fputcsv($handle, [
                        $day->date,
                        $day->order_count,
                        number_format($day->revenue, 2),
                        number_format($day->tax ?? 0, 2),
                        number_format($day->discount ?? 0, 2)
                    ]);
                }
            }

            rewind($handle);
            $content = stream_get_contents($handle);
            fclose($handle);

            return response()->streamDownload(function () use ($content) {
                echo $content;
            }, $filename, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'message' => 'Error generating CSV: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    protected function getCsvHeaders()
    {
        switch ($this->reportType) {
            case 'sales':
                return ['Date', 'Orders', 'Revenue', 'Tax', 'Discount'];
            case 'purchases':
                return ['Date', 'Purchases', 'Total Cost', 'Tax', 'Discount'];
            case 'inventory':
                return ['Product', 'SKU', 'Stock', 'Unit Cost', 'Total Value'];
            default:
                return ['Date', 'Value'];
        }
    }

    protected function getCsvRows()
    {
        $rows = [];

        switch ($this->reportType) {
            case 'sales':
                foreach ($this->details['daily'] ?? [] as $item) {
                    $rows[] = [
                        $item->date,
                        $item->order_count,
                        $item->revenue,
                        $item->tax ?? 0,
                        $item->discount ?? 0,
                    ];
                }
                break;

            case 'inventory':
                foreach ($this->details['top_products'] ?? [] as $item) {
                    $rows[] = [
                        $item->name,
                        $item->sku,
                        $item->stock_quantity,
                        $item->purchase_price,
                        $item->stock_quantity * $item->purchase_price,
                    ];
                }
                break;
        }

        return $rows;
    }

    public function getSummaryCardClass($key, $value)
    {
        if (str_contains($key, 'profit') || str_contains($key, 'income')) {
            return $value >= 0 ? 'bg-success-gradient' : 'bg-danger-gradient';
        }

        if (str_contains($key, 'loss') || str_contains($key, 'expense')) {
            return 'bg-danger-gradient';
        }

        if (str_contains($key, 'tax')) {
            return 'bg-warning-gradient';
        }

        if (str_contains($key, 'count') || str_contains($key, 'total_')) {
            return 'bg-info-gradient';
        }

        return 'bg-primary-gradient';
    }

    public function getSummaryIcon($key)
    {
        $icons = [
            'revenue' => 'fas fa-dollar-sign',
            'expenses' => 'fas fa-shopping-cart',
            'profit' => 'fas fa-chart-line',
            'loss' => 'fas fa-chart-line',
            'tax' => 'fas fa-percent',
            'count' => 'fas fa-shopping-bag',
            'orders' => 'fas fa-shopping-cart',
            'customers' => 'fas fa-users',
            'suppliers' => 'fas fa-truck',
            'products' => 'fas fa-box',
            'value' => 'fas fa-money-bill',
            'average' => 'fas fa-calculator',
            'total' => 'fas fa-calculator',
        ];

        foreach ($icons as $pattern => $icon) {
            if (str_contains($key, $pattern)) {
                return $icon;
            }
        }

        return 'fas fa-chart-bar';
    }

    public function formatSummaryLabel($key)
    {
        return str_replace('_', ' ', ucwords($key));
    }

    public function formatSummaryValue($key, $value)
    {
        if (!is_numeric($value)) {
            return $value;
        }

        // Keys that should have 2 decimals
        $decimalKeys = [
            'total_discount',
            'total_tax',
            'total_revenue',
            'average_order',
            'average_purchase',
            'total_spent',
            'total_cost',
            'refunds',
            'net_income',
            'gross_profit',
            'net_profit',
            'potential_profit',
            'average_spent_per_customer',
            'total_credit',
            'average_credit',
        ];

        // Keys that should be integers
        $integerKeys = [
            'total_orders',
            'count',
            'quantity',
            'stock',
            'total_customers',
            'active_customers',
            'inactive_customers',
            'total_suppliers',
            'active_suppliers',
            'inactive_suppliers',
            'customers_with_credit',
            'total_purchases',
            'total_products',
            'low_stock_count',
            'out_of_stock',
            'in_stock',
            'purchase_transactions',
            'sales_transactions',
            'customers_with_orders',
            'active_suppliers_in_period',
            'total_sold_in_period',
            'total_purchased_in_period',
        ];

        if (in_array($key, $decimalKeys)) {
            return number_format((float) $value, 2);
        }

        if (in_array($key, $integerKeys)) {
            return number_format((int) $value);
        }

        return number_format((float) $value, 2);
    }

    public function getCardBorderColor($key, $value)
    {
        if (str_contains($key, 'profit') || str_contains($key, 'income')) {
            return $value >= 0 ? 'success' : 'danger';
        }

        if (str_contains($key, 'loss') || str_contains($key, 'expense') || str_contains($key, 'refund')) {
            return 'danger';
        }

        if (str_contains($key, 'tax')) {
            return 'warning';
        }

        if (str_contains($key, 'count') || str_contains($key, 'total_orders') || str_contains($key, 'transactions')) {
            return 'primary';
        }

        if (str_contains($key, 'stock') || str_contains($key, 'inventory')) {
            return 'info';
        }

        if (str_contains($key, 'customer') || str_contains($key, 'user')) {
            return 'success';
        }

        if (str_contains($key, 'supplier')) {
            return 'secondary';
        }

        if (str_contains($key, 'average') || str_contains($key, 'avg')) {
            return 'info';
        }

        return 'primary';
    }

    public function getCardBorderColorValue($key, $value)
    {
        $colors = [
            'primary' => '#4e73df',
            'success' => '#1cc88a',
            'info' => '#36b9cc',
            'warning' => '#f6c23e',
            'danger' => '#e74a3b',
            'secondary' => '#858796',
        ];

        $borderClass = $this->getCardBorderColor($key, $value);
        return $colors[$borderClass] ?? $colors['primary'];
    }

    public function getCardTextColor($key, $value)
    {
        $borderClass = $this->getCardBorderColor($key, $value);

        $colors = [
            'primary' => 'text-primary',
            'success' => 'text-success',
            'info' => 'text-info',
            'warning' => 'text-warning',
            'danger' => 'text-danger',
            'secondary' => 'text-secondary',
        ];

        return $colors[$borderClass] ?? 'text-primary';
    }

    public function getIconColorClass($key, $value)
    {
        $borderClass = $this->getCardBorderColor($key, $value);

        $colors = [
            'primary' => 'text-primary',
            'success' => 'text-success',
            'info' => 'text-info',
            'warning' => 'text-warning',
            'danger' => 'text-danger',
            'secondary' => 'text-secondary',
        ];

        return $colors[$borderClass] ?? 'text-gray-300';
    }

    public function getProgressBarClass($key, $value)
    {
        $borderClass = $this->getCardBorderColor($key, $value);

        $classes = [
            'primary' => 'bg-primary',
            'success' => 'bg-success',
            'info' => 'bg-info',
            'warning' => 'bg-warning',
            'danger' => 'bg-danger',
            'secondary' => 'bg-secondary',
        ];

        return $classes[$borderClass] ?? 'bg-primary';
    }

    public function render()
    {
        return view('livewire.reports.index', [
            'customers' => Customer::select('id', 'name')->orderBy('name')->get(),
            'suppliers' => Supplier::select('id', 'name')->orderBy('name')->get(),
            'categories' => Category::select('id', 'name')->orderBy('name')->get(),
            'products' => Product::select('id', 'name')->orderBy('name')->limit(100)->get(),
            'users' => User::select('id', 'name')->orderBy('name')->get(),
        ])->layout('layouts.app');
    }
}