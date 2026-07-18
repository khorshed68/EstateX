<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EstateX - Premium Real Estate Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            background-color: #0b0c16;
            background-image: radial-gradient(circle at 50% 30%, rgba(99, 102, 241, 0.08) 0%, transparent 70%);
            background-attachment: fixed;
        }
        .bg-grid {
            background-size: 55px 55px;
            background-image: 
                linear-gradient(to right, rgba(255, 255, 255, 0.018) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(255, 255, 255, 0.018) 1px, transparent 1px);
        }
        .glass-panel {
            background: rgba(8, 12, 24, 0.45);
            backdrop-filter: blur(24px) saturate(180%);
            -webkit-backdrop-filter: blur(24px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.06);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.5);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .glass-panel:hover {
            background: rgba(12, 18, 36, 0.65);
            border-color: rgba(255, 255, 255, 0.12);
            transform: translateY(-6px);
        }
        .glowing-hero {
            text-shadow: 0 0 60px rgba(245, 158, 11, 0.2);
        }
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        .animate-float-slow {
            animation: floatSlow 10s ease-in-out infinite;
        }
        .animate-float-medium {
            animation: floatMedium 8s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-8px); }
        }
        @keyframes floatSlow {
            0%, 100% { transform: translateY(0px) scale(1); }
            50% { transform: translateY(-24px) scale(1.08); }
        }
        @keyframes floatMedium {
            0%, 100% { transform: translateY(0px) scale(1); }
            50% { transform: translateY(24px) scale(0.92); }
        }
    </style>
