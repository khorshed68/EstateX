@extends('layouts.admin')

@section('page_title', 'Database Analytics & Dashboard')

@section('content')
<div class="space-y-8">

    <!-- KPI Metric Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Card 1: Users -->
        <div class="glass-panel p-6 rounded-2xl flex items-center justify-between shadow-sm">
            <div>
                <span class="text-slate-400 text-xs font-semibold uppercase tracking-wider block">Total Platform Users</span>
                <h3 class="text-3xl font-outfit font-bold text-white mt-2">{{ number_format($totalUsers) }}</h3>
                <span class="text-xs text-blue-400 font-medium block mt-1">Registered Accounts</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400 text-xl shadow-inner">
                <i class="fa-solid fa-users"></i>
            </div>
        </div>

        <!-- Card 2: Listings -->
        <div class="glass-panel p-6 rounded-2xl flex items-center justify-between shadow-sm">
            <div>
                <span class="text-slate-400 text-xs font-semibold uppercase tracking-wider block">Active Listings</span>
                <h3 class="text-3xl font-outfit font-bold text-white mt-2">{{ number_format($totalListings) }}</h3>
                <span class="text-xs text-green-400 font-medium block mt-1">Available Marketplace</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-green-500/10 border border-green-500/20 flex items-center justify-center text-green-400 text-xl shadow-inner">
                <i class="fa-solid fa-house-circle-check"></i>
            </div>
        </div>

        <!-- Card 3: Revenue -->
        <div class="glass-panel p-6 rounded-2xl flex items-center justify-between shadow-sm">
            <div>
                <span class="text-slate-400 text-xs font-semibold uppercase tracking-wider block">Gross Revenue</span>
                <h3 class="text-3xl font-outfit font-bold text-white mt-2">৳{{ number_format($totalRevenue) }}</h3>
                <span class="text-xs text-yellow-400 font-medium block mt-1">Completed Deals</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-yellow-500/10 border border-yellow-500/20 flex items-center justify-center text-yellow-400 text-xl shadow-inner">
                <i class="fa-solid fa-money-bill-trend-up"></i>
            </div>
        </div>

        <!-- Card 4: Bookings -->
        <div class="glass-panel p-6 rounded-2xl flex items-center justify-between shadow-sm">
            <div>
                <span class="text-slate-400 text-xs font-semibold uppercase tracking-wider block">Booking Success Rate</span>
                <h3 class="text-3xl font-outfit font-bold text-white mt-2">{{ $successRate }}%</h3>
                <span class="text-xs text-indigo-400 font-medium block mt-1">{{ number_format($totalBookings) }} Total Appointments</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400 text-xl shadow-inner">
                <i class="fa-solid fa-calendar-check"></i>
            </div>
        </div>
    </div>

    <!-- Charts Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Monthly Revenue Trend -->
        <div class="glass-panel p-6 rounded-2xl shadow-sm">
            <h4 class="font-outfit font-semibold text-slate-200 text-sm mb-4 uppercase tracking-wider">Monthly Revenue Trend</h4>
            <div class="h-80 w-full">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Trending Locations -->
        <div class="glass-panel p-6 rounded-2xl shadow-sm">
            <h4 class="font-outfit font-semibold text-slate-200 text-sm mb-4 uppercase tracking-wider">Top Locations (Bookings vs Listings)</h4>
            <div class="h-80 w-full">
                <canvas id="locationChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Leaderboard and Trending Properties -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
        <!-- Agent Leaderboard -->
        <div class="glass-panel p-6 rounded-2xl shadow-sm overflow-hidden">
            <h4 class="font-outfit font-semibold text-slate-200 text-sm mb-4 uppercase tracking-wider">Agent Performance Leaderboard</h4>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="text-slate-500 border-b border-slate-800">
                            <th class="py-3 font-semibold">Agent Name</th>
                            <th class="py-3 font-semibold">Agency Name</th>
                            <th class="py-3 font-semibold">Active Listings</th>
                            <th class="py-3 font-semibold">Completed Deals</th>
                            <th class="py-3 font-semibold">Total Revenue</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/40 text-slate-300">
                        @forelse($agents as $agent)
                            <tr>
                                <td class="py-3 font-medium text-slate-200">{{ $agent->agent_name }}</td>
                                <td class="py-3 text-slate-400">{{ $agent->agencyname ?? 'Independent' }}</td>
                                <td class="py-3 text-center">{{ $agent->active_listings }}</td>
                                <td class="py-3 text-center text-blue-400 font-semibold">{{ $agent->completed_deals }}</td>
                                <td class="py-3 text-right font-bold text-green-400">৳{{ number_format($agent->total_revenue) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-4 text-center text-slate-500">No agent performance logs found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Trending Properties Table -->
        <div class="glass-panel p-6 rounded-2xl shadow-sm overflow-hidden">
            <h4 class="font-outfit font-semibold text-slate-200 text-sm mb-4 uppercase tracking-wider">Trending Properties (Scored Recommendation)</h4>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="text-slate-500 border-b border-slate-800">
                            <th class="py-3 font-semibold">Property Title</th>
                            <th class="py-3 font-semibold">Location</th>
                            <th class="py-3 font-semibold">Price</th>
                            <th class="py-3 font-semibold">Wishlists</th>
                            <th class="py-3 font-semibold">Bookings</th>
                            <th class="py-3 font-semibold">Trend Score</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/40 text-slate-300">
                        @forelse($trendingProperties as $prop)
                            <tr>
                                <td class="py-3 font-medium text-slate-200 truncate max-w-[160px]" title="{{ $prop->title }}">{{ $prop->title }}</td>
                                <td class="py-3 text-slate-400">{{ $prop->areaname }}, {{ $prop->city }}</td>
                                <td class="py-3 font-semibold text-slate-300">৳{{ number_format($prop->price) }}</td>
                                <td class="py-3 text-center">{{ $prop->wishlist_count }}</td>
                                <td class="py-3 text-center">{{ $prop->bookings_count }}</td>
                                <td class="py-3 text-center">
                                    <span class="px-2 py-0.5 rounded bg-indigo-500/20 text-indigo-400 font-bold border border-indigo-500/20">
                                        {{ $prop->trend_score }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-4 text-center text-slate-500">No property metrics logged yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Administrative Audit logs -->
    <div class="glass-panel p-6 rounded-2xl shadow-sm">
        <h4 class="font-outfit font-semibold text-slate-200 text-sm mb-4 uppercase tracking-wider">Administrative System Audit Logs (Oracle Database Triggers)</h4>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="text-slate-500 border-b border-slate-800">
                        <th class="py-3 font-semibold">Audit ID</th>
                        <th class="py-3 font-semibold">Performed By (Admin)</th>
                        <th class="py-3 font-semibold">Action Type</th>
                        <th class="py-3 font-semibold">Target Table</th>
                        <th class="py-3 font-semibold">Record Key ID</th>
                        <th class="py-3 font-semibold">Previous State (Old Values)</th>
                        <th class="py-3 font-semibold">Subsequent State (New Values)</th>
                        <th class="py-3 font-semibold">Performed At</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/40 text-slate-400">
                    @forelse($auditLogs as $log)
                        <tr class="hover:bg-slate-850/10">
                            <td class="py-3 font-bold text-slate-500">#{{ $log->id }}</td>
                            <td class="py-3 text-slate-200">{{ $log->admin_name }}</td>
                            <td class="py-3">
                                <span class="px-2 py-0.5 rounded font-bold text-[10px] uppercase border
                                    @if(str_contains($log->actionname, 'DELETE')) bg-red-500/10 text-red-400 border-red-500/20
                                    @elseif(str_contains($log->actionname, 'UPDATE')) bg-yellow-500/10 text-yellow-400 border-yellow-500/20
                                    @else bg-blue-500/10 text-blue-400 border-blue-500/20 @endif">
                                    {{ $log->actionname }}
                                </span>
                            </td>
                            <td class="py-3 text-slate-300 font-mono">{{ $log->tablename }}</td>
                            <td class="py-3 text-center text-slate-300">{{ $log->recordid }}</td>
                            <td class="py-3 font-mono text-[10px] max-w-[200px] truncate" title="{{ $log->oldvalues }}">{{ $log->oldvalues }}</td>
                            <td class="py-3 font-mono text-[10px] max-w-[200px] truncate text-slate-200" title="{{ $log->newvalues }}">{{ $log->newvalues }}</td>
                            <td class="py-3 text-[11px] text-slate-500">{{ $log->performedat }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-4 text-center text-slate-500">No database audit records available. Trigger-logged events will show up here.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Data passed from controller to JS variables
    const revenueData = @json($revenueTrend);
    const locationData = @json($hotLocations);

    // Revenue Chart Configuration
    const revCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revCtx, {
        type: 'line',
        data: {
            labels: revenueData.map(d => d.month),
            datasets: [{
                label: 'Gross Monthly Revenue (৳)',
                data: revenueData.map(d => d.total_revenue),
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.05)',
                borderWidth: 3,
                tension: 0.35,
                fill: true,
                pointBackgroundColor: '#60a5fa'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    grid: { color: 'rgba(255, 255, 255, 0.05)' },
                    ticks: { color: '#94a3b8', font: { family: 'Plus Jakarta Sans' } }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: '#94a3b8', font: { family: 'Plus Jakarta Sans' } }
                }
            }
        }
    });

    // Location Chart Configuration
    const locCtx = document.getElementById('locationChart').getContext('2d');
    new Chart(locCtx, {
        type: 'bar',
        data: {
            labels: locationData.map(d => `${d.areaname} (${d.city})`),
            datasets: [
                {
                    label: 'Properties Listed',
                    data: locationData.map(d => d.total_listings),
                    backgroundColor: '#818cf8',
                    borderRadius: 6
                },
                {
                    label: 'Bookings Received',
                    data: locationData.map(d => d.total_bookings_made),
                    backgroundColor: '#34d399',
                    borderRadius: 6
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: { color: '#e2e8f0', font: { family: 'Plus Jakarta Sans', size: 11 } }
                }
            },
            scales: {
                y: {
                    grid: { color: 'rgba(255, 255, 255, 0.05)' },
                    ticks: { color: '#94a3b8', font: { family: 'Plus Jakarta Sans' } }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: '#94a3b8', font: { family: 'Plus Jakarta Sans' } }
                }
            }
        }
    });
</script>
@endsection
