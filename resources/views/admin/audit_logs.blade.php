@extends('layouts.admin')

@section('page_title', 'System Audit Trail')

@section('content')
<div class="space-y-6">

    <!-- Search & Filter Card -->
    <div class="glass-panel p-6 rounded-2xl flex flex-col md:flex-row items-center justify-between gap-4">
        <div>
            <h3 class="text-lg font-bold text-slate-100">Action Audit Logs</h3>
            <p class="text-xs text-slate-400 mt-1">Review system logs of administrative actions, user changes, and listings moderation events.</p>
        </div>
        <form action="{{ route('admin.audit-logs') }}" method="GET" class="w-full md:w-auto flex gap-2">
            <div class="relative w-full md:w-80">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" name="search" value="{{ $search }}" placeholder="Search by operator, action, or table..." 
                       class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2.5 pl-10 pr-4 text-xs text-slate-200 placeholder-slate-700 focus:outline-none focus:border-blue-500 transition duration-200">
            </div>
            <button type="submit" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-500 rounded-xl text-xs font-bold text-white transition duration-200 shrink-0">
                Search
            </button>
            @if($search)
                <a href="{{ route('admin.audit-logs') }}" class="px-4 py-2.5 border border-slate-850 hover:bg-slate-800 rounded-xl text-xs font-bold text-slate-400 transition duration-200 shrink-0 flex items-center justify-center">
                    Clear
                </a>
            @endif
        </form>
    </div>

    <!-- Audit Logs Table -->
    <div class="glass-panel rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="text-slate-500 border-b border-slate-800 bg-slate-900/30">
                        <th class="p-4 font-semibold text-center w-16">ID</th>
                        <th class="p-4 font-semibold">Operator</th>
                        <th class="p-4 font-semibold">Action Trigger</th>
                        <th class="p-4 font-semibold">Table</th>
                        <th class="p-4 font-semibold">Record ID</th>
                        <th class="p-4 font-semibold">Old Value Details</th>
                        <th class="p-4 font-semibold">New Value Details</th>
                        <th class="p-4 font-semibold">Logged At</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/40 text-slate-300">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-850/5">
                            <td class="p-4 text-center font-bold text-slate-500">#{{ $log->id }}</td>
                            <td class="p-4 font-semibold text-slate-200">{{ $log->admin_name }}</td>
                            <td class="p-4">
                                <span class="px-2 py-0.5 rounded font-bold text-[9px] uppercase border bg-purple-500/10 text-purple-400 border-purple-500/20">
                                    {{ str_replace('_', ' ', $log->actionname) }}
                                </span>
                            </td>
                            <td class="p-4 font-mono text-slate-400 uppercase">{{ $log->tablename }}</td>
                            <td class="p-4 text-slate-300 font-bold">#{{ $log->recordid }}</td>
                            <td class="p-4 text-slate-400 break-all max-w-[200px]">{{ $log->oldvalues ?? 'NULL' }}</td>
                            <td class="p-4 text-slate-200 break-all max-w-[200px]">{{ $log->newvalues ?? 'NULL' }}</td>
                            <td class="p-4 text-slate-500 font-medium">{{ date('d M Y, h:i A', strtotime($log->performedat)) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-6 text-center text-slate-500">No action logs found in system database.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
