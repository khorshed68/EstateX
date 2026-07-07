@extends('layouts.agent')

@section('page_title', 'Sales & Commission Analytics')

@section('content')
<div class="space-y-6">

    <!-- Overview Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        
        <!-- Total Sales Volume -->
        <div class="glass-panel p-6 rounded-3xl relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-purple-500/10 rounded-full blur-xl group-hover:bg-purple-500/20 transition duration-300"></div>
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs text-slate-400 font-semibold uppercase tracking-wider">Total Sales Volume</span>
                <span class="w-8 h-8 rounded-xl bg-purple-500/10 border border-purple-500/20 flex items-center justify-center text-purple-400 text-xs">
                    <i class="fa-solid fa-handshake"></i>
                </span>
            </div>
            <h3 class="text-2xl font-bold font-outfit text-white">${{ number_format($totalSales, 2) }}</h3>
            <p class="text-[10px] text-slate-500 mt-2">Volume from closed reservations</p>
        </div>

        <!-- Earned Commission -->
        <div class="glass-panel p-6 rounded-3xl relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-indigo-500/10 rounded-full blur-xl group-hover:bg-indigo-500/20 transition duration-300"></div>
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs text-slate-400 font-semibold uppercase tracking-wider">Earned Commission (10%)</span>
                <span class="w-8 h-8 rounded-xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400 text-xs">
                    <i class="fa-solid fa-coins"></i>
                </span>
            </div>
            <h3 class="text-2xl font-bold font-outfit text-purple-400">${{ number_format($estimatedCommission, 2) }}</h3>
            <p class="text-[10px] text-slate-500 mt-2">10% standard representation rate</p>
        </div>

        <!-- Active Representations -->
        <div class="glass-panel p-6 rounded-3xl relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-blue-500/10 rounded-full blur-xl group-hover:bg-blue-500/20 transition duration-300"></div>
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs text-slate-400 font-semibold uppercase tracking-wider">Represented Listings</span>
                <span class="w-8 h-8 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400 text-xs">
                    <i class="fa-solid fa-house"></i>
                </span>
            </div>
            <h3 class="text-2xl font-bold font-outfit text-white">{{ $activeListings }}</h3>
            <p class="text-[10px] text-slate-500 mt-2">Properties assigned by owners</p>
        </div>

        <!-- Completed Deals -->
        <div class="glass-panel p-6 rounded-3xl relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-emerald-500/10 rounded-full blur-xl group-hover:bg-emerald-500/20 transition duration-300"></div>
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs text-slate-400 font-semibold uppercase tracking-wider">Completed Deals</span>
                <span class="w-8 h-8 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 text-xs">
                    <i class="fa-solid fa-circle-check"></i>
                </span>
            </div>
            <h3 class="text-2xl font-bold font-outfit text-white">{{ $completedDeals }}</h3>
            <p class="text-[10px] text-slate-500 mt-2">Site visits & reservations finalized</p>
        </div>

    </div>

    <!-- Charts & Pipeline Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Sales & Commission Chart -->
        <div class="glass-panel p-6 rounded-3xl lg:col-span-2">
            <h3 class="font-outfit font-bold text-base text-slate-200 mb-4">Monthly Commissions Trend</h3>
            <div class="relative h-72">
                <canvas id="commissionChart"></canvas>
            </div>
        </div>

        <!-- Sales Pipeline Funnel -->
        <div class="glass-panel p-6 rounded-3xl flex flex-col justify-between">
            <div>
                <h3 class="font-outfit font-bold text-base text-slate-200 mb-6">Active Sales Pipeline</h3>
                <div class="space-y-6">
                    
                    <!-- Leads -->
                    <div class="space-y-2">
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-slate-400 font-medium flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                                Booking Leads (Pending)
                            </span>
                            <span class="font-bold text-white">{{ $pipelineLeads }}</span>
                        </div>
                        <div class="w-full h-2.5 bg-slate-900 rounded-full overflow-hidden">
                            <div class="h-full bg-blue-500 rounded-full" style="width: {{ $pipelineLeads > 0 ? min(100, ($pipelineLeads / max(1, $pipelineLeads + $pipelineTours + $pipelineClosed)) * 100) : 0 }}%"></div>
                        </div>
                    </div>

                    <!-- Tours -->
                    <div class="space-y-2">
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-slate-400 font-medium flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-purple-500"></span>
                                Site Visits Scheduled (Approved)
                            </span>
                            <span class="font-bold text-white">{{ $pipelineTours }}</span>
                        </div>
                        <div class="w-full h-2.5 bg-slate-900 rounded-full overflow-hidden">
                            <div class="h-full bg-purple-500 rounded-full" style="width: {{ $pipelineTours > 0 ? min(100, ($pipelineTours / max(1, $pipelineLeads + $pipelineTours + $pipelineClosed)) * 100) : 0 }}%"></div>
                        </div>
                    </div>

                    <!-- Closed -->
                    <div class="space-y-2">
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-slate-400 font-medium flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                Reserved / Closed (Completed)
                            </span>
                            <span class="font-bold text-white">{{ $pipelineClosed }}</span>
                        </div>
                        <div class="w-full h-2.5 bg-slate-900 rounded-full overflow-hidden">
                            <div class="h-full bg-emerald-500 rounded-full" style="width: {{ $pipelineClosed > 0 ? min(100, ($pipelineClosed / max(1, $pipelineLeads + $pipelineTours + $pipelineClosed)) * 100) : 0 }}%"></div>
                        </div>
                    </div>

                </div>
            </div>
            
            <div class="pt-6 border-t border-slate-900 mt-6 text-center">
                <span class="text-[10px] text-slate-500 font-medium">Conversion rate matches user progression logs</span>
            </div>
        </div>

    </div>

</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('commissionChart').getContext('2d');
        
        // Prepare monthly data passed from controller
        const monthlyData = @json($monthlyTrends);
        
        let labels = [];
        let commissions = [];
        
        if (monthlyData && monthlyData.length > 0) {
            labels = monthlyData.map(item => item.month_str);
            commissions = monthlyData.map(item => item.commission);
        } else {
            // Placeholder/demo states if no real data available yet
            labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
            commissions = [0, 0, 0, 0, 0, 0];
        }

        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Commission Earned ($)',
                    data: commissions,
                    borderColor: '#a855f7',
                    backgroundColor: 'rgba(168, 85, 247, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#a855f7',
                    pointBorderColor: '#fff',
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        grid: {
                            color: 'rgba(255, 255, 255, 0.03)'
                        },
                        ticks: {
                            color: '#94a3b8',
                            font: {
                                size: 10
                            }
                        }
                    },
                    y: {
                        grid: {
                            color: 'rgba(255, 255, 255, 0.03)'
                        },
                        ticks: {
                            color: '#94a3b8',
                            font: {
                                size: 10
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
