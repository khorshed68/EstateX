@extends('layouts.owner')

@section('page_title', 'Owner Portfolio Dashboard')

@section('content')
<div class="space-y-8">

    <!-- Metrics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- Total Properties -->
        <div class="glass-panel p-6 rounded-3xl flex items-center justify-between border border-amber-500/10">
            <div>
                <span class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1">Listed Properties</span>
                <span class="font-outfit font-black text-3xl text-slate-200">{{ $totalProperties }}</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-500">
                <i class="fa-solid fa-house-circle-check text-xl"></i>
            </div>
        </div>

        <!-- Portfolio Value -->
        <div class="glass-panel p-6 rounded-3xl flex items-center justify-between border border-orange-500/10">
            <div>
                <span class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1">Portfolio Valuation</span>
                <span class="font-outfit font-black text-2xl text-amber-400">৳{{ number_format($portfolioValue) }}</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-orange-500/10 border border-orange-500/20 flex items-center justify-center text-orange-500">
                <i class="fa-solid fa-sack-dollar text-xl"></i>
            </div>
        </div>

        <!-- Bookings Scheduled -->
        <div class="glass-panel p-6 rounded-3xl flex items-center justify-between border border-yellow-500/10">
            <div>
                <span class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1">Scheduled Visits & Bookings</span>
                <span class="font-outfit font-black text-3xl text-slate-200">{{ $totalBookings }}</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-yellow-500/10 border border-yellow-500/20 flex items-center justify-center text-yellow-500">
                <i class="fa-solid fa-calendar-days text-xl"></i>
            </div>
        </div>

    </div>

    <!-- Listings Section Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h3 class="font-outfit font-bold text-xl text-slate-200">My Property Listings</h3>
            <p class="text-xs text-slate-500 mt-1">Manage active listings, prices, specifications, and assigned agents.</p>
        </div>
        <a href="{{ route('owner.properties.create') }}" class="px-5 py-2.5 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-400 hover:to-orange-500 text-white text-xs font-bold rounded-xl shadow-md shadow-amber-500/10 transition duration-200 flex items-center gap-2">
            <i class="fa-solid fa-circle-plus"></i>
            List New Property
        </a>
    </div>

    <!-- Properties Table / List -->
    <div class="glass-panel rounded-3xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-800 bg-slate-900/40 text-[10px] font-semibold text-slate-400 uppercase tracking-wider">
                        <th class="p-5">Property Details</th>
                        <th class="p-5">Type & Price</th>
                        <th class="p-5">Specifications</th>
                        <th class="p-5">Status</th>
                        <th class="p-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 text-xs">
                    @forelse($properties as $prop)
                        <tr class="hover:bg-slate-900/25 transition duration-150">
                            <!-- Image and Title -->
                            <td class="p-5 flex items-center gap-4">
                                <div class="w-14 h-14 rounded-xl bg-slate-950 border border-slate-850 overflow-hidden shrink-0">
                                    @if($prop->main_image)
                                        <img src="{{ $prop->main_image }}" alt="Property" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex flex-col items-center justify-center text-slate-700">
                                            <i class="fa-solid fa-image text-lg"></i>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-200 line-clamp-1">{{ $prop->title }}</h4>
                                    <span class="text-[10px] text-slate-500 flex items-center gap-1 mt-1">
                                        <i class="fa-solid fa-location-dot text-amber-500"></i>
                                        {{ $prop->areaname }}, {{ $prop->city }}
                                    </span>
                                </div>
                            </td>

                            <!-- Type & Price -->
                            <td class="p-5">
                                <span class="px-2 py-0.5 rounded bg-slate-800 text-[10px] text-slate-400 font-semibold uppercase tracking-wider block w-fit mb-1">
                                    {{ $prop->typename }}
                                </span>
                                <span class="font-outfit font-black text-sm text-amber-400">৳{{ number_format($prop->price) }}</span>
                            </td>

                            <!-- Specs -->
                            <td class="p-5 text-slate-300">
                                <div class="flex flex-col gap-0.5">
                                    <span>{{ $prop->bedrooms }} Beds &bull; {{ $prop->bathrooms }} Baths</span>
                                    <span class="text-slate-500 text-[10px]">{{ number_format($prop->areasize) }} Sq Ft &bull; {{ ucfirst($prop->furnishedstatus) }}</span>
                                </div>
                            </td>

                            <!-- Status Badge -->
                            <td class="p-5">
                                @if($prop->status === 'available')
                                    <span class="px-2 py-1 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-[10px] font-bold text-emerald-400 uppercase tracking-wide">
                                        Available
                                    </span>
                                @else
                                    <span class="px-2 py-1 rounded-lg bg-indigo-500/10 border border-indigo-500/20 text-[10px] font-bold text-indigo-400 uppercase tracking-wide">
                                        {{ ucfirst($prop->status) }}
                                    </span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="p-5 text-right">
                                <div class="flex justify-end items-center gap-2">
                                    <a href="{{ route('owner.properties.edit', $prop->id) }}" class="p-2 border border-slate-800 hover:border-slate-700 hover:bg-slate-800 rounded-xl text-slate-400 hover:text-white transition duration-200" title="Edit Listing">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </a>
                                    
                                    <form action="{{ route('owner.properties.delete', $prop->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to permanently delete this property listing?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 border border-red-500/10 hover:border-red-500/30 hover:bg-red-500/10 rounded-xl text-red-500 hover:text-red-400 transition duration-200" title="Delete Listing">
                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-16 text-center text-slate-500">
                                <i class="fa-solid fa-house-laptop text-4xl text-slate-700 mb-3"></i>
                                <h4 class="font-outfit font-bold text-slate-400">No Properties Listed Yet</h4>
                                <p class="text-xs text-slate-600 mt-1">Get started by creating your first property listing on EstateX.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
