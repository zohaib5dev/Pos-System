<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>404 - Page Not Found | {{ config('app.name', 'POS System') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
        
        .animated {
            animation-duration: 0.3s;
            animation-fill-mode: both;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .fadeIn {
            animation-name: fadeIn;
        }
        
        .bounce {
            animation: bounce 2s infinite;
        }
        
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-30px);
            }
            60% {
                transform: translateY(-15px);
            }
        }
        
        .floating {
            animation: floating 3s ease-in-out infinite;
        }
        
        @keyframes floating {
            0% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-20px);
            }
            100% {
                transform: translateY(0px);
            }
        }
        
        .rotate {
            animation: rotate 10s linear infinite;
        }
        
        @keyframes rotate {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-indigo-50 via-white to-purple-50 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-4xl w-full text-center animated fadeIn">
        <!-- Animated 404 Number -->
        <div class="relative mb-8">
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="w-64 h-64 bg-indigo-100 rounded-full opacity-20 floating"></div>
            </div>
            <div class="relative z-10">
                <span class="text-[150px] md:text-[200px] font-bold text-indigo-600 opacity-20 select-none">404</span>
            </div>
            <div class="absolute inset-0 flex items-center justify-center z-20">
                <div class="bg-white p-6 rounded-2xl shadow-2xl rotate" style="animation: rotate 20s linear infinite;">
                    <i class="fas fa-box-open text-7xl md:text-8xl text-indigo-600"></i>
                </div>
            </div>
        </div>

        <!-- Error Message -->
        <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4 animated fadeIn" style="animation-delay: 0.1s;">
            Oops! Page Not Found
        </h1>
        
        <p class="text-xl text-gray-600 mb-8 max-w-2xl mx-auto animated fadeIn" style="animation-delay: 0.2s;">
            The page you're looking for doesn't exist or has been moved. 
            Let's get you back on track!
        </p>

        <!-- Search Bar -->
        <div class="max-w-md mx-auto mb-8 animated fadeIn" style="animation-delay: 0.3s;">
            <form action="{{ route('dashboard') }}" method="GET" class="relative">
                <input type="text" 
                       name="search" 
                       placeholder="Search for products, orders, customers..." 
                       class="w-full px-6 py-4 pr-12 rounded-full border-2 border-gray-200 focus:border-indigo-400 focus:outline-none shadow-lg"
                       value="{{ request('search') }}">
                <button type="submit" class="absolute right-3 top-1/2 transform -translate-y-1/2 bg-indigo-600 text-white p-2 rounded-full hover:bg-indigo-700 transition-colors">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>

        <!-- Quick Links -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 max-w-3xl mx-auto mb-8 animated fadeIn" style="animation-delay: 0.4s;">
            <a href="{{ route('dashboard') }}" 
               class="bg-white p-4 rounded-xl shadow-md hover:shadow-lg transition-all hover:-translate-y-1">
                <i class="fas fa-tachometer-alt text-2xl text-indigo-600 mb-2"></i>
                <span class="block text-sm font-medium text-gray-700">Dashboard</span>
            </a>
            
            <a href="{{ route('pos.index') }}" 
               class="bg-white p-4 rounded-xl shadow-md hover:shadow-lg transition-all hover:-translate-y-1">
                <i class="fas fa-cash-register text-2xl text-green-600 mb-2"></i>
                <span class="block text-sm font-medium text-gray-700">POS</span>
            </a>
            
            <a href="{{ route('products.index') }}" 
               class="bg-white p-4 rounded-xl shadow-md hover:shadow-lg transition-all hover:-translate-y-1">
                <i class="fas fa-box text-2xl text-blue-600 mb-2"></i>
                <span class="block text-sm font-medium text-gray-700">Products</span>
            </a>
            
            <a href="{{ route('orders.index') }}" 
               class="bg-white p-4 rounded-xl shadow-md hover:shadow-lg transition-all hover:-translate-y-1">
                <i class="fas fa-shopping-cart text-2xl text-purple-600 mb-2"></i>
                <span class="block text-sm font-medium text-gray-700">Orders</span>
            </a>
        </div>

        <!-- Help Section -->
        <div class="bg-white/50 backdrop-blur-sm rounded-2xl p-6 max-w-2xl mx-auto border border-gray-200 animated fadeIn" style="animation-delay: 0.5s;">
            <h3 class="text-lg font-semibold text-gray-800 mb-3">Need Help?</h3>
            <div class="flex flex-col md:flex-row items-center justify-center gap-4">
                <a href="#" class="flex items-center space-x-2 text-indigo-600 hover:text-indigo-800">
                    <i class="fas fa-envelope"></i>
                    <span>Contact Support</span>
                </a>
                <span class="hidden md:block text-gray-300">|</span>
                <a href="#" class="flex items-center space-x-2 text-indigo-600 hover:text-indigo-800">
                    <i class="fas fa-file-alt"></i>
                    <span>Documentation</span>
                </a>
                <span class="hidden md:block text-gray-300">|</span>
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 text-indigo-600 hover:text-indigo-800">
                    <i class="fas fa-home"></i>
                    <span>Go to Dashboard</span>
                </a>
            </div>
        </div>

        <!-- Funny Message -->
        <p class="text-sm text-gray-400 mt-8 animated fadeIn" style="animation-delay: 0.6s;">
            <i class="fas fa-smile-wink mr-1"></i>
            Don't worry, even the best POS systems get lost sometimes!
        </p>

        <!-- Background Decoration -->
        <div class="fixed top-0 left-0 w-64 h-64 bg-indigo-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
        <div class="fixed top-0 right-0 w-64 h-64 bg-yellow-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>
        <div class="fixed bottom-0 left-0 w-64 h-64 bg-pink-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-4000"></div>
    </div>

    <style>
        @keyframes blob {
            0% {
                transform: translate(0px, 0px) scale(1);
            }
            33% {
                transform: translate(30px, -50px) scale(1.1);
            }
            66% {
                transform: translate(-20px, 20px) scale(0.9);
            }
            100% {
                transform: translate(0px, 0px) scale(1);
            }
        }
        
        .animate-blob {
            animation: blob 7s infinite;
        }
        
        .animation-delay-2000 {
            animation-delay: 2s;
        }
        
        .animation-delay-4000 {
            animation-delay: 4s;
        }
    </style>

    @if(app()->environment('local'))
        <!-- Debug Info (only shown in local environment) -->
        <div class="fixed bottom-4 left-4 bg-black text-white text-xs p-2 rounded opacity-50">
            {{ request()->url() }}
        </div>
    @endif
</body>
</html>