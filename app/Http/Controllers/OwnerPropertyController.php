<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class OwnerPropertyController extends Controller
{
    /**
     * Show the form for creating a new property.
     */
    public function create()
    {
        $locations = DB::select("SELECT id, areaName, city FROM locations ORDER BY city ASC, areaName ASC");
        $types = DB::select("SELECT id, typeName FROM property_types ORDER BY typeName ASC");
        $agents = DB::select("
            SELECT a.id, u.fullname, a.agencyName 
            FROM agents a 
            JOIN users u ON a.userId = u.id 
            ORDER BY u.fullname ASC
        ");
        $amenities = DB::select("SELECT * FROM amenities ORDER BY amenityName ASC");

        return view('owner.create', compact('locations', 'types', 'agents', 'amenities'));
    }

    /**
     * Store a newly created property listing.
     */
    public function store(Request $request)
    {
        $ownerId = session('owner_user_id');

        $request->validate([
            'title' => 'required|string|max:255',
            'prop_description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'area_size' => 'required|numeric|min:0',
            'bedrooms' => 'required|integer|min:0',
            'bathrooms' => 'required|integer|min:0',
            'location_id' => 'required|integer',
            'type_id' => 'required|integer',
            'furnished_status' => 'required|string|in:furnished,semi-furnished,unfurnished',
            'parking' => 'required|integer|min:0',
            'balcony' => 'required|integer|min:0',
            'lift' => 'required|integer|in:0,1',
            'swimming_pool' => 'required|integer|in:0,1',
            'pet_friendly' => 'required|integer|in:0,1',
            'agent_id' => 'nullable|integer',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'amenities' => 'nullable|array',
            'amenities.*' => 'integer',
        ]);

        try {
            DB::beginTransaction();

            // Generate next property ID
            $nextIdResult = DB::select("SELECT NVL(MAX(id), 0) + 1 AS next_id FROM properties");
            $propertyId = $nextIdResult[0]->next_id;

            // Insert property
            DB::insert("
                INSERT INTO properties (
                    id, ownerId, agentId, typeId, locationId, title, propDescription, price, 
                    areaSize, bedrooms, bathrooms, furnishedStatus, parking, balcony, lift, swimmingPool, petFriendly, status
                ) VALUES (
                    :id, :ownerId, :agentId, :typeId, :locationId, :title, :propDescription, :price,
                    :areaSize, :bedrooms, :bathrooms, :furnishedStatus, :parking, :balcony, :lift, :swimmingPool, :petFriendly, 'available'
                )
            ", [
                'id' => $propertyId,
                'ownerId' => $ownerId,
                'agentId' => $request->input('agent_id'),
                'typeId' => $request->input('type_id'),
                'locationId' => $request->input('location_id'),
                'title' => $request->input('title'),
                'propDescription' => $request->input('prop_description'),
                'price' => $request->input('price'),
                'areaSize' => $request->input('area_size'),
                'bedrooms' => $request->input('bedrooms'),
                'bathrooms' => $request->input('bathrooms'),
                'furnishedStatus' => $request->input('furnished_status'),
                'parking' => $request->input('parking'),
                'balcony' => $request->input('balcony'),
                'lift' => $request->input('lift'),
                'swimmingPool' => $request->input('swimming_pool'),
                'petFriendly' => $request->input('pet_friendly')
            ]);

            // Save Amenities
            if ($request->has('amenities')) {
                foreach ($request->input('amenities') as $amenityId) {
                    DB::insert("
                        INSERT INTO property_amenities (propertyId, amenityId) 
                        VALUES (:propertyId, :amenityId)
                    ", [
                        'propertyId' => $propertyId,
                        'amenityId' => $amenityId
                    ]);
                }
            }

            // Save Images
            if ($request->hasFile('images')) {
                $isFirst = true;
                $displayOrder = 1;
                foreach ($request->file('images') as $imageFile) {
                    $filename = time() . '_' . uniqid() . '.' . $imageFile->getClientOriginalExtension();
                    
                    // Create directory if it doesn't exist
                    $destPath = public_path('uploads/properties');
                    if (!File::exists($destPath)) {
                        File::makeDirectory($destPath, 0755, true, true);
                    }

                    $imageFile->move($destPath, $filename);
                    $dbPath = '/uploads/properties/' . $filename;

                    // Get next image ID
                    $nextImgIdResult = DB::select("SELECT NVL(MAX(id), 0) + 1 AS next_id FROM property_images");
                    $imageId = $nextImgIdResult[0]->next_id;

                    DB::insert("
                        INSERT INTO property_images (id, propertyId, imagePath, isMain, displayOrder) 
                        VALUES (:id, :propertyId, :imagePath, :isMain, :displayOrder)
                    ", [
                        'id' => $imageId,
                        'propertyId' => $propertyId,
                        'imagePath' => $dbPath,
                        'isMain' => $isFirst ? 1 : 0,
                        'displayOrder' => $displayOrder++
                    ]);

                    $isFirst = false;
                }
            }

            DB::commit();
            return redirect()->route('owner.dashboard')->with('success', 'Property listed successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to save property listing: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show the form for editing the specified property.
     */
    public function edit($id)
    {
        $ownerId = session('owner_user_id');

        $properties = DB::select("SELECT * FROM properties WHERE id = :id AND ownerId = :ownerId", [
            'id' => $id,
            'ownerId' => $ownerId
        ]);

        if (empty($properties)) {
            abort(404, 'Property not found.');
        }

        $property = $properties[0];

        $locations = DB::select("SELECT id, areaName, city FROM locations ORDER BY city ASC, areaName ASC");
        $types = DB::select("SELECT id, typeName FROM property_types ORDER BY typeName ASC");
        $agents = DB::select("
            SELECT a.id, u.fullname, a.agencyName 
            FROM agents a 
            JOIN users u ON a.userId = u.id 
            ORDER BY u.fullname ASC
        ");
        $amenities = DB::select("SELECT * FROM amenities ORDER BY amenityName ASC");

        // Fetch property's mapped amenities
        $activeAmenitiesResult = DB::select("SELECT amenityId FROM property_amenities WHERE propertyId = :id", ['id' => $id]);
        $activeAmenities = array_column($activeAmenitiesResult, 'amenityid');

        // Fetch property images
        $images = DB::select("SELECT * FROM property_images WHERE propertyId = :id ORDER BY displayOrder ASC", ['id' => $id]);

        return view('owner.edit', compact('property', 'locations', 'types', 'agents', 'amenities', 'activeAmenities', 'images'));
    }

    /**
     * Update the specified property.
     */
    public function update(Request $request, $id)
    {
        $ownerId = session('owner_user_id');

        // Verify ownership
        $properties = DB::select("SELECT id FROM properties WHERE id = :id AND ownerId = :ownerId", [
            'id' => $id,
            'ownerId' => $ownerId
        ]);

        if (empty($properties)) {
            abort(404, 'Property not found.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'prop_description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'area_size' => 'required|numeric|min:0',
            'bedrooms' => 'required|integer|min:0',
            'bathrooms' => 'required|integer|min:0',
            'location_id' => 'required|integer',
            'type_id' => 'required|integer',
            'furnished_status' => 'required|string|in:furnished,semi-furnished,unfurnished',
            'parking' => 'required|integer|min:0',
            'balcony' => 'required|integer|min:0',
            'lift' => 'required|integer|in:0,1',
            'swimming_pool' => 'required|integer|in:0,1',
            'pet_friendly' => 'required|integer|in:0,1',
            'agent_id' => 'nullable|integer',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'amenities' => 'nullable|array',
            'amenities.*' => 'integer',
            'delete_images' => 'nullable|array',
            'delete_images.*' => 'integer',
            'main_image_id' => 'nullable|integer',
        ]);

        try {
            DB::beginTransaction();

            // Update property listing details
            DB::update("
                UPDATE properties SET 
                    agentId = :agentId,
                    typeId = :typeId,
                    locationId = :locationId,
                    title = :title,
                    propDescription = :propDescription,
                    price = :price,
                    areaSize = :areaSize,
                    bedrooms = :bedrooms,
                    bathrooms = :bathrooms,
                    furnishedStatus = :furnishedStatus,
                    parking = :parking,
                    balcony = :balcony,
                    lift = :lift,
                    swimmingPool = :swimmingPool,
                    petFriendly = :petFriendly,
                    updatedAt = CURRENT_TIMESTAMP
                WHERE id = :id AND ownerId = :ownerId
            ", [
                'id' => $id,
                'ownerId' => $ownerId,
                'agentId' => $request->input('agent_id'),
                'typeId' => $request->input('type_id'),
                'locationId' => $request->input('location_id'),
                'title' => $request->input('title'),
                'propDescription' => $request->input('prop_description'),
                'price' => $request->input('price'),
                'areaSize' => $request->input('area_size'),
                'bedrooms' => $request->input('bedrooms'),
                'bathrooms' => $request->input('bathrooms'),
                'furnishedStatus' => $request->input('furnished_status'),
                'parking' => $request->input('parking'),
                'balcony' => $request->input('balcony'),
                'lift' => $request->input('lift'),
                'swimmingPool' => $request->input('swimming_pool'),
                'petFriendly' => $request->input('pet_friendly')
            ]);

            // Sync Amenities: delete old and insert new
            DB::delete("DELETE FROM property_amenities WHERE propertyId = :id", ['id' => $id]);
            if ($request->has('amenities')) {
                foreach ($request->input('amenities') as $amenityId) {
                    DB::insert("
                        INSERT INTO property_amenities (propertyId, amenityId) 
                        VALUES (:propertyId, :amenityId)
                    ", [
                        'propertyId' => $id,
                        'amenityId' => $amenityId
                    ]);
                }
            }

            // Handle deleted images
            if ($request->has('delete_images')) {
                foreach ($request->input('delete_images') as $imgId) {
                    $imgResult = DB::select("SELECT imagePath FROM property_images WHERE id = :id AND propertyId = :propertyId", [
                        'id' => $imgId,
                        'propertyId' => $id
                    ]);
                    if (!empty($imgResult)) {
                        $fullPath = public_path($imgResult[0]->imagepath);
                        if (File::exists($fullPath)) {
                            File::delete($fullPath);
                        }
                        DB::delete("DELETE FROM property_images WHERE id = :id", ['id' => $imgId]);
                    }
                }
            }

            // Handle upload of new images
            if ($request->hasFile('images')) {
                // Get current maximum display order
                $maxOrderResult = DB::select("SELECT NVL(MAX(displayOrder), 0) AS max_order FROM property_images WHERE propertyId = :id", ['id' => $id]);
                $displayOrder = $maxOrderResult[0]->max_order + 1;

                foreach ($request->file('images') as $imageFile) {
                    $filename = time() . '_' . uniqid() . '.' . $imageFile->getClientOriginalExtension();
                    $destPath = public_path('uploads/properties');
                    if (!File::exists($destPath)) {
                        File::makeDirectory($destPath, 0755, true, true);
                    }
                    $imageFile->move($destPath, $filename);
                    $dbPath = '/uploads/properties/' . $filename;

                    $nextImgIdResult = DB::select("SELECT NVL(MAX(id), 0) + 1 AS next_id FROM property_images");
                    $imageId = $nextImgIdResult[0]->next_id;

                    DB::insert("
                        INSERT INTO property_images (id, propertyId, imagePath, isMain, displayOrder) 
                        VALUES (:id, :propertyId, :imagePath, 0, :displayOrder)
                    ", [
                        'id' => $imageId,
                        'propertyId' => $id,
                        'imagePath' => $dbPath,
                        'displayOrder' => $displayOrder++
                    ]);
                }
            }

            // Handle setting an image as main
            $mainImageId = $request->input('main_image_id');
            if ($mainImageId) {
                // Remove main status from all images of this property
                DB::update("UPDATE property_images SET isMain = 0 WHERE propertyId = :id", ['id' => $id]);
                // Set the designated image as main
                DB::update("UPDATE property_images SET isMain = 1 WHERE id = :id AND propertyId = :propertyId", [
                    'id' => $mainImageId,
                    'propertyId' => $id
                ]);
            } else {
                // Ensure at least one image remains designated as main if any images exist
                $mainCount = DB::select("SELECT COUNT(*) AS cnt FROM property_images WHERE propertyId = :id AND isMain = 1", ['id' => $id]);
                if ($mainCount[0]->cnt == 0) {
                    $anyImage = DB::select("SELECT id FROM property_images WHERE propertyId = :id AND ROWNUM = 1", ['id' => $id]);
                    if (!empty($anyImage)) {
                        DB::update("UPDATE property_images SET isMain = 1 WHERE id = :id", ['id' => $anyImage[0]->id]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('owner.dashboard')->with('success', 'Property listing updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update property listing: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Delete the specified property listing.
     */
    public function destroy($id)
    {
        $ownerId = session('owner_user_id');

        // Check ownership
        $properties = DB::select("SELECT id FROM properties WHERE id = :id AND ownerId = :ownerId", [
            'id' => $id,
            'ownerId' => $ownerId
        ]);

        if (empty($properties)) {
            abort(404, 'Property not found.');
        }

        try {
            DB::beginTransaction();

            // Fetch and delete physical image files
            $images = DB::select("SELECT imagePath FROM property_images WHERE propertyId = :id", ['id' => $id]);
            foreach ($images as $img) {
                $fullPath = public_path($img->imagepath);
                if (File::exists($fullPath)) {
                    File::delete($fullPath);
                }
            }

            // Note: FK constraints on propertyId in property_images, property_amenities, and bookings have ON DELETE CASCADE.
            // But we delete them manually to be clean and prevent any issues
            DB::delete("DELETE FROM property_images WHERE propertyId = :id", ['id' => $id]);
            DB::delete("DELETE FROM property_amenities WHERE propertyId = :id", ['id' => $id]);
            DB::delete("DELETE FROM wishlist WHERE propertyId = :id", ['id' => $id]);
            
            // Delete the listing itself
            DB::delete("DELETE FROM properties WHERE id = :id AND ownerId = :ownerId", [
                'id' => $id,
                'ownerId' => $ownerId
            ]);

            DB::commit();
            return redirect()->route('owner.dashboard')->with('success', 'Property listing deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete listing: ' . $e->getMessage());
        }
    }
}
