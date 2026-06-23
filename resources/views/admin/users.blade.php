@extends('layouts.admin')

@section('page_title', 'User & Agent Accounts')

@section('content')
<div class="space-y-6">

    <!-- Search & Filter Card -->
    <div class="glass-panel p-6 rounded-2xl flex flex-col md:flex-row items-center justify-between gap-4">
        <div>
            <h3 class="text-lg font-bold text-slate-100">User Directory</h3>
            <p class="text-xs text-slate-400 mt-1">Manage system access roles, agents, and buyers.</p>
        </div>
        <form action="{{ route('admin.users') }}" method="GET" class="w-full md:w-auto flex gap-2">
            <div class="relative w-full md:w-80">
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
                <a href="{{ route('admin.users') }}" class="px-4 py-2.5 border border-slate-850 hover:bg-slate-800 rounded-xl text-xs font-bold text-slate-400 transition duration-200 shrink-0 flex items-center justify-center">
                    Clear
                </a>
            @endif
        </form>
    </div>

    <!-- Users Grid/Table -->
    <div class="glass-panel rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="text-slate-500 border-b border-slate-800 bg-slate-900/30">
                        <th class="p-4 font-semibold text-center w-16">ID</th>
                        <th class="p-4 font-semibold">Name & Contact</th>
                        <th class="p-4 font-semibold">Email</th>
                        <th class="p-4 font-semibold">Role</th>
                        <th class="p-4 font-semibold">Account Status</th>
                        <th class="p-4 font-semibold text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/40 text-slate-300">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-850/5">
                            <td class="p-4 text-center font-bold text-slate-500">#{{ $user->id }}</td>
                            <td class="p-4">
                                <div class="font-semibold text-slate-200 text-sm">{{ $user->fullname }}</div>
                                <div class="text-[10px] text-slate-500 mt-0.5"><i class="fa-solid fa-phone text-[9px] mr-1"></i>{{ $user->phone ?? 'No phone' }}</div>
                            </td>
                            <td class="p-4 font-mono text-slate-400">{{ $user->email }}</td>
                            <td class="p-4">
                                <span class="px-2 py-0.5 rounded font-bold text-[10px] uppercase border
                                    @if($user->roleName === 'admin') bg-purple-500/10 text-purple-400 border-purple-500/20
                                    @elseif($user->roleName === 'agent') bg-blue-500/10 text-blue-400 border-blue-500/20
                                    @else bg-green-500/10 text-green-400 border-green-500/20 @endif">
                                    {{ $user->roleName }}
                                </span>
                            </td>
                            <td class="p-4">
                                <span class="flex items-center gap-1.5 font-semibold 
                                    @if($user->status === 'active') text-green-400 @else text-red-400 @endif">
                                    <span class="w-1.5 h-1.5 rounded-full @if($user->status === 'active') bg-green-400 @else bg-red-400 @endif"></span>
                                    {{ ucfirst($user->status) }}
                                </span>
                            </td>
                            <td class="p-4 text-center">
                                @if($user->id === session('admin_user_id'))
                                    <span class="text-xs text-slate-600 italic">Self (Protected)</span>
                                @else
                                    <div class="flex items-center justify-center gap-2">
                                        @if($user->status === 'active')
                                            <!-- Suspend Trigger Form -->
                                            <form action="{{ route('admin.users.suspend', $user->id) }}" method="POST" onsubmit="return confirmSuspend(event, '{{ $user->fullname }}')">
                                                @csrf
                                                <input type="hidden" name="reason" id="reason_{{ $user->id }}" value="Administrative suspension">
                                                <button type="submit" class="px-3 py-1.5 bg-red-500/10 hover:bg-red-500/20 border border-red-500/20 hover:border-red-500/40 rounded-lg text-red-400 hover:text-red-300 font-bold transition duration-200">
                                                    <i class="fa-solid fa-user-slash mr-1"></i> Suspend
                                                </button>
                                            </form>
                                        @else
                                            <!-- Activate Action Form -->
                                            <form action="{{ route('admin.users.activate', $user->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="px-3 py-1.5 bg-green-500/10 hover:bg-green-500/20 border border-green-500/20 hover:border-green-500/40 rounded-lg text-green-400 hover:text-green-300 font-bold transition duration-200">
                                                    <i class="fa-solid fa-user-check mr-1"></i> Reactivate
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-6 text-center text-slate-500">No users found matching query.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function confirmSuspend(event, userName) {
        event.preventDefault();
        const reason = prompt(`Provide a suspension audit reason for user "${userName}":`, "Administrative suspension");
        if (reason === null) return false; // Cancelled
        
        const form = event.target;
        form.querySelector('input[name="reason"]').value = reason || "Suspended by Administrator";
        form.submit();
    }
</script>
@endsection
