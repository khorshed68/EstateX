<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EstateX - Agent Portal</title>
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
            background-color: #090614;
            color: #e2e8f0;
            background-image: 
                radial-gradient(at 0% 100%, rgba(168, 85, 247, 0.05) 0, transparent 50%),
                radial-gradient(at 100% 0%, rgba(99, 102, 241, 0.08) 0, transparent 50%);
        }
        .glass-panel {
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.04);
        }
        .sidebar-link-active {
            background: linear-gradient(135deg, rgba(168, 85, 247, 0.15) 0%, rgba(99, 102, 241, 0.15) 100%);
            border-left: 4px solid #a855f7;
            color: #d8b4fe;
        }
    </style>
    @yield('styles')
</head>
<body class="font-sans min-h-screen flex flex-col md:flex-row overflow-x-hidden">

    <!-- Sidebar -->
    <aside class="w-full md:w-64 bg-slate-950 border-r border-slate-900 flex flex-col justify-between shrink-0">
        <div>
            <!-- Brand Logo -->
            <div class="h-20 flex items-center px-6 border-b border-slate-900">
                <div class="flex items-center gap-2">
                    <div class="w-9 h-9 rounded-lg bg-gradient-to-tr from-purple-500 to-indigo-600 flex items-center justify-center text-white font-bold text-lg font-outfit shadow-lg shadow-purple-500/20">
                        EX
                    </div>
                    <div>
                        <span class="font-outfit font-extrabold text-xl tracking-tight bg-gradient-to-r from-purple-400 via-indigo-300 to-white bg-clip-text text-transparent">EstateX</span>
                        <span class="block text-[10px] text-slate-500 font-semibold tracking-widest uppercase">Agent Portal</span>
                    </div>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="mt-6 px-4 space-y-1">
                <a href="{{ route('agent.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-400 hover:text-white hover:bg-slate-900/50 transition duration-200 {{ Route::is('agent.dashboard') ? 'sidebar-link-active' : '' }}">
                    <i class="fa-solid fa-gauge-high text-lg w-5"></i>
                    <span class="font-medium text-sm">Dashboard</span>
                </a>
                <a href="{{ route('agent.properties') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-400 hover:text-white hover:bg-slate-900/50 transition duration-200 {{ Route::is('agent.properties') ? 'sidebar-link-active' : '' }}">
                    <i class="fa-solid fa-house-user text-lg w-5"></i>
                    <span class="font-medium text-sm">Assigned Listings</span>
                </a>
                <a href="{{ route('agent.bookings') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-400 hover:text-white hover:bg-slate-900/50 transition duration-200 {{ Route::is('agent.bookings') ? 'sidebar-link-active' : '' }}">
                    <i class="fa-solid fa-calendar-check text-lg w-5"></i>
                    <span class="font-medium text-sm">Leads & Bookings</span>
                </a>
                <a href="{{ route('agent.reviews') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-400 hover:text-white hover:bg-slate-900/50 transition duration-200 {{ Route::is('agent.reviews') ? 'sidebar-link-active' : '' }}">
                    <i class="fa-solid fa-star text-lg w-5"></i>
                    <span class="font-medium text-sm">My Reviews</span>
                </a>
                <a href="{{ route('agent.analytics') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-400 hover:text-white hover:bg-slate-900/50 transition duration-200 {{ Route::is('agent.analytics') ? 'sidebar-link-active' : '' }}">
                    <i class="fa-solid fa-chart-line text-lg w-5"></i>
                    <span class="font-medium text-sm">Sales & Commissions</span>
                </a>
                <a href="{{ route('agent.clients') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-400 hover:text-white hover:bg-slate-900/50 transition duration-200 {{ Route::is('agent.clients') ? 'sidebar-link-active' : '' }}">
                    <i class="fa-solid fa-users text-lg w-5"></i>
                    <span class="font-medium text-sm">My Clients CRM</span>
                </a>
                <a href="{{ route('agent.calendar') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-400 hover:text-white hover:bg-slate-900/50 transition duration-200 {{ Route::is('agent.calendar') ? 'sidebar-link-active' : '' }}">
                    <i class="fa-solid fa-calendar-days text-lg w-5"></i>
                    <span class="font-medium text-sm">Availability Calendar</span>
                </a>
                <a href="{{ route('agent.profile') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-400 hover:text-white hover:bg-slate-900/50 transition duration-200 {{ Route::is('agent.profile') ? 'sidebar-link-active' : '' }}">
                    <i class="fa-solid fa-user-gear text-lg w-5"></i>
                    <span class="font-medium text-sm">Profile Details</span>
                </a>
            </nav>
        </div>

        <!-- User Profile Footer -->
        <div class="p-4 border-t border-slate-900 bg-slate-950/40">
            <div class="flex items-center gap-3 mb-4">
                @if(session('agent_user_image'))
                    <img src="{{ session('agent_user_image') }}" alt="Profile" class="w-10 h-10 rounded-full object-cover shadow-inner border border-purple-500/20">
                @else
                    <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-purple-500 to-indigo-500 flex items-center justify-center font-bold text-white shadow-inner">
                        {{ substr(session('agent_user_name', 'Agent'), 0, 1) }}
                    </div>
                @endif
                <div>
                    <h4 class="text-sm font-semibold text-slate-200">{{ session('agent_user_name', 'Real Estate Agent') }}</h4>
                    <span class="text-xs text-purple-400 font-medium">Licensed Agent</span>
                </div>
            </div>
            <a href="{{ route('agent.logout') }}" class="w-full flex items-center justify-center gap-2 py-2 px-4 border border-red-500/20 hover:border-red-500/50 rounded-lg text-red-400 hover:text-red-300 hover:bg-red-500/10 text-xs font-semibold tracking-wider transition duration-200">
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
                Sign Out
            </a>
        </div>
    </aside>

    <!-- Main Content Area -->
    <main class="flex-1 flex flex-col min-w-0 bg-[#07050f]">
        <!-- Top bar -->
        <header class="h-20 bg-slate-950/60 border-b border-slate-900 flex items-center justify-between px-6 md:px-8 shrink-0">
            <h2 class="font-outfit font-bold text-lg md:text-xl text-slate-200">@yield('page_title', 'Agent Workspace')</h2>
            <div class="flex items-center gap-4">
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

            @yield('content')
        </div>
    </main>

    @yield('scripts')
    @include('layouts.water_wave')
</body>
</html>
