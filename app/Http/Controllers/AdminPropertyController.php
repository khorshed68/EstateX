<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class AdminPropertyController extends Controller
{
    /**
     * Display a listing of properties.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        if ($search) {
            $properties = DB::select("
                SELECT p.*, u.fullname AS owner_name, ag_u.fullname AS agent_name, 
                       l.areaName, l.city, pt.typeName
                FROM properties p
                JOIN users u ON p.ownerId = u.id
                LEFT JOIN agents ag ON p.agentId = ag.id
                LEFT JOIN users ag_u ON ag.userId = ag_u.id
                JOIN locations l ON p.locationId = l.id
                JOIN property_types pt ON p.typeId = pt.id
                WHERE (LOWER(p.title) LIKE :search OR LOWER(l.city) LIKE :search OR LOWER(l.areaName) LIKE :search)
                ORDER BY p.id ASC
            ", ['search' => '%' . strtolower($search) . '%']);
        } else {
            $properties = DB::select("
                SELECT p.*, u.fullname AS owner_name, ag_u.fullname AS agent_name, 
                       l.areaName, l.city, pt.typeName
                FROM properties p
                JOIN users u ON p.ownerId = u.id
                LEFT JOIN agents ag ON p.agentId = ag.id
                LEFT JOIN users ag_u ON ag.userId = ag_u.id
                JOIN locations l ON p.locationId = l.id
                JOIN property_types pt ON p.typeId = pt.id
                ORDER BY p.id ASC
            ");
        }

        return view('admin.properties', compact('properties', 'search'));
    }

    /**
     * Show form to list a new property.
     */
    public function create()
    {
        $locations = DB::select("SELECT id, areaName, city FROM locations ORDER BY city ASC, areaName ASC");
        $types = DB::select("SELECT id, typeName FROM property_types ORDER BY typeName ASC");
        $agents = DB::select("
            SELECT ag.id, u.fullname, ag.agencyName 
            FROM agents ag 
            JOIN users u ON ag.userId = u.id 
            ORDER BY u.fullname ASC
        ");
        $owners = DB::select("SELECT id, fullname, email FROM users WHERE roleId = 4 ORDER BY fullname ASC");
        $amenities = DB::select("SELECT * FROM amenities ORDER BY amenityName ASC");

        return view('admin.properties_form', compact('locations', 'types', 'agents', 'owners', 'amenities'));
    }

    /**
     * Store a newly created property.
     */
    public function store(Request $request)
    {
        $request->validate([
            'owner_id' => 'required|integer',
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

        $adminId = session('admin_user_id');

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
                'ownerId' => $request->input('owner_id'),
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

            // Log administrative action
            DB::insert("
                INSERT INTO admin_audit_logs (adminUserId, actionName, tableName, recordId, oldValues, newValues) 
                VALUES (:adminId, 'PROPERTY_CREATE', 'PROPERTIES', :recordId, NULL, :newValues)
            ", [
                'adminId' => $adminId,
                'recordId' => $propertyId,
                'newValues' => 'Title: ' . $request->input('title') . ', Price: ' . $request->input('price')
            ]);

            DB::commit();
            return redirect()->route('admin.properties')->with('success', 'Property listed successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to save property listing: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show form to edit an existing property.
     */
    public function edit($id)
    {
        $properties = DB::select("SELECT * FROM properties WHERE id = :id", ['id' => $id]);
        if (empty($properties)) {
            abort(404, 'Property not found.');
        }
        $property = $properties[0];

        $locations = DB::select("SELECT id, areaName, city FROM locations ORDER BY city ASC, areaName ASC");
        $types = DB::select("SELECT id, typeName FROM property_types ORDER BY typeName ASC");
        $agents = DB::select("
            SELECT ag.id, u.fullname, ag.agencyName 
            FROM agents ag 
            JOIN users u ON ag.userId = u.id 
            ORDER BY u.fullname ASC
        ");
        $owners = DB::select("SELECT id, fullname, email FROM users WHERE roleId = 4 ORDER BY fullname ASC");
        $amenities = DB::select("SELECT * FROM amenities ORDER BY amenityName ASC");
        
        $activeAmenitiesResult = DB::select("SELECT amenityId FROM property_amenities WHERE propertyId = :id", ['id' => $id]);
        $activeAmenities = array_column($activeAmenitiesResult, 'amenityid');
        if (empty($activeAmenities)) {
            $activeAmenities = array_column($activeAmenitiesResult, 'amenityId');
        }

        $images = DB::select("SELECT * FROM property_images WHERE propertyId = :id ORDER BY displayOrder ASC", ['id' => $id]);

        return view('admin.properties_form', compact('property', 'locations', 'types', 'agents', 'owners', 'amenities', 'activeAmenities', 'images'));
    }

    /**
     * Update an existing property.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'owner_id' => 'required|integer',
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

        $adminId = session('admin_user_id');

        $oldProp = DB::select("SELECT * FROM properties WHERE id = :id", ['id' => $id]);
        if (empty($oldProp)) {
            abort(404, 'Property not found.');
        }

        try {
            DB::beginTransaction();

            // Update property listing details
            DB::update("
                UPDATE properties SET 
                    ownerId = :ownerId,
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
                WHERE id = :id
            ", [
                'id' => $id,
                'ownerId' => $request->input('owner_id'),
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

            // Sync Amenities
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
                DB::update("UPDATE property_images SET isMain = 0 WHERE propertyId = :id", ['id' => $id]);
                DB::update("UPDATE property_images SET isMain = 1 WHERE id = :id AND propertyId = :propertyId", [
                    'id' => $mainImageId,
                    'propertyId' => $id
                ]);
            } else {
                $mainCount = DB::select("SELECT COUNT(*) AS cnt FROM property_images WHERE propertyId = :id AND isMain = 1", ['id' => $id]);
                if ($mainCount[0]->cnt == 0) {
                    $anyImage = DB::select("SELECT id FROM property_images WHERE propertyId = :id AND ROWNUM = 1", ['id' => $id]);
                    if (!empty($anyImage)) {
                        DB::update("UPDATE property_images SET isMain = 1 WHERE id = :id", ['id' => $anyImage[0]->id]);
                    }
                }
            }

            // Log action
            DB::insert("
                INSERT INTO admin_audit_logs (adminUserId, actionName, tableName, recordId, oldValues, newValues) 
                VALUES (:adminId, 'PROPERTY_UPDATE', 'PROPERTIES', :recordId, :oldValues, :newValues)
            ", [
                'adminId' => $adminId,
                'recordId' => $id,
                'oldValues' => 'Title: ' . $oldProp[0]->title . ', Price: ' . $oldProp[0]->price,
                'newValues' => 'Title: ' . $request->input('title') . ', Price: ' . $request->input('price')
            ]);

            DB::commit();
            return redirect()->route('admin.properties')->with('success', 'Property listing updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update property listing: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Delete a property listing.
     */
    public function destroy($id)
    {
        $adminId = session('admin_user_id');

        try {
            DB::statement("
                BEGIN 
                    PKG_ESTATEX_ADMIN.delete_property_listing(:propertyId, :adminId); 
                END;
            ", [
                'propertyId' => $id,
                'adminId'    => $adminId
            ]);

            return back()->with('success', 'Property listing and its historical associations deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error executing delete procedure: ' . $e->getMessage());
        }
    }

    /**
     * Update property listing status.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:available,pending,booked,sold'
        ]);

        $status = $request->input('status');
        $adminId = session('admin_user_id');

        $oldProp = DB::select("SELECT status FROM properties WHERE id = :id", ['id' => $id]);
        if (empty($oldProp)) {
            return back()->with('error', 'Property listing not found.');
        }

        try {
            DB::beginTransaction();

            DB::update("
                UPDATE properties 
                SET status = :status, updatedAt = CURRENT_TIMESTAMP 
                WHERE id = :id
            ", ['status' => $status, 'id' => $id]);

            // Log administrative action
            DB::insert("
                INSERT INTO admin_audit_logs (adminUserId, actionName, tableName, recordId, oldValues, newValues) 
                VALUES (:adminId, 'PROPERTY_STATUS_UPDATE', 'PROPERTIES', :recordId, :oldValues, :newValues)
            ", [
                'adminId' => $adminId,
                'recordId' => $id,
                'oldValues' => 'Status: ' . $oldProp[0]->status,
                'newValues' => 'Status: ' . $status
            ]);

            DB::commit();
            return back()->with('success', 'Property listing status updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update property status: ' . $e->getMessage());
        }
    }
}
