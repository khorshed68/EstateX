@extends('layouts.buyer')

@section('page_title', 'My Bookings & Reservations')

@section('content')
<div class="space-y-6">
    <div>
        <h3 class="text-lg font-bold text-slate-100 font-outfit">My Booking Portfolio</h3>
        <p class="text-xs text-slate-400 mt-1">Review status updates for your scheduled property visits and purchase reservations.</p>
    </div>

    <!-- Bookings Table/Grid -->
    <div class="glass-panel rounded-3xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="text-slate-500 border-b border-slate-800 bg-slate-900/30">
                        <th class="p-4 font-semibold text-center w-16">ID</th>
                        <th class="p-4 font-semibold">Service Type</th>
                        <th class="p-4 font-semibold">Property Listing Details</th>
                        <th class="p-4 font-semibold">Schedule / Timing</th>
                        <th class="p-4 font-semibold">Payment / Reference</th>
                        <th class="p-4 font-semibold">Booking Status</th>
                        <th class="p-4 font-semibold text-center">Feedback / Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/40 text-slate-300">
                    @forelse($bookings as $book)
                        <tr class="hover:bg-slate-850/5 align-top">
                            <td class="p-4 text-center font-bold text-slate-500">#{{ $book->id }}</td>
                            <td class="p-4">
                                @if($book->bookingtype === 'visit')
                                    <span class="px-2 py-0.5 rounded font-bold text-[9px] uppercase border bg-blue-500/10 text-blue-400 border-blue-500/20">
                                        Site Visit
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded font-bold text-[9px] uppercase border bg-purple-500/10 text-purple-400 border-purple-500/20">
                                        Reservation
                                    </span>
                                @endif
                            </td>
                            <td class="p-4">
                                <a href="{{ route('buyer.properties.show', $book->propertyid) }}" class="font-semibold text-slate-200 text-sm hover:underline line-clamp-1">
                                    {{ $book->title }}
                                </a>
                                <div class="text-[10px] text-slate-500 mt-1 flex items-center gap-1.5">
                                    <i class="fa-solid fa-location-dot text-[9px] text-emerald-500"></i>
                                    <span>{{ $book->areaname }}, {{ $book->city }}</span>
                                </div>
                            </td>
                            <td class="p-4">
                                @if($book->bookingtype === 'visit')
                                    <span class="block text-slate-300 font-medium">
                                        {{ $book->visitdate ? date('d M Y, h:i A', strtotime($book->visitdate)) : 'N/A' }}
                                    </span>
                                    <span class="text-[10px] text-slate-500 block mt-0.5">Tour Schedule</span>
                                @else
                                    <span class="block text-slate-300 font-medium">
                                        {{ date('d M Y', strtotime($book->startdate)) }} - {{ date('d M Y', strtotime($book->enddate)) }}
                                    </span>
                                    <span class="text-[10px] text-slate-500 block mt-0.5">Hold Duration</span>
                                @endif
                            </td>
                            <td class="p-4">
                                @if($book->bookingtype === 'reservation' && $book->referenceno)
                                    <span class="block font-bold text-slate-300 font-mono text-[11px]">{{ $book->referenceno }}</span>
                                    <span class="text-[10px] text-emerald-400 block mt-0.5">
                                        Paid: ৳{{ number_format($book->payment_amount) }} ({{ ucfirst($book->paymentmethod) }})
                                    </span>
                                @else
                                    <span class="text-slate-655 text-[10px] block">No payment required</span>
                                @endif
                            </td>
                            <td class="p-4">
                                <span class="flex items-center gap-1.5 font-semibold 
                                    @if($book->status === 'completed') text-green-400 
                                    @elseif($book->status === 'pending') text-amber-400 
                                    @else text-red-400 @endif">
                                    <span class="w-1.5 h-1.5 rounded-full 
                                        @if($book->status === 'completed') bg-green-500 
                                        @elseif($book->status === 'pending') bg-amber-500 
                                        @else bg-red-500 @endif animate-pulse"></span>
                                    {{ ucfirst($book->status) }}
                                </span>
                            </td>
                            <td class="p-4 text-center">
                                @if($book->status === 'completed')
                                    <!-- Review and Feedback Toggles -->
                                    <div class="flex flex-col gap-2 justify-center items-center">
                                        <button onclick="toggleReviewForm('property-form-{{ $book->id }}')" class="px-2.5 py-1 bg-slate-950 hover:bg-slate-900 border border-slate-800 rounded-lg text-[10px] font-bold text-slate-300 hover:text-white transition duration-200">
                                            <i class="fa-solid fa-house-circle-exclamation mr-1 text-emerald-500"></i> Review Property
                                        </button>
                                        @if($book->agent_name)
                                            <button onclick="toggleReviewForm('agent-form-{{ $book->id }}')" class="px-2.5 py-1 bg-slate-950 hover:bg-slate-900 border border-slate-800 rounded-lg text-[10px] font-bold text-slate-300 hover:text-white transition duration-200">
                                                <i class="fa-solid fa-user-pen mr-1 text-emerald-500"></i> Review Agent
                                            </button>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-slate-600 text-[10px]">Awaiting visit completion</span>
                                @endif
                            </td>
                        </tr>

                        <!-- Property Review Inline Drawer -->
                        <tr id="property-form-{{ $book->id }}" class="hidden bg-slate-950/40">
                            <td colspan="7" class="p-6 border-b border-slate-900">
                                <div class="max-w-xl mx-auto bg-slate-900/60 border border-slate-800 rounded-2xl p-5 space-y-4">
                                    <div class="flex justify-between items-center">
                                        <h5 class="text-xs font-bold text-slate-200 uppercase tracking-wider">Submit Review for: {{ $book->title }}</h5>
                                        <button onclick="toggleReviewForm('property-form-{{ $book->id }}')" class="text-slate-500 hover:text-slate-300"><i class="fa-solid fa-times"></i></button>
                                    </div>
                                    <form action="{{ route('buyer.reviews.property') }}" method="POST" class="space-y-4">
                                        @csrf
                                        <input type="hidden" name="property_id" value="{{ $book->propertyid }}">
                                        <div>
                                            <label class="block text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-2">Overall Score</label>
                                            <select name="rating" class="bg-slate-950 border border-slate-800 rounded-xl py-2 px-3 text-xs text-slate-300 focus:outline-none focus:border-emerald-500">
                                                <option value="5">⭐⭐⭐⭐⭐ (5/5 - Perfect)</option>
                                                <option value="4">⭐⭐⭐⭐ (4/5 - Excellent)</option>
                                                <option value="3">⭐⭐⭐ (3/5 - Average)</option>
                                                <option value="2">⭐⭐ (2/5 - Fair)</option>
                                                <option value="1">⭐ (1/5 - Poor)</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-2">Comments</label>
                                            <textarea name="comments" rows="3" required placeholder="Describe your experience with the property, facilities, and layout..."
                                                      class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2 px-3 text-xs text-slate-200 placeholder-slate-700 focus:outline-none focus:border-emerald-500"></textarea>
                                        </div>
                                        <div class="flex justify-end">
                                            <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 rounded-xl text-xs font-bold text-white transition duration-200">
                                                Submit Review
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <!-- Agent Review Inline Drawer -->
                        @if($book->agent_name)
                            <tr id="agent-form-{{ $book->id }}" class="hidden bg-slate-950/40">
                                <td colspan="7" class="p-6 border-b border-slate-900">
                                    <div class="max-w-xl mx-auto bg-slate-900/60 border border-slate-800 rounded-2xl p-5 space-y-4">
                                        <div class="flex justify-between items-center">
                                            <h5 class="text-xs font-bold text-slate-200 uppercase tracking-wider">Rate Representing Agent: {{ $book->agent_name }}</h5>
                                            <button onclick="toggleReviewForm('agent-form-{{ $book->id }}')" class="text-slate-500 hover:text-slate-300"><i class="fa-solid fa-times"></i></button>
                                        </div>
                                        <form action="{{ route('buyer.reviews.agent') }}" method="POST" class="space-y-4">
                                            @csrf
                                            <input type="hidden" name="agent_id" value="{{ $book->agentid }}">
                                            <div>
                                                <label class="block text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-2">Agent Professionalism</label>
                                                <select name="rating" class="bg-slate-950 border border-slate-800 rounded-xl py-2 px-3 text-xs text-slate-300 focus:outline-none focus:border-emerald-500">
                                                    <option value="5">⭐⭐⭐⭐⭐ (5/5 - Highly Professional)</option>
                                                    <option value="4">⭐⭐⭐⭐ (4/5 - Very Helpful)</option>
                                                    <option value="3">⭐⭐⭐ (3/5 - Average)</option>
                                                    <option value="2">⭐⭐ (2/5 - Fair)</option>
                                                    <option value="1">⭐ (1/5 - Unhelpful)</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-2">Review Notes</label>
                                                <textarea name="comments" rows="3" required placeholder="Describe your experience during the site visit and negotiation..."
                                                          class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2 px-3 text-xs text-slate-200 placeholder-slate-700 focus:outline-none focus:border-emerald-500"></textarea>
                                            </div>
                                            <div class="flex justify-end">
                                                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 rounded-xl text-xs font-bold text-white transition duration-200">
                                                    Submit Agent Rating
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endif

                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-600 text-xs">
                                <i class="fa-solid fa-calendar-xmark text-3xl mb-3 block"></i>
                                You have not booked any site visits or reservations yet.
                            </td>
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
    function toggleReviewForm(elementId) {
        const formRow = document.getElementById(elementId);
        if (formRow.classList.contains('hidden')) {
            formRow.classList.remove('hidden');
        } else {
            formRow.classList.add('hidden');
        }
    }
</script>
@endsection
