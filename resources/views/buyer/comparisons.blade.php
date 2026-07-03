@extends('layouts.buyer')

@section('page_title', 'Listing Comparisons')

@section('content')
<div class="space-y-6">

    <!-- Header Card -->
    <div class="glass-panel p-6 rounded-3xl">
        <h3 class="text-lg font-bold text-slate-100">Property Comparisons</h3>
        <p class="text-xs text-slate-400 mt-1">Review structural, spatial, and amenities details of compared properties side-by-side.</p>
    </div>

    <!-- Comparisons Container -->
    <div class="space-y-6">
        @forelse($comparisons as $comp)
            <div class="glass-panel rounded-3xl overflow-hidden p-6 relative border border-slate-900 hover:border-slate-800 transition duration-300">
                
                <!-- Remove Button -->
                <div class="absolute top-4 right-4 z-10">
                    <form action="{{ route('buyer.comparisons.remove', $comp->comparison_id) }}" method="POST" onsubmit="return confirm('Remove this comparison pair?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-8 h-8 rounded-full bg-slate-950/80 border border-slate-850 hover:border-red-500/50 hover:bg-red-500/10 flex items-center justify-center text-slate-400 hover:text-red-400 backdrop-blur-sm transition duration-200" title="Remove Comparison">
                            <i class="fa-solid fa-trash-can text-xs"></i>
                        </button>
                    </form>
                </div>

                <!-- Compared Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 divide-y md:divide-y-0 md:divide-x divide-slate-800/60">
                    
                    <!-- Property 1 Column -->
                    <div class="space-y-5">
                        <!-- Thumbnail & Title -->
                        <div class="flex gap-4 items-center">
                            <div class="w-24 h-20 rounded-2xl bg-slate-950 overflow-hidden shrink-0 border border-slate-850">
                                @if($comp->p1_image)
                                    <img src="{{ $comp->p1_image }}" alt="Property 1" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex flex-col items-center justify-center text-slate-700">
                                        <i class="fa-solid fa-image text-lg"></i>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <span class="px-2 py-0.5 rounded-lg bg-emerald-500/15 border border-emerald-500/20 text-[9px] font-bold text-emerald-400 uppercase tracking-wider">Property A</span>
                                <h4 class="font-outfit font-bold text-slate-200 text-sm mt-1 line-clamp-1">{{ $comp->p1_title }}</h4>
                                <span class="font-bold text-emerald-400 text-xs">৳{{ number_format($comp->p1_price) }}</span>
                            </div>
                        </div>

                        <!-- Specs Table -->
                        <div class="space-y-2 text-xs">
                            <div class="flex justify-between py-1 border-b border-slate-950">
                                <span class="text-slate-500">Area size:</span>
                                <span class="font-bold text-slate-300">{{ number_format($comp->p1_areasize) }} sqft</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-950">
                                <span class="text-slate-500">Bedrooms:</span>
                                <span class="font-bold text-slate-300">{{ $comp->p1_bedrooms }} Rooms</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-950">
                                <span class="text-slate-500">Bathrooms:</span>
                                <span class="font-bold text-slate-300">{{ $comp->p1_bathrooms }} Baths</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-950">
                                <span class="text-slate-500">Furnishing:</span>
                                <span class="font-bold text-slate-300 capitalize">{{ $comp->p1_furnishedstatus }}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-950">
                                <span class="text-slate-500">Parking Space:</span>
                                <span class="font-bold text-slate-300">{{ $comp->p1_parking > 0 ? 'Available (' . $comp->p1_parking . ')' : 'No' }}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-950">
                                <span class="text-slate-500">Amenities:</span>
                                <div class="flex flex-wrap justify-end gap-1 max-w-[180px]">
                                    @if($comp->p1_lift > 0)<span class="px-1.5 py-0.5 rounded bg-slate-950 text-[9px] text-slate-400 font-bold border border-slate-900">Lift</span>@endif
                                    @if($comp->p1_swimmingpool > 0)<span class="px-1.5 py-0.5 rounded bg-slate-950 text-[9px] text-slate-400 font-bold border border-slate-900">Pool</span>@endif
                                    @if($comp->p1_balcony > 0)<span class="px-1.5 py-0.5 rounded bg-slate-950 text-[9px] text-slate-400 font-bold border border-slate-900">Balcony</span>@endif
                                    @if($comp->p1_petfriendly > 0)<span class="px-1.5 py-0.5 rounded bg-slate-950 text-[9px] text-slate-400 font-bold border border-slate-900">Pet Friendly</span>@endif
                                    @if($comp->p1_lift == 0 && $comp->p1_swimmingpool == 0 && $comp->p1_balcony == 0 && $comp->p1_petfriendly == 0)
                                        <span class="text-slate-600 font-medium">None</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="pt-2">
                            <a href="{{ route('buyer.properties.show', $comp->p1_id) }}" class="w-full block py-2 bg-slate-950 hover:bg-slate-900 border border-slate-850 hover:border-emerald-500 rounded-xl text-center text-xs font-bold text-slate-300 hover:text-white transition duration-200">
                                View Listing Details
                            </a>
                        </div>
                    </div>

                    <!-- Property 2 Column -->
                    <div class="space-y-5 pt-6 md:pt-0 md:pl-8">
                        <!-- Thumbnail & Title -->
                        <div class="flex gap-4 items-center">
                            <div class="w-24 h-20 rounded-2xl bg-slate-950 overflow-hidden shrink-0 border border-slate-850">
                                @if($comp->p2_image)
                                    <img src="{{ $comp->p2_image }}" alt="Property 2" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex flex-col items-center justify-center text-slate-700">
                                        <i class="fa-solid fa-image text-lg"></i>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <span class="px-2 py-0.5 rounded-lg bg-teal-500/15 border border-teal-500/20 text-[9px] font-bold text-teal-400 uppercase tracking-wider">Property B</span>
                                <h4 class="font-outfit font-bold text-slate-200 text-sm mt-1 line-clamp-1">{{ $comp->p2_title }}</h4>
                                <span class="font-bold text-teal-400 text-xs">৳{{ number_format($comp->p2_price) }}</span>
                            </div>
                        </div>

                        <!-- Specs Table -->
                        <div class="space-y-2 text-xs">
                            <div class="flex justify-between py-1 border-b border-slate-950">
                                <span class="text-slate-500">Area size:</span>
                                <span class="font-bold text-slate-300">{{ number_format($comp->p2_areasize) }} sqft</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-950">
                                <span class="text-slate-500">Bedrooms:</span>
                                <span class="font-bold text-slate-300">{{ $comp->p2_bedrooms }} Rooms</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-950">
                                <span class="text-slate-500">Bathrooms:</span>
                                <span class="font-bold text-slate-300">{{ $comp->p2_bathrooms }} Baths</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-950">
                                <span class="text-slate-500">Furnishing:</span>
                                <span class="font-bold text-slate-300 capitalize">{{ $comp->p2_furnishedstatus }}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-950">
                                <span class="text-slate-500">Parking Space:</span>
                                <span class="font-bold text-slate-300">{{ $comp->p2_parking > 0 ? 'Available (' . $comp->p2_parking . ')' : 'No' }}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-950">
                                <span class="text-slate-500">Amenities:</span>
                                <div class="flex flex-wrap justify-end gap-1 max-w-[180px]">
                                    @if($comp->p2_lift > 0)<span class="px-1.5 py-0.5 rounded bg-slate-950 text-[9px] text-slate-400 font-bold border border-slate-900">Lift</span>@endif
                                    @if($comp->p2_swimmingpool > 0)<span class="px-1.5 py-0.5 rounded bg-slate-950 text-[9px] text-slate-400 font-bold border border-slate-900">Pool</span>@endif
                                    @if($comp->p2_balcony > 0)<span class="px-1.5 py-0.5 rounded bg-slate-950 text-[9px] text-slate-400 font-bold border border-slate-900">Balcony</span>@endif
                                    @if($comp->p2_petfriendly > 0)<span class="px-1.5 py-0.5 rounded bg-slate-950 text-[9px] text-slate-400 font-bold border border-slate-900">Pet Friendly</span>@endif
                                    @if($comp->p2_lift == 0 && $comp->p2_swimmingpool == 0 && $comp->p2_balcony == 0 && $comp->p2_petfriendly == 0)
                                        <span class="text-slate-600 font-medium">None</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="pt-2">
                            <a href="{{ route('buyer.properties.show', $comp->p2_id) }}" class="w-full block py-2 bg-slate-950 hover:bg-slate-900 border border-slate-850 hover:border-emerald-500 rounded-xl text-center text-xs font-bold text-slate-300 hover:text-white transition duration-200">
                                View Listing Details
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        @empty
            <div class="py-16 text-center glass-panel rounded-3xl">
                <i class="fa-solid fa-code-compare text-4xl text-slate-700 mb-3"></i>
                <h4 class="font-outfit font-bold text-slate-300">No Comparisons Created</h4>
                <p class="text-xs text-slate-500 mt-1">Open property listing details and select another available listing to compare specs side-by-side.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
