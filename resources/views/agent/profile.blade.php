@extends('layouts.agent')

@section('page_title', 'Profile Details')

@section('content')
    <div class="mb-6">
        <h3 class="font-outfit font-bold text-lg text-slate-200">Manage Professional Profile</h3>
        <p class="text-xs text-slate-500 mt-0.5">Update your personal registration details and agency presentation settings.</p>
    </div>

    <form action="{{ route('agent.profile.update') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        @csrf

        <!-- Left Column: Profile Fields (2/3 width) -->
        <div class="lg:col-span-2 glass-panel p-8 rounded-3xl space-y-6 h-fit">
            
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

            <!-- Save Changes Button -->
            <div class="pt-2">
                <button type="submit" class="water-wave-btn w-full py-3.5 bg-gradient-to-r from-purple-500 to-indigo-600 hover:from-purple-400 hover:to-indigo-500 text-white text-sm font-bold tracking-wide rounded-xl shadow-lg shadow-purple-500/20 hover:shadow-purple-500/30 transition duration-200">
                    <span class="relative z-10">Save Changes</span>
                </button>
            </div>

        </div>

        <!-- Right Column: Profile Picture Card (1/3 width) -->
        <div class="lg:col-span-1 space-y-6 h-fit">
            <div class="glass-panel p-6 rounded-3xl flex flex-col items-center justify-center text-center relative group">
                <h3 class="font-outfit font-bold text-sm text-slate-200 mb-6">Profile Photo</h3>
                
                <!-- Image Wrapper -->
                <div class="relative w-36 h-36 rounded-full overflow-hidden shadow-inner border border-slate-800 bg-slate-950/60 mb-6 flex items-center justify-center">
                    @if($profile->profileimage)
                        <img id="avatar-preview" src="/{{ $profile->profileimage }}" alt="Profile Photo" class="w-full h-full object-cover">
                    @else
                        <div id="avatar-placeholder" class="w-full h-full bg-gradient-to-tr from-purple-600 to-indigo-600 flex items-center justify-center font-bold text-white text-4xl shadow-inner uppercase">
                            {{ substr(old('fullname', $profile->fullname), 0, 1) }}
                        </div>
                        <img id="avatar-preview" class="w-full h-full object-cover hidden">
                    @endif
                </div>

                <!-- Custom styled upload button -->
                <div class="w-full">
                    <label for="profile_image" class="block py-2.5 px-4 bg-slate-900 border border-slate-800 hover:bg-slate-850 hover:border-slate-700 text-slate-300 rounded-xl text-xs font-bold tracking-wide cursor-pointer transition duration-200 hover:text-white">
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
