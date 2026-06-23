@extends('layouts.admin')

@section('page_title', 'Real Estate Property Listings')

@section('content')
<div class="space-y-6">

    <!-- Search & Filter Card -->
    <div class="glass-panel p-6 rounded-2xl flex flex-col md:flex-row items-center justify-between gap-4">
        <div>
            <h3 class="text-lg font-bold text-slate-100">Marketplace Inventory</h3>
            <p class="text-xs text-slate-400 mt-1">Review active property listings, pricing models, and ownership.</p>
        </div>
        <form action="{{ route('admin.properties') }}" method="GET" class="w-full md:w-auto flex gap-2">
            <div class="relative w-full md:w-80">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" name="search" value="{{ $search }}" placeholder="Search by title, city, or area..." 
                       class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2.5 pl-10 pr-4 text-xs text-slate-200 placeholder-slate-600 focus:outline-none focus:border-blue-500 transition duration-200">
            </div>
            <button type="submit" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-500 rounded-xl text-xs font-bold text-white transition duration-200 shrink-0">
                Search
            </button>
            @if($search)
                <a href="{{ route('admin.properties') }}" class="px-4 py-2.5 border border-slate-850 hover:bg-slate-800 rounded-xl text-xs font-bold text-slate-400 transition duration-200 shrink-0 flex items-center justify-center">
                    Clear
                </a>
            @endif
        </form>
    </div>

    <!-- Properties Inventory Table -->
    <div class="glass-panel rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="text-slate-500 border-b border-slate-800 bg-slate-900/30">
                        <th class="p-4 font-semibold text-center w-16">ID</th>
                        <th class="p-4 font-semibold">Property Listing Title</th>
                        <th class="p-4 font-semibold">Location</th>
                        <th class="p-4 font-semibold">Price</th>
                        <th class="p-4 font-semibold">Agent Representation</th>
                        <th class="p-4 font-semibold">Status</th>
                        <th class="p-4 font-semibold text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/40 text-slate-300">
                    @forelse($properties as $prop)
                        <tr class="hover:bg-slate-850/5">
                            <td class="p-4 text-center font-bold text-slate-500">#{{ $prop->id }}</td>
                            <td class="p-4">
                                <div class="font-semibold text-slate-200 text-sm truncate max-w-[240px]" title="{{ $prop->title }}">{{ $prop->title }}</div>
                                <div class="flex items-center gap-3 text-[10px] text-slate-500 mt-1">
                                    <span><i class="fa-solid fa-layer-group text-[9px] mr-1"></i>{{ $prop->typename }}</span>
                                    <span>•</span>
                                    <span><i class="fa-solid fa-maximize text-[9px] mr-1"></i>{{ number_format($prop->areasize) }} sqft</span>
                                </div>
                            </td>
                            <td class="p-4">
                                <div class="text-slate-300">{{ $prop->areaname }}</div>
                                <div class="text-[10px] text-slate-500 mt-0.5">{{ $prop->city }}</div>
                            </td>
                            <td class="p-4 font-bold text-green-400 font-outfit text-sm">
                                ৳{{ number_format($prop->price) }}
                            </td>
                            <td class="p-4">
                                @if($prop->agent_name)
                                    <div class="text-slate-300">{{ $prop->agent_name }}</div>
                                    <div class="text-[10px] text-slate-500 mt-0.5">Assigned Agent</div>
                                @else
                                    <span class="text-slate-500 italic">Self Represented (Owner)</span>
                                @endif
                            </td>
                            <td class="p-4">
                                <span class="flex items-center gap-1.5 font-semibold 
                                    @if($prop->status === 'available') text-green-400 
                                    @elseif($prop->status === 'booked') text-yellow-400 
                                    @else text-red-400 @endif">
                                    <span class="w-1.5 h-1.5 rounded-full 
                                        @if($prop->status === 'available') bg-green-400 
                                        @elseif($prop->status === 'booked') bg-yellow-400 
                                        @else bg-red-400 @endif"></span>
                                    {{ ucfirst($prop->status) }}
                                </span>
                            </td>
                            <td class="p-4 text-center">
                                <form action="{{ route('admin.properties.delete', $prop->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete listing #{{ $prop->id }}? This will fire PL/SQL cascade constraints and write to the audit trail.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 bg-red-500/10 hover:bg-red-500/20 border border-red-500/20 hover:border-red-500/40 rounded-lg text-red-400 hover:text-red-300 font-bold transition duration-200">
                                        <i class="fa-solid fa-trash-can mr-1"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-6 text-center text-slate-500">No properties available in directory.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
