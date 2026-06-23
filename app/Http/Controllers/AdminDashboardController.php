<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    /**
     * Show the main admin dashboard with statistics and trends.
     */
    public function index()
    {
        // 1. Fetch KPI metrics using PL/SQL Stored Procedure with Output parameters
        $totalUsers = 0;
        $totalListings = 0;
        $totalRevenue = 0;
        $totalBookings = 0;
        $successRate = 0.00;

        try {
            $pdo = DB::getPdo();
            $stmt = $pdo->prepare("
                BEGIN 
                    PKG_ESTATEX_ADMIN.get_dashboard_summary_kpis(
                        :total_users, 
                        :total_listings, 
                        :total_revenue, 
                        :total_bookings, 
                        :success_rate
                    ); 
                END;
            ");
            
            $stmt->bindParam(':total_users', $totalUsers, \PDO::PARAM_INT | \PDO::PARAM_INPUT_OUTPUT, 38);
            $stmt->bindParam(':total_listings', $totalListings, \PDO::PARAM_INT | \PDO::PARAM_INPUT_OUTPUT, 38);
            $stmt->bindParam(':total_revenue', $totalRevenue, \PDO::PARAM_INT | \PDO::PARAM_INPUT_OUTPUT, 38);
            $stmt->bindParam(':total_bookings', $totalBookings, \PDO::PARAM_INT | \PDO::PARAM_INPUT_OUTPUT, 38);
            $stmt->bindParam(':success_rate', $successRate, \PDO::PARAM_STR | \PDO::PARAM_INPUT_OUTPUT, 38);
            
            $stmt->execute();
        } catch (\Exception $e) {
            // Fallback to views if PL/SQL binding encounters drivers limitations
            $summary = DB::select("SELECT * FROM v_admin_dashboard_summary");
            if (!empty($summary)) {
                $row = $summary[0];
                $totalUsers = $row->total_users;
                $totalListings = $row->total_listings;
                $totalRevenue = $row->total_revenue;
                $totalBookings = $row->total_bookings;
                $completed = $row->completed_bookings;
                $successRate = $totalBookings > 0 ? round(($completed / $totalBookings) * 100, 2) : 0;
            }
        }

        // 2. Monthly Revenue Trend (for charts)
        $revenueTrend = DB::select("SELECT * FROM v_monthly_revenue_trend ORDER BY month ASC");

        // 3. Hot Locations
        $hotLocations = DB::select("
            SELECT * FROM (
                SELECT city, areaName, total_listings, avg_price, total_bookings_made 
                FROM v_trending_locations 
                ORDER BY total_bookings_made DESC, total_listings DESC
            ) WHERE ROWNUM <= 5
        ");

        // 4. Agent Performance Leaderboard
        $agents = DB::select("
            SELECT a.id AS agent_id,
                   u.fullname AS agent_name,
                   a.agencyName,
                   a.rating AS avg_rating,
                   (SELECT COUNT(*) FROM properties WHERE agentId = a.id) AS active_listings,
                   (SELECT COUNT(*) FROM bookings b JOIN properties p ON b.propertyId = p.id WHERE p.agentId = a.id AND b.status = 'completed') AS completed_deals,
                   NVL((SELECT SUM(t.amount) FROM transactions t JOIN bookings b ON t.bookingId = b.id JOIN properties p ON b.propertyId = p.id WHERE p.agentId = a.id AND t.status = 'completed'), 0) AS total_revenue
            FROM agents a
            JOIN users u ON a.userId = u.id
            ORDER BY total_revenue DESC, avg_rating DESC
        ");

        // 5. Trending Properties (Collaborative/Algorithmic Ranking)
        $trendingProperties = DB::select("
            SELECT * FROM (
                SELECT p.id AS property_id,
                       p.title,
                       p.price,
                       l.areaName,
                       l.city,
                       (SELECT COUNT(*) FROM wishlist WHERE propertyId = p.id) AS wishlist_count,
                       (SELECT COUNT(*) FROM bookings WHERE propertyId = p.id) AS bookings_count,
                       ((SELECT COUNT(*) FROM wishlist WHERE propertyId = p.id) * 10 + (SELECT COUNT(*) FROM bookings WHERE propertyId = p.id) * 30) AS trend_score
                FROM properties p
                JOIN locations l ON p.locationId = l.id
                ORDER BY trend_score DESC
            ) WHERE ROWNUM <= 5
        ");

        // 6. Recent Audit Logs
        $auditLogs = DB::select("
            SELECT * FROM (
                SELECT al.*, u.fullname AS admin_name 
                FROM admin_audit_logs al
                JOIN users u ON al.adminUserId = u.id
                ORDER BY al.performedAt DESC
            ) WHERE ROWNUM <= 10
        ");

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalListings',
            'totalRevenue',
            'totalBookings',
            'successRate',
            'revenueTrend',
            'hotLocations',
            'agents',
            'trendingProperties',
            'auditLogs'
        ));
    }
}
