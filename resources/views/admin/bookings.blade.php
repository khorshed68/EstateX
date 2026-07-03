@extends('layouts.admin')

@section('page_title', 'System Bookings & Visits')

@section('content')
<div class="space-y-6">

    <!-- Search & Filter Card -->
    <div class="glass-panel p-6 rounded-2xl flex flex-col md:flex-row items-center justify-between gap-4">
        <div>
            <h3 class="text-lg font-bold text-slate-100">Global Bookings Directory</h3>
            <p class="text-xs text-slate-400 mt-1">Review scheduled property site visits and reservation requests across the platform.</p>
        </div>
        <form action="{{ route('admin.bookings') }}" method="GET" class="w-full md:w-auto flex gap-2">
            <div class="relative w-full md:w-80">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" name="search" value="{{ $search }}" placeholder="Search by buyer name or property..." 
                       class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2.5 pl-10 pr-4 text-xs text-slate-200 placeholder-slate-700 focus:outline-none focus:border-blue-500 transition duration-200">
            </div>
            <button type="submit" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-500 rounded-xl text-xs font-bold text-white transition duration-200 shrink-0">
                Search
            </button>
            @if($search)
                <a href="{{ route('admin.bookings') }}" class="px-4 py-2.5 border border-slate-850 hover:bg-slate-800 rounded-xl text-xs font-bold text-slate-400 transition duration-200 shrink-0 flex items-center justify-center">
                    Clear
                </a>
            @endif
        </form>
    </div>

    <!-- Bookings Table -->
    <div class="glass-panel rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="text-slate-500 border-b border-slate-800 bg-slate-900/30">
                        <th class="p-4 font-semibold text-center w-16">ID</th>
                        <th class="p-4 font-semibold">Property Details</th>
                        <th class="p-4 font-semibold">Client / Buyer</th>
                        <th class="p-4 font-semibold">Assigned Agent</th>
                        <th class="p-4 font-semibold">Type</th>
                        <th class="p-4 font-semibold">Status</th>
                        <th class="p-4 font-semibold">Deposit Payment</th>
                        <th class="p-4 font-semibold text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/40 text-slate-300">
                    @forelse($bookings as $booking)
                        <tr class="hover:bg-slate-850/5">
                            <td class="p-4 text-center font-bold text-slate-500">#{{ $booking->id }}</td>
                            <td class="p-4">
                                <div class="font-semibold text-slate-200 text-sm">{{ $booking->title }}</div>
                                <div class="text-[10px] text-blue-400 mt-0.5 font-bold">৳{{ number_format($booking->property_price) }}</div>
                                <div class="text-[10px] text-slate-500 mt-0.5"><i class="fa-solid fa-location-dot mr-1"></i>{{ $booking->areaname }}, {{ $booking->city }}</div>
                            </td>
                            <td class="p-4">
                                <div class="font-semibold text-slate-200">{{ $booking->buyer_name }}</div>
                                <div class="text-[10px] text-slate-500 mt-0.5">{{ $booking->buyer_email }}</div>
                                <div class="text-[10px] text-slate-500">{{ $booking->buyer_phone }}</div>
                            </td>
                            <td class="p-4">
                                <span class="font-medium text-slate-300">{{ $booking->agent_name ?? 'Unassigned' }}</span>
                            </td>
                            <td class="p-4">
                                <span class="px-2 py-0.5 rounded font-bold text-[9px] uppercase border
                                    @if($booking->bookingtype === 'reservation') bg-indigo-500/10 text-indigo-400 border-indigo-500/20
                                    @else bg-pink-500/10 text-pink-400 border-pink-500/20 @endif">
                                    {{ $booking->bookingtype }}
                                </span>
                            </td>
                            <td class="p-4 capitalize">
                                @if($booking->status === 'completed')
                                    <span class="px-2 py-0.5 rounded font-bold text-[10px] bg-green-500/10 text-green-400 border border-green-500/20">Completed</span>
                                @elseif($booking->status === 'approved')
                                    <span class="px-2 py-0.5 rounded font-bold text-[10px] bg-blue-500/10 text-blue-400 border border-blue-500/20">Approved</span>
                                @elseif($booking->status === 'rejected')
                                    <span class="px-2 py-0.5 rounded font-bold text-[10px] bg-red-500/10 text-red-400 border border-red-500/20">Rejected</span>
                                @else
                                    <span class="px-2 py-0.5 rounded font-bold text-[10px] bg-amber-500/10 text-amber-400 border border-amber-500/20">Pending</span>
                                @endif
                            </td>
                            <td class="p-4">
                                @if($booking->payment_amount)
                                    <div class="font-semibold text-slate-200">৳{{ number_format($booking->payment_amount) }}</div>
                                    <div class="text-[9px] text-slate-500 capitalize">{{ $booking->paymentmethod }} ({{ $booking->payment_status }})</div>
                                @else
                                    <span class="text-[10px] text-slate-500 italic">No deposit</span>
                                @endif
                            </td>
                            <td class="p-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <!-- Status Update Selection Form -->
                                    <form action="{{ route('admin.bookings.action', $booking->id) }}" method="POST" class="flex gap-1.5 items-center">
                                        @csrf
                                        <select name="status" onchange="this.form.submit()" 
                                                class="bg-slate-950 border border-slate-800 rounded-lg py-1 px-2 text-[10px] text-slate-300 focus:outline-none focus:border-blue-500">
                                            <option value="pending" {{ $booking->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="approved" {{ $booking->status === 'approved' ? 'selected' : '' }}>Approve</option>
                                            <option value="rejected" {{ $booking->status === 'rejected' ? 'selected' : '' }}>Reject</option>
                                            <option value="completed" {{ $booking->status === 'completed' ? 'selected' : '' }}>Complete</option>
                                        </select>
                                    </form>

                                    <!-- Delete Button -->
                                    <form action="{{ route('admin.bookings.delete', $booking->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to permanently cancel and delete this booking?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 bg-red-500/10 hover:bg-red-500/20 border border-red-500/20 hover:border-red-500/40 rounded-lg text-red-400 hover:text-red-300 font-bold transition duration-200">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-6 text-center text-slate-500">No bookings scheduled in the system database.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
