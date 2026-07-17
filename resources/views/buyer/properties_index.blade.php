@extends('layouts.buyer')

@section('page_title', 'Real Estate Marketplace')

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    /* Custom Leaflet Dark Theme Overrides */
    .leaflet-popup-content-wrapper, .leaflet-popup-tip {
        background: #0d111a !important; /* Matches main dark theme */
        border: 1px solid rgba(16, 185, 129, 0.2) !important;
        border-radius: 16px !important;
        color: #f1f5f9 !important;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5) !important;
    }
    .leaflet-popup-close-button {
        color: #94a3b8 !important;
        padding: 8px 8px 0 0 !important;
    }
    .water-wave-btn {
        position: relative;
        overflow: hidden;
    }
    .water-ripple-wave {
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.35); /* Translucent water wave color */
        width: 250px;
        height: 250px;
        transform: translate(-50%, -50%) scale(0);
        pointer-events: none;
        animation: rippleAnimation 0.6s cubic-bezier(0.1, 0.8, 0.3, 1) forwards;
    }
    @keyframes rippleAnimation {
        0% {
            transform: translate(-50%, -50%) scale(0);
            opacity: 0.8;
        }
        100% {
            transform: translate(-50%, -50%) scale(1);
            opacity: 0;
        }
    }
    /* Global water wave cursor effect */
    .global-water-ripple {
        position: absolute;
        border-radius: 50%;
        border: 1.5px solid rgba(16, 185, 129, 0.3); /* Translucent emerald wave border */
        background: radial-gradient(circle, rgba(16, 185, 129, 0.08) 0%, transparent 80%);
        pointer-events: none;
        width: 80px;
        height: 80px;
        transform: translate(-50%, -50%) scale(0);
        z-index: 50;
        animation: globalRippleAnimation 1.2s cubic-bezier(0.1, 0.8, 0.3, 1) forwards;
    }
    @keyframes globalRippleAnimation {
        0% {
            transform: translate(-50%, -50%) scale(0);
            opacity: 0.9;
        }
        100% {
            transform: translate(-50%, -50%) scale(1);
            opacity: 0;
        }
    }
</style>
@endsection

