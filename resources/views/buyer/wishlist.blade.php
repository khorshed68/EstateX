@extends('layouts.buyer')

@section('page_title', 'Saved Listings')

@section('content')
<div class="space-y-6">
    <div>
        <h3 class="text-lg font-bold text-slate-100 font-outfit">My Wishlist Directory</h3>
        <p class="text-xs text-slate-400 mt-1">Direct shortcut access to all property listings you have pinned for review.</p>
    </div>

    <!-- Wishlist Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @forelse($properties as $prop)
            <div class="glass-panel rounded-3xl overflow-hidden group flex flex-col justify-between hover:border-slate-700 transition duration-300">
                <div>
                    <!-- Image -->
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

                        <!-- Remove Button top-right -->
                        <div class="absolute top-4 right-4">
                            <form action="{{ route('buyer.wishlist.remove', $prop->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-8 h-8 rounded-full bg-red-500/10 border border-red-500/30 flex items-center justify-center text-red-400 hover:bg-red-500/20 backdrop-blur-sm transition duration-200" title="Remove from Wishlist">
                                    <i class="fa-solid fa-trash-can text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Description Details -->
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

                <!-- Footer Price & View Details -->
                <div class="p-5 pt-0 border-t-0 flex items-center justify-between mt-auto">
                    <div>
                        <span class="block text-[9px] text-slate-500 uppercase tracking-widest">Price</span>
                        <span class="font-outfit font-black text-base text-emerald-400">৳{{ number_format($prop->price) }}</span>
                    </div>
                    <a href="{{ route('buyer.properties.show', $prop->id) }}" class="px-4 py-2 bg-slate-950 border border-slate-800 hover:border-slate-700 hover:bg-slate-900 rounded-xl text-xs font-bold text-slate-300 hover:text-white transition duration-200">
                        View Details
                    </a>
                </div>
            </div>
        @empty
            <div class="md:col-span-3 py-16 text-center glass-panel rounded-3xl">
                <i class="fa-solid fa-heart-circle-xmark text-4xl text-slate-700 mb-3"></i>
                <h4 class="font-outfit font-bold text-slate-300">Your Wishlist is Empty</h4>
                <p class="text-xs text-slate-500 mt-1">Start browsing listings in the marketplace and click the heart icon to save them here.</p>
                <a href="{{ route('buyer.dashboard') }}" class="inline-block mt-4 px-5 py-2 bg-emerald-600 hover:bg-emerald-500 text-xs font-bold text-white rounded-xl transition duration-200 shadow-md">
                    Explore Properties
                </a>
            </div>
        @endforelse
    </div>
</div>
@endsection
