<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AgentBookingController extends Controller
{
    /**
     * Display bookings on properties assigned to this agent.
     */
    public function index()
    {
        $agentId = session('agent_id');

        $bookings = DB::select("
            SELECT b.*, p.title, p.price AS property_price, l.areaName, l.city, 
                   u.fullname AS buyer_name, u.email AS buyer_email, u.phone AS buyer_phone,
                   t.amount AS payment_amount, t.paymentMethod, t.status AS payment_status
            FROM bookings b
            JOIN properties p ON b.propertyId = p.id
            JOIN locations l ON p.locationId = l.id
            JOIN users u ON b.userId = u.id
            LEFT JOIN transactions t ON t.bookingId = b.id
            WHERE b.agentId = :agentId
            ORDER BY b.createdAt DESC
        ", ['agentId' => $agentId]);

        return view('agent.bookings', compact('bookings'));
    }

    /**
     * Approve booking.
     */
    public function approve($id)
    {
        $agentId = session('agent_id');

        // Verify booking belongs to property assigned to agent
        $booking = DB::select("
            SELECT id FROM bookings WHERE id = :id AND agentId = :agentId
        ", ['id' => $id, 'agentId' => $agentId]);

        if (empty($booking)) {
            return back()->with('error', 'Booking request not found or not assigned to you.');
        }

        DB::update("
            UPDATE bookings 
            SET status = 'approved', updatedAt = CURRENT_TIMESTAMP 
            WHERE id = :id
        ", ['id' => $id]);

        return back()->with('success', 'Booking visit approved successfully.');
    }

    /**
     * Reject booking.
     */
    public function reject($id)
    {
        $agentId = session('agent_id');

        $booking = DB::select("
            SELECT id FROM bookings WHERE id = :id AND agentId = :agentId
        ", ['id' => $id, 'agentId' => $agentId]);

        if (empty($booking)) {
            return back()->with('error', 'Booking request not found or not assigned to you.');
        }

        DB::update("
            UPDATE bookings 
            SET status = 'rejected', updatedAt = CURRENT_TIMESTAMP 
            WHERE id = :id
        ", ['id' => $id]);

        return back()->with('success', 'Booking visit rejected.');
    }

    /**
     * Complete booking.
     */
    public function complete($id)
    {
        $agentId = session('agent_id');

        $booking = DB::select("
            SELECT id FROM bookings WHERE id = :id AND agentId = :agentId
        ", ['id' => $id, 'agentId' => $agentId]);

        if (empty($booking)) {
            return back()->with('error', 'Booking request not found or not assigned to you.');
        }

        DB::update("
            UPDATE bookings 
            SET status = 'completed', updatedAt = CURRENT_TIMESTAMP 
            WHERE id = :id
        ", ['id' => $id]);

        return back()->with('success', 'Booking visit marked as completed.');
    }
}
