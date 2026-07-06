<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EstateX - Agent Registration</title>
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

    <div class="w-full max-w-xl my-8">
        <!-- Logo and Branding -->
        <div class="flex flex-col items-center mb-8">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-purple-500 to-indigo-600 flex items-center justify-center text-white font-bold text-2xl font-outfit shadow-xl shadow-purple-500/20 mb-4 animate-pulse">
                EX
            </div>
            <h1 class="font-outfit font-black text-3xl text-white tracking-tight">EstateX</h1>
            <p class="text-slate-400 text-sm mt-1">Real Estate Agent Workspace Signup</p>
        </div>

        <!-- Glass Registration Card -->
        <div class="bg-slate-950/80 border border-slate-900 rounded-3xl p-8 glowing-card backdrop-blur-md">
            <div class="mb-6">
                <h2 class="text-xl font-bold text-slate-100">Agent Registration</h2>
                <p class="text-xs text-slate-500 mt-1">Create an account to manage listings, accept bookings, and work with property owners.</p>
            </div>

            <!-- Error and Success Messages -->
            @if(session('error'))
                <div class="mb-4 p-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-400 text-xs flex items-center gap-2">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <form action="{{ route('agent.register.submit') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Full Name -->
                    <div>
                        <label for="fullname" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Full Name</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500">
                                <i class="fa-solid fa-user text-xs"></i>
                            </span>
                            <input type="text" id="fullname" name="fullname" value="{{ old('fullname') }}" required placeholder="Sheikh Sadi" 
                                   class="w-full bg-slate-950 border border-slate-900 rounded-xl py-3 pl-10 pr-4 text-sm text-slate-200 placeholder-slate-700 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition duration-200">
                        </div>
                        @error('fullname')
                            <span class="text-red-400 text-[11px] mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Email Address</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500">
                                <i class="fa-solid fa-envelope text-xs"></i>
                            </span>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="sadi@estatex.com" 
                                   class="w-full bg-slate-950 border border-slate-900 rounded-xl py-3 pl-10 pr-4 text-sm text-slate-200 placeholder-slate-700 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition duration-200">
                        </div>
                        @error('email')
                            <span class="text-red-400 text-[11px] mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
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

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Confirm Password</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500">
                                <i class="fa-solid fa-circle-check text-xs"></i>
                            </span>
                            <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="••••••••" 
                                   class="w-full bg-slate-950 border border-slate-900 rounded-xl py-3 pl-10 pr-4 text-sm text-slate-200 placeholder-slate-700 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition duration-200">
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Phone Number</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500">
                                <i class="fa-solid fa-phone text-xs"></i>
                            </span>
                            <input type="text" id="phone" name="phone" value="{{ old('phone') }}" placeholder="017XXXXXXXX" 
                                   class="w-full bg-slate-950 border border-slate-900 rounded-xl py-3 pl-10 pr-4 text-sm text-slate-200 placeholder-slate-700 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition duration-200">
                        </div>
                        @error('phone')
                            <span class="text-red-400 text-[11px] mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Agency Name -->
                    <div>
                        <label for="agency_name" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Agency Name</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500">
                                <i class="fa-solid fa-building text-xs"></i>
                            </span>
                            <input type="text" id="agency_name" name="agency_name" value="{{ old('agency_name') }}" placeholder="Khulna Realty" 
                                   class="w-full bg-slate-950 border border-slate-900 rounded-xl py-3 pl-10 pr-4 text-sm text-slate-200 placeholder-slate-700 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition duration-200">
                        </div>
                        @error('agency_name')
                            <span class="text-red-400 text-[11px] mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- License Number -->
                    <div>
                        <label for="license_no" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">License Number</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500">
                                <i class="fa-solid fa-id-card text-xs"></i>
                            </span>
                            <input type="text" id="license_no" name="license_no" value="{{ old('license_no') }}" required placeholder="LIC-9982" 
                                   class="w-full bg-slate-950 border border-slate-900 rounded-xl py-3 pl-10 pr-4 text-sm text-slate-200 placeholder-slate-700 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition duration-200">
                        </div>
                        @error('license_no')
                            <span class="text-red-400 text-[11px] mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Experience Years -->
                    <div>
                        <label for="experience_years" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Years of Experience</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500">
                                <i class="fa-solid fa-briefcase text-xs"></i>
                            </span>
                            <input type="number" id="experience_years" name="experience_years" value="{{ old('experience_years', 0) }}" required min="0" placeholder="5" 
                                   class="w-full bg-slate-950 border border-slate-900 rounded-xl py-3 pl-10 pr-4 text-sm text-slate-200 placeholder-slate-700 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition duration-200">
                        </div>
                        @error('experience_years')
                            <span class="text-red-400 text-[11px] mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Profile Image -->
                <div>
                    <label for="profile_image" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Profile Picture</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500">
                            <i class="fa-solid fa-image text-xs"></i>
                        </span>
                        <input type="file" id="profile_image" name="profile_image" accept="image/*"
                               class="w-full bg-slate-950 border border-slate-900 rounded-xl py-2.5 pl-10 pr-4 text-xs text-slate-400 file:mr-4 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-[10px] file:font-bold file:bg-purple-500/10 file:text-purple-400 hover:file:bg-purple-500/20 file:cursor-pointer transition duration-200">
                    </div>
                    @error('profile_image')
                        <span class="text-red-400 text-[11px] mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- About me -->
                <div>
                    <label for="about" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">About Description</label>
                    <textarea id="about" name="about" rows="3" placeholder="Tell owners about yourself, your areas of expertise, etc..." 
                              class="w-full bg-slate-950 border border-slate-900 rounded-xl py-3 px-4 text-sm text-slate-200 placeholder-slate-700 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition duration-200">{{ old('about') }}</textarea>
                    @error('about')
                        <span class="text-red-400 text-[11px] mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-purple-500 to-indigo-600 hover:from-purple-400 hover:to-indigo-500 text-white text-sm font-bold tracking-wide rounded-xl shadow-lg shadow-purple-500/20 hover:shadow-purple-500/30 transition duration-200">
                    Register as Agent
                </button>
            </form>

            <!-- Footer links -->
            <div class="mt-6 text-center">
                <span class="text-xs text-slate-400">Already registered as an agent?</span>
                <a href="{{ route('agent.login') }}" class="text-xs text-purple-400 hover:text-purple-300 font-bold ml-1 hover:underline">Sign In</a>
            </div>
        </div>

        <div class="text-center mt-6">
            <a href="/" class="text-xs text-slate-500 hover:text-slate-400 flex items-center justify-center gap-2 transition duration-200">
                <i class="fa-solid fa-arrow-left"></i>
                Back to Portal Directory
            </a>
        </div>
    </div>

</body>
</html>
