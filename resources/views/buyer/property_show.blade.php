@extends('layouts.buyer')

@section('page_title', 'Property Inventory Details')

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    /* Custom Leaflet Dark Theme Overrides */
    .leaflet-popup-content-wrapper, .leaflet-popup-tip {
        background: #0d111a !important;
        border: 1px solid rgba(16, 185, 129, 0.2) !important;
        border-radius: 16px !important;
        color: #f1f5f9 !important;
    }
    .leaflet-popup-close-button {
        color: #94a3b8 !important;
    }
</style>
@endsection

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

            <!-- Location & Nearby Landmarks Section -->
            @if($property->latitude && $property->longitude)
                <div class="glass-panel p-6 rounded-3xl space-y-4">
                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Property Location & Landmarks</h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-5">
                        <!-- Left: Leaflet Map -->
                        <div class="md:col-span-8">
                            <div id="property-detail-map" class="w-full h-72 rounded-2xl overflow-hidden border border-slate-800/80 z-10"></div>
                        </div>

                        <!-- Right: Nearby Landmarks List -->
                        <div class="md:col-span-4 space-y-4">
                            <h5 class="text-xs font-bold text-slate-200">Nearby Landmarks</h5>
                            <div id="landmarks-list" class="space-y-3 max-h-64 overflow-y-auto pr-1">
                                <!-- Generated Dynamically by JS -->
                            </div>
                        </div>
                    </div>
                </div>
            @endif

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

                    <!-- Visit Date & Time Slot Input (Show for visits) -->
                    <div id="visit-date-container" class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="visit_date" class="block text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-2">Visit Date</label>
                            <input type="date" id="visit_date" name="visit_date" min="{{ date('Y-m-d') }}"
                                   class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2.5 px-3 text-xs text-slate-200 focus:outline-none focus:border-emerald-500 transition duration-200">
                        </div>
                        <div>
                            <label for="visit_slot" class="block text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-2">Time Slot</label>
                            <select id="visit_slot" name="visit_slot"
                                    class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2.5 px-3 text-xs text-slate-300 focus:outline-none focus:border-emerald-500 transition duration-200">
                                <option value="">Select Slot</option>
                                <option value="09:00">09:00 AM - 10:00 AM</option>
                                <option value="10:00">10:00 AM - 11:00 AM</option>
                                <option value="11:00">11:00 AM - 12:00 PM</option>
                                <option value="13:00">01:00 PM - 02:00 PM</option>
                                <option value="14:00">02:00 PM - 03:00 PM</option>
                                <option value="15:00">03:00 PM - 04:00 PM</option>
                                <option value="16:00">04:00 PM - 05:00 PM</option>
                            </select>
                        </div>
                    </div>

                    <!-- Reservation Dates (Show for reservations) -->
                    <div id="reservation-dates-container" class="hidden grid grid-cols-2 gap-3">
                        <div>
                            <label for="start_date" class="block text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-2">Start Date</label>
                            <input type="date" id="start_date" name="start_date" min="{{ date('Y-m-d') }}"
                                   class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2.5 px-3 text-xs text-slate-200 focus:outline-none focus:border-emerald-500 transition duration-200">
                        </div>
                        <div>
                            <label for="end_date" class="block text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-2">End Date</label>
                            <input type="date" id="end_date" name="end_date" min="{{ date('Y-m-d') }}"
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

            <!-- Compare Listing Card -->
            <div class="glass-panel p-6 rounded-3xl space-y-4">
                <div>
                    <h3 class="font-outfit font-bold text-xs text-slate-100 uppercase tracking-wider">Compare Listings</h3>
                    <p class="text-[11px] text-slate-500 mt-1">Select another property to compare side-by-side with this one.</p>
                </div>
                
                <form action="{{ route('buyer.comparisons.add') }}" method="POST" class="space-y-3">
                    @csrf
                    <input type="hidden" name="property_id_1" value="{{ $property->id }}">
                    
                    <div>
                        <select name="property_id_2" required
                                class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2 px-3 text-xs text-slate-300 focus:outline-none focus:border-emerald-500 transition duration-200">
                            <option value="">-- Choose Property --</option>
                            @foreach($comparisonProperties as $compProp)
                                <option value="{{ $compProp->id }}">
                                    {{ $compProp->title }} (৳{{ number_format($compProp->price) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" 
                            class="w-full py-2.5 bg-slate-950 border border-slate-805 hover:border-emerald-500 hover:bg-slate-900 text-slate-300 hover:text-emerald-400 text-xs font-bold rounded-xl transition duration-200">
                        <i class="fa-solid fa-code-compare mr-1"></i> Start Comparison
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
            document.getElementById('visit_slot').required = true;
            document.getElementById('start_date').required = false;
            document.getElementById('end_date').required = false;
        } else {
            visitContainer.classList.add('hidden');
            reservationContainer.classList.remove('hidden');
            feeNotice.classList.remove('hidden');
            submitBtn.textContent = 'Pay Deposit & Reserve';

            document.getElementById('visit_date').required = false;
            document.getElementById('visit_slot').required = false;
            document.getElementById('start_date').required = true;
            document.getElementById('end_date').required = true;
        }
    }
    
    // Set initial state and listener for start_date changes
    document.addEventListener('DOMContentLoaded', function() {
        toggleBookingFields('visit');

        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');
        if (startDateInput && endDateInput) {
            startDateInput.addEventListener('change', function() {
                endDateInput.min = this.value;
            });
        }

        // Initialize Property Detail Map and Landmarks
        initPropertyDetailMap();
    });

    function initPropertyDetailMap() {
        const propLat = {{ $property->latitude ?? 'null' }};
        const propLng = {{ $property->longitude ?? 'null' }};
        
        if (!propLat || !propLng) return;

        // Import Leaflet dynamically by appending script if not already present
        if (typeof L === 'undefined') {
            const script = document.createElement('script');
            script.src = "https://unpkg.com/leaflet@1.9.4/dist/leaflet.js";
            script.onload = () => setupMap(propLat, propLng);
            document.head.appendChild(script);
        } else {
            setupMap(propLat, propLng);
        }
    }

    function setupMap(lat, lng) {
        const map = L.map('property-detail-map').setView([lat, lng], 14);
        
        // Add standard readable tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19
        }).addTo(map);

        // Add main property marker
        const propertyMarker = L.marker([lat, lng]).addTo(map);
        propertyMarker.bindPopup('<b class="text-white text-xs font-outfit">{{ $property->title }}</b><br><span class="text-slate-400 text-[10px]">Property Location</span>').openPopup();

        // Calculate distances for landmarks and display/plot them
        const landmarkTemplates = [
            { name: "KUET Academic Building", type: "education", lat: 22.9015, lng: 89.5020, icon: "fa-graduation-cap", color: "#60a5fa" },
            { name: "Khulna City Medical College", type: "healthcare", lat: 22.8210, lng: 89.5535, icon: "fa-hospital", color: "#f87171" },
            { name: "Sonadanga Bus Terminal", type: "transit", lat: 22.8192, lng: 89.5420, icon: "fa-bus", color: "#fbbf24" },
            { name: "KUET Central Mosque", type: "landmark", lat: 22.9000, lng: 89.5030, icon: "fa-mosque", color: "#34d399" },
            { name: "New Market Khulna", type: "shopping", lat: 22.8250, lng: 89.5500, icon: "fa-bag-shopping", color: "#a78bfa" },
            { name: "Khulna University", type: "education", lat: 22.8020, lng: 89.5350, icon: "fa-school", color: "#60a5fa" },
            { name: "Abu Naser Hospital", type: "healthcare", lat: 22.8480, lng: 89.5300, icon: "fa-user-nurse", color: "#f87171" },
            { name: "Rupsha Bridge viewpoint", type: "scenic", lat: 22.7950, lng: 89.5850, icon: "fa-bridge", color: "#22d3ee" }
        ];

        const listContainer = document.getElementById('landmarks-list');
        if (!listContainer) return;
        
        listContainer.innerHTML = '';

        // Compute distances and sort
        const calculatedLandmarks = landmarkTemplates.map(lm => {
            const distance = calculateDistance(lat, lng, lm.lat, lm.lng);
            return { ...lm, distance };
        }).sort((a, b) => a.distance - b.distance);

        // Render closest landmarks
        calculatedLandmarks.forEach(lm => {
            const distStr = lm.distance.toFixed(1) + ' km';
            
            // Travel times estimation (Assume 40 km/h drive, 5 km/h walk)
            const walkTime = Math.round((lm.distance / 5) * 60);
            const driveTime = Math.max(1, Math.round((lm.distance / 40) * 60));
            const timeDesc = walkTime < 25 ? `${walkTime}m walk` : `${driveTime}m drive`;

            // Append to DOM list
            const lmItem = document.createElement('div');
            lmItem.className = 'flex items-start gap-2.5 p-2 rounded-xl bg-slate-950/40 border border-slate-900/60 hover:bg-slate-900/40 transition duration-150';
            lmItem.innerHTML = `
                <div class="w-7 h-7 rounded-lg flex items-center justify-center text-xs shrink-0" style="background-color: ${lm.color}15; border: 1px solid ${lm.color}30; color: ${lm.color}">
                    <i class="fa-solid ${lm.icon}"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <span class="font-bold text-[11px] text-slate-200 block line-clamp-1">${lm.name}</span>
                    <span class="text-[9px] text-slate-500 uppercase font-semibold">${lm.type}</span>
                </div>
                <div class="text-right shrink-0">
                    <span class="text-[11px] font-bold text-slate-300 block">${distStr}</span>
                    <span class="text-[9px] text-emerald-400 font-medium block">${timeDesc}</span>
                </div>
            `;
            listContainer.appendChild(lmItem);

            // Plot circle markers for landmarks
            L.circleMarker([lm.lat, lm.lng], {
                radius: 6,
                fillColor: lm.color,
                color: '#fff',
                weight: 1,
                opacity: 1,
                fillOpacity: 0.8
            }).addTo(map)
              .bindPopup(`<b style="color: ${lm.color}">${lm.name}</b><br><span style="font-size: 10px; color: #94a3b8">${lm.type.toUpperCase()} &bull; ${distStr} away</span>`);
        });
    }

    function calculateDistance(lat1, lon1, lat2, lon2) {
        const R = 6371; // Radius of earth in km
        const dLat = deg2rad(lat2 - lat1);
        const dLon = deg2rad(lon2 - lon1);
        const a = 
            Math.sin(dLat/2) * Math.sin(dLat/2) +
            Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) * 
            Math.sin(dLon/2) * Math.sin(dLon/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        return R * c;
    }

    function deg2rad(deg) {
        return deg * (Math.PI/180);
    }
</script>
@endsection
