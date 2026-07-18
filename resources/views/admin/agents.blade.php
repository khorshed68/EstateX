@extends('layouts.admin')

@section('page_title', 'Agent Moderation')

@section('content')
<div class="space-y-6">

    <!-- Search & Info Card -->
    <div class="glass-panel p-6 rounded-2xl flex flex-col md:flex-row items-center justify-between gap-4">
        <div>
            <h3 class="text-lg font-bold text-slate-100">Agent Accounts</h3>
            <p class="text-xs text-slate-400 mt-1">Moderate agent licensing, update rating scores, and review experience metrics.</p>
        </div>
        <form action="{{ route('admin.agents') }}" method="GET" class="w-full md:w-auto flex gap-2">
            <div class="relative w-full md:w-80">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" name="search" value="{{ $search }}" placeholder="Search by name or agency..." 
                       class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2.5 pl-10 pr-4 text-xs text-slate-200 placeholder-slate-700 focus:outline-none focus:border-blue-500 transition duration-200">
            </div>
            <button type="submit" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-500 rounded-xl text-xs font-bold text-white transition duration-200 shrink-0">
                Search
            </button>
            @if($search)
                <a href="{{ route('admin.agents') }}" class="px-4 py-2.5 border border-slate-850 hover:bg-slate-800 rounded-xl text-xs font-bold text-slate-400 transition duration-200 shrink-0 flex items-center justify-center">
                    Clear
                </a>
            @endif
        </form>
    </div>

    <!-- Agents List Grid/Table -->
    <div class="glass-panel rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="text-slate-500 border-b border-slate-800 bg-slate-900/30">
                        <th class="p-4 font-semibold text-center w-16">ID</th>
                        <th class="p-4 font-semibold">Agent Name</th>
                        <th class="p-4 font-semibold">Agency & License</th>
                        <th class="p-4 font-semibold">Experience</th>
                        <th class="p-4 font-semibold">Active Listings</th>
                        <th class="p-4 font-semibold">Rating Score</th>
                        <th class="p-4 font-semibold text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/40 text-slate-300">
                    @forelse($agents as $agent)
                        <tr class="hover:bg-slate-850/5">
                            <td class="p-4 text-center font-bold text-slate-500">#{{ $agent->id }}</td>
                            <td class="p-4">
                                <div class="font-semibold text-slate-200 text-sm">{{ $agent->fullname }}</div>
                                <div class="text-[10px] text-slate-500 mt-0.5"><i class="fa-solid fa-envelope mr-1"></i>{{ $agent->email }}</div>
                                <div class="text-[10px] text-slate-500"><i class="fa-solid fa-phone mr-1"></i>{{ $agent->phone ?? 'No phone' }}</div>
                            </td>
                            <td class="p-4">
                                <div class="font-medium text-slate-300">{{ $agent->agencyname ?? 'Independent Agent' }}</div>
                                <div class="text-[10px] font-mono text-slate-500 mt-0.5">{{ $agent->licenseno }}</div>
                            </td>
                            <td class="p-4">
                                <span class="font-semibold text-slate-300">{{ $agent->experienceyears }} Years</span>
                            </td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 rounded-lg bg-blue-500/10 border border-blue-500/20 text-blue-400 font-bold text-[10px]">
                                    {{ $agent->listings_count }} Listed
                                </span>
                            </td>
                            <td class="p-4">
                                <div class="flex items-center gap-1.5 font-bold text-amber-400 text-sm">
                                    <span>{{ number_format($agent->rating, 2) }}</span>
                                    <i class="fa-solid fa-star text-xs"></i>
                                </div>
                            </td>
                            <td class="p-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <!-- Moderate Profile (Agent Specific) -->
                                    <button onclick="openEditModal({{ json_encode($agent) }})" 
                                            class="px-3 py-1.5 bg-purple-500/10 hover:bg-purple-500/20 border border-purple-500/20 hover:border-purple-500/40 rounded-lg text-purple-400 hover:text-purple-300 font-bold transition duration-200"
                                            title="Moderate Credentials">
                                        <i class="fa-solid fa-user-pen mr-1"></i> Moderate
                                    </button>

                                    <!-- Edit User Details -->
                                    <a href="{{ route('admin.users.edit', $agent->userid) }}" 
                                       class="px-3 py-1.5 bg-blue-500/10 hover:bg-blue-500/20 border border-blue-500/20 hover:border-blue-500/40 rounded-lg text-blue-400 hover:text-blue-300 font-bold transition duration-200">
                                        <i class="fa-solid fa-pen-to-square mr-1"></i> Edit
                                    </a>
                                    
                                    <!-- Suspend / Reactivate User -->
                                    @if($agent->status === 'active')
                                        <form action="{{ route('admin.users.suspend', $agent->userid) }}" method="POST" onsubmit="return confirmSuspend(event, '{{ $agent->fullname }}')">
                                            @csrf
                                            <input type="hidden" name="reason" id="reason_{{ $agent->userid }}" value="Administrative suspension">
                                            <button type="submit" class="px-3 py-1.5 bg-red-500/10 hover:bg-red-500/20 border border-red-500/20 hover:border-red-500/40 rounded-lg text-red-400 hover:text-red-300 font-bold transition duration-200">
                                                <i class="fa-solid fa-user-slash mr-1"></i> Suspend
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.users.activate', $agent->userid) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 bg-green-500/10 hover:bg-green-500/20 border border-green-500/20 hover:border-green-500/40 rounded-lg text-green-400 hover:text-green-300 font-bold transition duration-200">
                                                @if($agent->status === 'pending')
                                                    <i class="fa-solid fa-user-check mr-1"></i> Approve
                                                @else
                                                    <i class="fa-solid fa-user-check mr-1"></i> Reactivate
                                                @endif
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Delete User -->
                                    <form action="{{ route('admin.users.delete', $agent->userid) }}" method="POST" onsubmit="return confirm('Are you sure you want to permanently delete agent {{ $agent->fullname }}? All listings, wishlist items, bookings, and profile details will be removed.');">
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
                            <td colspan="7" class="p-6 text-center text-slate-500">No agents found matching search query.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Moderation Modal -->
