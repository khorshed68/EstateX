<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EstateX - Agent Sign In</title>
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
            background-color: #030209;
            background-image: 
                radial-gradient(at 0% 100%, rgba(168, 85, 247, 0.12) 0, transparent 50%),
                radial-gradient(at 100% 0%, rgba(99, 102, 241, 0.15) 0, transparent 50%);
        }
        .glowing-card {
            box-shadow: 0 0 50px -12px rgba(168, 85, 247, 0.15);
        }
    </style>
</head>
<body class="font-sans min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md">
        <!-- Logo and Branding -->
        <div class="flex flex-col items-center mb-8">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-purple-500 to-indigo-600 flex items-center justify-center text-white font-bold text-2xl font-outfit shadow-xl shadow-purple-500/20 mb-4 animate-pulse">
                EX
            </div>
            <h1 class="font-outfit font-black text-3xl text-white tracking-tight">EstateX</h1>
            <p class="text-slate-400 text-sm mt-1">Manage listings & bookings representing owners</p>
        </div>

        <!-- Glass Login Card -->
        <div class="bg-slate-950/80 border border-slate-900 rounded-3xl p-8 glowing-card backdrop-blur-md">
            <div class="mb-6">
                <h2 class="text-xl font-bold text-slate-100">Agent Sign In</h2>
                <p class="text-xs text-slate-500 mt-1">Access your assigned property leads and client visits.</p>
            </div>

            <!-- Error and Success Messages -->
            @if(session('error'))
                <div class="mb-4 p-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-400 text-xs flex items-center gap-2">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if(session('success'))
                <div class="mb-4 p-4 rounded-xl bg-green-500/10 border border-green-500/30 text-green-400 text-xs flex items-center gap-2">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <form action="{{ route('agent.login.submit') }}" method="POST" class="space-y-5">
                @csrf
                
                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Email Address</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500">
                            <i class="fa-solid fa-envelope text-xs"></i>
                        </span>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="agent@estatex.com" 
                               class="w-full bg-slate-950 border border-slate-900 rounded-xl py-3 pl-10 pr-4 text-sm text-slate-200 placeholder-slate-700 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition duration-200">
                    </div>
                    @error('email')
                        <span class="text-red-400 text-[11px] mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500">
                            <i class="fa-solid fa-lock text-xs"></i>
                        </span>
                        <input type="password" id="password" name="password" required placeholder="••••••••" 
                               class="w-full bg-slate-950 border border-slate-900 rounded-xl py-3 pl-10 pr-4 text-sm text-slate-200 placeholder-slate-700 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition duration-200">
                    </div>
                    @error('password')
                        <span class="text-red-400 text-[11px] mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-purple-500 to-indigo-600 hover:from-purple-400 hover:to-indigo-500 text-white text-sm font-bold tracking-wide rounded-xl shadow-lg shadow-purple-500/20 hover:shadow-purple-500/30 transition duration-200">
                    Sign In as Agent
                </button>
            </form>

            <!-- Test Credentials Quick-Fill -->
            <div class="mt-6 pt-6 border-t border-slate-900">
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block mb-2.5">Demo Agent Account</span>
                <button onclick="document.getElementById('email').value='sadi@estatex.com'; document.getElementById('password').value='agent123';"
                        class="w-full py-2 bg-slate-900 hover:bg-slate-800 border border-slate-900 hover:border-slate-800 text-slate-300 hover:text-white rounded-lg text-xs font-semibold flex items-center justify-center gap-2 transition duration-200">
                    <i class="fa-solid fa-circle-play text-purple-400"></i>
                    Quick-Fill: Sheikh Sadi (Agent)
                </button>
            </div>

            <!-- Footer links -->
            <div class="mt-6 text-center">
                <span class="text-xs text-slate-400">Not registered as an agent?</span>
                <a href="{{ route('agent.register') }}" class="text-xs text-purple-400 hover:text-purple-300 font-bold ml-1 hover:underline">Create Account</a>
            </div>
        </div>

        <div class="text-center mt-6">
            <a href="/" class="text-xs text-slate-500 hover:text-slate-400 flex items-center justify-center gap-2 transition duration-200">
                <i class="fa-solid fa-arrow-left"></i>
                Back to Portal Directory
            </a>
        </div>
    </div>

    @include('layouts.water_wave')
</body>
</html>
