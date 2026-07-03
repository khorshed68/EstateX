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

    /**
     * Display a listing of bookings for administration control.
     */
    public function bookings(Request $request)
    {
        $search = $request->input('search');

        if ($search) {
            $bookings = DB::select("
                SELECT b.*, p.title, p.price AS property_price, l.areaName, l.city, 
                       u.fullname AS buyer_name, u.email AS buyer_email, u.phone AS buyer_phone,
                       ag_u.fullname AS agent_name,
                       t.amount AS payment_amount, t.paymentMethod, t.status AS payment_status
                FROM bookings b
                JOIN properties p ON b.propertyId = p.id
                JOIN locations l ON p.locationId = l.id
                JOIN users u ON b.userId = u.id
                LEFT JOIN agents ag ON b.agentId = ag.id
                LEFT JOIN users ag_u ON ag.userId = ag_u.id
                LEFT JOIN transactions t ON t.bookingId = b.id
                WHERE (LOWER(p.title) LIKE :search OR LOWER(u.fullname) LIKE :search)
                ORDER BY b.createdAt DESC
            ", ['search' => '%' . strtolower($search) . '%']);
        } else {
            $bookings = DB::select("
                SELECT b.*, p.title, p.price AS property_price, l.areaName, l.city, 
                       u.fullname AS buyer_name, u.email AS buyer_email, u.phone AS buyer_phone,
                       ag_u.fullname AS agent_name,
                       t.amount AS payment_amount, t.paymentMethod, t.status AS payment_status
                FROM bookings b
                JOIN properties p ON b.propertyId = p.id
                JOIN locations l ON p.locationId = l.id
                JOIN users u ON b.userId = u.id
                LEFT JOIN agents ag ON b.agentId = ag.id
                LEFT JOIN users ag_u ON ag.userId = ag_u.id
                LEFT JOIN transactions t ON t.bookingId = b.id
                ORDER BY b.createdAt DESC
            ");
        }

        return view('admin.bookings', compact('bookings', 'search'));
    }

    /**
     * Moderate booking status.
     */
    public function bookingAction(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:pending,approved,rejected,completed'
        ]);

        $status = $request->input('status');
        $adminId = session('admin_user_id');

        $oldBooking = DB::select("SELECT status FROM bookings WHERE id = :id", ['id' => $id]);
        if (empty($oldBooking)) {
            return back()->with('error', 'Booking request not found.');
        }

        try {
            DB::beginTransaction();

            DB::update("
                UPDATE bookings 
                SET status = :status, updatedAt = CURRENT_TIMESTAMP 
                WHERE id = :id
            ", ['status' => $status, 'id' => $id]);

            // Log
            DB::insert("
                INSERT INTO admin_audit_logs (adminUserId, actionName, tableName, recordId, oldValues, newValues) 
                VALUES (:adminId, 'BOOKING_MODERATION', 'BOOKINGS', :recordId, :oldValues, :newValues)
            ", [
                'adminId' => $adminId,
                'recordId' => $id,
                'oldValues' => 'Status: ' . $oldBooking[0]->status,
                'newValues' => 'Status: ' . $status
            ]);

            DB::commit();
            return back()->with('success', 'Booking status updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update booking status: ' . $e->getMessage());
        }
    }

    /**
     * Delete booking request.
     */
    public function bookingDelete($id)
    {
        $adminId = session('admin_user_id');

        try {
            DB::beginTransaction();

            // Log delete action
            DB::insert("
                INSERT INTO admin_audit_logs (adminUserId, actionName, tableName, recordId, oldValues, newValues) 
                VALUES (:adminId, 'BOOKING_DELETE', 'BOOKINGS', :recordId, 'EXISTED', 'DELETED')
            ", [
                'adminId' => $adminId,
                'recordId' => $id
            ]);

            DB::delete("DELETE FROM bookings WHERE id = :id", ['id' => $id]);

            DB::commit();
            return back()->with('success', 'Booking registration cancelled and deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error deleting booking: ' . $e->getMessage());
        }
    }

    /**
     * Display a ledger of payment transactions.
     */
    public function transactions(Request $request)
    {
        $search = $request->input('search');

        if ($search) {
            $transactions = DB::select("
                SELECT t.*, b.bookingType, p.title, u.fullname AS buyer_name
                FROM transactions t
                JOIN bookings b ON t.bookingId = b.id
                JOIN properties p ON b.propertyId = p.id
                JOIN users u ON b.userId = u.id
                WHERE (LOWER(p.title) LIKE :search OR LOWER(u.fullname) LIKE :search OR LOWER(t.referenceNo) LIKE :search)
                ORDER BY t.transactionDate DESC
            ", ['search' => '%' . strtolower($search) . '%']);
        } else {
            $transactions = DB::select("
                SELECT t.*, b.bookingType, p.title, u.fullname AS buyer_name
                FROM transactions t
                JOIN bookings b ON t.bookingId = b.id
                JOIN properties p ON b.propertyId = p.id
                JOIN users u ON b.userId = u.id
                ORDER BY t.transactionDate DESC
            ");
        }

        return view('admin.transactions', compact('transactions', 'search'));
    }

    /**
     * Display administrative audit logs.
     */
    public function auditLogs(Request $request)
    {
        $search = $request->input('search');

        if ($search) {
            $logs = DB::select("
                SELECT al.*, u.fullname AS admin_name
                FROM admin_audit_logs al
                JOIN users u ON al.adminUserId = u.id
                WHERE (LOWER(al.actionName) LIKE :search OR LOWER(u.fullname) LIKE :search OR LOWER(al.tableName) LIKE :search)
                ORDER BY al.performedAt DESC
            ", ['search' => '%' . strtolower($search) . '%']);
        } else {
            $logs = DB::select("
                SELECT al.*, u.fullname AS admin_name
                FROM admin_audit_logs al
                JOIN users u ON al.adminUserId = u.id
                ORDER BY al.performedAt DESC
            ");
        }

        return view('admin.audit_logs', compact('logs', 'search'));
    }
}
