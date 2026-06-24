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
                radial-gradient(at 0% 100%, rgba(16, 185, 129, 0.1) 0, transparent 50%),
                radial-gradient(at 100% 0%, rgba(59, 130, 246, 0.12) 0, transparent 50%);
        }
        .glass-panel {
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.04);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        }
        .glowing-hero {
            text-shadow: 0 0 40px rgba(16, 185, 129, 0.3);
        }
    </style>
</head>
<body class="font-sans min-h-screen flex flex-col justify-between text-slate-200">

    <!-- Top Navigation / Connection Status -->
    <header class="w-full max-w-7xl mx-auto px-6 py-6 flex justify-between items-center">
        <div class="flex items-center gap-2">
            <div class="w-9 h-9 rounded-lg bg-gradient-to-tr from-emerald-500 to-blue-600 flex items-center justify-center text-white font-bold text-lg font-outfit shadow-lg shadow-emerald-500/20">
                EX
            </div>
            <span class="font-outfit font-extrabold text-xl tracking-tight bg-gradient-to-r from-emerald-400 via-teal-200 to-white bg-clip-text text-transparent">EstateX</span>
        </div>
        <div class="flex items-center gap-2 bg-slate-900/80 border border-slate-800 rounded-full px-4 py-1.5 text-xs">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            <span class="text-slate-400 font-medium">Oracle Live Connection</span>
        </div>
    </header>

    <!-- Main Content -->
    <main class="w-full max-w-4xl mx-auto px-6 py-12 flex flex-col items-center justify-center my-auto">
        
        <!-- Hero Introduction -->
        <div class="text-center mb-12 max-w-2xl">
            <h1 class="font-outfit font-black text-4xl md:text-5xl text-white tracking-tight leading-tight mb-4">
                Premium Real Estate <br>
                <span class="bg-gradient-to-r from-emerald-400 via-teal-300 to-blue-500 bg-clip-text text-transparent glowing-hero">Database Platform</span>
            </h1>
            <p class="text-slate-400 text-sm md:text-base leading-relaxed">
                Connect, browse, and secure high-value real estate. Select a portal below to sign in or create an account to start your journey.
            </p>
        </div>

        <!-- Portal Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 w-full">
            
            <!-- Buyer Portal Card -->
            <div class="glass-panel p-8 rounded-3xl flex flex-col justify-between hover:border-emerald-500/30 transition duration-300 group">
                <div>
                    <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 mb-6 group-hover:scale-105 transition duration-300">
                        <i class="fa-solid fa-house-chimney text-xl"></i>
                    </div>
                    <h3 class="font-outfit font-bold text-2xl text-slate-100 mb-3">Buyer Marketplace</h3>
                    <p class="text-xs text-slate-400 leading-relaxed mb-6">
                        Browse verified properties, save listings to your wishlist, schedule guided site visits, and reserve units with active transaction logs.
                    </p>
                    <ul class="space-y-2 mb-8">
                        <li class="flex items-center gap-2 text-xs text-slate-300">
                            <i class="fa-solid fa-circle-check text-emerald-500"></i>
                            <span>Browse with custom pricing & area filters</span>
                        </li>
                        <li class="flex items-center gap-2 text-xs text-slate-300">
                            <i class="fa-solid fa-circle-check text-emerald-500"></i>
                            <span>Book visits & make reservation deposits</span>
                        </li>
                        <li class="flex items-center gap-2 text-xs text-slate-300">
                            <i class="fa-solid fa-circle-check text-emerald-500"></i>
                            <span>Submit property & agent reviews</span>
                        </li>
                    </ul>
                </div>
                <div class="space-y-3">
                    <a href="{{ route('buyer.login') }}" class="w-full block py-3 text-center bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-white text-xs font-bold tracking-wide rounded-xl shadow-lg shadow-emerald-500/10 hover:shadow-emerald-500/20 transition duration-200">
                        Sign In as Buyer
                    </a>
                    <a href="{{ route('buyer.register') }}" class="w-full block py-3 text-center bg-slate-900/60 hover:bg-slate-800 border border-slate-800 hover:border-slate-700 text-slate-300 hover:text-white text-xs font-bold tracking-wide rounded-xl transition duration-200">
                        Register New Account
                    </a>
                </div>
            </div>

            <!-- Administrative Console Card -->
            <div class="glass-panel p-8 rounded-3xl flex flex-col justify-between hover:border-blue-500/30 transition duration-300 group">
                <div>
                    <div class="w-12 h-12 rounded-2xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400 mb-6 group-hover:scale-105 transition duration-300">
                        <i class="fa-solid fa-user-shield text-xl"></i>
                    </div>
                    <h3 class="font-outfit font-bold text-2xl text-slate-100 mb-3">Admin Console</h3>
                    <p class="text-xs text-slate-400 leading-relaxed mb-6">
                        Access system-wide tools to manage directories, moderate property listings, review agent performance leaderboards, and audit platform transaction history.
                    </p>
                    <ul class="space-y-2 mb-8">
                        <li class="flex items-center gap-2 text-xs text-slate-300">
                            <i class="fa-solid fa-circle-check text-blue-500"></i>
                            <span>Suspend/activate platform users</span>
                        </li>
                        <li class="flex items-center gap-2 text-xs text-slate-300">
                            <i class="fa-solid fa-circle-check text-blue-500"></i>
                            <span>Moderate & delete property catalog listings</span>
                        </li>
                        <li class="flex items-center gap-2 text-xs text-slate-300">
                            <i class="fa-solid fa-circle-check text-blue-500"></i>
                            <span>Audit daily performance & system metrics</span>
                        </li>
                    </ul>
                </div>
                <div>
                    <a href="{{ route('admin.login') }}" class="w-full block py-3 text-center bg-gradient-to-r from-blue-600 to-indigo-500 hover:from-blue-500 hover:to-indigo-400 text-white text-xs font-bold tracking-wide rounded-xl shadow-lg shadow-blue-500/10 hover:shadow-blue-500/20 transition duration-200">
                        Access Admin Panel
                    </a>
                    <div class="text-[10px] text-slate-500 text-center mt-4">
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

</body>
</html>
