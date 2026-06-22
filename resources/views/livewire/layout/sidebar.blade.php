<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{route('dashboard')}}" class="app-brand-link">
            <img src="{{getLogo()}}" alt="logo" width="150px">
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="ti menu-toggle-icon d-none d-xl-block ti-sm align-middle"></i>
            <i class="ti ti-x d-block d-xl-none ti-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow mb-2"></div>
    <ul class="menu-inner py-1">

        <!-- Dashboard  -->
        @can('dashboard')
        <li class="mt-2 menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <a href="{{ route('dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-layout-dashboard"></i>
                <div>Dashboard</div>
            </a>
        </li>
        @endcan

        <!-- Products Section -->
        @canany(['view products', 'create products', 'edit products', 'delete products'])
        <li class="menu-header small text-uppercase mt-2">
            <span class="menu-header-text">Products</span>
        </li>
        @endcanany

        @can('view products')
        <li class="menu-item {{ request()->routeIs('products*') ? 'active' : '' }}">
            <a href="{{ route('products.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-box"></i>
                <div>Products</div>
            </a>
        </li>
        @endcan

        @can('view categories')
        <li class="menu-item {{ request()->routeIs('categories*') ? 'active' : '' }}">
            <a href="{{ route('categories.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-tags"></i>
                <div>Categories</div>
            </a>
        </li>
        @endcan

        @can('view brands')
        <li class="menu-item {{ request()->routeIs('brands*') ? 'active' : '' }}">
            <a href="{{ route('brands.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-copyright"></i>
                <div>Brands</div>
            </a>
        </li>
        @endcan

        <!-- Sales & Purchases Section -->
        @canany(['view orders', 'view customers', 'view suppliers', 'view purchases'])
        <li class="menu-header small text-uppercase mt-2">
            <span class="menu-header-text">Sales & Purchases</span>
        </li>
        @endcanany

        @can('view orders')
        <li class="menu-item {{ request()->routeIs('orders*') ? 'active' : '' }}">
            <a href="{{ route('orders.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-shopping-cart"></i>
                <div>Orders</div>
            </a>
        </li>
        @endcan

        @can('view customers')
        <li class="menu-item {{ request()->routeIs('customers*') ? 'active' : '' }}">
            <a href="{{ route('customers.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-users"></i>
                <div>Customers</div>
            </a>
        </li>
        @endcan

        @can('view suppliers')
        <li class="menu-item {{ request()->routeIs('suppliers*') ? 'active' : '' }}">
            <a href="{{ route('suppliers.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-truck-delivery"></i>
                <div>Suppliers</div>
            </a>
        </li>
        @endcan

        @can('view purchases')
        <li class="menu-item {{ request()->routeIs('purchases*') ? 'active' : '' }}">
            <a href="{{ route('purchases.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-basket"></i>
                <div>Purchases</div>
            </a>
        </li>
        @endcan

        <!-- Inventory & Expenses Section -->
        @canany(['view products', 'view expenses', 'manage stock'])
        <li class="menu-header small text-uppercase mt-2">
            <span class="menu-header-text">Inventory & Expenses</span>
        </li>
        @endcanany

        @can('view products')
        <li class="menu-item {{ request()->routeIs('inventory*') ? 'active' : '' }}">
            <a href="{{ route('inventory.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-box"></i>
                <div>Inventory</div>
            </a>
        </li>
        @endcan

        @can('view expenses')
        <li class="menu-item {{ request()->routeIs('expenses*') ? 'active' : '' }}">
            <a href="{{ route('expenses.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-file-invoice"></i>
                <div>Expenses</div>
            </a>
        </li>
        @endcan

        <!-- Reports -->
        @can('view reports')
        <li class="menu-item {{ request()->routeIs('reports*') ? 'active' : '' }}">
            <a href="{{ route('reports.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-chart-pie"></i>
                <div>Reports</div>
            </a>
        </li>
        @endcan

        <!-- System Section -->
        @canany(['manage settings', 'view activity logs'])
        <li class="menu-header small text-uppercase mt-2">
            <span class="menu-header-text">System</span>
        </li>
        @endcanany

        @can('manage settings')
        <li class="menu-item {{ request()->routeIs('settings*') ? 'active' : '' }}">
            <a href="{{ route('settings.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-settings"></i>
                <div>Settings</div>
            </a>
        </li>
        @endcan

        @can('view activity logs')
        <li class="menu-item {{ request()->routeIs('activity-logs*') ? 'active' : '' }}">
            <a href="{{ route('activity-logs.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-history"></i>
                <div>Activity Logs</div>
            </a>
        </li>
        @endcan


    </ul>
</aside>