@extends('layouts.agent')

@section('page_title', 'My Active Clients CRM')

@section('content')
<div class="space-y-6">

    <!-- Header Description Card -->
    <div class="glass-panel p-6 rounded-3xl flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h3 class="font-outfit font-bold text-base text-slate-200">Client Relationship Management</h3>
            <p class="text-xs text-slate-400 mt-1">Track active leads, schedule site tours, and coordinate bookings for active buyers on your listings.</p>
        </div>
        <div class="flex items-center gap-2 px-3 py-1.5 bg-purple-500/10 border border-purple-500/20 rounded-xl text-xs font-bold text-purple-400">
            <i class="fa-solid fa-users text-sm"></i>
            <span>{{ count($clients) }} Active Clients</span>
        </div>
    </div>

    <!-- Clients CRM Table -->
    <div class="glass-panel rounded-3xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-900 bg-slate-950/40 text-slate-400 text-[10px] font-semibold uppercase tracking-wider">
                        <th class="py-4 px-6">Client Name</th>
                        <th class="py-4 px-6">Contact details</th>
                        <th class="py-4 px-6">Interested Property</th>
                        <th class="py-4 px-6 text-center">Inquiry Type</th>
                        <th class="py-4 px-6">Last Touchpoint</th>
                        <th class="py-4 px-6 text-center">Status / Stage</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-900 text-xs text-slate-200">
                    @forelse($clients as $client)
                        <tr class="hover:bg-slate-950/20 transition duration-150">
                            <!-- Client Avatar & Name -->
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-purple-500 to-indigo-500 flex items-center justify-center font-bold text-white shadow-sm uppercase">
                                        {{ substr($client->client_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <span class="font-bold text-slate-100 block">{{ $client->client_name }}</span>
                                        <span class="text-[10px] text-slate-500 font-medium">Buyer Account</span>
                                    </div>
                                </div>
                            </td>
                            <!-- Contacts -->
                            <td class="py-4 px-6 space-y-1">
                                <div class="flex items-center gap-1.5 text-slate-300">
                                    <i class="fa-solid fa-envelope text-[10px] text-slate-500"></i>
                                    <span>{{ $client->client_email }}</span>
                                </div>
                                <div class="flex items-center gap-1.5 text-slate-300">
                                    <i class="fa-solid fa-phone text-[10px] text-slate-500"></i>
                                    <span>{{ $client->client_phone ?? 'No Phone' }}</span>
                                </div>
                            </td>
                            <!-- Property -->
                            <td class="py-4 px-6">
                                <span class="font-semibold text-slate-100 line-clamp-1 max-w-xs">{{ $client->property_title }}</span>
                            </td>
                            <!-- Inquiry Type -->
                            <td class="py-4 px-6 text-center">
                                @if($client->bookingtype === 'reservation')
                                    <span class="px-2.5 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[9px] font-bold uppercase tracking-wider">
                                        Reservation
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-400 text-[9px] font-bold uppercase tracking-wider">
                                        Site Visit
                                    </span>
                                @endif
                            </td>
                            <!-- Last Touchpoint -->
                            <td class="py-4 px-6 text-slate-400">
                                {{ date('Y-m-d H:i A', strtotime($client->createdat)) }}
                            </td>
                            <!-- Status Stage -->
                            <td class="py-4 px-6 text-center">
                                @if($client->status === 'completed')
                                    <span class="px-2.5 py-1 rounded-full bg-green-500/20 border border-green-500/30 text-green-400 text-[9px] font-bold uppercase tracking-wider">
                                        Closed
                                    </span>
                                @elseif($client->status === 'approved')
                                    <span class="px-2.5 py-1 rounded-full bg-purple-500/20 border border-purple-500/30 text-purple-400 text-[9px] font-bold uppercase tracking-wider">
                                        Scheduled
                                    </span>
                                @elseif($client->status === 'pending')
                                    <span class="px-2.5 py-1 rounded-full bg-amber-500/20 border border-amber-500/30 text-amber-400 text-[9px] font-bold uppercase tracking-wider">
                                        Lead / Pending
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full bg-red-500/20 border border-red-500/30 text-red-400 text-[9px] font-bold uppercase tracking-wider">
                                        Cancelled
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-500">
                                <div class="flex flex-col items-center justify-center gap-3">
                                    <i class="fa-solid fa-users-slash text-3xl text-slate-600"></i>
                                    <span class="text-xs">No active clients assigned or booked yet.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
