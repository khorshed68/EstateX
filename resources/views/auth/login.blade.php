<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EstateX - Administrative Sign In</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;855&family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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
            background-color: #050811;
            background-image: 
                radial-gradient(at 0% 0%, rgba(30, 58, 138, 0.15) 0, transparent 50%),
                radial-gradient(at 100% 100%, rgba(99, 102, 241, 0.15) 0, transparent 50%);
        }
        .glowing-box {
            box-shadow: 0 0 40px -10px rgba(59, 130, 246, 0.2);
        }
    </style>
</head>
<body class="font-sans min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md">
        <!-- Logo Header -->
        <div class="flex flex-col items-center mb-8">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-500 flex items-center justify-center text-white font-bold text-2xl font-outfit shadow-xl shadow-blue-500/25 mb-4 animate-bounce">
                EX
            </div>
            <h1 class="font-outfit font-black text-3xl text-white tracking-tight">EstateX</h1>
            <p class="text-slate-400 text-sm mt-1">Real Estate Database Administration</p>
        </div>

        <!-- Form Card -->
        <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-8 glowing-box backdrop-blur-md">
            <h2 class="text-xl font-bold text-slate-100 mb-6">Administrator Portal</h2>

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

            <form action="{{ route('admin.login.submit') }}" method="POST" class="space-y-5">
                @csrf
                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Email Address</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500">
                            <i class="fa-solid fa-envelope"></i>
                        </span>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="admin@estatex.com" 
                               class="w-full bg-slate-950 border border-slate-850 rounded-xl py-3 pl-10 pr-4 text-sm text-slate-200 placeholder-slate-600 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition duration-200">
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
                            <i class="fa-solid fa-lock"></i>
                        </span>
                        <input type="password" id="password" name="password" required placeholder="••••••••" 
                               class="w-full bg-slate-950 border border-slate-850 rounded-xl py-3 pl-10 pr-4 text-sm text-slate-200 placeholder-slate-600 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition duration-200">
                    </div>
                    @error('password')
                        <span class="text-red-400 text-[11px] mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Sign In Button -->
                <button type="submit" 
                        class="w-full py-3.5 bg-gradient-to-r from-blue-600 to-indigo-500 hover:from-blue-500 hover:to-indigo-400 text-white text-sm font-bold tracking-wide rounded-xl shadow-lg shadow-blue-500/10 hover:shadow-blue-500/20 active:scale-[0.98] transition duration-200 mt-2">
                    Verify & Access Portal
                </button>
            </form>
        </div>

        <div class="text-center mt-6">
            <span class="text-xs text-slate-600">Secure Database Lab Connection</span>
        </div>
    </div>

</body>
</html>
