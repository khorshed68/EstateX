@extends('layouts.owner')

@section('page_title', 'Visits & Reservations Management')

@section('content')
<div class="space-y-6">
    
    <div>
        <h3 class="font-outfit font-bold text-xl text-slate-200">Scheduled Tours & Bookings</h3>
        <p class="text-xs text-slate-500 mt-1">Review, approve, or finalize booking requests submitted by prospective buyers.</p>
    </div>

    <!-- Bookings Grid/Table -->
    <div class="glass-panel rounded-3xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-800 bg-slate-900/40 text-[10px] font-semibold text-slate-400 uppercase tracking-wider">
                        <th class="p-5">Booking Details</th>
                        <th class="p-5">Client Information</th>
                        <th class="p-5">Booking Type</th>
                        <th class="p-5">Payment Details</th>
                        <th class="p-5">Status</th>
                        <th class="p-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 text-xs">
                    @forelse($bookings as $booking)
                        <tr class="hover:bg-slate-900/25 transition duration-150">
                            <!-- Booking Details -->
                            <td class="p-5">
                                <span class="font-bold text-slate-200 block mb-0.5">{{ $booking->title }}</span>
                                <span class="text-[10px] text-slate-500 block mb-1">
                                    <i class="fa-solid fa-location-dot mr-1"></i>{{ $booking->areaname }}, {{ $booking->city }}
                                </span>
                                @if($booking->visitdate)
                                    <span class="text-[10px] text-amber-500 font-mono block">
                                        <i class="fa-regular fa-clock mr-1"></i>Visit Date: {{ date('M d, Y h:i A', strtotime($booking->visitdate)) }}
                                    </span>
                                @endif
                                @if($booking->notes)
                                    <span class="text-[10px] text-slate-400 italic block mt-1 line-clamp-1" title="{{ $booking->notes }}">
                                        Note: "{{ $booking->notes }}"
                                    </span>
                                @endif
                            </td>

                            <!-- Client Details -->
                            <td class="p-5 text-slate-300">
                                <div class="flex flex-col gap-1">
                                    <span class="font-semibold text-slate-200">{{ $booking->buyer_name }}</span>
                                    <span class="text-[10px] text-slate-500"><i class="fa-solid fa-envelope mr-1 text-[9px]"></i>{{ $booking->buyer_email }}</span>
                                    @if($booking->buyer_phone)
                                        <span class="text-[10px] text-slate-500"><i class="fa-solid fa-phone mr-1 text-[9px]"></i>{{ $booking->buyer_phone }}</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Booking Type -->
                            <td class="p-5">
                                @if($booking->bookingtype === 'reservation')
                                    <span class="px-2 py-0.5 rounded bg-orange-500/10 border border-orange-500/20 text-[10px] font-bold text-orange-400 uppercase tracking-wide">
                                        Unit Reservation
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded bg-amber-500/10 border border-amber-500/20 text-[10px] font-bold text-amber-400 uppercase tracking-wide">
                                        Site Visit
                                    </span>
                                @endif
                                <span class="block text-[10px] text-slate-500 mt-1">Guests: {{ $booking->guests }}</span>
                            </td>

                            <!-- Payment Details -->
                            <td class="p-5 text-slate-300">
                                @if($booking->payment_amount)
                                    <span class="font-outfit font-black text-amber-400">৳{{ number_format($booking->payment_amount) }}</span>
                                    <span class="block text-[9px] text-slate-500 uppercase mt-0.5">
                                        {{ str_replace('_', ' ', $booking->paymentmethod) }} &bull; {{ $booking->payment_status }}
                                    </span>
                                @else
                                    <span class="text-slate-500 italic">No deposit (Free Visit)</span>
                                @endif
                            </td>

                            <!-- Status Badge -->
                            <td class="p-5">
                                @if($booking->status === 'pending')
                                    <span class="px-2 py-1 rounded bg-yellow-500/10 border border-yellow-500/20 text-[9px] font-bold text-yellow-500 uppercase tracking-wide">
                                        Pending Approval
                                    </span>
                                @elseif($booking->status === 'approved')
                                    <span class="px-2 py-1 rounded bg-blue-500/10 border border-blue-500/20 text-[9px] font-bold text-blue-400 uppercase tracking-wide">
                                        Approved / Scheduled
                                    </span>
                                @elseif($booking->status === 'completed')
                                    <span class="px-2 py-1 rounded bg-emerald-500/10 border border-emerald-500/20 text-[9px] font-bold text-emerald-400 uppercase tracking-wide">
                                        Completed
                                    </span>
                                @else
                                    <span class="px-2 py-1 rounded bg-red-500/10 border border-red-500/20 text-[9px] font-bold text-red-400 uppercase tracking-wide">
                                        Rejected
                                    </span>
                                @endif
                            </td>

                            <!-- Action Buttons -->
                            <td class="p-5 text-right">
                                <div class="flex justify-end gap-1.5">
                                    @if($booking->status === 'pending')
                                        <!-- Approve form -->
                                        <form action="{{ route('owner.bookings.approve', $booking->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg font-bold text-[10px] transition duration-200">
                                                Approve
                                            </button>
                                        </form>
                                        <!-- Reject form -->
                                        <form action="{{ route('owner.bookings.reject', $booking->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1.5 bg-red-500/10 border border-red-500/20 text-red-400 hover:bg-red-500/20 rounded-lg font-bold text-[10px] transition duration-200">
                                                Reject
                                            </button>
                                        </form>
                                    @elseif($booking->status === 'approved')
                                        <!-- Complete form -->
                                        <form action="{{ route('owner.bookings.complete', $booking->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 bg-amber-500 hover:bg-amber-400 text-slate-950 rounded-lg font-bold text-[10px] transition duration-200">
                                                Mark Completed
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-slate-600 italic text-[10px]">No actions</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-16 text-center text-slate-500">
                                <i class="fa-solid fa-calendar-xmark text-4xl text-slate-700 mb-3"></i>
                                <h4 class="font-outfit font-bold text-slate-400">No Booking History Found</h4>
                                <p class="text-xs text-slate-600 mt-1">Visits and reservations placed on your properties will be logged here.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
