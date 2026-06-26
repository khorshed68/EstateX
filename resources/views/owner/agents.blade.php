@extends('layouts.owner')

@section('page_title', 'Manage Representing Agents')

@section('content')
<div class="space-y-8">
    
    <!-- Header -->
    <div>
        <h3 class="font-outfit font-bold text-xl text-slate-200">Agent Representation & Assignments</h3>
        <p class="text-xs text-slate-500 mt-1">Assign verified platform agents to handle client communication, tours, and unit negotiations for your properties.</p>
    </div>

    <!-- Assignments Panel -->
    <div class="glass-panel p-6 rounded-3xl border border-amber-500/10">
        <h4 class="font-outfit font-bold text-base text-slate-300 mb-4">Current Assignments</h4>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse($properties as $property)
                <div class="bg-slate-950/60 border border-slate-900 rounded-2xl p-4 flex flex-col justify-between gap-4">
                    <div>
                        <span class="text-[9px] uppercase font-bold text-slate-500 tracking-wider">Property Listing</span>
                        <h5 class="font-semibold text-slate-200 text-sm mt-0.5 line-clamp-1">{{ $property->title }}</h5>
                        <div class="text-[10px] text-slate-400 mt-1">
                            Represented by: 
                            @if($property->agent_name)
                                <span class="text-amber-400 font-bold">{{ $property->agent_name }}</span>
                            @else
                                <span class="text-slate-500 italic">Self-Represented (Owner)</span>
                            @endif
                        </div>
                    </div>
                    
                    <form action="{{ route('owner.properties.assign-agent', $property->id) }}" method="POST" class="flex gap-2 items-center">
                        @csrf
                        <select name="agent_id" class="flex-1 bg-slate-900 border border-slate-800 rounded-xl py-2 px-3 text-xs text-slate-300 focus:outline-none focus:border-amber-500 transition duration-200">
                            <option value="">No Agent (Self-Represented)</option>
                            @foreach($agents as $agent)
                                <option value="{{ $agent->agent_id }}" {{ $property->agentid == $agent->agent_id ? 'selected' : '' }}>
                                    {{ $agent->fullname }} ({{ $agent->agencyname }})
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-400 text-slate-950 rounded-xl font-bold text-[10px] transition duration-200">
                            Reassign
                        </button>
                    </form>
                </div>
            @empty
                <div class="md:col-span-2 py-8 text-center text-slate-500">
                    <i class="fa-solid fa-house-circle-exclamation text-3xl mb-2 text-slate-700"></i>
                    <h5 class="font-bold text-slate-400">No properties listed yet</h5>
                    <p class="text-[11px] text-slate-600">List properties first to assign representation.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Active Agents Directory -->
    <div>
        <h4 class="font-outfit font-bold text-base text-slate-300 mb-4">Active Platform Agents</h4>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @forelse($agents as $agent)
                <div class="glass-panel p-5 rounded-2xl flex flex-col justify-between hover:border-slate-700 transition duration-300">
                    <div>
                        <!-- Header / Agency -->
                        <div class="flex justify-between items-start mb-3">
                            <span class="px-2 py-0.5 rounded bg-amber-500/10 border border-amber-500/20 text-[9px] font-bold text-amber-400 uppercase tracking-wider">
                                {{ $agent->agencyname }}
                            </span>
                            <div class="flex items-center gap-1 text-[10px] text-yellow-500 font-bold">
                                <i class="fa-solid fa-star text-[9px]"></i>
                                <span>{{ number_format($agent->rating, 2) }}</span>
                            </div>
                        </div>

                        <!-- Name & Stats -->
                        <h5 class="font-outfit font-bold text-slate-200 text-base mb-1">{{ $agent->fullname }}</h5>
                        <p class="text-[10px] text-slate-500 mb-4">{{ $agent->experienceyears }} Years Experience</p>

                        <!-- Contact info -->
                        <div class="space-y-1.5 py-3 border-t border-slate-900 text-[10px] text-slate-400">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-envelope text-slate-600"></i>
                                <span>{{ $agent->email }}</span>
                            </div>
                            @if($agent->phone)
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-phone text-slate-600"></i>
                                    <span>{{ $agent->phone }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="md:col-span-3 py-12 text-center glass-panel rounded-2xl text-slate-500">
                    <i class="fa-solid fa-user-xmark text-4xl mb-3 text-slate-700"></i>
                    <h5 class="font-bold text-slate-400">No active agents found</h5>
                    <p class="text-xs text-slate-600">No agents are registered on the platform at the moment.</p>
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection
