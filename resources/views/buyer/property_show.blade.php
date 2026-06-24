@extends('layouts.buyer')

@section('page_title', 'Property Inventory Details')

@section('content')
<div class="space-y-6">
    <!-- Back to Catalog Link -->
    <div>
        <a href="{{ route('buyer.dashboard') }}" class="inline-flex items-center gap-2 text-xs text-slate-400 hover:text-white transition duration-200">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Back to marketplace catalog</span>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Left Side: Property Gallery & Specifications (8 cols) -->
        <div class="lg:col-span-8 space-y-6">
            
            <!-- Showcase Image Gallery -->
            <div class="glass-panel p-4 rounded-3xl space-y-4">
                <div class="relative h-[350px] md:h-[450px] bg-slate-950 rounded-2xl overflow-hidden shadow-inner">
                    @if(count($images) > 0)
                        <!-- Main active image -->
                        <img id="main-gallery-image" src="{{ $images[0]->imagepath }}" alt="{{ $property->title }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex flex-col items-center justify-center text-slate-700">
                            <i class="fa-solid fa-image text-5xl mb-3"></i>
                            <span class="text-xs uppercase tracking-widest font-bold">No images available</span>
                        </div>
                    @endif
                    <div class="absolute top-4 left-4">
                        <span class="px-2 py-1 rounded-lg bg-emerald-500/20 border border-emerald-500/40 text-[9px] font-bold text-emerald-400 uppercase tracking-wider backdrop-blur-sm">
                            {{ $property->typename }}
                        </span>
                    </div>
                </div>

                @if(count($images) > 1)
                    <!-- Gallery Thumbnails -->
                    <div class="flex gap-3 overflow-x-auto pb-2 scrollbar-thin">
                        @foreach($images as $img)
                            <button onclick="document.getElementById('main-gallery-image').src='{{ $img->imagepath }}'" 
                                    class="w-20 h-16 rounded-xl overflow-hidden bg-slate-950 border border-slate-800 hover:border-emerald-500 transition duration-200 shrink-0">
                                <img src="{{ $img->imagepath }}" alt="Thumbnail" class="w-full h-full object-cover">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Details, Specs and Amenities -->
            <div class="glass-panel p-6 rounded-3xl space-y-6">
                <!-- Header Title & Price -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 pb-6 border-b border-slate-900">
                    <div>
                        <h3 class="font-outfit font-black text-xl md:text-2xl text-slate-100">{{ $property->title }}</h3>
                        <p class="text-xs text-slate-500 mt-1 flex items-center gap-1.5">
                            <i class="fa-solid fa-location-dot text-emerald-500"></i>
                            {{ $property->areaname }}, {{ $property->city }}, {{ $property->country }}
                        </p>
                    </div>
                    <div class="text-left md:text-right shrink-0">
                        <span class="block text-[10px] text-slate-500 uppercase tracking-widest">Market Value</span>
                        <span class="font-outfit font-black text-xl md:text-2xl text-emerald-400">৳{{ number_format($property->price) }}</span>
                    </div>
                </div>

                <!-- Specs Grid -->
                <div>
                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Property Specifications</h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="bg-slate-950/50 border border-slate-900 rounded-2xl p-4 text-center">
                            <i class="fa-solid fa-maximize text-slate-500 mb-1.5 text-sm"></i>
                            <span class="block text-[9px] text-slate-500 uppercase tracking-wider">Area Size</span>
                            <span class="font-bold text-xs text-slate-200 font-outfit">{{ number_format($property->areasize) }} sqft</span>
                        </div>
                        <div class="bg-slate-950/50 border border-slate-900 rounded-2xl p-4 text-center">
                            <i class="fa-solid fa-bed text-slate-500 mb-1.5 text-sm"></i>
                            <span class="block text-[9px] text-slate-500 uppercase tracking-wider">Bedrooms</span>
                            <span class="font-bold text-xs text-slate-200 font-outfit">{{ $property->bedrooms }} Rooms</span>
                        </div>
                        <div class="bg-slate-950/50 border border-slate-900 rounded-2xl p-4 text-center">
                            <i class="fa-solid fa-bath text-slate-500 mb-1.5 text-sm"></i>
                            <span class="block text-[9px] text-slate-500 uppercase tracking-wider">Bathrooms</span>
                            <span class="font-bold text-xs text-slate-200 font-outfit">{{ $property->bathrooms }} Baths</span>
                        </div>
                        <div class="bg-slate-950/50 border border-slate-900 rounded-2xl p-4 text-center">
                            <i class="fa-solid fa-couch text-slate-500 mb-1.5 text-sm"></i>
                            <span class="block text-[9px] text-slate-500 uppercase tracking-wider">Furnished</span>
                            <span class="font-bold text-xs text-slate-200 font-outfit capitalize">{{ $property->furnishedstatus }}</span>
                        </div>
                    </div>
                </div>

                <!-- Features checklist -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-xs text-slate-300">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid {{ $property->parking > 0 ? 'fa-square-check text-emerald-500' : 'fa-square-xmark text-slate-700' }}"></i>
                        <span>Parking ({{ $property->parking }})</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fa-solid {{ $property->balcony > 0 ? 'fa-square-check text-emerald-500' : 'fa-square-xmark text-slate-700' }}"></i>
                        <span>Balcony ({{ $property->balcony }})</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fa-solid {{ $property->lift > 0 ? 'fa-square-check text-emerald-500' : 'fa-square-xmark text-slate-700' }}"></i>
                        <span>Lift Access</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fa-solid {{ $property->swimmingpool > 0 ? 'fa-square-check text-emerald-500' : 'fa-square-xmark text-slate-700' }}"></i>
                        <span>Private Pool</span>
                    </div>
                </div>

                <!-- Description Text -->
                <div class="space-y-3 pt-4 border-t border-slate-900">
                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Listing Description</h4>
                    <p class="text-xs text-slate-400 leading-relaxed font-sans">{{ $property->propdescription }}</p>
                </div>

                <!-- Amenities Checklist -->
                @if(count($amenities) > 0)
                    <div class="space-y-4 pt-4 border-t border-slate-900">
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Property Amenities</h4>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                            @foreach($amenities as $am)
                                <div class="flex items-center gap-2.5 p-3 bg-slate-950/20 border border-slate-900 rounded-xl text-xs text-slate-300">
                                    @if($am->icon)
                                        <i class="{{ $am->icon }} text-emerald-500 text-sm"></i>
                                    @else
                                        <i class="fa-solid fa-circle-check text-emerald-500 text-xs"></i>
                                    @endif
                                    <span>{{ $am->amenityname }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Customer Reviews Section -->
            <div class="glass-panel p-6 rounded-3xl space-y-4">
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Reviews & Ratings</h4>
                
                <div class="space-y-4 divide-y divide-slate-900">
                    @forelse($reviews as $rev)
                        <div class="pt-4 first:pt-0">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <span class="text-xs font-bold text-slate-200 block">{{ $rev->user_name }}</span>
                                    <span class="text-[9px] text-slate-500">{{ date('d M Y, h:i A', strtotime($rev->createdat)) }}</span>
                                </div>
                                <div class="flex items-center gap-1 font-outfit text-xs text-amber-400 font-bold bg-amber-400/10 px-2 py-0.5 rounded-lg border border-amber-400/20">
                                    <i class="fa-solid fa-star text-[10px]"></i>
                                    <span>{{ number_format($rev->rating, 1) }}</span>
                                </div>
                            </div>
                            <p class="text-xs text-slate-400 leading-relaxed italic">"{{ $rev->comments }}"</p>
                        </div>
                    @empty
                        <div class="py-6 text-center text-slate-600 text-xs">
                            <i class="fa-solid fa-comments-slash text-2xl mb-2 block"></i>
                            No reviews have been written for this property yet.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- Right Side: Booking Panel & Agent Profile (4 cols) -->
        <div class="lg:col-span-4 space-y-6">
            
            <!-- Dynamic Booking Panel -->
            <div class="glass-panel p-6 rounded-3xl glowing-card space-y-6">
                <div>
                    <h3 class="font-outfit font-black text-lg text-slate-100">Portal Reservations</h3>
                    <p class="text-[11px] text-slate-500 mt-1">Book a guided site tour or place a temporary reservation fee.</p>
                </div>

                <form action="{{ route('buyer.bookings.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="property_id" value="{{ $property->id }}">

                    <!-- Booking Type Toggle -->
                    <div>
                        <label class="block text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-2">Service Type</label>
                        <div class="grid grid-cols-2 gap-2 p-1 bg-slate-950 border border-slate-900 rounded-xl">
                            <label class="cursor-pointer">
                                <input type="radio" name="booking_type" value="visit" checked onchange="toggleBookingFields(this.value)" class="sr-only peer">
                                <div class="py-2.5 text-center text-xs font-bold text-slate-400 rounded-lg peer-checked:bg-emerald-600 peer-checked:text-white transition duration-200">
                                    Schedule Visit
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="booking_type" value="reservation" onchange="toggleBookingFields(this.value)" class="sr-only peer">
                                <div class="py-2.5 text-center text-xs font-bold text-slate-400 rounded-lg peer-checked:bg-emerald-600 peer-checked:text-white transition duration-200">
                                    Reserve Unit
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Visit Date Input (Show for visits) -->
                    <div id="visit-date-container">
                        <label for="visit_date" class="block text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-2">Visit Date & Time</label>
                        <input type="datetime-local" id="visit_date" name="visit_date" 
                               class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2.5 px-3 text-xs text-slate-200 focus:outline-none focus:border-emerald-500 transition duration-200">
                    </div>

                    <!-- Reservation Dates (Show for reservations) -->
                    <div id="reservation-dates-container" class="hidden grid grid-cols-2 gap-3">
                        <div>
                            <label for="start_date" class="block text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-2">Start Date</label>
                            <input type="date" id="start_date" name="start_date" 
                                   class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2.5 px-3 text-xs text-slate-200 focus:outline-none focus:border-emerald-500 transition duration-200">
                        </div>
                        <div>
                            <label for="end_date" class="block text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-2">End Date</label>
                            <input type="date" id="end_date" name="end_date" 
                                   class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2.5 px-3 text-xs text-slate-200 focus:outline-none focus:border-emerald-500 transition duration-200">
                        </div>
                    </div>

                    <!-- Guests count -->
                    <div>
                        <label for="guests" class="block text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-2">Attendees / Guests</label>
                        <input type="number" id="guests" name="guests" value="1" min="1" required
                               class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2.5 px-3 text-xs text-slate-200 focus:outline-none focus:border-emerald-500 transition duration-200">
                    </div>

                    <!-- Notes -->
                    <div>
                        <label for="notes" class="block text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-2">Special Requests / Notes</label>
                        <textarea id="notes" name="notes" rows="3" placeholder="Optional notes for the representing agent..."
                                  class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2.5 px-3 text-xs text-slate-200 placeholder-slate-700 focus:outline-none focus:border-emerald-500 transition duration-200"></textarea>
                    </div>

                    <!-- Reservation Fee Notice -->
                    <div id="reservation-fee-notice" class="hidden p-4 bg-emerald-500/10 border border-emerald-500/30 rounded-2xl text-[11px] text-emerald-400 space-y-1">
                        <span class="block font-bold"><i class="fa-solid fa-circle-info mr-1"></i>Reservation Deposit Required</span>
                        <span>A 1% booking deposit of <strong>৳{{ number_format(round($property->price * 0.01, 2)) }}</strong> will be billed immediately via credit card to secure the property.</span>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" id="booking-submit-btn"
                            class="w-full py-3.5 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-white text-xs font-bold tracking-wide rounded-xl shadow-lg shadow-emerald-500/10 hover:shadow-emerald-500/20 active:scale-[0.98] transition duration-200">
                        Schedule Site Tour
                    </button>
                </form>
            </div>

            <!-- Agent representation profile card -->
            @if($property->agent_name)
                <div class="glass-panel p-6 rounded-3xl space-y-4">
                    <div>
                        <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Representing Agent</h4>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center font-bold font-outfit text-white text-lg shadow-md shrink-0">
                            {{ substr($property->agent_name, 0, 1) }}
                        </div>
                        <div>
                            <h5 class="text-sm font-bold text-slate-200">{{ $property->agent_name }}</h5>
                            <span class="text-[10px] text-slate-500 font-medium">{{ $property->agencyname ?? 'Independent Representation' }}</span>
                        </div>
                    </div>

                    <div class="py-2.5 border-t border-b border-slate-900 flex justify-between items-center text-xs">
                        <span class="text-slate-500">Agent Rating:</span>
                        <span class="font-bold text-amber-400 flex items-center gap-1">
                            <i class="fa-solid fa-star text-[10px]"></i>
                            {{ number_format($property->agent_rating, 2) }}
                        </span>
                    </div>

                    <div class="space-y-2 text-xs text-slate-400">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-envelope text-slate-600 w-4"></i>
                            <span class="font-mono text-[11px]">{{ $property->agent_email }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-phone text-slate-600 w-4"></i>
                            <span class="font-mono text-[11px]">{{ $property->agent_phone ?? 'No phone contact' }}</span>
                        </div>
                    </div>
                </div>
            @endif

        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
    function toggleBookingFields(type) {
        const visitContainer = document.getElementById('visit-date-container');
        const reservationContainer = document.getElementById('reservation-dates-container');
        const feeNotice = document.getElementById('reservation-fee-notice');
        const submitBtn = document.getElementById('booking-submit-btn');

        if (type === 'visit') {
            visitContainer.classList.remove('hidden');
            reservationContainer.classList.add('hidden');
            feeNotice.classList.add('hidden');
            submitBtn.textContent = 'Schedule Site Tour';
            
            document.getElementById('visit_date').required = true;
            document.getElementById('start_date').required = false;
            document.getElementById('end_date').required = false;
        } else {
            visitContainer.classList.add('hidden');
            reservationContainer.classList.remove('hidden');
            feeNotice.classList.remove('hidden');
            submitBtn.textContent = 'Pay Deposit & Reserve';

            document.getElementById('visit_date').required = false;
            document.getElementById('start_date').required = true;
            document.getElementById('end_date').required = true;
        }
    }
    
    // Set initial state
    document.addEventListener('DOMContentLoaded', function() {
        toggleBookingFields('visit');
    });
</script>
@endsection
