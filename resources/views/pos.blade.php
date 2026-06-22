<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'POS System') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{asset('assets/img/logo.png')}}" />


    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">


    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        
        * {
            transition: background-color 0.2s ease, border-color 0.2s ease, color 0.2s ease;
        }

        input[type="number"]::-webkit-inner-spin-button,
        input[type="number"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        input[type="number"] {
            -moz-appearance: textfield;
        }

        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .dark ::-webkit-scrollbar-track {
            background: #2d3748;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e0;
            border-radius: 10px;
        }

        .dark ::-webkit-scrollbar-thumb {
            background: #4a5568;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .dark ::-webkit-scrollbar-thumb:hover {
            background: #718096;
        }

        /* Aspect ratio */
        .aspect-w-1 {
            position: relative;
            padding-bottom: 100%;
        }

        .aspect-w-1>* {
            position: absolute;
            height: 100%;
            width: 100%;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
        }

        /* Mobile optimizations */
        @media (max-width: 768px) {
            .cart-section {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                height: 80vh;
                transform: translateY(100%);
                transition: transform 0.3s ease-in-out;
                z-index: 40;
            }

            .cart-section.open {
                transform: translateY(0);
            }

            .cart-toggle {
                position: fixed;
                bottom: 1rem;
                right: 1rem;
                z-index: 50;
            }

            .mobile-search {
                width: 100%;
            }
        }
    </style>

    @livewireStyles
</head>

<body class="font-sans antialiased bg-gray-100 dark:bg-gray-900 transition-colors duration-200">
    @livewire('pos')

    <!-- Dark Mode Script -->
    <script>
        // Initialize dark mode from localStorage on page load
        (function() {
            const savedDarkMode = localStorage.getItem('darkMode') === 'true';
            if (savedDarkMode) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();

        document.addEventListener('livewire:init', function() {
            Livewire.on('dark-mode-toggled', (data) => {

                if (data.darkMode) {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('darkMode', 'true');
                } else {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('darkMode', 'false');
                }
            });
        });
    </script>

    {{-- Prevent Livewire from crashing when offline --}}
    <script>
        // If we're offline, intercept Livewire's network calls so they fail
        // silently instead of crashing Alpine.js
        if (!navigator.onLine) {
            // Override fetch to block Livewire requests silently
            const originalFetch = window.fetch;
            window.fetch = function(url, options) {
                const urlStr = typeof url === 'string' ? url : url?.url || '';
                if (urlStr.includes('/livewire/') ||
                    (options?.headers?.['X-Livewire'])) {
                    console.log('[Offline] Blocked Livewire request:', urlStr);
                    return Promise.reject(new Error('Offline'));
                }
                return originalFetch.apply(this, arguments);
            };

            // Override XMLHttpRequest too
            const OriginalXHR = window.XMLHttpRequest;
            window.XMLHttpRequest = function() {
                const xhr = new OriginalXHR();
                const originalOpen = xhr.open.bind(xhr);
                xhr.open = function(method, url, ...args) {
                    if (typeof url === 'string' && url.includes('/livewire/')) {
                        console.log('[Offline] Blocked XHR Livewire request:', url);
                        // Make it a no-op
                        xhr.send = () => {};
                    }
                    return originalOpen(method, url, ...args);
                };
                return xhr;
            };
        }
    </script>
    @if(app()->environment('production') || true)
    <script>
        // Only initialize Livewire if online
        window.__livewire_disabled = !navigator.onLine;
    </script>
    @endif
    @livewireScripts
    <script>
        // If offline, Alpine loaded via Livewire may have failed
        // Detect and restart Alpine using our cached Vite bundle
        document.addEventListener('DOMContentLoaded', () => {
            if (!navigator.onLine && typeof Alpine === 'undefined') {
                console.log('Alpine not loaded — Livewire offline. Loading from Vite bundle...');
                import('/build/assets/app-S1UIilcl.js').catch(e => console.error('Alpine load failed:', e));
            }
        });
    </script>

    <script src="assets/vendor/js/bootstrap.js"></script>

    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js')
                .then(reg => console.log('SW registered:', reg.scope))
                .catch(err => console.error('SW failed:', err));
        }
    </script>

    @stack('scripts')
</body>

</html>