@extends('layouts.owner')

@section('page_title', 'Profile Details')

@section('content')
<div class="px-6 py-6 md:px-8">

    <div class="mb-6">
        <h3 class="font-outfit font-bold text-lg text-slate-200">Manage Landlord Profile</h3>
        <p class="text-xs text-slate-500 mt-0.5">Update your personal account credentials, contact phone, and security password settings.</p>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/30 rounded-2xl text-emerald-400 text-xs font-semibold flex items-center gap-2">
            <i class="fa-solid fa-circle-check text-sm"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 p-4 bg-red-500/10 border border-red-500/30 rounded-2xl text-red-400 text-xs font-semibold flex items-center gap-2">
            <i class="fa-solid fa-circle-exclamation text-sm"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <form action="{{ route('owner.profile.update') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        @csrf

        <!-- Left Column: Fields (2/3 width) -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Account Details Card -->
            <div class="glass-panel p-8 rounded-3xl space-y-6">
                <h4 class="font-outfit font-bold text-sm text-slate-200 border-b border-slate-900 pb-3">Personal Information</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Full Name -->
                    <div>
                        <label for="fullname" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Full Name</label>
                        <input type="text" id="fullname" name="fullname" value="{{ old('fullname', $profile->fullname) }}" required 
                               class="w-full bg-slate-950 border border-slate-800 rounded-xl py-3 px-4 text-sm text-slate-200 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition duration-200">
                        @error('fullname')
                            <span class="text-red-400 text-[11px] mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Email Address</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $profile->email) }}" required 
                               class="w-full bg-slate-950 border border-slate-800 rounded-xl py-3 px-4 text-sm text-slate-200 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition duration-200">
                        @error('email')
                            <span class="text-red-400 text-[11px] mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Phone Number -->
                <div>
                    <label for="phone" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Phone Number</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone', $profile->phone) }}" 
                           class="w-full bg-slate-950 border border-slate-800 rounded-xl py-3 px-4 text-sm text-slate-200 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition duration-200">
                    @error('phone')
                        <span class="text-red-400 text-[11px] mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Password / Security Card -->
            <div class="glass-panel p-8 rounded-3xl space-y-6">
                <h4 class="font-outfit font-bold text-sm text-slate-200 border-b border-slate-900 pb-3">Update Password</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <!-- Current Password -->
                    <div>
                        <label for="current_password" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Current Password</label>
                        <input type="password" id="current_password" name="current_password" placeholder="••••••••"
                               class="w-full bg-slate-950 border border-slate-800 rounded-xl py-3 px-4 text-sm text-slate-200 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition duration-200">
                        @error('current_password')
                            <span class="text-red-400 text-[11px] mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- New Password -->
                    <div>
                        <label for="new_password" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">New Password</label>
                        <input type="password" id="new_password" name="new_password" placeholder="Min 6 chars"
                               class="w-full bg-slate-950 border border-slate-800 rounded-xl py-3 px-4 text-sm text-slate-200 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition duration-200">
                        @error('new_password')
                            <span class="text-red-400 text-[11px] mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="new_password_confirmation" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Confirm Password</label>
                        <input type="password" id="new_password_confirmation" name="new_password_confirmation" placeholder="Confirm"
                               class="w-full bg-slate-950 border border-slate-800 rounded-xl py-3 px-4 text-sm text-slate-200 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition duration-200">
                    </div>
                </div>
            </div>

            <!-- Save Changes Button -->
            <div>
                <button type="submit" class="water-wave-btn w-full py-3.5 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-400 hover:to-orange-500 text-white text-sm font-bold tracking-wide rounded-xl shadow-lg shadow-orange-500/20 hover:shadow-orange-500/30 transition duration-200">
                    <span class="relative z-10">Save Changes</span>
                </button>
            </div>

        </div>

        <!-- Right Column: Avatar upload card (1/3 width) -->
        <div class="lg:col-span-1 space-y-6 h-fit">
            <div class="glass-panel p-6 rounded-3xl flex flex-col items-center justify-center text-center relative group">
                <h3 class="font-outfit font-bold text-sm text-slate-200 mb-6">Profile Photo</h3>
                
                <!-- Image Wrapper -->
                <div class="relative w-36 h-36 rounded-full overflow-hidden shadow-inner border border-slate-800 bg-slate-950/60 mb-6 flex items-center justify-center">
                    @if($profile->profileimage)
                        <img id="avatar-preview" src="{{ str_starts_with($profile->profileimage, '/') ? $profile->profileimage : '/' . $profile->profileimage }}" alt="Profile Photo" class="w-full h-full object-cover">
                    @else
                        <div id="avatar-placeholder" class="w-full h-full bg-gradient-to-tr from-amber-500 to-orange-600 flex items-center justify-center font-bold text-white text-4xl shadow-inner uppercase">
                            {{ substr(old('fullname', $profile->fullname), 0, 1) }}
                        </div>
                        <img id="avatar-preview" class="w-full h-full object-cover hidden">
                    @endif
                </div>

                <!-- Custom styled upload button -->
                <div class="w-full">
                    <label for="profile_image" class="block py-2.5 px-4 bg-slate-950 border border-slate-800 hover:bg-slate-900 hover:border-slate-750 text-slate-300 rounded-xl text-xs font-bold tracking-wide cursor-pointer transition duration-200 hover:text-white">
                        <i class="fa-solid fa-cloud-arrow-up mr-2"></i>
                        Upload New Photo
                    </label>
                    <input type="file" id="profile_image" name="profile_image" accept="image/*" class="hidden">
                    <p class="text-[10px] text-slate-500 mt-3 leading-relaxed">
                        Supports JPG, PNG or GIF.<br>Max size 2MB.
                    </p>
                    @error('profile_image')
                        <span class="text-red-400 text-[11px] mt-2 block">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

    </form>

</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('profile_image');
        const previewImg = document.getElementById('avatar-preview');
        const placeholder = document.getElementById('avatar-placeholder');

        if (fileInput) {
            fileInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        if (previewImg) {
                            previewImg.src = e.target.result;
                            previewImg.classList.remove('hidden');
                        }
                        if (placeholder) {
                            placeholder.classList.add('hidden');
                        }
                    }
                    reader.readAsDataURL(file);
                }
            });
        }
    });
</script>
@endsection