@section('content')
<div id="marketplace-container" class="space-y-6 relative overflow-hidden">

    <!-- Premium Filters Card -->
    <div class="glass-panel p-6 rounded-3xl">
        <form action="{{ route('buyer.dashboard') }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
            
            <!-- Search Title -->
            <div class="md:col-span-3">
                <label for="search" class="block text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-2">Search Listings</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-500">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </span>
                    <input type="text" id="search" name="search" value="{{ $search }}" placeholder="Search listings..." 
                           class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2 pl-9 pr-4 text-xs text-slate-200 placeholder-slate-600 focus:outline-none focus:border-emerald-500 transition duration-200">
                </div>
            </div>

            <!-- Location Dropdown -->
            <div class="md:col-span-2">
                <label for="location_id" class="block text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-2">Location</label>
                <select id="location_id" name="location_id" 
                        class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2 px-3 text-xs text-slate-300 focus:outline-none focus:border-emerald-500 transition duration-200">
                    <option value="">All Locations</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc->id }}" {{ $locationId == $loc->id ? 'selected' : '' }}>
                            {{ $loc->areaname }}, {{ $loc->city }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Property Type -->
            <div class="md:col-span-2">
                <label for="type_id" class="block text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-2">Type</label>
                <select id="type_id" name="type_id" 
                        class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2 px-3 text-xs text-slate-300 focus:outline-none focus:border-emerald-500 transition duration-200">
                    <option value="">All Types</option>
                    @foreach($types as $t)
                        <option value="{{ $t->id }}" {{ $typeId == $t->id ? 'selected' : '' }}>
                            {{ $t->typename }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Price Range -->
            <div class="md:col-span-1.5 md:col-span-1">
                <label class="block text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-2">Min Price</label>
                <input type="number" name="min_price" value="{{ $minPrice }}" placeholder="Min" 
                       class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2 px-3 text-xs text-slate-200 focus:outline-none focus:border-emerald-500 transition duration-200">
            </div>

            <div class="md:col-span-1.5 md:col-span-1">
                <label class="block text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-2">Max Price</label>
                <input type="number" name="max_price" value="{{ $maxPrice }}" placeholder="Max" 
                       class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2 px-3 text-xs text-slate-200 focus:outline-none focus:border-emerald-500 transition duration-200">
            </div>

            <!-- Sort Option -->
            <div class="md:col-span-3">
                <label for="sort" class="block text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-2">Sort Listings</label>
                <select id="sort" name="sort" onchange="this.form.submit()" 
                        class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2 px-3 text-xs text-slate-300 focus:outline-none focus:border-emerald-500 transition duration-200">
                    <option value="newest" {{ $sort === 'newest' ? 'selected' : '' }}>Newest Added</option>
                    <option value="price_asc" {{ $sort === 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                    <option value="price_desc" {{ $sort === 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                    <option value="size_asc" {{ $sort === 'size_asc' ? 'selected' : '' }}>Area: Small to Large</option>
                    <option value="size_desc" {{ $sort === 'size_desc' ? 'selected' : '' }}>Area: Large to Small</option>
                </select>
            </div>

            <!-- Advanced Filters Toggle -->
            <div class="md:col-span-12 flex justify-start mt-2">
                <button type="button" onclick="toggleAdvancedFilters()" class="water-wave-btn relative overflow-hidden px-4 py-2 border border-emerald-500/20 hover:border-emerald-500/50 bg-emerald-500/5 hover:bg-emerald-500/10 rounded-xl text-xs font-bold text-emerald-400 transition duration-300 flex items-center gap-1.5 focus:outline-none">
                    <i id="advanced-chevron" class="fa-solid fa-chevron-down text-[10px] relative z-10"></i>
                    <span class="relative z-10">Advanced Specifications Filters</span>
                </button>
            </div>

            <!-- Advanced Filters Drawer -->
            <div id="advanced-filters-drawer" class="hidden md:col-span-12 grid grid-cols-1 md:grid-cols-12 gap-4 pt-4 border-t border-slate-900 w-full">
                <!-- Bedrooms -->
                <div class="md:col-span-2">
                    <label for="bedrooms" class="block text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-2">Min Bedrooms</label>
                    <select id="bedrooms" name="bedrooms" 
                            class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2 px-3 text-xs text-slate-300 focus:outline-none focus:border-emerald-500 transition duration-200">
                        <option value="">Any</option>
                        <option value="1" {{ $bedrooms == '1' ? 'selected' : '' }}>1+ Bed</option>
                        <option value="2" {{ $bedrooms == '2' ? 'selected' : '' }}>2+ Bed</option>
                        <option value="3" {{ $bedrooms == '3' ? 'selected' : '' }}>3+ Bed</option>
                        <option value="4" {{ $bedrooms == '4' ? 'selected' : '' }}>4+ Bed</option>
                    </select>
                </div>

                <!-- Bathrooms -->
                <div class="md:col-span-2">
                    <label for="bathrooms" class="block text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-2">Min Bathrooms</label>
                    <select id="bathrooms" name="bathrooms" 
                            class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2 px-3 text-xs text-slate-300 focus:outline-none focus:border-emerald-500 transition duration-200">
                        <option value="">Any</option>
                        <option value="1" {{ $bathrooms == '1' ? 'selected' : '' }}>1+ Bath</option>
                        <option value="2" {{ $bathrooms == '2' ? 'selected' : '' }}>2+ Bath</option>
                        <option value="3" {{ $bathrooms == '3' ? 'selected' : '' }}>3+ Bath</option>
                        <option value="4" {{ $bathrooms == '4' ? 'selected' : '' }}>4+ Bath</option>
                    </select>
                </div>

                <!-- Min Area -->
                <div class="md:col-span-2">
                    <label for="min_area" class="block text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-2">Min Area (sqft)</label>
                    <input type="number" id="min_area" name="min_area" value="{{ $minArea }}" placeholder="Min sqft" 
                           class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2 px-3 text-xs text-slate-200 focus:outline-none focus:border-emerald-500 transition duration-200">
                </div>

                <!-- Max Area -->
                <div class="md:col-span-2">
                    <label for="max_area" class="block text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-2">Max Area (sqft)</label>
                    <input type="number" id="max_area" name="max_area" value="{{ $maxArea }}" placeholder="Max sqft" 
                           class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2 px-3 text-xs text-slate-200 focus:outline-none focus:border-emerald-500 transition duration-200">
                </div>

                <!-- Furnished Status -->
                <div class="md:col-span-4">
                    <label for="furnished_status" class="block text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-2">Furnished Status</label>
                    <select id="furnished_status" name="furnished_status" 
                            class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2 px-3 text-xs text-slate-300 focus:outline-none focus:border-emerald-500 transition duration-200">
                        <option value="">Any</option>
                        <option value="furnished" {{ $furnishedStatus === 'furnished' ? 'selected' : '' }}>Fully Furnished</option>
                        <option value="semi-furnished" {{ $furnishedStatus === 'semi-furnished' ? 'selected' : '' }}>Semi-Furnished</option>
                        <option value="unfurnished" {{ $furnishedStatus === 'unfurnished' ? 'selected' : '' }}>Unfurnished</option>
                    </select>
                </div>

                <!-- Amenity Checkboxes -->
                <div class="md:col-span-12 grid grid-cols-2 md:grid-cols-5 gap-4 mt-2">
                    <label class="flex items-center gap-2 cursor-pointer text-xs text-slate-400 hover:text-slate-200 transition duration-200">
                        <input type="checkbox" name="parking" value="1" {{ $parking ? 'checked' : '' }} class="rounded border-slate-805 bg-slate-955 text-emerald-500 focus:ring-emerald-500">
                        <span>Parking Available</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer text-xs text-slate-400 hover:text-slate-200 transition duration-200">
                        <input type="checkbox" name="balcony" value="1" {{ $balcony ? 'checked' : '' }} class="rounded border-slate-805 bg-slate-955 text-emerald-500 focus:ring-emerald-500">
                        <span>Has Balcony</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer text-xs text-slate-400 hover:text-slate-200 transition duration-200">
                        <input type="checkbox" name="lift" value="1" {{ $lift ? 'checked' : '' }} class="rounded border-slate-805 bg-slate-955 text-emerald-500 focus:ring-emerald-500">
                        <span>Elevator / Lift</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer text-xs text-slate-400 hover:text-slate-200 transition duration-200">
                        <input type="checkbox" name="swimming_pool" value="1" {{ $swimmingPool ? 'checked' : '' }} class="rounded border-slate-805 bg-slate-955 text-emerald-500 focus:ring-emerald-500">
                        <span>Swimming Pool</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer text-xs text-slate-400 hover:text-slate-200 transition duration-200">
                        <input type="checkbox" name="pet_friendly" value="1" {{ $petFriendly ? 'checked' : '' }} class="rounded border-slate-805 bg-slate-955 text-emerald-500 focus:ring-emerald-500">
                        <span>Pet Friendly</span>
                    </label>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="md:col-span-12 flex justify-end gap-2 mt-2">
                @if($search || $locationId || $typeId || $minPrice || $maxPrice || $bedrooms || $bathrooms || $minArea || $maxArea || $furnishedStatus || $parking || $balcony || $lift || $swimmingPool || $petFriendly)
                    <a href="{{ route('buyer.dashboard') }}" class="px-4 py-2 border border-slate-800 hover:bg-slate-800 rounded-xl text-xs font-bold text-slate-400 transition duration-200 flex items-center justify-center">
                        Clear Filters
                    </a>
                @endif
                <button type="submit" class="water-wave-btn relative overflow-hidden px-6 py-2 bg-emerald-600 hover:bg-emerald-500 rounded-xl text-xs font-bold text-white transition duration-300 shadow-md shadow-emerald-600/10">
                    <span class="relative z-10">Apply Filters</span>
                </button>
            </div>

        </form>
    </div>

    <!-- View Switcher -->
    <div class="flex justify-between items-center bg-slate-900/40 p-4 rounded-3xl border border-slate-900/50">
        <div>
            <h4 class="font-outfit font-bold text-sm text-slate-200">Available Listings</h4>
            <p class="text-[10px] text-slate-500">Showing {{ count($properties) }} active properties</p>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" onclick="switchView('list')" id="btn-list-view" class="px-3.5 py-1.5 rounded-xl border border-emerald-500/20 bg-emerald-500/10 text-emerald-400 text-xs font-bold transition duration-200 flex items-center gap-1.5 focus:outline-none">
                <i class="fa-solid fa-list-ul"></i>
                <span>List View</span>
            </button>
            <button type="button" onclick="switchView('map')" id="btn-map-view" class="px-3.5 py-1.5 rounded-xl border border-slate-800 hover:bg-slate-900 text-slate-400 text-xs font-bold transition duration-200 flex items-center gap-1.5 focus:outline-none">
                <i class="fa-solid fa-map-location-dot"></i>
                <span>Map Search</span>
            </button>
        </div>
    </div>

    <!-- Properties List View -->
    <div id="properties-list-view" class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @forelse($properties as $prop)
            <div class="glass-panel rounded-3xl overflow-hidden group flex flex-col justify-between hover:border-slate-700 transition duration-300">
                <div>
                    <!-- Image Showcase -->
                    <div class="relative h-48 bg-slate-950 overflow-hidden shrink-0">
                        @if($prop->main_image)
                            <img src="{{ $prop->main_image }}" alt="{{ $prop->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        @else
                            <div class="w-full h-full flex flex-col items-center justify-center text-slate-700 bg-slate-950">
                                <i class="fa-solid fa-image text-3xl mb-2"></i>
                                <span class="text-[9px] uppercase tracking-wider font-semibold">Image Coming Soon</span>
                            </div>
                        @endif

                        <!-- Badges -->
                        <div class="absolute top-4 left-4">
                            <span class="px-2 py-1 rounded-lg bg-emerald-500/20 border border-emerald-500/40 text-[9px] font-bold text-emerald-400 uppercase tracking-wider backdrop-blur-sm">
                                {{ $prop->typename }}
                            </span>
                        </div>

                        <!-- Wishlist Toggle Heart -->
                        <div class="absolute top-4 right-4">
                            @if($prop->is_wishlisted)
                                <form action="{{ route('buyer.wishlist.remove', $prop->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-8 h-8 rounded-full bg-red-500/10 border border-red-500/30 flex items-center justify-center text-red-400 hover:bg-red-500/20 backdrop-blur-sm transition duration-200" title="Remove from Wishlist">
                                        <i class="fa-solid fa-heart text-xs"></i>
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('buyer.wishlist.add', $prop->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-8 h-8 rounded-full bg-slate-950/40 border border-slate-800 flex items-center justify-center text-slate-300 hover:text-red-400 hover:bg-red-500/10 backdrop-blur-sm transition duration-200" title="Add to Wishlist">
                                        <i class="fa-regular fa-heart text-xs"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    <!-- Details Content -->
                    <div class="p-5">
                        <div class="flex items-center gap-1.5 text-[10px] text-slate-500 mb-1">
                            <i class="fa-solid fa-location-dot text-[9px] text-emerald-500"></i>
                            <span>{{ $prop->areaname }}, {{ $prop->city }}</span>
                        </div>
                        <h4 class="font-outfit font-bold text-base text-slate-200 line-clamp-1 group-hover:text-white transition duration-200">{{ $prop->title }}</h4>
                        
                        <!-- Specs -->
                        <div class="flex items-center gap-4 mt-3 py-2 border-t border-b border-slate-900 text-[10px] text-slate-400">
                            <span class="flex items-center gap-1"><i class="fa-solid fa-bed text-slate-500"></i> {{ $prop->bedrooms }} Bed</span>
                            <span class="flex items-center gap-1"><i class="fa-solid fa-bath text-slate-500"></i> {{ $prop->bathrooms }} Bath</span>
                            <span class="flex items-center gap-1"><i class="fa-solid fa-maximize text-slate-500"></i> {{ number_format($prop->areasize) }} sqft</span>
                        </div>
                    </div>
                </div>

                <!-- Footer Price & Action -->
                <div class="p-5 pt-0 border-t-0 flex items-center justify-between mt-auto">
                    <div>
                        <span class="block text-[9px] text-slate-500 uppercase tracking-widest">Pricing</span>
                        <span class="font-outfit font-black text-base text-emerald-400">৳{{ number_format($prop->price) }}</span>
                    </div>
                    <a href="{{ route('buyer.properties.show', $prop->id) }}" class="px-4 py-2 bg-slate-950 border border-slate-800 hover:border-slate-700 hover:bg-slate-900 rounded-xl text-xs font-bold text-slate-300 hover:text-white transition duration-200">
                        View Details
                    </a>
                </div>
            </div>
        @empty
            <div class="md:col-span-3 py-16 text-center glass-panel rounded-3xl">
                <i class="fa-solid fa-house-circle-xmark text-4xl text-slate-700 mb-3"></i>
                <h4 class="font-outfit font-bold text-slate-300">No Listings Match Your Search</h4>
                <p class="text-xs text-slate-500 mt-1">Try adjusting your filters or keyword criteria.</p>
            </div>
        @endforelse
    </div>

    <!-- Map View Container (hidden by default) -->
    <div id="properties-map-view" class="hidden glass-panel p-4 rounded-3xl space-y-4">
        <div id="properties-map" class="w-full h-[550px] rounded-2xl overflow-hidden border border-slate-800/80 z-10"></div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function toggleAdvancedFilters(forceOpen = false) {
        const drawer = document.getElementById('advanced-filters-drawer');
        const chevron = document.getElementById('advanced-chevron');
        
        if (forceOpen || drawer.classList.contains('hidden')) {
            drawer.classList.remove('hidden');
            drawer.classList.add('grid');
            chevron.classList.remove('fa-chevron-down');
            chevron.classList.add('fa-chevron-up');
        } else {
            drawer.classList.add('hidden');
            drawer.classList.remove('grid');
            chevron.classList.add('fa-chevron-down');
            chevron.classList.remove('fa-chevron-up');
        }
    }
    
    let mapInitialized = false;
    let leafletMap = null;

    function switchView(view) {
        const listView = document.getElementById('properties-list-view');
        const mapView = document.getElementById('properties-map-view');
        const btnList = document.getElementById('btn-list-view');
        const btnMap = document.getElementById('btn-map-view');
        
        if (!listView || !mapView) return;
        
        if (view === 'list') {
            listView.classList.remove('hidden');
            listView.classList.add('grid');
            mapView.classList.add('hidden');
            
            if (btnList && btnMap) {
                btnList.className = "px-3.5 py-1.5 rounded-xl border border-emerald-500/20 bg-emerald-500/10 text-emerald-400 text-xs font-bold transition duration-200 flex items-center gap-1.5 focus:outline-none";
                btnMap.className = "px-3.5 py-1.5 rounded-xl border border-slate-800 hover:bg-slate-900 text-slate-400 text-xs font-bold transition duration-200 flex items-center gap-1.5 focus:outline-none";
            }
        } else {
            listView.classList.add('hidden');
            listView.classList.remove('grid');
            mapView.classList.remove('hidden');
            
            if (btnList && btnMap) {
                btnList.className = "px-3.5 py-1.5 rounded-xl border border-slate-800 hover:bg-slate-900 text-slate-400 text-xs font-bold transition duration-200 flex items-center gap-1.5 focus:outline-none";
                btnMap.className = "px-3.5 py-1.5 rounded-xl border border-emerald-500/20 bg-emerald-500/10 text-emerald-400 text-xs font-bold transition duration-200 flex items-center gap-1.5 focus:outline-none";
            }
            
            // Lazy load and initialize Leaflet
            if (!mapInitialized) {
                // Dynamically import Leaflet JS if not already loaded
                if (typeof L === 'undefined') {
                    const script = document.createElement('script');
                    script.src = "https://unpkg.com/leaflet@1.9.4/dist/leaflet.js";
                    script.onload = () => {
                        initPropertiesMap();
                        setTimeout(() => {
                            if (leafletMap) leafletMap.invalidateSize();
                        }, 250);
                    };
                    document.head.appendChild(script);
                } else {
                    initPropertiesMap();
                    setTimeout(() => {
                        if (leafletMap) leafletMap.invalidateSize();
                    }, 250);
                }
            } else if (leafletMap) {
                setTimeout(() => {
                    leafletMap.invalidateSize();
                }, 250);
            }
        }
    }

    function initPropertiesMap() {
        const properties = @json($properties);
        
        let centerLat = 22.8456;
        let centerLng = 89.5403;
        
        // Find first location to center on
        for (let prop of properties) {
            if (prop.latitude && prop.longitude) {
                centerLat = parseFloat(prop.latitude);
                centerLng = parseFloat(prop.longitude);
                break;
            }
        }
        
        // Initialize Leaflet Map
        leafletMap = L.map('properties-map').setView([centerLat, centerLng], 13);
        
        // Add standard bright, highly readable tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19
        }).addTo(leafletMap);
        
        // Add markers with coordinate jittering for overlapping properties
        const coordinatesCount = {};
        
        properties.forEach(prop => {
            if (prop.latitude && prop.longitude) {
                let lat = parseFloat(prop.latitude);
                let lng = parseFloat(prop.longitude);
                
                // Key based on latitude and longitude (rounded to 5 decimals to detect close/same points)
                const coordKey = `${lat.toFixed(5)},${lng.toFixed(5)}`;
                
                if (coordinatesCount[coordKey]) {
                    const count = coordinatesCount[coordKey];
                    // Distribute overlapping markers in a small spiral circle
                    const angle = count * (2 * Math.PI / 8); 
                    const radius = 0.00025 * (1 + Math.floor(count / 8) * 0.4); 
                    lat += Math.sin(angle) * radius;
                    lng += Math.cos(angle) * radius;
                    coordinatesCount[coordKey]++;
                } else {
                    coordinatesCount[coordKey] = 1;
                }

                const marker = L.marker([lat, lng]).addTo(leafletMap);
                
                const mainImg = prop.main_image ? (prop.main_image.startsWith('/') ? prop.main_image : '/' + prop.main_image) : 'https://placehold.co/600x400/0f172a/e2e8f0?text=Property';
                const detailsUrl = `/buyer/properties/${prop.id}`;
                const formattedPrice = Number(prop.price).toLocaleString();
                
                const popupContent = `
                    <div class="w-48 text-slate-200 p-1">
                        <img src="${mainImg}" class="w-full h-24 object-cover rounded-xl mb-2 border border-slate-800">
                        <h4 class="font-bold font-outfit text-xs text-white line-clamp-1">${prop.title}</h4>
                        <p class="text-[10px] text-slate-400 mt-0.5 flex items-center gap-1">
                            <i class="fa-solid fa-location-dot text-emerald-500"></i> ${prop.areaname}
                        </p>
                        <div class="flex justify-between items-center mt-2.5 pt-2 border-t border-slate-900">
                            <span class="text-emerald-400 font-bold text-xs">৳${formattedPrice}</span>
                            <a href="${detailsUrl}" class="text-[10px] font-bold text-white bg-emerald-600 px-2.5 py-1 rounded-lg hover:bg-emerald-500 transition duration-150">Details</a>
                        </div>
                    </div>
                `;
                marker.bindPopup(popupContent);
            }
        });
        
        mapInitialized = true;
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Automatically open the drawer if any advanced filters are active
        const hasAdvancedFilters = {{ ($bedrooms || $bathrooms || $minArea || $maxArea || $furnishedStatus || $parking || $balcony || $lift || $swimmingPool || $petFriendly) ? 'true' : 'false' }};
        if (hasAdvancedFilters) {
            toggleAdvancedFilters(true);
        }

        // Water wave cursor ripple effect for buttons
        document.querySelectorAll('.water-wave-btn').forEach(btn => {
            btn.addEventListener('mousemove', function(e) {
                const rect = btn.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                
                const ripple = document.createElement('span');
                ripple.className = 'water-ripple-wave';
                ripple.style.left = `${x}px`;
                ripple.style.top = `${y}px`;
                
                btn.appendChild(ripple);
                
                setTimeout(() => {
                    ripple.remove();
                }, 600);
            });
        });
    });
</script>
@endsection
