<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BuyerDashboardController extends Controller
{
    /**
     * Display a listing of properties for buyers to browse.
     */
    public function index(Request $request)
    {
        $userId = session('buyer_user_id');
        
        $search = $request->input('search');
        $locationId = $request->input('location_id');
        $typeId = $request->input('type_id');
        $minPrice = $request->input('min_price');
        $maxPrice = $request->input('max_price');
        
        // Build base query to select active listings
        $query = "
            SELECT p.*, l.areaName, l.city, pt.typeName,
                   (SELECT imagePath FROM property_images WHERE propertyId = p.id AND isMain = 1 AND ROWNUM = 1) AS main_image
            FROM properties p
            JOIN locations l ON p.locationId = l.id
            JOIN property_types pt ON p.typeId = pt.id
            WHERE p.status = 'available'
        ";
        
        $bindings = [];
        
        if ($search) {
            $query .= " AND (LOWER(p.title) LIKE :search OR LOWER(p.propDescription) LIKE :search)";
            $bindings['search'] = '%' . strtolower($search) . '%';
        }
        
        if ($locationId) {
            $query .= " AND p.locationId = :location_id";
            $bindings['location_id'] = $locationId;
        }
        
        if ($typeId) {
            $query .= " AND p.typeId = :type_id";
            $bindings['type_id'] = $typeId;
        }
        
        if ($minPrice) {
            $query .= " AND p.price >= :min_price";
            $bindings['min_price'] = $minPrice;
        }
        
        if ($maxPrice) {
            $query .= " AND p.price <= :max_price";
            $bindings['max_price'] = $maxPrice;
        }
        
        $sort = $request->input('sort', 'newest');
        
        if ($sort === 'price_asc') {
            $query .= " ORDER BY p.price ASC";
        } elseif ($sort === 'price_desc') {
            $query .= " ORDER BY p.price DESC";
        } elseif ($sort === 'size_asc') {
            $query .= " ORDER BY p.areaSize ASC";
        } elseif ($sort === 'size_desc') {
            $query .= " ORDER BY p.areaSize DESC";
        } else {
            $query .= " ORDER BY p.id DESC";
        }
        
        $properties = DB::select($query, $bindings);
        
        // Get user's wishlist IDs to map heart status icons
        $wishlistResult = DB::select("SELECT propertyId FROM wishlist WHERE userId = :userId", ['userId' => $userId]);
        $wishlistIds = array_column($wishlistResult, 'propertyid'); // mapped to lowercase
        
        foreach ($properties as $prop) {
            $prop->is_wishlisted = in_array($prop->id, $wishlistIds);
        }
        
        // Fetch filter options using raw SQL
        $locations = DB::select("SELECT id, areaName, city FROM locations ORDER BY city ASC, areaName ASC");
        $types = DB::select("SELECT id, typeName FROM property_types ORDER BY typeName ASC");
        
        return view('buyer.properties_index', compact('properties', 'locations', 'types', 'search', 'locationId', 'typeId', 'minPrice', 'maxPrice', 'sort'));
    }

    /**
     * Display a single property details.
     */
    public function show($id)
    {
        $userId = session('buyer_user_id');
        
        // Fetch property details via raw join query
        $propertyData = DB::select("
            SELECT p.*, l.areaName, l.city, l.country, pt.typeName,
                   u.fullname AS agent_name, u.email AS agent_email, u.phone AS agent_phone, ag.id AS agent_id, ag.agencyName, ag.rating AS agent_rating
            FROM properties p
            JOIN locations l ON p.locationId = l.id
            JOIN property_types pt ON p.typeId = pt.id
            LEFT JOIN agents ag ON p.agentId = ag.id
            LEFT JOIN users u ON ag.userId = u.id
            WHERE p.id = :id AND p.status = 'available'
        ", ['id' => $id]);
        
        if (empty($propertyData)) {
            abort(404, 'Property listing not found.');
        }
        
        $property = $propertyData[0];
        
        // Fetch images
        $images = DB::select("SELECT * FROM property_images WHERE propertyId = :id ORDER BY displayOrder ASC", ['id' => $id]);
        
        // Fetch amenities
        $amenities = DB::select("
            SELECT a.* 
            FROM amenities a 
            JOIN property_amenities pa ON a.id = pa.amenityId 
            WHERE pa.propertyId = :id
            ORDER BY a.amenityName ASC
        ", ['id' => $id]);
        
        // Fetch reviews
        $reviews = DB::select("
            SELECT r.*, u.fullname AS user_name 
            FROM reviews r 
            JOIN users u ON r.userId = u.id 
            WHERE r.propertyId = :id 
            ORDER BY r.createdAt DESC
        ", ['id' => $id]);
        
        // Check if wishlisted
        $wishlistedCount = DB::select("SELECT COUNT(*) AS cnt FROM wishlist WHERE userId = :userId AND propertyId = :propertyId", [
            'userId' => $userId,
            'propertyId' => $id
        ]);
        $property->is_wishlisted = (!empty($wishlistedCount) && $wishlistedCount[0]->cnt > 0);
        
        // Fetch all other available properties in the system for comparison dropdown selection
        $comparisonProperties = DB::select("
            SELECT id, title, price 
            FROM properties 
            WHERE id != :id AND status = 'available' 
            ORDER BY title ASC
        ", ['id' => $id]);
        
        return view('buyer.property_show', compact('property', 'images', 'amenities', 'reviews', 'comparisonProperties'));
    }

    /**
     * Display the buyer's saved property comparisons.
     */
    public function comparisons(Request $request)
    {
        $userId = session('buyer_user_id');

        $comparisons = DB::select("
            SELECT c.id AS comparison_id, c.createdAt AS compared_at,
                   p1.id AS p1_id, p1.title AS p1_title, p1.price AS p1_price, p1.areaSize AS p1_areasize, p1.bedrooms AS p1_bedrooms, p1.bathrooms AS p1_bathrooms, p1.furnishedStatus AS p1_furnishedstatus, p1.parking AS p1_parking, p1.balcony AS p1_balcony, p1.lift AS p1_lift, p1.swimmingPool AS p1_swimmingpool, p1.petFriendly AS p1_petfriendly, p1.status AS p1_status,
                   (SELECT imagePath FROM property_images WHERE propertyId = p1.id AND isMain = 1 AND ROWNUM = 1) AS p1_image,
                   p2.id AS p2_id, p2.title AS p2_title, p2.price AS p2_price, p2.areaSize AS p2_areasize, p2.bedrooms AS p2_bedrooms, p2.bathrooms AS p2_bathrooms, p2.furnishedStatus AS p2_furnishedstatus, p2.parking AS p2_parking, p2.balcony AS p2_balcony, p2.lift AS p2_lift, p2.swimmingPool AS p2_swimmingpool, p2.petFriendly AS p2_petfriendly, p2.status AS p2_status,
                   (SELECT imagePath FROM property_images WHERE propertyId = p2.id AND isMain = 1 AND ROWNUM = 1) AS p2_image
            FROM comparisons c
            JOIN properties p1 ON c.propertyId1 = p1.id
            JOIN properties p2 ON c.propertyId2 = p2.id
            WHERE c.userId = :userId
            ORDER BY c.createdAt DESC
        ", ['userId' => $userId]);

        return view('buyer.comparisons', compact('comparisons'));
    }

    /**
     * Add a pair of properties to comparison list.
     */
    public function addComparison(Request $request)
    {
        $request->validate([
            'property_id_1' => 'required|integer',
            'property_id_2' => 'required|integer|different:property_id_1'
        ]);

        $userId = session('buyer_user_id');
        $p1 = $request->input('property_id_1');
        $p2 = $request->input('property_id_2');

        // Check if both properties exist and are active
        $check1 = DB::select("SELECT id FROM properties WHERE id = :id AND status = 'available'", ['id' => $p1]);
        $check2 = DB::select("SELECT id FROM properties WHERE id = :id AND status = 'available'", ['id' => $p2]);

        if (empty($check1) || empty($check2)) {
            return back()->with('error', 'One or both of the selected properties are no longer available.');
        }

        // Check if this pair (in either direction) is already in the comparison list
        $existing = DB::select("
            SELECT id FROM comparisons 
            WHERE userId = :userId 
              AND ((propertyId1 = :p1 AND propertyId2 = :p2) OR (propertyId1 = :p2 AND propertyId2 = :p1))
        ", ['userId' => $userId, 'p1' => $p1, 'p2' => $p2]);

        if (!empty($existing)) {
            return redirect()->route('buyer.comparisons')->with('info', 'These properties are already compared in your list.');
        }

        try {
            // Generate next sequence ID manually using NVL MAX
            $nextIdResult = DB::select("SELECT NVL(MAX(id), 0) + 1 AS next_id FROM comparisons");
            $nextId = $nextIdResult[0]->next_id;

            DB::insert("
                INSERT INTO comparisons (id, userId, propertyId1, propertyId2)
                VALUES (:id, :userId, :p1, :p2)
            ", [
                'id' => $nextId,
                'userId' => $userId,
                'p1' => $p1,
                'p2' => $p2
            ]);

            return redirect()->route('buyer.comparisons')->with('success', 'Properties added to comparison list.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to add properties to comparison list: ' . $e->getMessage());
        }
    }

    /**
     * Remove a comparison record.
     */
    public function removeComparison($id)
    {
        $userId = session('buyer_user_id');

        try {
            DB::delete("DELETE FROM comparisons WHERE id = :id AND userId = :userId", [
                'id' => $id,
                'userId' => $userId
            ]);

            return back()->with('success', 'Comparison listing removed.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to remove comparison listing: ' . $e->getMessage());
        }
    }
}
