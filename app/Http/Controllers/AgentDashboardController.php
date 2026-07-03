<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AgentDashboardController extends Controller
{
    /**
     * Display the agent dashboard metrics and recent bookings.
     */
    public function index()
    {
        $agentId = session('agent_id');

        // 1. Total Assigned Properties Count
        $propCount = DB::select("
            SELECT COUNT(*) AS cnt 
            FROM properties 
            WHERE agentId = :agentId
        ", ['agentId' => $agentId]);
        $totalProperties = $propCount[0]->cnt;

        // 2. Total Bookings / Leads Count
        $bookingCount = DB::select("
            SELECT COUNT(*) AS cnt 
            FROM bookings 
            WHERE agentId = :agentId
        ", ['agentId' => $agentId]);
        $totalBookings = $bookingCount[0]->cnt;

        // 3. Average Rating
        $ratingResult = DB::select("
            SELECT NVL(AVG(rating), 0) AS avg_rating 
            FROM agent_reviews 
            WHERE agentId = :agentId
        ", ['agentId' => $agentId]);
        $averageRating = round($ratingResult[0]->avg_rating, 2);

        // 4. Recent Bookings (top 5 visits or reservations)
        $recentBookings = DB::select("
            SELECT * FROM (
                SELECT b.*, p.title, p.price AS property_price, 
                       u.fullname AS buyer_name, u.email AS buyer_email, u.phone AS buyer_phone, 
                       l.areaName, l.city
                FROM bookings b
                JOIN properties p ON b.propertyId = p.id
                JOIN locations l ON p.locationId = l.id
                JOIN users u ON b.userId = u.id
                WHERE b.agentId = :agentId
                ORDER BY b.createdAt DESC
            ) WHERE ROWNUM <= 5
        ", ['agentId' => $agentId]);

        return view('agent.dashboard', compact(
            'totalProperties',
            'totalBookings',
            'averageRating',
            'recentBookings'
        ));
    }

    /**
     * Display reviews written about the agent.
     */
    public function reviews()
    {
        $agentId = session('agent_id');

        $reviews = DB::select("
            SELECT r.*, u.fullname AS buyer_name, u.email AS buyer_email
            FROM agent_reviews r
            JOIN users u ON r.userId = u.id
            WHERE r.agentId = :agentId
            ORDER BY r.createdAt DESC
        ", ['agentId' => $agentId]);

        return view('agent.reviews', compact('reviews'));
    }
}
