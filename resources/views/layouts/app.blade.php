<!DOCTYPE html>
<html lang="en" data-bs-theme="light">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no" />
    <title>{{ config('app.name', 'POS System') }}</title>
    <base href="{{ URL::to('/') }}/">
    <meta name="description" content="POS System" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/logo.png') }}" />

    <!-- Google Font (Inter) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <link rel="stylesheet" href="{{ asset('assets/styles.css') }}">
    @livewireStyles
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>

<body>
    <div class="main-wrapper">
        <aside class="sidebar" id="sidebar">
            <div class="brand">
                <a href="{{ route('dashboard') }}" class="d-flex align-items-center gap-2">
                    <img src="{{ asset('assets/img/logo.png') }}" alt="logo" style="max-height:38px; width:150px" class="img-fluid">
                </a>
                <button class="btn-close btn-close-white d-md-none" type="button" data-bs-dismiss="sidebar" aria-label="Close" onclick="document.getElementById('sidebar').classList.remove('show')"></button>
            </div>

            <div class="menu-inner py-2">
                <!-- Dashboard -->
                @can('dashboard')
                <div class="menu-item mt-2">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="bi bi-grid-1x2-fill"></i> Dashboard
                    </a>
                </div>
                @endcan

                <!-- Products section -->
                @canany(['view products', 'create products', 'edit products', 'delete products'])
                <div class="menu-header">Products</div>
                @endcanany
                @can('view products')
                <div class="menu-item">
                    <a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products*') ? 'active' : '' }}">
                        <i class="bi bi-box"></i> Products
                    </a>
                </div>
                @endcan
                @can('view categories')
                <div class="menu-item">
                    <a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories*') ? 'active' : '' }}">
                        <i class="bi bi-tags"></i> Categories
                    </a>
                </div>
                @endcan
                @can('view brands')
                <div class="menu-item">
                    <a href="{{ route('brands.index') }}" class="nav-link {{ request()->routeIs('brands*') ? 'active' : '' }}">
                        <i class="bi bi-c-circle"></i> Brands
                    </a>
                </div>
                @endcan

                <!-- Sales & Purchases -->
                @canany(['view orders', 'view customers', 'view suppliers', 'view purchases'])
                <div class="menu-header">Sales & Purchases</div>
                @endcanany
                @can('view orders')
                <div class="menu-item">
                    <a href="{{ route('orders.index') }}" class="nav-link {{ request()->routeIs('orders*') ? 'active' : '' }}">
                        <i class="bi bi-cart3"></i> Orders
                    </a>
                </div>
                @endcan
                @can('view customers')
                <div class="menu-item">
                    <a href="{{ route('customers.index') }}" class="nav-link {{ request()->routeIs('customers*') ? 'active' : '' }}">
                        <i class="bi bi-people"></i> Customers
                    </a>
                </div>
                @endcan
                @can('view suppliers')
                <div class="menu-item">
                    <a href="{{ route('suppliers.index') }}" class="nav-link {{ request()->routeIs('suppliers*') ? 'active' : '' }}">
                        <i class="bi bi-truck"></i> Suppliers
                    </a>
                </div>
                @endcan
                @can('view purchases')
                <div class="menu-item">
                    <a href="{{ route('purchases.index') }}" class="nav-link {{ request()->routeIs('purchases*') ? 'active' : '' }}">
                        <i class="bi bi-basket"></i> Purchases
                    </a>
                </div>
                @endcan

                <!-- Inventory & Expenses -->
                @canany(['view products', 'view expenses', 'manage stock'])
                <div class="menu-header">Inventory & Expenses</div>
                @endcanany
                @can('view products')
                <div class="menu-item">
                    <a href="{{ route('inventory.index') }}" class="nav-link {{ request()->routeIs('inventory*') ? 'active' : '' }}">
                        <i class="bi bi-boxes"></i> Inventory
                    </a>
                </div>
                @endcan
                @can('view expenses')
                <div class="menu-item">
                    <a href="{{ route('expenses.index') }}" class="nav-link {{ request()->routeIs('expenses*') ? 'active' : '' }}">
                        <i class="bi bi-receipt"></i> Expenses
                    </a>
                </div>
                @endcan

                <!-- Reports -->
                @can('view reports')
                <div class="menu-item">
                    <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports*') ? 'active' : '' }}">
                        <i class="bi bi-pie-chart-fill"></i> Reports
                    </a>
                </div>
                @endcan

                <!-- System -->
                @canany(['manage settings', 'view activity logs'])
                <div class="menu-header">System</div>
                @endcanany
                @can('manage settings')
                <div class="menu-item">
                    <a href="{{ route('settings.index') }}" class="nav-link {{ request()->routeIs('settings*') ? 'active' : '' }}">
                        <i class="bi bi-gear"></i> Settings
                    </a>
                </div>
                @endcan
                @can('view activity logs')
                <div class="menu-item">
                    <a href="{{ route('activity-logs.index') }}" class="nav-link {{ request()->routeIs('activity-logs*') ? 'active' : '' }}">
                        <i class="bi bi-clock-history"></i> Activity Logs
                    </a>
                </div>
                @endcan
            </div>
        </aside>

        
        <div class="content-area">
            
            <nav class="top-navbar">
                <div class="d-flex align-items-center gap-2">
                    <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
                        <i class="bi bi-list"></i>
                    </button>
                    <a href="{{ route('pos.index') }}" target="_blank" class="btn btn-sm btn-primary d-flex align-items-center gap-1">
                        <i class="bi bi-cash"></i> <span class=" d-sm-inline">POS</span>
                    </a>
                </div>

                <div class="nav-icons">
                    
                    <button class="btn-icon" id="darkModeToggle" aria-label="Toggle dark mode">
                        <i class="bi bi-moon-stars theme-icon" id="themeIcon"></i>
                    </button>

                    <div class="dropdown dropdown-user">
                        <a class="dropdown-toggle d-flex align-items-center gap-1 text-decoration-none text-reset" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="fw-semibold d-none d-sm-inline">{{ Auth::user()->name ?? 'User' }}</span>
                            <i class="bi bi-person-circle fs-4"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end user-dropdown-menu">
                            <li><a class="dropdown-item" href="profile"><i class="bi bi-person-check me-2"></i>My Account</a></li>
                            <li><a class="dropdown-item" href="{{ route('settings.index') }}"><i class="bi bi-gear me-2"></i>Settings</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form action="{{ route('logout') }}" method="post" class="d-inline">
                                    @csrf
                                    <button class="dropdown-item" type="submit"><i class="bi bi-box-arrow-right me-2"></i>Log Out</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            
            @if (session()->has('error'))
            <div class="page-content flash-message pb-0">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
            @endif

            <main class="page-content">
                {{ $slot }}
            </main>

            <footer class="footer-bar d-flex flex-wrap justify-content-between align-items-center">
                <span>© {{ date('Y') }}, Created and Hosted by <a href="#" class="fw-semibold text-reset">ZAK</a></span>
                <span class="text-muted small">v1.0</span>
            </footer>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    @livewireScripts

    <script>
        (function() {
            const themeIcon = document.getElementById('themeIcon');
            const darkToggle = document.getElementById('darkModeToggle');
            const html = document.documentElement;

            const storedTheme = localStorage.getItem('customTheme');
            if (storedTheme === 'dark') {
                html.setAttribute('data-bs-theme', 'dark');
                themeIcon.classList.remove('bi-moon-stars');
                themeIcon.classList.add('bi-sun');
            } else if (storedTheme === 'light') {
                html.setAttribute('data-bs-theme', 'light');
                themeIcon.classList.remove('bi-sun');
                themeIcon.classList.add('bi-moon-stars');
            } else {
                html.setAttribute('data-bs-theme', 'light');
                themeIcon.classList.add('bi-moon-stars');
            }

            darkToggle.addEventListener('click', function(e) {
                e.preventDefault();
                const current = html.getAttribute('data-bs-theme');
                if (current === 'dark') {
                    html.setAttribute('data-bs-theme', 'light');
                    themeIcon.classList.remove('bi-sun');
                    themeIcon.classList.add('bi-moon-stars');
                    localStorage.setItem('customTheme', 'light');
                } else {
                    html.setAttribute('data-bs-theme', 'dark');
                    themeIcon.classList.remove('bi-moon-stars');
                    themeIcon.classList.add('bi-sun');
                    localStorage.setItem('customTheme', 'dark');
                }
            });

            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    sidebar.classList.toggle('show');
                });
            }
            
            document.addEventListener('click', function(e) {
                if (window.innerWidth < 768) {
                    const isClickInside = sidebar.contains(e.target) || sidebarToggle.contains(e.target);
                    if (!isClickInside && sidebar.classList.contains('show')) {
                        sidebar.classList.remove('show');
                    }
                }
            });

            document.querySelectorAll('.sidebar .nav-link').forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth < 768) {
                        sidebar.classList.remove('show');
                    }
                });
            });

            document.querySelectorAll('a[href]:not([href="#"])').forEach(link => {
                const url = new URL(link.href, window.location.origin);

                const isReload =
                    url.pathname === '/dashboard' ||
                    url.pathname.startsWith('/dashboard/') || 
                    url.pathname === '/pos' ||  
                    url.pathname.startsWith('/pos/');

                if (!isReload && !link.hasAttribute('wire:navigate')) {
                    link.setAttribute('wire:navigate', '');
                }
            });

        })();
    </script>

    @stack('scripts')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "5000",
        };
    </script>


    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('notify', (data) => {

                let payload = data;

                if (Array.isArray(data) && data.length === 1) {
                    payload = data[0];
                } else if (Array.isArray(data)) {
                    payload = data[0];
                }

                const message = payload?.message || payload?.msg || 'Notification';
                const type = payload?.type || payload?.status || 'info';

                if (window.toastr && typeof window.toastr[type] === 'function') {
                    window.toastr[type](message);
                } else {
                    const validTypes = ['success', 'error', 'warning', 'info'];
                    const fallbackType = validTypes.includes(type) ? type : 'info';

                    if (window.toastr && typeof window.toastr[fallbackType] === 'function') {
                        window.toastr[fallbackType](message);
                    } else {
                        console.error('Toastr method not found for type:', type);
                        alert(message);
                    }
                }
            });
        });
    </script>
</body>

</html>