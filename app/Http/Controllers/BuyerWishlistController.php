<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BuyerWishlistController extends Controller
{
    /**
     * Display the user's wishlist.
     */
    public function index()
    {
        $userId = session('buyer_user_id');
        
        $properties = DB::select("
            SELECT p.*, l.areaName, l.city, pt.typeName, w.id AS wishlist_id,
                   (SELECT imagePath FROM property_images WHERE propertyId = p.id AND isMain = 1 AND ROWNUM = 1) AS main_image
            FROM wishlist w
            JOIN properties p ON w.propertyId = p.id
            JOIN locations l ON p.locationId = l.id
            JOIN property_types pt ON p.typeId = pt.id
            WHERE w.userId = :userId
            ORDER BY w.createdAt DESC
        ", ['userId' => $userId]);
        
        return view('buyer.wishlist', compact('properties'));
    }

    /**
     * Add a property to the user's wishlist.
     */
    public function add($id)
    {
        $userId = session('buyer_user_id');
        
        // Verify it isn't already wishlisted
        $existing = DB::select("SELECT id FROM wishlist WHERE userId = :userId AND propertyId = :propertyId", [
            'userId' => $userId,
            'propertyId' => $id
        ]);
        
        if (!empty($existing)) {
            return back()->with('info', 'Property is already in your wishlist.');
        }
        
        try {
            // Get next ID manually (since schema doesn't specify an auto-increment trigger)
            $nextIdResult = DB::select("SELECT NVL(MAX(id), 0) + 1 AS next_id FROM wishlist");
            $nextId = $nextIdResult[0]->next_id;
            
            DB::insert("INSERT INTO wishlist (id, userId, propertyId) VALUES (:id, :userId, :propertyId)", [
                'id' => $nextId,
                'userId' => $userId,
                'propertyId' => $id
            ]);
            
            return back()->with('success', 'Property has been added to your wishlist.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error adding to wishlist: ' . $e->getMessage());
        }
    }

    /**
     * Remove a property from the user's wishlist.
     */
    public function remove($id)
    {
        $userId = session('buyer_user_id');
        
        try {
            DB::delete("DELETE FROM wishlist WHERE userId = :userId AND propertyId = :propertyId", [
                'userId' => $userId,
                'propertyId' => $id
            ]);
            
            return back()->with('success', 'Property removed from wishlist.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error removing from wishlist: ' . $e->getMessage());
        }
    }
}
