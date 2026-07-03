@extends('layouts.admin')

@section('page_title', 'Payment Transaction Ledger')

@section('content')
<div class="space-y-6">

    <!-- Search & Filter Card -->
    <div class="glass-panel p-6 rounded-2xl flex flex-col md:flex-row items-center justify-between gap-4">
        <div>
            <h3 class="text-lg font-bold text-slate-100">Financial Ledger</h3>
            <p class="text-xs text-slate-400 mt-1">Review all reservation deposits and holding fee transactions recorded in the system.</p>
        </div>
        <form action="{{ route('admin.transactions') }}" method="GET" class="w-full md:w-auto flex gap-2">
            <div class="relative w-full md:w-80">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" name="search" value="{{ $search }}" placeholder="Search by property, buyer, or reference..." 
                       class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2.5 pl-10 pr-4 text-xs text-slate-200 placeholder-slate-700 focus:outline-none focus:border-blue-500 transition duration-200">
            </div>
            <button type="submit" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-500 rounded-xl text-xs font-bold text-white transition duration-200 shrink-0">
                Search
            </button>
            @if($search)
                <a href="{{ route('admin.transactions') }}" class="px-4 py-2.5 border border-slate-850 hover:bg-slate-800 rounded-xl text-xs font-bold text-slate-400 transition duration-200 shrink-0 flex items-center justify-center">
                    Clear
                </a>
            @endif
        </form>
    </div>

    <!-- Transactions Table -->
    <div class="glass-panel rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="text-slate-500 border-b border-slate-800 bg-slate-900/30">
                        <th class="p-4 font-semibold text-center w-16">ID</th>
                        <th class="p-4 font-semibold">Reference Code</th>
                        <th class="p-4 font-semibold">Property Listing</th>
                        <th class="p-4 font-semibold">Buyer Name</th>
                        <th class="p-4 font-semibold">Ledger Entry</th>
                        <th class="p-4 font-semibold">Amount</th>
                        <th class="p-4 font-semibold">Method</th>
                        <th class="p-4 font-semibold">Status</th>
                        <th class="p-4 font-semibold">Logged At</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/40 text-slate-300">
                    @forelse($transactions as $tx)
                        <tr class="hover:bg-slate-850/5">
                            <td class="p-4 text-center font-bold text-slate-500">#{{ $tx->id }}</td>
                            <td class="p-4 font-mono text-slate-400 font-bold uppercase">{{ $tx->referenceno }}</td>
                            <td class="p-4 font-semibold text-slate-200">{{ $tx->title }}</td>
                            <td class="p-4 text-slate-300">{{ $tx->buyer_name }}</td>
                            <td class="p-4 capitalize">
                                <span class="px-2 py-0.5 rounded font-bold text-[9px] bg-slate-800 text-slate-400 border border-slate-700">
                                    {{ str_replace('_', ' ', $tx->transactiontype) }}
                                </span>
                            </td>
                            <td class="p-4 text-blue-400 font-bold">৳{{ number_format($tx->amount) }}</td>
                            <td class="p-4 capitalize">{{ $tx->paymentmethod }}</td>
                            <td class="p-4">
                                @if($tx->status === 'completed')
                                    <span class="flex items-center gap-1.5 font-semibold text-green-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-400"></span>
                                        Completed
                                    </span>
                                @elseif($tx->status === 'failed')
                                    <span class="flex items-center gap-1.5 font-semibold text-red-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span>
                                        Failed
                                    </span>
                                @else
                                    <span class="flex items-center gap-1.5 font-semibold text-amber-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                                        {{ ucfirst($tx->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="p-4 text-slate-500 font-medium">{{ date('d M Y, h:i A', strtotime($tx->transactiondate)) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="p-6 text-center text-slate-500">No payment transaction logs found in system database.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
