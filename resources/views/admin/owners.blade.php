@extends('layouts.admin')

@section('page_title', 'Owner Accounts')

@section('content')
<div class="space-y-6">

    <!-- Search & Info Card -->
    <div class="glass-panel p-6 rounded-2xl flex flex-col md:flex-row items-center justify-between gap-4">
        <div>
            <h3 class="text-lg font-bold text-slate-100">Owner Accounts</h3>
            <p class="text-xs text-slate-400 mt-1">Manage property owner profiles, listings count, and access permissions.</p>
        </div>
        <div class="w-full md:w-auto flex flex-col md:flex-row gap-3">
            <button onclick="openAddOwnerModal()" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-500 rounded-xl text-xs font-bold text-white transition duration-200 shrink-0 flex items-center justify-center gap-1.5 shadow-lg shadow-blue-500/10">
                <i class="fa-solid fa-plus text-xs"></i> Add New Owner
            </button>
            <form action="{{ route('admin.owners') }}" method="GET" class="w-full md:w-auto flex gap-2">
                <div class="relative w-full md:w-64">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </span>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search by name or email..." 
                           class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2.5 pl-10 pr-4 text-xs text-slate-200 placeholder-slate-600 focus:outline-none focus:border-blue-500 transition duration-200">
                </div>
                <button type="submit" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-500 rounded-xl text-xs font-bold text-white transition duration-200 shrink-0">
                    Search
                </button>
                @if($search)
                    <a href="{{ route('admin.owners') }}" class="px-4 py-2.5 border border-slate-850 hover:bg-slate-800 rounded-xl text-xs font-bold text-slate-400 transition duration-200 shrink-0 flex items-center justify-center">
                        Clear
                    </a>
                @endif
            </form>
        </div>
    </div>

    <!-- Owners List Grid/Table -->
    <div class="glass-panel rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="text-slate-500 border-b border-slate-800 bg-slate-900/30">
                        <th class="p-4 font-semibold text-center w-16">ID</th>
                        <th class="p-4 font-semibold">Owner Name</th>
                        <th class="p-4 font-semibold">Email</th>
                        <th class="p-4 font-semibold">Properties Listed</th>
                        <th class="p-4 font-semibold">Account Status</th>
                        <th class="p-4 font-semibold text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/40 text-slate-300">
                    @forelse($owners as $owner)
                        <tr class="hover:bg-slate-850/5">
                            <td class="p-4 text-center font-bold text-slate-500">#{{ $owner->id }}</td>
                            <td class="p-4">
                                <div class="font-semibold text-slate-200 text-sm">{{ $owner->fullname }}</div>
                                <div class="text-[10px] text-slate-500 mt-0.5"><i class="fa-solid fa-phone text-[9px] mr-1"></i>{{ $owner->phone ?? 'No phone' }}</div>
                            </td>
                            <td class="p-4 font-mono text-slate-400">{{ $owner->email }}</td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 rounded-lg bg-blue-500/10 border border-blue-500/20 text-blue-400 font-bold text-[10px]">
                                    {{ $owner->listings_count }} Listed
                                </span>
                            </td>
                            <td class="p-4">
                                <span class="flex items-center gap-1.5 font-semibold 
                                    @if($owner->status === 'active') text-green-400 
                                    @elseif($owner->status === 'pending') text-amber-400 
                                    @else text-red-400 @endif">
                                    <span class="w-1.5 h-1.5 rounded-full 
                                        @if($owner->status === 'active') bg-green-400 
                                        @elseif($owner->status === 'pending') bg-amber-400 
                                        @else bg-red-400 @endif"></span>
                                    {{ ucfirst($owner->status) }}
                                </span>
                            </td>
                            <td class="p-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.users.edit', $owner->id) }}" class="px-3 py-1.5 bg-blue-500/10 hover:bg-blue-500/20 border border-blue-500/20 hover:border-blue-500/40 rounded-lg text-blue-400 hover:text-blue-300 font-bold transition duration-200">
                                        <i class="fa-solid fa-pen-to-square mr-1"></i> Edit
                                    </a>
                                    
                                    @if($owner->status === 'active')
                                        <!-- Suspend Trigger Form -->
                                        <form action="{{ route('admin.users.suspend', $owner->id) }}" method="POST" onsubmit="return confirmSuspend(event, '{{ $owner->fullname }}')">
                                            @csrf
                                            <input type="hidden" name="reason" id="reason_{{ $owner->id }}" value="Administrative suspension">
                                            <button type="submit" class="px-3 py-1.5 bg-red-500/10 hover:bg-red-500/20 border border-red-500/20 hover:border-red-500/40 rounded-lg text-red-400 hover:text-red-300 font-bold transition duration-200">
                                                <i class="fa-solid fa-user-slash mr-1"></i> Suspend
                                            </button>
                                        </form>
                                    @else
                                        <!-- Activate Action Form -->
                                        <form action="{{ route('admin.users.activate', $owner->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 bg-green-500/10 hover:bg-green-500/20 border border-green-500/20 hover:border-green-500/40 rounded-lg text-green-400 hover:text-green-300 font-bold transition duration-200">
                                                @if($owner->status === 'pending')
                                                    <i class="fa-solid fa-user-check mr-1"></i> Approve
                                                @else
                                                    <i class="fa-solid fa-user-check mr-1"></i> Reactivate
                                                @endif
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Delete Action Form -->
                                    <form action="{{ route('admin.users.delete', $owner->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to permanently delete owner {{ $owner->fullname }}? All listings, wishlist items, bookings, and profile details will be removed.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1.5 bg-red-600/10 hover:bg-red-600/20 border border-red-600/20 hover:border-red-600/40 rounded-lg text-red-400 hover:text-red-300 font-bold transition duration-200">
                                            <i class="fa-solid fa-trash-can mr-1"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-6 text-center text-slate-500">No owner accounts found matching query.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Owner Modal -->
