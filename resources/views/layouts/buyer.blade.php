<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EstateX - Buyer Portal</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        outfit: ['Outfit', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background-color: #080c14;
            color: #e2e8f0;
        }
        .glass-panel {
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.04);
        }
        .sidebar-link-active {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.15) 0%, rgba(59, 130, 246, 0.15) 100%);
            border-left: 4px solid #10b981;
            color: #34d399;
        }
    </style>
    @yield('styles')
</head>
<body class="font-sans min-h-screen flex flex-col md:flex-row overflow-x-hidden">

    <!-- Sidebar -->
    <aside class="w-full md:w-64 bg-slate-900 border-r border-slate-800 flex flex-col justify-between shrink-0">
        <div>
            <!-- Brand Logo -->
            <div class="h-20 flex items-center px-6 border-b border-slate-800">
                <div class="flex items-center gap-2">
                    <div class="w-9 h-9 rounded-lg bg-gradient-to-tr from-emerald-500 to-blue-600 flex items-center justify-center text-white font-bold text-lg font-outfit shadow-lg shadow-emerald-500/20">
                        EX
                    </div>
                    <div>
                        <span class="font-outfit font-extrabold text-xl tracking-tight bg-gradient-to-r from-emerald-400 via-teal-200 to-white bg-clip-text text-transparent">EstateX</span>
                        <span class="block text-[10px] text-slate-500 font-semibold tracking-widest uppercase">Buyer Portal</span>
                    </div>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="mt-6 px-4 space-y-1">
                <a href="{{ route('buyer.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800/50 transition duration-200 {{ Route::is('buyer.dashboard') || Route::is('buyer.properties.show') ? 'sidebar-link-active' : '' }}">
                    <i class="fa-solid fa-house-chimney text-lg w-5"></i>
                    <span class="font-medium text-sm">Marketplace</span>
                </a>
                <a href="{{ route('buyer.wishlist') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800/50 transition duration-200 {{ Route::is('buyer.wishlist') ? 'sidebar-link-active' : '' }}">
                    <i class="fa-solid fa-heart text-lg w-5"></i>
                    <span class="font-medium text-sm">My Wishlist</span>
                </a>
                <a href="{{ route('buyer.comparisons') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800/50 transition duration-200 {{ Route::is('buyer.comparisons') ? 'sidebar-link-active' : '' }}">
                    <i class="fa-solid fa-code-compare text-lg w-5"></i>
                    <span class="font-medium text-sm">Compare Listings</span>
                </a>
                <a href="{{ route('buyer.bookings') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800/50 transition duration-200 {{ Route::is('buyer.bookings') ? 'sidebar-link-active' : '' }}">
                    <i class="fa-solid fa-calendar-check text-lg w-5"></i>
                    <span class="font-medium text-sm">My Bookings</span>
                </a>
            </nav>
        </div>

        <!-- User Profile Footer -->
        <div class="p-4 border-t border-slate-800 bg-slate-950/40">
            <div class="flex items-center gap-3 mb-4">
                @if(session('buyer_user_image'))
                    <img src="{{ session('buyer_user_image') }}" alt="Profile" class="w-10 h-10 rounded-full object-cover shadow-inner border border-emerald-500/20">
                @else
                    <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-emerald-500 to-teal-500 flex items-center justify-center font-bold text-white shadow-inner">
                        {{ substr(session('buyer_user_name', 'Buyer'), 0, 1) }}
                    </div>
                @endif
                <div>
                    <h4 class="text-sm font-semibold text-slate-200">{{ session('buyer_user_name', 'Guest Buyer') }}</h4>
                    <span class="text-xs text-emerald-400 font-medium">Standard Member</span>
                </div>
            </div>
            <a href="{{ route('buyer.logout') }}" class="w-full flex items-center justify-center gap-2 py-2 px-4 border border-red-500/20 hover:border-red-500/50 rounded-lg text-red-400 hover:text-red-300 hover:bg-red-500/10 text-xs font-semibold tracking-wider transition duration-200">
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
                Sign Out
            </a>
        </div>
    </aside>

    <!-- Main Content Area -->
    <main class="flex-1 flex flex-col min-w-0 bg-[#060a12]">
        <!-- Top bar -->
        <header class="h-20 bg-slate-900/60 border-b border-slate-800 flex items-center justify-between px-6 md:px-8 shrink-0">
            <h2 class="font-outfit font-bold text-lg md:text-xl text-slate-200">@yield('page_title', 'Browse Properties')</h2>
            <div class="flex items-center gap-4">
                <div class="text-right hidden sm:block">
                    <span class="text-xs text-slate-500 block">Database Tunnel</span>
                    <span class="text-xs font-bold text-emerald-500 flex items-center gap-1.5 justify-end">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Oracle Connected (PDO)
                    </span>
                </div>
            </div>
        </header>

        <!-- Dynamic Content Body -->
        <div class="flex-1 overflow-y-auto p-6 md:p-8">
            <!-- Flash Message Alerts -->
            @if(session('success'))
                <div class="mb-6 p-4 rounded-xl bg-green-500/10 border border-green-500/30 text-green-400 text-xs flex items-center gap-3">
                    <i class="fa-solid fa-circle-check text-sm"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-400 text-xs flex items-center gap-3">
                    <i class="fa-solid fa-circle-exclamation text-sm"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if(session('info'))
                <div class="mb-6 p-4 rounded-xl bg-blue-500/10 border border-blue-500/30 text-blue-400 text-xs flex items-center gap-3">
                    <i class="fa-solid fa-circle-info text-sm"></i>
                    <span>{{ session('info') }}</span>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    @yield('scripts')
</body>
</html>
