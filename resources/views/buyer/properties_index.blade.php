@extends('layouts.buyer')

@section('page_title', 'Real Estate Marketplace')

@section('content')
<div class="space-y-6">

    <!-- Premium Filters Card -->
    <div class="glass-panel p-6 rounded-3xl">
        <form action="{{ route('buyer.dashboard') }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
            
            <!-- Search Title -->
            <div class="md:col-span-4">
                <label for="search" class="block text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-2">Search Listings</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-500">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </span>
                    <input type="text" id="search" name="search" value="{{ $search }}" placeholder="Search by title, features..." 
                           class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2 pl-9 pr-4 text-xs text-slate-200 placeholder-slate-655 focus:outline-none focus:border-emerald-500 transition duration-200">
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
            <div class="md:col-span-2">
                <label class="block text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-2">Min Price (৳)</label>
                <input type="number" name="min_price" value="{{ $minPrice }}" placeholder="Min" 
                       class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2 px-3 text-xs text-slate-200 focus:outline-none focus:border-emerald-500 transition duration-200">
            </div>

            <div class="md:col-span-2">
                <label class="block text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-2">Max Price (৳)</label>
                <input type="number" name="max_price" value="{{ $maxPrice }}" placeholder="Max" 
                       class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2 px-3 text-xs text-slate-200 focus:outline-none focus:border-emerald-500 transition duration-200">
            </div>

            <!-- Action Buttons -->
            <div class="md:col-span-12 flex justify-end gap-2 mt-2">
                @if($search || $locationId || $typeId || $minPrice || $maxPrice)
                    <a href="{{ route('buyer.dashboard') }}" class="px-4 py-2 border border-slate-800 hover:bg-slate-800 rounded-xl text-xs font-bold text-slate-400 transition duration-200 flex items-center justify-center">
                        Clear Filters
                    </a>
                @endif
                <button type="submit" class="px-6 py-2 bg-emerald-600 hover:bg-emerald-500 rounded-xl text-xs font-bold text-white transition duration-200 shadow-md shadow-emerald-600/10">
                    Apply Filters
                </button>
            </div>

        </form>
    </div>

    <!-- Properties Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
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
</div>
@endsection
