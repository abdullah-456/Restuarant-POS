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
        .sidebar {
            transition: transform 0.3s ease, width 0.3s ease;
        }

        .sidebar.collapsed {
            width: 72px;
        }

        .sidebar.collapsed .sidebar-text {
            display: none;
        }

        .main-content {
            transition: margin-left 0.3s ease;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                width: 280px;
            }

            .sidebar.mobile-open {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0 !important;
            }
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.6rem 0.75rem;
            margin: 0 0.5rem;
            border-radius: 0.5rem;
            color: rgba(255, 255, 255, 0.75);
            transition: all 0.2s;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, 0.12);
            color: white;
        }

        .nav-link.active {
            background: rgba(255, 255, 255, 0.18);
            color: white;
            box-shadow: inset 3px 0 0 #facc15;
        }

        .nav-link i {
            width: 1.5rem;
            text-align: center;
            flex-shrink: 0;
        }

        .nav-link .sidebar-text {
            margin-left: 0.75rem;
        }
    </style>
</head>

<body class="bg-gray-50">
    <div class="fixed inset-0 bg-black/50 z-40 hidden" id="sidebar-overlay"></div>
    <div class="sidebar fixed left-0 top-0 h-full w-64 bg-gradient-to-b from-blue-900 to-blue-800 text-white shadow-2xl z-50 flex flex-col"
        id="sidebar">
        {{-- Logo --}}
        <div class="px-4 py-5 border-b border-white/10 flex-shrink-0">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3 sidebar-text">
                    <div
                        class="w-9 h-9 bg-gradient-to-br from-blue-400 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-utensils text-white text-sm"></i>
                    </div>
                    <div>
                        <h1 class="text-base font-bold leading-tight">POS System</h1>
                        <p class="text-[10px] text-white/40 uppercase tracking-widest">Admin Panel</p>
                    </div>
                </div>
                <button id="sidebar-toggle" class="text-white/60 hover:text-white md:hidden p-1"><i
                        class="fas fa-times"></i></button>
                <button id="sidebar-toggle-desktop" class="text-white/60 hover:text-white hidden md:block p-1"><i
                        class="fas fa-bars"></i></button>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 overflow-y-auto py-4 space-y-0.5">
            <div class="px-4 mb-1">
                <p class="text-[10px] uppercase tracking-widest text-white/30 font-semibold px-2 sidebar-text">
                    Navigation</p>
            </div>
            <a href="{{ route('admin.dashboard') }}"
                class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-home"></i><span class="sidebar-text">Dashboard</span>
            </a>
            <a href="{{ route('admin.users.index') }}"
                class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="fas fa-users"></i><span class="sidebar-text">Manage Users</span>
            </a>
            <a href="{{ route('admin.tables.index') }}"
                class="nav-link {{ request()->routeIs('admin.tables.*') ? 'active' : '' }}">
                <i class="fas fa-table"></i><span class="sidebar-text">Manage Tables</span>
            </a>
            <a href="{{ route('admin.categories.index') }}"
                class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                <i class="fas fa-folder"></i><span class="sidebar-text">Categories</span>
            </a>
            <a href="{{ route('admin.menu-items.index') }}"
                class="nav-link {{ request()->routeIs('admin.menu-items.*') ? 'active' : '' }}">
                <i class="fas fa-utensils"></i><span class="sidebar-text">Menu Items</span>
            </a>
            <a href="{{ route('admin.orders.index') }}"
                class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                <i class="fas fa-receipt"></i><span class="sidebar-text">Orders</span>
            </a>
            <a href="{{ route('admin.reports.index') }}"
                class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                <i class="fas fa-chart-bar"></i><span class="sidebar-text">Reports</span>
            </a>
            <a href="{{ route('admin.settings.index') }}"
                class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                <i class="fas fa-cog"></i><span class="sidebar-text">Settings</span>
            </a>

            <div class="px-4 mt-4 mb-1">
                <p class="text-[10px] uppercase tracking-widest text-white/30 font-semibold px-2 sidebar-text">Displays
                </p>
            </div>
            <a href="{{ route('waiter.dashboard') }}" target="_blank" class="nav-link">
                <i class="fas fa-person-walking"></i>
                <span class="sidebar-text flex-1">Waiter View</span>
                <i class="fas fa-external-link-alt text-[10px] text-white/30 sidebar-text"></i>
            </a>
            <a href="{{ route('kitchen.dashboard') }}" target="_blank" class="nav-link">
                <i class="fas fa-fire-burner"></i>
                <span class="sidebar-text flex-1">Kitchen Display</span>
                <i class="fas fa-external-link-alt text-[10px] text-white/30 sidebar-text"></i>
            </a>
            <a href="{{ route('cashier.dashboard') }}" target="_blank" class="nav-link">
                <i class="fas fa-cash-register"></i>
                <span class="sidebar-text flex-1">Cashier Display</span>
                <i class="fas fa-external-link-alt text-[10px] text-white/30 sidebar-text"></i>
            </a>
        </nav>

        {{-- User footer --}}
        <div class="flex-shrink-0 p-4 border-t border-white/10 bg-black/20">
            <div class="flex items-center gap-3 mb-3">
                <div
                    class="w-8 h-8 bg-gradient-to-br from-blue-400 to-indigo-600 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                    {{ Str::upper(Str::substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="sidebar-text min-w-0">
                    <p class="text-sm font-semibold truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-white/40">{{ ucfirst(auth()->user()->role ?? 'admin') }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full flex items-center justify-center gap-2 px-3 py-2 bg-red-500/20 hover:bg-red-500/30 border border-red-500/20 rounded-lg text-red-300 hover:text-white transition text-sm">
                    <i class="fas fa-sign-out-alt"></i>
                    <span class="sidebar-text">Logout</span>
                </button>
            </form>
        </div>
    </div>

    {{-- Main content area --}}
    <div class="main-content ml-0 md:ml-64 min-h-screen flex flex-col">
        {{-- Topbar --}}
        <div class="bg-white border-b border-gray-200 sticky top-0 z-30 shadow-sm">
            <div class="px-4 md:px-6 py-3 md:py-4">
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <button id="mobile-menu-toggle"
                            class="md:hidden text-gray-500 hover:text-gray-700 p-1 rounded-lg hover:bg-gray-100 transition">
                            <i class="fas fa-bars text-lg"></i>
                        </button>
                        <div>
                            <h2 class="text-lg md:text-xl font-bold text-gray-900 leading-tight">
                                @yield('page-title', 'Dashboard')</h2>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-xs text-gray-400 hidden md:inline">{{ now()->format('D, d M Y') }}</span>
                        <div class="flex items-center gap-2 bg-gray-100 rounded-full px-3 py-1.5">
                            <div
                                class="w-5 h-5 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center">
                                <span
                                    class="text-white text-[10px] font-bold">{{ Str::upper(Str::substr(auth()->user()->name, 0, 1)) }}</span>
                            </div>
                            <span
                                class="text-xs font-medium text-gray-700 hidden sm:inline">{{ auth()->user()->name }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex-1 p-4 md:p-6">
            @yield('content')
        </div>
    </div>

    <script type="module">
        $(document).ready(function () {
            $('#sidebar-toggle-desktop').on('click', function () {
                $('#sidebar').toggleClass('collapsed');
                if ($('#sidebar').hasClass('collapsed')) {
                    $('.main-content').removeClass('ml-64').addClass('ml-[72px]');
                } else {
                    $('.main-content').removeClass('ml-[72px]').addClass('ml-64');
                }
            });
            $('#mobile-menu-toggle, #sidebar-toggle').on('click', function () {
                $('#sidebar').toggleClass('mobile-open');
                $('#sidebar-overlay').toggleClass('hidden');
            });
            $('#sidebar-overlay').on('click', function () {
                $('#sidebar').removeClass('mobile-open');
                $('#sidebar-overlay').addClass('hidden');
            });
            $(window).on('resize', function () {
                if ($(window).width() > 768) {
                    $('#sidebar').removeClass('mobile-open');
                    $('#sidebar-overlay').addClass('hidden');
                }
            });
            @if(session('success'))
                window.Alert.toast("{{ addslashes(session('success')) }}", 'success');
            @endif
            @if(session('error'))
                window.Alert.toast("{{ addslashes(session('error')) }}", 'error');
            @endif
            @if(session('info'))
                window.Alert.toast("{{ addslashes(session('info')) }}", 'info');
            @endif
        });
        window.showConfirm = function (title, message, callback) {
            window.Alert.confirm(title, message).then((result) => {
                if (result.isConfirmed && callback) callback();
            });
        };
        window.showToast = function (message, type = 'success') {
            window.Alert.toast(message, type);
        };
    </script>
    @stack('scripts')
</body>

</html>