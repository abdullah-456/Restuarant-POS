<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin - ' . config('app.name', 'POS System'))</title>
    @vite(['resources/css/app.css', 'resources/css/responsive.css', 'resources/js/app.js'])
    @stack('styles')
    <style>
        .sidebar { transition: transform 0.3s ease, width 0.3s ease; }
        .sidebar.collapsed { width: 80px; }
        .sidebar.collapsed .sidebar-text { display: none; }
        .main-content { transition: margin-left 0.3s ease; }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); width: 280px; }
            .sidebar.mobile-open { transform: translateX(0); }
            .main-content { margin-left: 0 !important; }
            .sidebar-overlay { display: none; }
            .sidebar-overlay.active { display: block; }
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="sidebar-overlay fixed inset-0 bg-black bg-opacity-50 z-40 hidden" id="sidebar-overlay"></div>
    <div class="sidebar fixed left-0 top-0 h-full w-64 bg-gradient-to-b from-blue-900 to-blue-800 text-white shadow-lg z-50" id="sidebar">
        <div class="p-4 md:p-6 border-b border-blue-700">
            <div class="flex items-center justify-between">
                <h1 class="text-lg md:text-xl font-bold sidebar-text">POS System</h1>
                <button id="sidebar-toggle" class="text-white hover:text-blue-200 md:hidden"><i class="fas fa-times"></i></button>
                <button id="sidebar-toggle-desktop" class="text-white hover:text-blue-200 hidden md:block"><i class="fas fa-bars"></i></button>
            </div>
        </div>
        <nav class="mt-4 md:mt-6 overflow-y-auto" style="max-height: calc(100vh - 200px);">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 md:px-6 py-2.5 md:py-3 text-white hover:bg-blue-700 transition {{ request()->routeIs('admin.dashboard') ? 'bg-blue-700 border-r-4 border-yellow-400' : '' }}">
                <i class="fas fa-home w-6"></i>
                <span class="ml-3 sidebar-text">Dashboard</span>
            </a>
            <a href="{{ route('admin.users.index') }}" class="flex items-center px-4 md:px-6 py-2.5 md:py-3 text-white hover:bg-blue-700 transition {{ request()->routeIs('admin.users.*') ? 'bg-blue-700 border-r-4 border-yellow-400' : '' }}">
                <i class="fas fa-users w-6"></i>
                <span class="ml-3 sidebar-text">Manage Users</span>
            </a>
            <a href="{{ route('admin.tables.index') }}" class="flex items-center px-4 md:px-6 py-2.5 md:py-3 text-white hover:bg-blue-700 transition {{ request()->routeIs('admin.tables.*') ? 'bg-blue-700 border-r-4 border-yellow-400' : '' }}">
                <i class="fas fa-table w-6"></i>
                <span class="ml-3 sidebar-text">Manage Tables</span>
            </a>
            <a href="{{ route('admin.categories.index') }}" class="flex items-center px-4 md:px-6 py-2.5 md:py-3 text-white hover:bg-blue-700 transition {{ request()->routeIs('admin.categories.*') ? 'bg-blue-700 border-r-4 border-yellow-400' : '' }}">
                <i class="fas fa-folder w-6"></i>
                <span class="ml-3 sidebar-text">Manage Categories</span>
            </a>
            <a href="{{ route('admin.menu-items.index') }}" class="flex items-center px-4 md:px-6 py-2.5 md:py-3 text-white hover:bg-blue-700 transition {{ request()->routeIs('admin.menu-items.*') ? 'bg-blue-700 border-r-4 border-yellow-400' : '' }}">
                <i class="fas fa-utensils w-6"></i>
                <span class="ml-3 sidebar-text">Manage Menu Items</span>
            </a>
            <a href="{{ route('admin.orders.index') }}" class="flex items-center px-4 md:px-6 py-2.5 md:py-3 text-white hover:bg-blue-700 transition {{ request()->routeIs('admin.orders.*') ? 'bg-blue-700 border-r-4 border-yellow-400' : '' }}">
                <i class="fas fa-shopping-cart w-6"></i>
                <span class="ml-3 sidebar-text">View Orders</span>
            </a>
            <a href="{{ route('admin.reports.index') }}" class="flex items-center px-4 md:px-6 py-2.5 md:py-3 text-white hover:bg-blue-700 transition {{ request()->routeIs('admin.reports.*') ? 'bg-blue-700 border-r-4 border-yellow-400' : '' }}">
                <i class="fas fa-chart-bar w-6"></i>
                <span class="ml-3 sidebar-text">Reports</span>
            </a>
            <a href="{{ route('admin.settings.index') }}" class="flex items-center px-4 md:px-6 py-2.5 md:py-3 text-white hover:bg-blue-700 transition {{ request()->routeIs('admin.settings.*') ? 'bg-blue-700 border-r-4 border-yellow-400' : '' }}">
                <i class="fas fa-cog w-6"></i>
                <span class="ml-3 sidebar-text">Settings</span>
            </a>
            
            <div class="px-4 md:px-6 py-4 border-t border-blue-700 mt-2">
                <p class="text-xs uppercase text-blue-300 font-bold mb-2 sidebar-text">View Displays</p>
                <div class="space-y-1">
                    <a href="{{ route('waiter.dashboard') }}" target="_blank" class="flex items-center text-sm text-blue-100 hover:text-white transition">
                        <i class="fas fa-external-link-alt w-4 mr-2"></i>
                        <span class="sidebar-text">Waiter View</span>
                    </a>
                    <a href="{{ route('kitchen.dashboard') }}" target="_blank" class="flex items-center text-sm text-blue-100 hover:text-white transition">
                        <i class="fas fa-external-link-alt w-4 mr-2"></i>
                        <span class="sidebar-text">Kitchen Display</span>
                    </a>
                    <a href="{{ route('cashier.dashboard') }}" target="_blank" class="flex items-center text-sm text-blue-100 hover:text-white transition">
                        <i class="fas fa-external-link-alt w-4 mr-2"></i>
                        <span class="sidebar-text">Cashier Display</span>
                    </a>
                </div>
            </div>
        </nav>
        <div class="absolute bottom-0 left-0 right-0 p-4 md:p-6 border-t border-blue-700">
            <div class="flex items-center">
                <div class="w-8 h-8 md:w-10 md:h-10 bg-blue-700 rounded-full flex items-center justify-center"><i class="fas fa-user text-xs md:text-sm"></i></div>
                <div class="ml-3 sidebar-text">
                    <p class="text-xs md:text-sm font-semibold">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-blue-200">{{ ucfirst(auth()->user()->role ?? 'admin') }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="mt-3 md:mt-4">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center px-4 py-2 bg-red-600 hover:bg-red-700 rounded transition sidebar-text text-sm md:text-base">
                    <i class="fas fa-sign-out-alt mr-2"></i><span>Logout</span>
                </button>
            </form>
        </div>
    </div>

    <div class="main-content ml-0 md:ml-64 min-h-screen">
        <div class="bg-white shadow-sm border-b sticky top-0 z-30">
            <div class="px-4 md:px-6 py-3 md:py-4">
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <button id="mobile-menu-toggle" class="md:hidden text-gray-600 hover:text-gray-800"><i class="fas fa-bars text-xl"></i></button>
                        <h2 class="text-lg md:text-2xl font-bold text-gray-800">@yield('page-title', 'Dashboard')</h2>
                    </div>
                    <div class="flex items-center space-x-2 md:space-x-4">
                        <span class="text-xs md:text-sm text-gray-600 hidden sm:inline">{{ now()->format('l, F d, Y') }}</span>
                        <span class="text-xs md:text-sm text-gray-600 sm:hidden">{{ now()->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="p-4 md:p-6">
            @yield('content')
        </div>
    </div>

    @stack('scripts')
    <script>
        $(document).ready(function() {
            // Sidebar toggles
            $('#sidebar-toggle-desktop').on('click', function() {
                $('#sidebar').toggleClass('collapsed');
                if ($('#sidebar').hasClass('collapsed')) {
                    $('.main-content').removeClass('ml-64').addClass('ml-20');
                } else {
                    $('.main-content').removeClass('ml-20').addClass('ml-64');
                }
            });
            $('#mobile-menu-toggle, #sidebar-toggle').on('click', function() {
                $('#sidebar').toggleClass('mobile-open');
                $('#sidebar-overlay').toggleClass('hidden');
            });
            $('#sidebar-overlay').on('click', function() {
                $('#sidebar').removeClass('mobile-open');
                $('#sidebar-overlay').addClass('hidden');
            });
            $(window).on('resize', function() {
                if ($(window).width() > 768) {
                    $('#sidebar').removeClass('mobile-open');
                    $('#sidebar-overlay').addClass('hidden');
                }
            });

            // Session Messages
            @if(session('success'))
                window.Alert.toast("{{ session('success') }}", 'success');
            @endif
            @if(session('error'))
                window.Alert.toast("{{ session('error') }}", 'error');
            @endif
            @if(session('info'))
                window.Alert.toast("{{ session('info') }}", 'info');
            @endif
        });

        // Global showConfirm using SweetAlert
        window.showConfirm = function(title, message, callback) {
            window.Alert.confirm(title, message).then((result) => {
                if (result.isConfirmed && callback) {
                    callback();
                }
            });
        };

        // Standardized Toast
        window.showToast = function(message, type = 'success') {
            window.Alert.toast(message, type);
        };
    </script>
    @stack('scripts')
</body>
</html>
