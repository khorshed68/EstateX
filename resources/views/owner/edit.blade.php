@extends('layouts.owner')

@section('page_title', 'Edit Property Listing')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="glass-panel p-8 rounded-3xl border border-amber-500/10 mb-8">
        
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h3 class="font-outfit font-bold text-xl text-slate-200">Modify Listing details</h3>
                <p class="text-xs text-slate-500 mt-1">Update price models, specifications, amenities, or photo galleries.</p>
            </div>
            <span class="px-2 py-1 rounded bg-slate-950 border border-slate-800 text-[10px] text-amber-500 font-mono">
                Listing ID: #{{ $property->id }}
            </span>
        </div>

        <form action="{{ route('owner.properties.update', $property->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Listing Title -->
            <div>
                <label for="title" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Listing Title</label>
                <input type="text" id="title" name="title" value="{{ old('title', $property->title) }}" required placeholder="e.g. Modern 3BHK Apartment in Sonadanga" 
                       class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2.5 px-4 text-sm text-slate-200 placeholder-slate-600 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition duration-200">
                @error('title')
                    <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <!-- Property Description -->
            <div>
                <label for="prop_description" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Property Description</label>
                <textarea id="prop_description" name="prop_description" rows="4" placeholder="Describe the layout, neighborhood, safety, amenities, etc..." 
                          class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2.5 px-4 text-sm text-slate-200 placeholder-slate-600 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition duration-200">{{ old('prop_description', $property->propdescription) }}</textarea>
                @error('prop_description')
                    <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <!-- Basic Specs Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Location -->
                <div>
                    <label for="location_id" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Location Area</label>
                    <select id="location_id" name="location_id" required 
                            class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2.5 px-3 text-sm text-slate-300 focus:outline-none focus:border-amber-500 transition duration-200">
                        <option value="">Select Location</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" {{ old('location_id', $property->locationid) == $loc->id ? 'selected' : '' }}>
                                {{ $loc->areaname }}, {{ $loc->city }}
                            </option>
                        @endforeach
                    </select>
                    @error('location_id')
                        <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Property Type -->
                <div>
                    <label for="type_id" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Property Type</label>
                    <select id="type_id" name="type_id" required 
                            class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2.5 px-3 text-sm text-slate-300 focus:outline-none focus:border-amber-500 transition duration-200">
                        <option value="">Select Type</option>
                        @foreach($types as $type)
                            <option value="{{ $type->id }}" {{ old('type_id', $property->typeid) == $type->id ? 'selected' : '' }}>
                                {{ $type->typename }}
                            </option>
                        @endforeach
                    </select>
                    @error('type_id')
                        <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Price (BDT) -->
                <div>
                    <label for="price" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Price (৳ BDT)</label>
                    <input type="number" id="price" name="price" value="{{ old('price', $property->price) }}" required placeholder="e.g. 15000000" 
                           class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2.5 px-4 text-sm text-slate-200 placeholder-slate-600 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition duration-200">
                    @error('price')
                        <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Area Size (Sq Ft) -->
                <div>
                    <label for="area_size" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Area Size (Sq Ft)</label>
                    <input type="number" id="area_size" name="area_size" value="{{ old('area_size', $property->areasize) }}" required placeholder="e.g. 1800" 
                           class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2.5 px-4 text-sm text-slate-200 placeholder-slate-600 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition duration-200">
                    @error('area_size')
                        <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Detail Specs Grid -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <!-- Bedrooms -->
                <div>
                    <label for="bedrooms" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Bedrooms</label>
                    <input type="number" id="bedrooms" name="bedrooms" value="{{ old('bedrooms', $property->bedrooms) }}" required 
                           class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2.5 px-4 text-sm text-slate-200 focus:outline-none focus:border-amber-500 transition duration-200">
                </div>

                <!-- Bathrooms -->
                <div>
                    <label for="bathrooms" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Bathrooms</label>
                    <input type="number" id="bathrooms" name="bathrooms" value="{{ old('bathrooms', $property->bathrooms) }}" required 
                           class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2.5 px-4 text-sm text-slate-200 focus:outline-none focus:border-amber-500 transition duration-200">
                </div>

                <!-- Parking Slots -->
                <div>
                    <label for="parking" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Parking</label>
                    <input type="number" id="parking" name="parking" value="{{ old('parking', $property->parking) }}" required 
                           class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2.5 px-4 text-sm text-slate-200 focus:outline-none focus:border-amber-500 transition duration-200">
                </div>

                <!-- Balconies -->
                <div>
                    <label for="balcony" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Balconies</label>
                    <input type="number" id="balcony" name="balcony" value="{{ old('balcony', $property->balcony) }}" required 
                           class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2.5 px-4 text-sm text-slate-200 focus:outline-none focus:border-amber-500 transition duration-200">
                </div>
            </div>

            <!-- Dropdowns & Toggles -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Furnished Status -->
                <div>
                    <label for="furnished_status" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Furnished Status</label>
                    <select id="furnished_status" name="furnished_status" required 
                            class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2.5 px-3 text-sm text-slate-300 focus:outline-none focus:border-amber-500 transition duration-200">
                        <option value="unfurnished" {{ old('furnished_status', $property->furnishedstatus) == 'unfurnished' ? 'selected' : '' }}>Unfurnished</option>
                        <option value="semi-furnished" {{ old('furnished_status', $property->furnishedstatus) == 'semi-furnished' ? 'selected' : '' }}>Semi-Furnished</option>
                        <option value="furnished" {{ old('furnished_status', $property->furnishedstatus) == 'furnished' ? 'selected' : '' }}>Furnished</option>
                    </select>
                </div>

                <!-- Assign Agent -->
                <div>
                    <label for="agent_id" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Assign Agent (Optional)</label>
                    <select id="agent_id" name="agent_id" 
                            class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2.5 px-3 text-sm text-slate-300 focus:outline-none focus:border-amber-500 transition duration-200">
                        <option value="">No Agent (Self-Represented)</option>
                        @foreach($agents as $agent)
                            <option value="{{ $agent->id }}" {{ old('agent_id', $property->agentid) == $agent->id ? 'selected' : '' }}>
                                {{ $agent->fullname }} ({{ $agent->agencyname }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Features Checkboxes -->
            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Unit Amenities & Features</label>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    <!-- Lift -->
                    <label class="flex items-center gap-3 bg-slate-950/60 border border-slate-850 hover:border-slate-800 rounded-xl p-3 cursor-pointer">
                        <input type="checkbox" name="lift" value="1" {{ old('lift', $property->lift) == 1 ? 'checked' : '' }} class="rounded border-slate-800 text-amber-500 focus:ring-amber-500 bg-slate-900">
                        <span class="text-xs text-slate-300 font-medium"><i class="fa-solid fa-elevator text-amber-500/80 mr-1"></i> Elevator/Lift</span>
                    </label>

                    <!-- Swimming Pool -->
                    <label class="flex items-center gap-3 bg-slate-950/60 border border-slate-850 hover:border-slate-800 rounded-xl p-3 cursor-pointer">
                        <input type="checkbox" name="swimming_pool" value="1" {{ old('swimming_pool', $property->swimmingpool) == 1 ? 'checked' : '' }} class="rounded border-slate-800 text-amber-500 focus:ring-amber-500 bg-slate-900">
                        <span class="text-xs text-slate-300 font-medium"><i class="fa-solid fa-person-swimming text-amber-500/80 mr-1"></i> Pool</span>
                    </label>

                    <!-- Pet Friendly -->
                    <label class="flex items-center gap-3 bg-slate-950/60 border border-slate-850 hover:border-slate-800 rounded-xl p-3 cursor-pointer">
                        <input type="checkbox" name="pet_friendly" value="1" {{ old('pet_friendly', $property->petfriendly) == 1 ? 'checked' : '' }} class="rounded border-slate-800 text-amber-500 focus:ring-amber-500 bg-slate-900">
                        <span class="text-xs text-slate-300 font-medium"><i class="fa-solid fa-paw text-amber-500/80 mr-1"></i> Pet Friendly</span>
                    </label>
                </div>
            </div>

            <!-- Custom Database Amenities -->
            @if(!empty($amenities))
                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3 font-outfit">Additional Mapped Amenities</label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                        @foreach($amenities as $amenity)
                            <label class="flex items-center gap-3 bg-slate-950/60 border border-slate-850 hover:border-slate-800 rounded-xl p-3 cursor-pointer">
                                <input type="checkbox" name="amenities[]" value="{{ $amenity->id }}" {{ in_array($amenity->id, $activeAmenities) ? 'checked' : '' }} class="rounded border-slate-800 text-amber-500 focus:ring-amber-500 bg-slate-900">
                                <span class="text-xs text-slate-300 font-medium">{{ $amenity->amenityname }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Image Management -->
            @if(!empty($images))
                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Manage Existing Images</label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                        @foreach($images as $img)
                            <div class="relative bg-slate-950 border border-slate-850 rounded-2xl p-2 group flex flex-col justify-between">
                                <div class="w-full h-24 rounded-lg bg-slate-900 overflow-hidden mb-2">
                                    <img src="{{ $img->imagepath }}" alt="Property Gallery" class="w-full h-full object-cover">
                                </div>
                                <div class="space-y-2">
                                    <!-- Delete Checkbox -->
                                    <label class="flex items-center gap-2 cursor-pointer text-[10px] text-slate-400">
                                        <input type="checkbox" name="delete_images[]" value="{{ $img->id }}" class="rounded text-red-500 focus:ring-red-500 bg-slate-900 border-slate-800">
                                        <span>Delete Image</span>
                                    </label>
                                    
                                    <!-- Main Radio -->
                                    <label class="flex items-center gap-2 cursor-pointer text-[10px] text-slate-400">
                                        <input type="radio" name="main_image_id" value="{{ $img->id }}" {{ $img->ismain == 1 ? 'checked' : '' }} class="text-amber-500 focus:ring-amber-500 bg-slate-900 border-slate-800">
                                        <span>Set as Thumbnail</span>
                                    </label>
                                </div>
                                @if($img->ismain == 1)
                                    <span class="absolute top-4 left-4 bg-amber-500 text-slate-950 font-bold font-outfit text-[9px] uppercase tracking-wide px-2 py-0.5 rounded shadow">
                                        Main Thumbnail
                                    </span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Upload More Images -->
            <div>
                <label for="images" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Upload More Images</label>
                <div class="relative w-full bg-slate-950 border border-slate-800 rounded-xl p-6 text-center hover:border-amber-500/40 transition duration-200">
                    <input type="file" id="images" name="images[]" multiple accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                    <div class="flex flex-col items-center justify-center space-y-2">
                        <i class="fa-solid fa-cloud-arrow-up text-3xl text-slate-600"></i>
                        <span class="text-xs text-slate-300 font-semibold">Click or drag images to upload</span>
                        <span class="text-[10px] text-slate-500">Supports JPG, PNG, WEBP up to 2MB per file.</span>
                    </div>
                </div>
                @error('images')
                    <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <!-- Action buttons -->
            <div class="flex justify-end gap-3 pt-4 border-t border-slate-900">
                <a href="{{ route('owner.dashboard') }}" class="px-5 py-2.5 border border-slate-800 hover:bg-slate-800 rounded-xl text-xs font-bold text-slate-400 hover:text-white transition duration-200">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-400 hover:to-orange-500 text-white text-xs font-bold rounded-xl shadow-lg shadow-amber-500/10 transition duration-200">
                    Save Changes
                </button>
            </div>

        </form>
    </div>
</div>
@endsection
