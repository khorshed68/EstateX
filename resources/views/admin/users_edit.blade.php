@extends('layouts.admin')

@section('page_title', 'Edit User Account')

@section('content')
<div class="max-w-md mx-auto">
    <div class="glass-panel p-8 rounded-3xl border border-blue-500/10 mb-8">
        
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h3 class="font-outfit font-bold text-lg text-slate-200">Modify User Account</h3>
                <p class="text-xs text-slate-500 mt-1">Update profile information, access roles, and status flags.</p>
            </div>
            <span class="px-2 py-1 rounded bg-slate-950 border border-slate-800 text-[10px] text-blue-500 font-mono">
                User ID: #{{ $user->id }}
            </span>
        </div>

        <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="space-y-4">
            @csrf

            <!-- Full Name -->
            <div>
                <label for="fullname" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Full Name</label>
                <input type="text" id="fullname" name="fullname" value="{{ old('fullname', $user->fullname) }}" required 
                       class="w-full bg-slate-950 border border-slate-850 rounded-xl py-2.5 px-4 text-xs text-slate-200 focus:outline-none focus:border-blue-500 transition duration-200">
                @error('fullname')
                    <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <!-- Email Address -->
            <div>
                <label for="email" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Email Address</label>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required 
                       class="w-full bg-slate-950 border border-slate-850 rounded-xl py-2.5 px-4 text-xs text-slate-200 focus:outline-none focus:border-blue-500 transition duration-200">
                @error('email')
                    <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <!-- Phone Number -->
            <div>
                <label for="phone" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Phone Number</label>
                <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="e.g. 017xxxxxxxx"
                       class="w-full bg-slate-950 border border-slate-850 rounded-xl py-2.5 px-4 text-xs text-slate-200 focus:outline-none focus:border-blue-500 transition duration-200">
                @error('phone')
                    <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <!-- Account Password (Optional) -->
            <div>
                <label for="password" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">New Password (Leave blank to keep current)</label>
                <input type="password" id="password" name="password" minlength="6" placeholder="••••••••"
                       class="w-full bg-slate-950 border border-slate-850 rounded-xl py-2.5 px-4 text-xs text-slate-200 focus:outline-none focus:border-blue-500 transition duration-200">
                @error('password')
                    <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <!-- Account Role -->
            <div>
                <label for="role_id" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Account Role</label>
                <select id="role_id" name="role_id" required 
                        class="w-full bg-slate-950 border border-slate-850 rounded-xl py-2.5 px-4 text-xs text-slate-200 focus:outline-none focus:border-blue-500 transition duration-200">
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ old('role_id', $user->roleid) == $role->id ? 'selected' : '' }}>
                            {{ ucfirst($role->rolename) }} ({{ $role->roledescription }})
                        </option>
                    @endforeach
                </select>
                @error('role_id')
                    <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <!-- Account Status -->
            <div>
                <label for="status" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Account Status</label>
                <select id="status" name="status" required 
                        class="w-full bg-slate-950 border border-slate-850 rounded-xl py-2.5 px-4 text-xs text-slate-200 focus:outline-none focus:border-blue-500 transition duration-200">
                    <option value="active" {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="suspended" {{ old('status', $user->status) == 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>
                @error('status')
                    <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <!-- Action buttons -->
            <div class="flex justify-end gap-3 pt-4 border-t border-slate-900">
                <a href="{{ route('admin.users') }}" class="px-5 py-2.5 border border-slate-800 hover:bg-slate-800 rounded-xl text-xs font-bold text-slate-400 hover:text-white transition duration-200">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold rounded-xl shadow-lg shadow-blue-500/10 transition duration-200">
                    Save Changes
                </button>
            </div>

        </form>
    </div>
</div>
@endsection
