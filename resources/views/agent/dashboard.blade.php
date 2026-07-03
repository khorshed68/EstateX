@extends('layouts.agent')

@section('page_title', 'Dashboard Overview')

@section('content')
    <!-- Metrics Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Assigned Listings -->
        <div class="glass-panel p-6 rounded-2xl flex items-center justify-between border-l-4 border-purple-500">
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Assigned Properties</span>
                <span class="text-3xl font-black text-white mt-1 block">{{ $totalProperties }}</span>
                <span class="text-[10px] text-slate-500 mt-2 block">Direct representation delegated by owners</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-purple-500/10 flex items-center justify-center text-purple-400">
                <i class="fa-solid fa-house-user text-xl"></i>
            </div>
        </div>

        <!-- Leads / Bookings -->
        <div class="glass-panel p-6 rounded-2xl flex items-center justify-between border-l-4 border-indigo-500">
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Total Bookings</span>
                <span class="text-3xl font-black text-white mt-1 block">{{ $totalBookings }}</span>
                <span class="text-[10px] text-slate-500 mt-2 block">Guided visits & reservations requested</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-500/10 flex items-center justify-center text-indigo-400">
                <i class="fa-solid fa-calendar-check text-xl"></i>
            </div>
        </div>

        <!-- Rating -->
        <div class="glass-panel p-6 rounded-2xl flex items-center justify-between border-l-4 border-fuchsia-500">
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Average Rating</span>
                <div class="flex items-baseline gap-2 mt-1">
                    <span class="text-3xl font-black text-white">{{ $averageRating }}</span>
                    <span class="text-xs text-fuchsia-400"><i class="fa-solid fa-star"></i></span>
                </div>
                <span class="text-[10px] text-slate-500 mt-2 block">Based on feedback from client reviews</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-fuchsia-500/10 flex items-center justify-center text-fuchsia-400">
                <i class="fa-solid fa-star-half-stroke text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Recent Bookings Table -->
    <div class="glass-panel rounded-2xl overflow-hidden">
        <div class="p-6 border-b border-slate-900 flex justify-between items-center">
            <div>
                <h3 class="font-outfit font-bold text-lg text-slate-200">Recent Visits & Reservations</h3>
                <p class="text-xs text-slate-500 mt-0.5">Visits requested on properties delegated to you</p>
            </div>
            <a href="{{ route('agent.bookings') }}" class="text-xs text-purple-400 hover:text-purple-300 font-bold transition duration-150">
                View All Bookings <i class="fa-solid fa-arrow-right ml-1"></i>
            </a>
        </div>

        @if(empty($recentBookings))
            <div class="p-12 text-center">
                <div class="w-16 h-16 rounded-full bg-slate-900 flex items-center justify-center text-slate-600 mx-auto mb-4">
                    <i class="fa-solid fa-calendar-xmark text-2xl"></i>
                </div>
                <h4 class="text-slate-400 font-semibold text-sm">No recent bookings found</h4>
                <p class="text-xs text-slate-600 mt-1">Once clients schedule a visit, it will appear here.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-900/40 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                            <th class="py-4 px-6">Property / Price</th>
                            <th class="py-4 px-6">Client Info</th>
                            <th class="py-4 px-6">Type</th>
                            <th class="py-4 px-6">Status</th>
                            <th class="py-4 px-6">Scheduled At</th>
                            <th class="py-4 px-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-900 text-xs text-slate-300">
                        @foreach($recentBookings as $booking)
                            <tr class="hover:bg-slate-950/40 transition duration-150">
                                <td class="py-4 px-6">
                                    <span class="font-semibold text-slate-200 block">{{ $booking->title }}</span>
                                    <span class="text-[10px] text-purple-400 font-medium block mt-0.5">৳{{ number_format($booking->property_price) }}</span>
                                    <span class="text-[10px] text-slate-500 block mt-0.5">{{ $booking->areaname }}, {{ $booking->city }}</span>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="font-medium text-slate-200 block">{{ $booking->buyer_name }}</span>
                                    <span class="text-[10px] text-slate-500 block mt-0.5">{{ $booking->buyer_email }}</span>
                                    <span class="text-[10px] text-slate-500 block">{{ $booking->buyer_phone }}</span>
                                </td>
                                <td class="py-4 px-6 capitalize">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $booking->bookingtype === 'reservation' ? 'bg-indigo-500/10 text-indigo-400 border border-indigo-500/20' : 'bg-pink-500/10 text-pink-400 border border-pink-500/20' }}">
                                        {{ $booking->bookingtype }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 capitalize">
                                    @if($booking->status === 'completed')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-green-500/10 text-green-400 border border-green-500/20">
                                            Completed
                                        </span>
                                    @elseif($booking->status === 'approved')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-500/10 text-blue-400 border border-blue-500/20">
                                            Approved
                                        </span>
                                    @elseif($booking->status === 'rejected')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-red-500/10 text-red-400 border border-red-500/20">
                                            Rejected
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20 animate-pulse">
                                            Pending
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-6">
                                    <span class="text-[11px] font-medium text-slate-400">{{ date('d M Y, h:i A', strtotime($booking->createdat)) }}</span>
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <div class="flex justify-end gap-2">
                                        @if($booking->status === 'pending')
                                            <form action="{{ route('agent.bookings.approve', $booking->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="px-2.5 py-1 bg-green-500/10 hover:bg-green-500/20 border border-green-500/30 hover:border-green-500/50 rounded-lg text-green-400 hover:text-green-300 font-bold transition duration-150">
                                                    Approve
                                                </button>
                                            </form>
                                            <form action="{{ route('agent.bookings.reject', $booking->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="px-2.5 py-1 bg-red-500/10 hover:bg-red-500/20 border border-red-500/30 hover:border-red-500/50 rounded-lg text-red-400 hover:text-red-300 font-bold transition duration-150">
                                                    Reject
                                                </button>
                                            </form>
                                        @elseif($booking->status === 'approved')
                                            <form action="{{ route('agent.bookings.complete', $booking->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="px-2.5 py-1 bg-blue-500/10 hover:bg-blue-500/20 border border-blue-500/30 hover:border-blue-500/50 rounded-lg text-blue-400 hover:text-blue-300 font-bold transition duration-150">
                                                    Complete
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-[10px] text-slate-500 font-semibold italic">No actions</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
