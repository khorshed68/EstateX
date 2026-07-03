@extends('layouts.agent')

@section('page_title', 'Profile Details')

@section('content')
    <div class="mb-6">
        <h3 class="font-outfit font-bold text-lg text-slate-200">Manage Professional Profile</h3>
        <p class="text-xs text-slate-500 mt-0.5">Update your personal registration details and agency presentation settings.</p>
    </div>

    <div class="max-w-2xl">
        <div class="glass-panel p-8 rounded-3xl">
            <form action="{{ route('agent.profile.update') }}" method="POST" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Full Name -->
                    <div>
                        <label for="fullname" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Full Name</label>
                        <input type="text" id="fullname" name="fullname" value="{{ old('fullname', $profile->fullname) }}" required 
                               class="w-full bg-slate-950 border border-slate-900 rounded-xl py-3 px-4 text-sm text-slate-200 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition duration-200">
                        @error('fullname')
                            <span class="text-red-400 text-[11px] mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Email Address</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $profile->email) }}" required 
                               class="w-full bg-slate-950 border border-slate-900 rounded-xl py-3 px-4 text-sm text-slate-200 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition duration-200">
                        @error('email')
                            <span class="text-red-400 text-[11px] mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Phone Number</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone', $profile->phone) }}" 
                               class="w-full bg-slate-950 border border-slate-900 rounded-xl py-3 px-4 text-sm text-slate-200 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition duration-200">
                        @error('phone')
                            <span class="text-red-400 text-[11px] mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Agency Name -->
                    <div>
                        <label for="agency_name" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Agency Name</label>
                        <input type="text" id="agency_name" name="agency_name" value="{{ old('agency_name', $profile->agencyname) }}" 
                               class="w-full bg-slate-950 border border-slate-900 rounded-xl py-3 px-4 text-sm text-slate-200 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition duration-200">
                        @error('agency_name')
                            <span class="text-red-400 text-[11px] mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- License Number -->
                    <div>
                        <label for="license_no" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">License Number</label>
                        <input type="text" id="license_no" name="license_no" value="{{ old('license_no', $profile->licenseno) }}" required 
                               class="w-full bg-slate-950 border border-slate-900 rounded-xl py-3 px-4 text-sm text-slate-200 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition duration-200">
                        @error('license_no')
                            <span class="text-red-400 text-[11px] mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Experience Years -->
                    <div>
                        <label for="experience_years" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Years of Experience</label>
                        <input type="number" id="experience_years" name="experience_years" value="{{ old('experience_years', $profile->experienceyears) }}" required min="0" 
                               class="w-full bg-slate-950 border border-slate-900 rounded-xl py-3 px-4 text-sm text-slate-200 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition duration-200">
                        @error('experience_years')
                            <span class="text-red-400 text-[11px] mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- About Description -->
                <div>
                    <label for="about" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">About Description</label>
                    <textarea id="about" name="about" rows="4" 
                              class="w-full bg-slate-950 border border-slate-900 rounded-xl py-3 px-4 text-sm text-slate-200 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition duration-200">{{ old('about', $profile->about) }}</textarea>
                    @error('about')
                        <span class="text-red-400 text-[11px] mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Action Button -->
                <div class="pt-2">
                    <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-purple-500 to-indigo-600 hover:from-purple-400 hover:to-indigo-500 text-white text-sm font-bold tracking-wide rounded-xl shadow-lg shadow-purple-500/20 hover:shadow-purple-500/30 transition duration-200">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
