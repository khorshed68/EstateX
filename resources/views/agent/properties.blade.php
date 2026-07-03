@extends('layouts.agent')

@section('page_title', 'Assigned Properties')

@section('content')
    <div class="mb-6">
        <h3 class="font-outfit font-bold text-lg text-slate-200">Represented Properties</h3>
        <p class="text-xs text-slate-500 mt-0.5">Properties delegated to you by owners for client representation.</p>
    </div>

    @if(empty($properties))
        <div class="glass-panel rounded-2xl p-12 text-center">
            <div class="w-16 h-16 rounded-full bg-slate-900 flex items-center justify-center text-slate-600 mx-auto mb-4">
                <i class="fa-solid fa-house-circle-exclamation text-2xl"></i>
            </div>
            <h4 class="text-slate-400 font-semibold text-sm">No properties assigned yet</h4>
            <p class="text-xs text-slate-600 mt-1">Once property owners assign their listings to you, they will appear here.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($properties as $property)
                <div class="glass-panel rounded-2xl overflow-hidden flex flex-col justify-between border hover:border-purple-500/20 transition duration-300 group">
                    <div>
                        <!-- Image Container -->
                        <div class="relative h-44 bg-slate-950 overflow-hidden">
                            @if($property->main_image)
                                <img src="{{ asset($property->main_image) }}" alt="{{ $property->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            @else
                                <div class="w-full h-full flex flex-col items-center justify-center text-slate-700 bg-slate-900/50">
                                    <i class="fa-solid fa-image text-3xl mb-2"></i>
                                    <span class="text-[10px] uppercase font-bold tracking-wider">No Main Image</span>
                                </div>
                            @endif
                            <!-- Status badge -->
                            <div class="absolute top-3 right-3">
                                @if($property->status === 'available')
                                    <span class="px-2.5 py-1 rounded-full text-[9px] font-bold bg-green-500/10 text-green-400 border border-green-500/20 uppercase tracking-wider">Available</span>
                                @elseif($property->status === 'pending')
                                    <span class="px-2.5 py-1 rounded-full text-[9px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20 uppercase tracking-wider">Pending</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[9px] font-bold bg-slate-500/10 text-slate-400 border border-slate-500/20 uppercase tracking-wider">{{ $property->status }}</span>
                                @endif
                            </div>
                        </div>

                        <!-- Info Body -->
                        <div class="p-5">
                            <span class="text-[10px] font-bold text-purple-400 uppercase tracking-widest block">{{ $property->typename }}</span>
                            <h4 class="font-outfit font-bold text-base text-slate-200 mt-1 line-clamp-1">{{ $property->title }}</h4>
                            <p class="text-[11px] text-slate-500 mt-1 flex items-center gap-1.5">
                                <i class="fa-solid fa-location-dot"></i>
                                {{ $property->areaname }}, {{ $property->city }}
                            </p>

                            <!-- Attributes -->
                            <div class="grid grid-cols-3 gap-2 py-3.5 border-y border-slate-900 my-4 text-[10px] font-semibold text-slate-400">
                                <div class="flex items-center gap-1.5">
                                    <i class="fa-solid fa-bed text-purple-400"></i>
                                    <span>{{ $property->bedrooms ?? 0 }} Beds</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <i class="fa-solid fa-bath text-purple-400"></i>
                                    <span>{{ $property->bathrooms ?? 0 }} Baths</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <i class="fa-solid fa-maximize text-purple-400"></i>
                                    <span>{{ number_format($property->areasize) }} sqft</span>
                                </div>
                            </div>

                            <!-- Owner Info -->
                            <div>
                                <span class="text-[9px] font-bold text-slate-500 uppercase tracking-wider block">Property Owner</span>
                                <div class="flex items-center gap-2.5 mt-1.5">
                                    <div class="w-7 h-7 rounded-full bg-slate-800 flex items-center justify-center font-bold text-xs text-slate-300">
                                        {{ substr($property->owner_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <span class="text-xs font-semibold text-slate-300 block leading-tight">{{ $property->owner_name }}</span>
                                        <span class="text-[10px] text-slate-500 block leading-none mt-0.5">{{ $property->owner_email }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Price Footer -->
                    <div class="px-5 py-4 border-t border-slate-900 bg-slate-950/40 flex justify-between items-center">
                        <span class="text-xs text-slate-500">Value Portfolio</span>
                        <span class="font-outfit font-extrabold text-sm text-white">৳{{ number_format($property->price) }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
