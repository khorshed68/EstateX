<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OwnerDashboardController extends Controller
{
    /**
     * Display owner dashboard with metrics and listings.
     */
    public function index()
    {
        $ownerId = session('owner_user_id');

        // Fetch metrics using raw SQL
        $propCountResult = DB::select("SELECT COUNT(*) AS cnt FROM properties WHERE ownerId = :ownerId", ['ownerId' => $ownerId]);
        $totalProperties = $propCountResult[0]->cnt;

        $bookingCountResult = DB::select("
            SELECT COUNT(*) AS cnt 
            FROM bookings b 
            JOIN properties p ON b.propertyId = p.id 
            WHERE p.ownerId = :ownerId
        ", ['ownerId' => $ownerId]);
        $totalBookings = $bookingCountResult[0]->cnt;

        $portfolioValResult = DB::select("SELECT NVL(SUM(price), 0) AS val FROM properties WHERE ownerId = :ownerId", ['ownerId' => $ownerId]);
        $portfolioValue = $portfolioValResult[0]->val;

        // Fetch properties list
        $properties = DB::select("
            SELECT p.*, l.areaName, l.city, pt.typeName,
                   (SELECT imagePath FROM property_images WHERE propertyId = p.id AND isMain = 1 AND ROWNUM = 1) AS main_image
            FROM properties p
            JOIN locations l ON p.locationId = l.id
            JOIN property_types pt ON p.typeId = pt.id
            WHERE p.ownerId = :ownerId
            ORDER BY p.id DESC
        ", ['ownerId' => $ownerId]);

        return view('owner.dashboard', compact('totalProperties', 'totalBookings', 'portfolioValue', 'properties'));
    }
}
