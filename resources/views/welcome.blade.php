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
            background-color: #030712;
            background-image: 
                radial-gradient(circle at center, rgba(3, 7, 18, 0.2) 0%, rgba(3, 7, 18, 0.90) 80%),
                url('/images/welcome_bg.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }
        .glass-panel {
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.04);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        }
        .glowing-hero {
            text-shadow: 0 0 40px rgba(245, 158, 11, 0.2);
        }
    </style>
</head>
<body class="font-sans min-h-screen flex flex-col justify-between text-slate-200">

    <!-- Top Navigation / Connection Status -->
    <header class="w-full max-w-7xl mx-auto px-6 py-6 flex justify-between items-center">
        <div class="flex items-center gap-2">
            <div class="w-9 h-9 rounded-lg bg-gradient-to-tr from-amber-500 to-orange-600 flex items-center justify-center text-white font-bold text-lg font-outfit shadow-lg shadow-amber-500/20">
                EX
            </div>
            <span class="font-outfit font-extrabold text-xl tracking-tight bg-gradient-to-r from-amber-400 via-orange-200 to-white bg-clip-text text-transparent">EstateX</span>
        </div>
    </header>

    <!-- Main Content -->
    <main class="w-full max-w-6xl mx-auto px-6 py-12 flex flex-col items-center justify-center my-auto">
        
        <!-- Hero Introduction -->
        <div class="text-center mb-12 max-w-2xl">
            <h1 class="font-outfit font-black text-4xl md:text-5xl text-white tracking-tight leading-tight mb-4">
                Premium Real Estate <br>
                <span class="bg-gradient-to-r from-amber-400 via-orange-400 to-blue-500 bg-clip-text text-transparent glowing-hero">Database Platform</span>
            </h1>
            <p class="text-slate-400 text-sm md:text-base leading-relaxed">
                Choose your portal below to sign in or create an account. All platforms leverage raw SQL queries communicating with Oracle.
            </p>
        </div>

        <!-- Portal Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 w-full">
            
            <!-- Buyer Portal Card -->
            <div class="glass-panel p-6 rounded-3xl flex flex-col justify-between hover:border-emerald-500/30 transition duration-300 group">
                <div>
                    <div class="w-10 h-10 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 mb-6 group-hover:scale-105 transition duration-300">
                        <i class="fa-solid fa-house-chimney text-lg"></i>
                    </div>
                    <h3 class="font-outfit font-bold text-xl text-slate-100 mb-2">Buyer Marketplace</h3>
                    <p class="text-[11px] text-slate-400 leading-relaxed mb-6">
                        Browse verified properties, save listings to your wishlist, schedule guided site visits, and reserve units with active transaction logs.
                    </p>
                    <ul class="space-y-2 mb-8 text-[11px]">
                        <li class="flex items-center gap-2 text-slate-300">
                            <i class="fa-solid fa-circle-check text-emerald-500"></i>
                            <span>Browse with custom pricing filters</span>
                        </li>
                        <li class="flex items-center gap-2 text-slate-300">
                            <i class="fa-solid fa-circle-check text-emerald-500"></i>
                            <span>Book visits & make reservation deposits</span>
                        </li>
                    </ul>
                </div>
                <div class="space-y-2.5">
                    <a href="{{ route('buyer.login') }}" class="w-full block py-2.5 text-center bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-white text-xs font-bold tracking-wide rounded-xl shadow-lg shadow-emerald-500/10 transition duration-200">
                        Sign In as Buyer
                    </a>
                    <a href="{{ route('buyer.register') }}" class="w-full block py-2.5 text-center bg-slate-900/60 hover:bg-slate-800 border border-slate-800 hover:border-slate-700 text-slate-300 hover:text-white text-xs font-bold tracking-wide rounded-xl transition duration-200">
                        Register New Account
                    </a>
                </div>
            </div>

            <!-- Property Owner Card -->
            <div class="glass-panel p-6 rounded-3xl flex flex-col justify-between hover:border-amber-500/30 transition duration-300 group">
                <div>
                    <div class="w-10 h-10 rounded-2xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400 mb-6 group-hover:scale-105 transition duration-300">
                        <i class="fa-solid fa-house-laptop text-lg"></i>
                    </div>
                    <h3 class="font-outfit font-bold text-xl text-slate-100 mb-2">Owner Workspace</h3>
                    <p class="text-[11px] text-slate-400 leading-relaxed mb-6">
                        List residential or commercial spaces, manage photo galleries, delegate listing representations to agents, and review buyer bookings.
                    </p>
                    <ul class="space-y-2 mb-8 text-[11px]">
                        <li class="flex items-center gap-2 text-slate-300">
                            <i class="fa-solid fa-circle-check text-amber-500"></i>
                            <span>Publish listings & upload media files</span>
                        </li>
                        <li class="flex items-center gap-2 text-slate-300">
                            <i class="fa-solid fa-circle-check text-amber-500"></i>
                            <span>Approve visits & delegate representation</span>
                        </li>
                    </ul>
                </div>
                <div class="space-y-2.5">
                    <a href="{{ route('owner.login') }}" class="w-full block py-2.5 text-center bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-400 hover:to-orange-500 text-slate-950 font-bold tracking-wide rounded-xl shadow-lg shadow-amber-500/10 transition duration-200">
                        Sign In as Owner
                    </a>
                    <a href="{{ route('owner.register') }}" class="w-full block py-2.5 text-center bg-slate-900/60 hover:bg-slate-800 border border-slate-800 hover:border-slate-700 text-slate-300 hover:text-white text-xs font-bold tracking-wide rounded-xl transition duration-200">
                        Register Owner Account
                    </a>
                </div>
            </div>

            <!-- Real Estate Agent Card -->
            <div class="glass-panel p-6 rounded-3xl flex flex-col justify-between hover:border-purple-500/30 transition duration-300 group">
                <div>
                    <div class="w-10 h-10 rounded-2xl bg-purple-500/10 border border-purple-500/20 flex items-center justify-center text-purple-400 mb-6 group-hover:scale-105 transition duration-300">
                        <i class="fa-solid fa-user-tie text-lg"></i>
                    </div>
                    <h3 class="font-outfit font-bold text-xl text-slate-100 mb-2">Agent Workspace</h3>
                    <p class="text-[11px] text-slate-400 leading-relaxed mb-6">
                        Manage assigned property delegations, approve buyer visit requests, record site tours, and review client satisfaction feedback.
                    </p>
                    <ul class="space-y-2 mb-8 text-[11px]">
                        <li class="flex items-center gap-2 text-slate-300">
                            <i class="fa-solid fa-circle-check text-purple-500"></i>
                            <span>View assigned listings representing owners</span>
                        </li>
                        <li class="flex items-center gap-2 text-slate-300">
                            <i class="fa-solid fa-circle-check text-purple-500"></i>
                            <span>Manage visitor bookings & calendar leads</span>
                        </li>
                    </ul>
                </div>
                <div class="space-y-2.5">
                    <a href="{{ route('agent.login') }}" class="w-full block py-2.5 text-center bg-gradient-to-r from-purple-500 to-indigo-600 hover:from-purple-400 hover:to-indigo-500 text-white font-bold tracking-wide rounded-xl shadow-lg shadow-purple-500/10 transition duration-200 text-xs">
                        Sign In as Agent
                    </a>
                    <a href="{{ route('agent.register') }}" class="w-full block py-2.5 text-center bg-slate-900/60 hover:bg-slate-800 border border-slate-800 hover:border-slate-700 text-slate-300 hover:text-white text-xs font-bold tracking-wide rounded-xl transition duration-200">
                        Register Agent Account
                    </a>
                </div>
            </div>

            <!-- Administrative Console Card -->
            <div class="glass-panel p-6 rounded-3xl flex flex-col justify-between hover:border-blue-500/30 transition duration-300 group">
                <div>
                    <div class="w-10 h-10 rounded-2xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400 mb-6 group-hover:scale-105 transition duration-300">
                        <i class="fa-solid fa-user-shield text-lg"></i>
                    </div>
                    <h3 class="font-outfit font-bold text-xl text-slate-100 mb-2">Admin Console</h3>
                    <p class="text-[11px] text-slate-400 leading-relaxed mb-6">
                        Access system-wide tools to manage directories, moderate property listings, review agent performance leaderboards, and audit platform transaction history.
                    </p>
                    <ul class="space-y-2 mb-8 text-[11px]">
                        <li class="flex items-center gap-2 text-slate-300">
                            <i class="fa-solid fa-circle-check text-blue-500"></i>
                            <span>Suspend/activate platform users</span>
                        </li>
                        <li class="flex items-center gap-2 text-slate-300">
                            <i class="fa-solid fa-circle-check text-blue-500"></i>
                            <span>Moderate & delete property listings</span>
                        </li>
                    </ul>
                </div>
                <div>
                    <a href="{{ route('admin.login') }}" class="w-full block py-2.5 text-center bg-gradient-to-r from-blue-600 to-indigo-500 hover:from-blue-500 hover:to-indigo-400 text-white text-xs font-bold tracking-wide rounded-xl shadow-lg shadow-blue-500/10 transition duration-200">
                        Access Admin Panel
                    </a>
                    <div class="text-[9px] text-slate-500 text-center mt-4">
                        Authorized staff only. Session audits are logged.
                    </div>
                </div>
            </div>

        </div>

    </main>

    <!-- Footer -->
    <footer class="w-full py-6 text-center border-t border-slate-900/50 mt-12">
        <span class="text-xs text-slate-600 font-medium">EstateX Platform &copy; 2026. Powered by raw SQL & Oracle Database.</span>
    </footer>

    @include('layouts.water_wave')
</body>
</html>
