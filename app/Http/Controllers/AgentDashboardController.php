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

    /**
     * Display Agent sales & commission analytics dashboard.
     */
    public function analytics()
    {
        $agentId = session('agent_id');

        // Total Closed Sales Volume
        $salesResult = DB::select("
            SELECT NVL(SUM(p.price), 0) AS total_sales
            FROM bookings b
            JOIN properties p ON b.propertyId = p.id
            WHERE b.agentId = :agentId
              AND b.bookingType = 'reservation'
              AND b.status = 'completed'
        ", ['agentId' => $agentId]);
        $totalSales = $salesResult[0]->total_sales;

        // Estimated Commission (10% rate)
        $estimatedCommission = round($totalSales * 0.10, 2);

        // Active Listings Count
        $listingsResult = DB::select("
            SELECT COUNT(*) AS active_listings
            FROM properties
            WHERE agentId = :agentId
        ", ['agentId' => $agentId]);
        $activeListings = $listingsResult[0]->active_listings;

        // Completed Deals Count
        $dealsResult = DB::select("
            SELECT COUNT(*) AS completed_deals
            FROM bookings
            WHERE agentId = :agentId
              AND status = 'completed'
        ", ['agentId' => $agentId]);
        $completedDeals = $dealsResult[0]->completed_deals;

        // Sales Pipeline Metrics
        $pipelineLeads = DB::select("SELECT COUNT(*) AS cnt FROM bookings WHERE agentId = :agentId AND status = 'pending'", ['agentId' => $agentId])[0]->cnt;
        $pipelineTours = DB::select("SELECT COUNT(*) AS cnt FROM bookings WHERE agentId = :agentId AND bookingType = 'visit' AND status = 'approved'", ['agentId' => $agentId])[0]->cnt;
        $pipelineClosed = DB::select("SELECT COUNT(*) AS cnt FROM bookings WHERE agentId = :agentId AND bookingType = 'reservation' AND status = 'completed'", ['agentId' => $agentId])[0]->cnt;

        // Commission trends group by month (last 6 months)
        $monthlyTrends = DB::select("
            SELECT TO_CHAR(b.createdAt, 'YYYY-MM') AS month_str,
                   NVL(SUM(p.price * 0.10), 0) AS commission
            FROM bookings b
            JOIN properties p ON b.propertyId = p.id
            WHERE b.agentId = :agentId
              AND b.bookingType = 'reservation'
              AND b.status = 'completed'
              AND b.createdAt >= ADD_MONTHS(SYSDATE, -6)
            GROUP BY TO_CHAR(b.createdAt, 'YYYY-MM')
            ORDER BY month_str ASC
        ", ['agentId' => $agentId]);

        return view('agent.analytics', compact(
            'totalSales',
            'estimatedCommission',
            'activeListings',
            'completedDeals',
            'pipelineLeads',
            'pipelineTours',
            'pipelineClosed',
            'monthlyTrends'
        ));
    }

    /**
     * Display unique clients active tracking CRM.
     */
    public function clients()
    {
        $agentId = session('agent_id');

        // Fetch latest booking and details for each unique buyer
        $clients = DB::select("
            SELECT * FROM (
                SELECT u.fullname AS client_name, u.email AS client_email, u.phone AS client_phone,
                       p.title AS property_title, b.bookingType, b.status, b.createdAt,
                       ROW_NUMBER() OVER (PARTITION BY u.id ORDER BY b.createdAt DESC) as rn
                FROM bookings b
                JOIN users u ON b.userId = u.id
                JOIN properties p ON b.propertyId = p.id
                WHERE b.agentId = :agentId
            ) WHERE rn = 1
            ORDER BY createdAt DESC
        ", ['agentId' => $agentId]);

        return view('agent.clients', compact('clients'));
    }

    /**
     * Display Agent calendar & availability management.
     */
    public function calendar()
    {
        $agentId = session('agent_id');

        $unavailableDates = DB::select("
            SELECT id, unavailableDate, reason
            FROM agent_availability
            WHERE agentId = :agentId
            ORDER BY unavailableDate ASC
        ", ['agentId' => $agentId]);

        return view('agent.calendar', compact('unavailableDates'));
    }

    /**
     * Store an unavailable date for the agent.
     */
    public function storeCalendar(Request $request)
    {
        $agentId = session('agent_id');

        $request->validate([
            'unavailable_date' => 'required|date',
            'reason' => 'nullable|string|max:255'
        ]);

        $dateInput = $request->input('unavailable_date');
        $reason = $request->input('reason') ?? 'Unavailable';

        $formattedDate = date('Y-m-d', strtotime($dateInput));

        // Block past dates
        if (strtotime($formattedDate) < strtotime(date('Y-m-d'))) {
            return back()->with('error', 'Cannot mark a past date as unavailable.');
        }

        // Check if date is already registered
        $existing = DB::select("
            SELECT id FROM agent_availability
            WHERE agentId = :agentId
              AND TRUNC(unavailableDate) = TO_DATE(:dateInput, 'YYYY-MM-DD')
        ", [
            'agentId' => $agentId,
            'dateInput' => $formattedDate
        ]);

        if (!empty($existing)) {
            return back()->with('error', 'The date ' . $formattedDate . ' is already marked as unavailable.');
        }

        // Insert new record
        DB::insert("
            INSERT INTO agent_availability (id, agentId, unavailableDate, reason)
            VALUES (
                (SELECT NVL(MAX(id), 0) + 1 FROM agent_availability),
                :agentId,
                TO_DATE(:dateInput, 'YYYY-MM-DD'),
                :reason
            )
        ", [
            'agentId' => $agentId,
            'dateInput' => $formattedDate,
            'reason' => $reason
        ]);

        return back()->with('success', 'Date ' . $formattedDate . ' successfully marked as unavailable.');
    }

    /**
     * Delete an unavailable date record.
     */
    public function deleteCalendar($id)
    {
        $agentId = session('agent_id');

        DB::delete("
            DELETE FROM agent_availability
            WHERE id = :id AND agentId = :agentId
        ", [
            'id' => $id,
            'agentId' => $agentId
        ]);

        return back()->with('success', 'Unavailable date successfully removed.');
    }
}