<div id="editModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-slate-900 border border-slate-800 rounded-3xl w-full max-w-md p-6 overflow-hidden relative shadow-2xl">
        <div class="mb-5">
            <h3 class="font-outfit font-bold text-lg text-slate-100">Moderate Agent Profile</h3>
            <p class="text-xs text-slate-500 mt-1">Review and override credential attributes for: <span id="agent_name_display" class="font-bold text-slate-300"></span></p>
        </div>
        
        <form id="editForm" method="POST" class="space-y-4">
            @csrf
            
            <!-- Agency Name -->
            <div>
                <label for="agency_name" class="block text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-2">Agency Name</label>
                <input type="text" id="agency_name" name="agency_name" 
                       class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2.5 px-3.5 text-xs text-slate-200 focus:outline-none focus:border-blue-500 transition duration-200">
            </div>

            <!-- License Number -->
            <div>
                <label for="license_no" class="block text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-2">License Number</label>
                <input type="text" id="license_no" name="license_no" required 
                       class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2.5 px-3.5 text-xs text-slate-200 focus:outline-none focus:border-blue-500 transition duration-200">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <!-- Experience Years -->
                <div>
                    <label for="experience_years" class="block text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-2">Experience Years</label>
                    <input type="number" id="experience_years" name="experience_years" required min="0" 
                           class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2.5 px-3.5 text-xs text-slate-200 focus:outline-none focus:border-blue-500 transition duration-200">
                </div>

                <!-- Rating -->
                <div>
                    <label for="rating" class="block text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-2">Rating Score (0-5)</label>
                    <input type="number" id="rating" name="rating" required min="0" max="5" step="0.01" 
                           class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2.5 px-3.5 text-xs text-slate-200 focus:outline-none focus:border-blue-500 transition duration-200">
                </div>
            </div>

            <div class="flex justify-end gap-2.5 pt-4">
                <button type="button" onclick="closeEditModal()" 
                        class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold rounded-lg border border-slate-750 transition duration-200">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold rounded-lg transition duration-200">
                    Save Moderations
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openEditModal(agent) {
        document.getElementById('agent_name_display').textContent = agent.fullname;
        document.getElementById('agency_name').value = agent.agencyname || '';
        document.getElementById('license_no').value = agent.licenseno;
        document.getElementById('experience_years').value = agent.experienceyears;
        document.getElementById('rating').value = agent.rating;
        
        // Dynamic form action
        const form = document.getElementById('editForm');
        form.action = `/admin/agents/${agent.id}/update`;
        
        // Show Modal
        document.getElementById('editModal').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }

    function confirmSuspend(event, userName) {
        event.preventDefault();
        const reason = prompt(`Provide a suspension audit reason for agent "${userName}":`, "Administrative suspension");
        if (reason === null) return false; // Cancelled
        
        const form = event.target;
        form.querySelector('input[name="reason"]').value = reason || "Suspended by Administrator";
        form.submit();
    }
</script>
@endsection