<div id="addOwnerModal" class="fixed inset-0 bg-slate-955/80 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-slate-900 border border-slate-800 rounded-3xl w-full max-w-md p-6 overflow-hidden relative shadow-2xl">
        <div class="mb-5 flex justify-between items-center">
            <div>
                <h3 class="font-outfit font-bold text-lg text-slate-100">Add New Owner</h3>
                <p class="text-xs text-slate-500 mt-1">Create a new property owner account directly.</p>
            </div>
            <button onclick="closeAddOwnerModal()" class="w-8 h-8 rounded-full bg-slate-950 border border-slate-800 hover:border-slate-700 flex items-center justify-center text-slate-400 hover:text-white transition duration-200">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-4">
            @csrf
            
            <!-- Preset roleId = 4 for Property Owner -->
            <input type="hidden" name="role_id" value="4">

            <!-- Full Name -->
            <div>
                <label for="modal_fullname" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Full Name</label>
                <input type="text" id="modal_fullname" name="fullname" required 
                       class="w-full bg-slate-950 border border-slate-850 rounded-xl py-2.5 px-4 text-xs text-slate-200 focus:outline-none focus:border-blue-500 transition duration-200">
            </div>

            <!-- Email -->
            <div>
                <label for="modal_email" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Email Address</label>
                <input type="email" id="modal_email" name="email" required 
                       class="w-full bg-slate-950 border border-slate-850 rounded-xl py-2.5 px-4 text-xs text-slate-200 focus:outline-none focus:border-blue-500 transition duration-200">
            </div>

            <!-- Password -->
            <div>
                <label for="modal_password" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Password</label>
                <input type="password" id="modal_password" name="password" required minlength="6"
                       class="w-full bg-slate-950 border border-slate-850 rounded-xl py-2.5 px-4 text-xs text-slate-200 focus:outline-none focus:border-blue-500 transition duration-200">
            </div>

            <!-- Phone -->
            <div>
                <label for="modal_phone" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Phone Number</label>
                <input type="text" id="modal_phone" name="phone" placeholder="e.g. 017xxxxxxxx"
                       class="w-full bg-slate-950 border border-slate-850 rounded-xl py-2.5 px-4 text-xs text-slate-200 focus:outline-none focus:border-blue-500 transition duration-200">
            </div>

            <!-- Submit -->
            <div class="pt-2">
                <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold tracking-wide rounded-xl shadow-lg shadow-blue-500/20 transition duration-200">
                    Create Owner Account
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function confirmSuspend(event, userName) {
        event.preventDefault();
        const reason = prompt(`Provide a suspension audit reason for owner "${userName}":`, "Administrative suspension");
        if (reason === null) return false; // Cancelled
        
        const form = event.target;
        form.querySelector('input[name="reason"]').value = reason || "Suspended by Administrator";
        form.submit();
    }

    function openAddOwnerModal() {
        document.getElementById('addOwnerModal').classList.remove('hidden');
    }

    function closeAddOwnerModal() {
        document.getElementById('addOwnerModal').classList.add('hidden');
    }
</script>
@endsection
