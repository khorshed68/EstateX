<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EstateX - Create Owner Account</title>
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
                radial-gradient(at 0% 100%, rgba(245, 158, 11, 0.12) 0, transparent 50%),
                radial-gradient(at 100% 0%, rgba(234, 88, 12, 0.15) 0, transparent 50%);
        }
        .glowing-card {
            box-shadow: 0 0 50px -12px rgba(245, 158, 11, 0.15);
        }
    </style>
</head>
<body class="font-sans min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md my-8">
        <!-- Logo and Branding -->
        <div class="flex flex-col items-center mb-8">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-amber-500 to-orange-600 flex items-center justify-center text-white font-bold text-2xl font-outfit shadow-xl shadow-amber-500/20 mb-4 animate-pulse">
                EX
            </div>
            <h1 class="font-outfit font-black text-3xl text-white tracking-tight">EstateX</h1>
            <p class="text-slate-400 text-sm mt-1">Join the network to list and manage property portfolios</p>
        </div>

        <!-- Glass Registration Card -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-8 glowing-card backdrop-blur-md">
            <div class="mb-6">
                <h2 class="text-xl font-bold text-slate-100">Create Owner Account</h2>
                <p class="text-xs text-slate-500 mt-1">Register to represent your properties, link with agents, and track site visits.</p>
            </div>

            <!-- Error and Success Messages -->
            @if(session('error'))
                <div class="mb-4 p-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-400 text-xs flex items-center gap-2">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <form action="{{ route('owner.register.submit') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                
                <!-- Full Name -->
                <div>
                    <label for="fullname" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Full Name</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500">
                            <i class="fa-solid fa-user text-xs"></i>
                        </span>
                        <input type="text" id="fullname" name="fullname" value="{{ old('fullname') }}" required placeholder="Jane Doe" 
                               class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2.5 pl-10 pr-4 text-sm text-slate-200 placeholder-slate-600 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition duration-200">
                    </div>
                    @error('fullname')
                        <span class="text-red-400 text-[11px] mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Email Address</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500">
                            <i class="fa-solid fa-envelope text-xs"></i>
                        </span>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="jane@example.com" 
                               class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2.5 pl-10 pr-4 text-sm text-slate-200 placeholder-slate-600 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition duration-200">
                    </div>
                    @error('email')
                        <span class="text-red-400 text-[11px] mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Phone Number -->
                <div>
                    <label for="phone" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Phone Number</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500">
                            <i class="fa-solid fa-phone text-xs"></i>
                        </span>
                        <input type="text" id="phone" name="phone" value="{{ old('phone') }}" placeholder="01700000000" 
                               class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2.5 pl-10 pr-4 text-sm text-slate-200 placeholder-slate-600 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition duration-200">
                    </div>
                    @error('phone')
                        <span class="text-red-400 text-[11px] mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Profile Image -->
                <div>
                    <label for="profile_image" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Profile Picture</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500">
                            <i class="fa-solid fa-image text-xs"></i>
                        </span>
                        <input type="file" id="profile_image" name="profile_image" accept="image/*"
                               class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2 pl-10 pr-4 text-xs text-slate-400 file:mr-4 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-[10px] file:font-bold file:bg-amber-500/10 file:text-amber-400 hover:file:bg-amber-500/20 file:cursor-pointer transition duration-200">
                    </div>
                    @error('profile_image')
                        <span class="text-red-400 text-[11px] mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500">
                            <i class="fa-solid fa-lock text-xs"></i>
                        </span>
                        <input type="password" id="password" name="password" required placeholder="Min 6 characters" 
                               class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2.5 pl-10 pr-4 text-sm text-slate-200 placeholder-slate-600 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition duration-200">
                    </div>
                    @error('password')
                        <span class="text-red-400 text-[11px] mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Confirm Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500">
                            <i class="fa-solid fa-check-double text-xs"></i>
                        </span>
                        <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Repeat password" 
                               class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2.5 pl-10 pr-4 text-sm text-slate-200 placeholder-slate-600 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition duration-200">
                    </div>
                </div>

                <!-- Register Button -->
                <button type="submit" 
                        class="w-full py-3.5 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-400 hover:to-orange-500 text-white text-sm font-bold tracking-wide rounded-xl shadow-lg shadow-amber-500/10 hover:shadow-amber-500/20 active:scale-[0.98] transition duration-200 mt-4">
                    Create Account
                </button>
            </form>

            <div class="mt-6 text-center text-xs text-slate-500">
                Already have an owner account? 
                <a href="{{ route('owner.login') }}" class="text-amber-400 hover:text-amber-300 font-bold hover:underline transition duration-200">Log In Here</a>
            </div>
        </div>
    </div>

    @include('layouts.water_wave')
</body>
</html>