</head>
<body class="font-sans min-h-screen flex flex-col justify-between text-slate-200 overflow-x-hidden relative">

    <!-- Cyber Grid Background -->
    <div class="absolute inset-0 bg-grid opacity-80 pointer-events-none -z-20"></div>

    <!-- Top Navigation -->
    <header class="w-full max-w-7xl mx-auto px-8 py-8 flex justify-between items-center relative z-10">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-500 to-orange-600 flex items-center justify-center text-white font-bold text-xl font-outfit shadow-lg shadow-orange-500/20">
                EX
            </div>
            <span class="font-outfit font-extrabold text-2xl tracking-tight bg-gradient-to-r from-amber-400 via-orange-300 to-white bg-clip-text text-transparent">EstateX</span>
        </div>
        <div class="hidden md:flex items-center gap-6 text-[10px] font-extrabold text-slate-400 uppercase tracking-[0.2em]">
            <span class="hover:text-amber-400 transition cursor-pointer">Marketplace</span>
            <span>•</span>
            <span class="hover:text-amber-400 transition cursor-pointer">Analytics</span>
            <span>•</span>
            <span class="hover:text-amber-400 transition cursor-pointer">Workspace</span>
        </div>
    </header>

    <!-- Main Content -->
    <main class="w-full max-w-7xl mx-auto px-8 py-8 flex flex-col items-center justify-center my-auto relative z-10">
        
        <!-- Hero Introduction -->
        <div class="text-center mb-16 max-w-3xl animate-float">
            <div class="inline-flex items-center gap-2 px-3.5 py-2 rounded-full bg-slate-950/80 border border-slate-900 text-[10px] uppercase tracking-[0.15em] font-extrabold text-amber-400 mb-6 shadow-inner">
                <span class="w-2 h-2 rounded-full bg-amber-400 animate-ping"></span>
                Integrated Real Estate Ecosystem
            </div>
            <h1 class="font-outfit font-black text-4xl md:text-5xl text-white tracking-tight leading-tight mb-6">
                Property Management System, <br>
                <span class="bg-gradient-to-r from-amber-400 via-orange-400 to-blue-500 bg-clip-text text-transparent glowing-hero">Marketplace & Analytics Platform</span>
            </h1>
            <p class="text-slate-400 text-sm max-w-xl mx-auto leading-relaxed">
                Experience next-generation asset dealing. Select your portal below to manage properties, inspect financial ledgers, or browse listings.
            </p>
        </div>

        <!-- Portal Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 w-full">
            
            <!-- Buyer Portal Card -->
            <div class="glass-panel p-8 rounded-[32px] flex flex-col justify-between hover:border-emerald-500/30 hover:shadow-[0_0_40px_rgba(16,185,129,0.08)] transition duration-300 group">
                <div>
                    <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 mb-8 group-hover:scale-110 group-hover:bg-emerald-500/20 transition duration-300">
                        <i class="fa-solid fa-house-chimney text-xl"></i>
                    </div>
                    <h3 class="font-outfit font-bold text-2xl text-slate-100 mb-3">Buyer Portal</h3>
                    <p class="text-xs text-slate-400 leading-relaxed mb-8">
                        Browse verified properties, save listings to wishlists, schedule guided site visits, and lock reservation deposits.
                    </p>
                    <ul class="space-y-3.5 mb-10 text-xs">
                        <li class="flex items-center gap-3 text-slate-300">
                            <i class="fa-solid fa-circle-check text-emerald-500 text-sm"></i>
                            <span>Custom pricing filters</span>
                        </li>
                        <li class="flex items-center gap-3 text-slate-300">
                            <i class="fa-solid fa-circle-check text-emerald-500 text-sm"></i>
                            <span>Site visit appointment logs</span>
                        </li>
                    </ul>
                </div>
                <div class="space-y-3">
                    <a href="{{ route('buyer.login') }}" class="w-full block py-3 text-center bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-white text-xs font-bold tracking-wide rounded-xl shadow-lg shadow-emerald-500/20 transition duration-200">
                        Sign In as Buyer
                    </a>
                    <a href="{{ route('buyer.register') }}" class="w-full block py-3 text-center bg-slate-950/40 hover:bg-slate-900 border border-slate-800 hover:border-slate-700 text-slate-300 hover:text-white text-xs font-bold tracking-wide rounded-xl transition duration-200">
                        Register Account
                    </a>
                </div>
            </div>

            <!-- Property Owner Card -->
            <div class="glass-panel p-8 rounded-[32px] flex flex-col justify-between hover:border-amber-500/30 hover:shadow-[0_0_40px_rgba(245,158,11,0.08)] transition duration-300 group">
                <div>
                    <div class="w-12 h-12 rounded-2xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400 mb-8 group-hover:scale-110 group-hover:bg-amber-500/20 transition duration-300">
                        <i class="fa-solid fa-house-laptop text-xl"></i>
                    </div>
                    <h3 class="font-outfit font-bold text-2xl text-slate-100 mb-3">Owner Panel</h3>
                    <p class="text-xs text-slate-400 leading-relaxed mb-8">
                        Publish residential or commercial listings, upload photo galleries, delegate agent representations, and manage bookings.
                    </p>
                    <ul class="space-y-3.5 mb-10 text-xs">
                        <li class="flex items-center gap-3 text-slate-300">
                            <i class="fa-solid fa-circle-check text-amber-500 text-sm"></i>
                            <span>Publish & modify property inventory</span>
                        </li>
                        <li class="flex items-center gap-3 text-slate-300">
                            <i class="fa-solid fa-circle-check text-amber-500 text-sm"></i>
                            <span>Approve visits & represent agents</span>
                        </li>
                    </ul>
                </div>
                <div class="space-y-3">
                    <a href="{{ route('owner.login') }}" class="w-full block py-3 text-center bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-400 hover:to-orange-500 text-slate-950 font-bold tracking-wide rounded-xl shadow-lg shadow-amber-500/20 transition duration-200">
                        Sign In as Owner
                    </a>
                    <a href="{{ route('owner.register') }}" class="w-full block py-3 text-center bg-slate-950/40 hover:bg-slate-900 border border-slate-800 hover:border-slate-700 text-slate-300 hover:text-white text-xs font-bold tracking-wide rounded-xl transition duration-200">
                        Register Account
                    </a>
                </div>
            </div>

            <!-- Real Estate Agent Card -->
            <div class="glass-panel p-8 rounded-[32px] flex flex-col justify-between hover:border-purple-500/30 hover:shadow-[0_0_40px_rgba(168,85,247,0.08)] transition duration-300 group">
                <div>
                    <div class="w-12 h-12 rounded-2xl bg-purple-500/10 border border-purple-500/20 flex items-center justify-center text-purple-400 mb-8 group-hover:scale-110 group-hover:bg-purple-500/20 transition duration-300">
                        <i class="fa-solid fa-user-tie text-xl"></i>
                    </div>
                    <h3 class="font-outfit font-bold text-2xl text-slate-100 mb-3">Agent Console</h3>
                    <p class="text-xs text-slate-400 leading-relaxed mb-8">
                        Coordinate representation offers, record physical property visits, adjust schedules, and analyze commission trends.
                    </p>
                    <ul class="space-y-3.5 mb-10 text-xs">
                        <li class="flex items-center gap-3 text-slate-300">
                            <i class="fa-solid fa-circle-check text-purple-500 text-sm"></i>
                            <span>Schedule slot configurations</span>
                        </li>
                        <li class="flex items-center gap-3 text-slate-300">
                            <i class="fa-solid fa-circle-check text-purple-500 text-sm"></i>
                            <span>CRM buyer communication feeds</span>
                        </li>
                    </ul>
                </div>
                <div class="space-y-3">
                    <a href="{{ route('agent.login') }}" class="w-full block py-3 text-center bg-gradient-to-r from-purple-500 to-indigo-600 hover:from-purple-400 hover:to-indigo-500 text-white font-bold tracking-wide rounded-xl shadow-lg shadow-purple-500/20 transition duration-200">
                        Sign In as Agent
                    </a>
                    <a href="{{ route('agent.register') }}" class="w-full block py-3 text-center bg-slate-950/40 hover:bg-slate-900 border border-slate-800 hover:border-slate-700 text-slate-300 hover:text-white text-xs font-bold tracking-wide rounded-xl transition duration-200">
                        Register Account
                    </a>
                </div>
            </div>

            <!-- Administrative Console Card -->
            <div class="glass-panel p-8 rounded-[32px] flex flex-col justify-between hover:border-blue-500/30 hover:shadow-[0_0_40px_rgba(59,130,246,0.08)] transition duration-300 group">
                <div>
                    <div class="w-12 h-12 rounded-2xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400 mb-8 group-hover:scale-110 group-hover:bg-blue-500/20 transition duration-300">
                        <i class="fa-solid fa-user-shield text-xl"></i>
                    </div>
                    <h3 class="font-outfit font-bold text-2xl text-slate-100 mb-3">Admin Console</h3>
                    <p class="text-xs text-slate-400 leading-relaxed mb-8">
                        Govern directories, audit platform transaction metrics, evaluate agent performance leaderboards, and adjust user scopes.
                    </p>
                    <ul class="space-y-3.5 mb-10 text-xs">
                        <li class="flex items-center gap-3 text-slate-300">
                            <i class="fa-solid fa-circle-check text-blue-500 text-sm"></i>
                            <span>Moderate platform users & status</span>
                        </li>
                        <li class="flex items-center gap-3 text-slate-300">
                            <i class="fa-solid fa-circle-check text-blue-500 text-sm"></i>
                            <span>Cascade deletes & database logs</span>
                        </li>
                    </ul>
                </div>
                <div>
                    <a href="{{ route('admin.login') }}" class="w-full block py-3 text-center bg-gradient-to-r from-blue-600 to-indigo-500 hover:from-blue-500 hover:to-indigo-400 text-white text-xs font-bold tracking-wide rounded-xl shadow-lg shadow-blue-500/20 transition duration-200">
                        Access Admin Panel
                    </a>
                    <div class="text-[10px] text-slate-500 text-center mt-5 font-semibold">
                        Authorized Personnel Only
                    </div>
                </div>
            </div>

        </div>



    </main>

    <!-- Footer -->
    <footer class="w-full py-8 text-center border-t border-slate-900/40 mt-16 relative z-10">
        <span class="text-xs text-slate-500 font-medium">EstateX Platform &copy; 2026. Powered by raw SQL & Oracle Database.</span>
    </footer>

    @include('layouts.water_wave')
</body>
</html>
