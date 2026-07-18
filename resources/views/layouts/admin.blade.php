<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EstateX - Administration Dashboard</title>
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
            background-color: #0b0f19;
            color: #f3f4f6;
        }
        .glass-panel {
            background: rgba(17, 24, 39, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        .sidebar-link-active {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.2) 0%, rgba(99, 102, 241, 0.2) 100%);
            border-left: 4px solid #3b82f6;
            color: #60a5fa;
        }
    </style>
    @yield('styles')
</head>
<body class="font-sans min-h-screen flex flex-col md:flex-row overflow-x-hidden">

    <!-- Sidebar -->
    <aside class="w-full md:w-64 bg-slate-900 border-r border-slate-800 flex flex-col justify-between shrink-0">
        <div>
            <!-- Brand -->
            <div class="h-20 flex items-center px-6 border-b border-slate-800">
                <div class="flex items-center gap-2">
                    <div class="w-9 h-9 rounded-lg bg-gradient-to-tr from-blue-600 to-indigo-500 flex items-center justify-center text-white font-bold text-lg font-outfit shadow-lg shadow-blue-500/20">
                        EX
                    </div>
                    <div>
                        <span class="font-outfit font-extrabold text-xl tracking-tight bg-gradient-to-r from-blue-400 via-indigo-200 to-white bg-clip-text text-transparent">EstateX</span>
                        <span class="block text-[10px] text-slate-500 font-semibold tracking-widest uppercase">Admin Portal</span>
                    </div>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="mt-6 px-4 space-y-1">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800/50 transition duration-200 {{ Route::is('admin.dashboard') ? 'sidebar-link-active' : '' }}">
                    <i class="fa-solid fa-chart-line text-lg w-5"></i>
                    <span class="font-medium text-sm">Dashboard Overview</span>
                </a>
                <a href="{{ route('admin.users') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800/50 transition duration-200 {{ Route::is('admin.users') ? 'sidebar-link-active' : '' }}">
                    <i class="fa-solid fa-users text-lg w-5"></i>
                    <span class="font-medium text-sm">User Management</span>
                </a>
                <a href="{{ route('admin.agents') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800/50 transition duration-200 {{ Route::is('admin.agents') ? 'sidebar-link-active' : '' }}">
                    <i class="fa-solid fa-user-tie text-lg w-5"></i>
                    <span class="font-medium text-sm">Agent Management</span>
                </a>
                <a href="{{ route('admin.owners') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800/50 transition duration-200 {{ Route::is('admin.owners') ? 'sidebar-link-active' : '' }}">
                    <i class="fa-solid fa-user-gear text-lg w-5"></i>
                    <span class="font-medium text-sm">Owner Management</span>
                </a>
                <a href="{{ route('admin.properties') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800/50 transition duration-200 {{ Route::is('admin.properties') ? 'sidebar-link-active' : '' }}">
                    <i class="fa-solid fa-house-chimney text-lg w-5"></i>
                    <span class="font-medium text-sm">Property Listings</span>
                </a>
                <a href="{{ route('admin.bookings') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800/50 transition duration-200 {{ Route::is('admin.bookings') ? 'sidebar-link-active' : '' }}">
                    <i class="fa-solid fa-calendar-check text-lg w-5"></i>
                    <span class="font-medium text-sm">Bookings & Visits</span>
                </a>
                <a href="{{ route('admin.transactions') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800/50 transition duration-200 {{ Route::is('admin.transactions') ? 'sidebar-link-active' : '' }}">
                    <i class="fa-solid fa-receipt text-lg w-5"></i>
                    <span class="font-medium text-sm">Transaction Ledger</span>
                </a>
                <a href="{{ route('admin.audit-logs') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800/50 transition duration-200 {{ Route::is('admin.audit-logs') ? 'sidebar-link-active' : '' }}">
                    <i class="fa-solid fa-file-shield text-lg w-5"></i>
                    <span class="font-medium text-sm">System Audit Logs</span>
                </a>
            </nav>
        </div>

        <!-- Footer / Admin Bio -->
        <div class="p-4 border-t border-slate-800 bg-slate-950/40">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-500 flex items-center justify-center font-bold text-white shadow-inner">
                    A
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-slate-200">{{ session('admin_user_name', 'Administrator') }}</h4>
                    <span class="text-xs text-blue-400 font-medium">System Operator</span>
                </div>
            </div>
            <a href="{{ route('admin.logout') }}" class="w-full flex items-center justify-center gap-2 py-2 px-4 border border-red-500/20 hover:border-red-500/50 rounded-lg text-red-400 hover:text-red-300 hover:bg-red-500/10 text-xs font-semibold tracking-wider transition duration-200">
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
                Sign Out
            </a>
        </div>
    </aside>

    <!-- Main Content Area -->
    <main class="flex-1 flex flex-col min-w-0 bg-[#070b13]">
        <!-- Top bar -->
        <header class="h-20 bg-slate-900/60 border-b border-slate-800 flex items-center justify-between px-6 md:px-8">
            <h2 class="font-outfit font-bold text-lg md:text-xl text-slate-200">@yield('page_title', 'Dashboard')</h2>
            <div class="flex items-center gap-4">
            </div>
        </header>

        <!-- Dynamic Page Content -->
        <div class="p-6 md:p-8 flex-1">
            <!-- Toast Notifications -->
            @if(session('success'))
                <div class="mb-6 p-4 rounded-lg bg-green-500/10 border border-green-500/30 text-green-400 text-sm flex items-center gap-3 shadow-lg shadow-green-500/5">
                    <i class="fa-solid fa-circle-check text-base"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 rounded-lg bg-red-500/10 border border-red-500/30 text-red-400 text-sm flex items-center gap-3 shadow-lg shadow-red-500/5">
                    <i class="fa-solid fa-triangle-exclamation text-base"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    @yield('scripts')
    @include('layouts.water_wave')
</body>
</html>
