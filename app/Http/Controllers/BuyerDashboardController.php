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
        
        $query .= " ORDER BY p.id DESC";
        
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
        
        return view('buyer.properties_index', compact('properties', 'locations', 'types', 'search', 'locationId', 'typeId', 'minPrice', 'maxPrice'));
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
        
        return view('buyer.property_show', compact('property', 'images', 'amenities', 'reviews'));
    }
}
