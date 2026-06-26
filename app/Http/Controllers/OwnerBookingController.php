<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OwnerBookingController extends Controller
{
    /**
     * Display bookings on owner's properties.
     */
    public function index()
    {
        $ownerId = session('owner_user_id');

        $bookings = DB::select("
            SELECT b.*, p.title, p.price AS property_price, l.areaName, l.city, 
                   u.fullname AS buyer_name, u.email AS buyer_email, u.phone AS buyer_phone,
                   t.amount AS payment_amount, t.paymentMethod, t.status AS payment_status
            FROM bookings b
            JOIN properties p ON b.propertyId = p.id
            JOIN locations l ON p.locationId = l.id
            JOIN users u ON b.userId = u.id
            LEFT JOIN transactions t ON t.bookingId = b.id
            WHERE p.ownerId = :ownerId
            ORDER BY b.createdAt DESC
        ", ['ownerId' => $ownerId]);

        return view('owner.bookings', compact('bookings'));
    }

    /**
     * Approve booking.
     */
    public function approve($id)
    {
        $ownerId = session('owner_user_id');

        // Verify booking belongs to property owned by user
        $booking = DB::select("
            SELECT b.id 
            FROM bookings b
            JOIN properties p ON b.propertyId = p.id
            WHERE b.id = :id AND p.ownerId = :ownerId
        ", ['id' => $id, 'ownerId' => $ownerId]);

        if (empty($booking)) {
            return back()->with('error', 'Booking request not found.');
        }

        DB::update("UPDATE bookings SET status = 'approved', updatedAt = CURRENT_TIMESTAMP WHERE id = :id", ['id' => $id]);

        return back()->with('success', 'Booking visit approved successfully.');
    }

    /**
     * Reject booking.
     */
    public function reject($id)
    {
        $ownerId = session('owner_user_id');

        $booking = DB::select("
            SELECT b.id 
            FROM bookings b
            JOIN properties p ON b.propertyId = p.id
            WHERE b.id = :id AND p.ownerId = :ownerId
        ", ['id' => $id, 'ownerId' => $ownerId]);

        if (empty($booking)) {
            return back()->with('error', 'Booking request not found.');
        }

        DB::update("UPDATE bookings SET status = 'rejected', updatedAt = CURRENT_TIMESTAMP WHERE id = :id", ['id' => $id]);

        return back()->with('success', 'Booking visit rejected.');
    }

    /**
     * Complete booking.
     */
    public function complete($id)
    {
        $ownerId = session('owner_user_id');

        $booking = DB::select("
            SELECT b.id 
            FROM bookings b
            JOIN properties p ON b.propertyId = p.id
            WHERE b.id = :id AND p.ownerId = :ownerId
        ", ['id' => $id, 'ownerId' => $ownerId]);

        if (empty($booking)) {
            return back()->with('error', 'Booking request not found.');
        }

        DB::update("UPDATE bookings SET status = 'completed', updatedAt = CURRENT_TIMESTAMP WHERE id = :id", ['id' => $id]);

        return back()->with('success', 'Booking visit marked as completed.');
    }
}
