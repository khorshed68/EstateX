<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BuyerBookingController extends Controller
{
    /**
     * Display the user's booking history.
     */
    public function index()
    {
        $userId = session('buyer_user_id');
        
        $bookings = DB::select("
            SELECT b.*, p.title, p.price AS property_price, l.areaName, l.city, u.fullname AS agent_name,
                   t.amount AS payment_amount, t.paymentMethod, t.referenceNo, t.status AS payment_status
            FROM bookings b
            JOIN properties p ON b.propertyId = p.id
            JOIN locations l ON p.locationId = l.id
            LEFT JOIN agents ag ON b.agentId = ag.id
            LEFT JOIN users u ON ag.userId = u.id
            LEFT JOIN transactions t ON t.bookingId = b.id
            WHERE b.userId = :userId
            ORDER BY b.createdAt DESC
        ", ['userId' => $userId]);
        
        return view('buyer.bookings', compact('bookings'));
    }

    /**
     * Store a new booking (site visit or unit reservation).
     */
    public function store(Request $request)
    {
        $userId = session('buyer_user_id');
        
        $request->validate([
            'property_id' => 'required|integer',
            'booking_type' => 'required|string|in:visit,reservation',
            'visit_date' => 'nullable|string', 
            'start_date' => 'nullable|date',   
            'end_date' => 'nullable|date',     
            'guests' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:1000'
        ]);

        $propertyId = $request->input('property_id');
        $bookingType = $request->input('booking_type');

        // Fetch property details using raw select
        $propertyData = DB::select("SELECT agentId, price FROM properties WHERE id = :id", ['id' => $propertyId]);
        if (empty($propertyData)) {
            return back()->with('error', 'Selected property listing does not exist.');
        }
        
        $property = $propertyData[0];
        $agentId = $property->agentid;
        
        // Calculate total amount (1% booking fee for unit reservations)
        $totalAmount = 0.00;
        if ($bookingType === 'reservation') {
            $totalAmount = round($property->price * 0.01, 2);
        }

        try {
            DB::beginTransaction();

            // Get next ID manually (since sequence isn't auto-triggered)
            $nextBookingIdResult = DB::select("SELECT NVL(MAX(id), 0) + 1 AS next_id FROM bookings");
            $bookingId = $nextBookingIdResult[0]->next_id;

            // Formats visit date if provided
            $visitDate = $request->input('visit_date') ? date('Y-m-d H:i:s', strtotime($request->input('visit_date'))) : null;
            $startDate = $request->input('start_date') ? date('Y-m-d', strtotime($request->input('start_date'))) : null;
            $endDate = $request->input('end_date') ? date('Y-m-d', strtotime($request->input('end_date'))) : null;

            // Assemble query with conditional date format helpers for Oracle compatibility
            $visitDateSql = $visitDate ? "TO_TIMESTAMP(:visitDate, 'YYYY-MM-DD HH24:MI:SS')" : "NULL";
            $startDateSql = $startDate ? "TO_DATE(:startDate, 'YYYY-MM-DD')" : "NULL";
            $endDateSql = $endDate ? "TO_DATE(:endDate, 'YYYY-MM-DD')" : "NULL";
            
            $sql = "
                INSERT INTO bookings (
                    id, userId, propertyId, agentId, bookingType, visitDate, startDate, endDate, guests, status, totalAmount, notes
                ) VALUES (
                    :id, :userId, :propertyId, :agentId, :bookingType, 
                    {$visitDateSql}, 
                    {$startDateSql}, 
                    {$endDateSql}, 
                    :guests, 'pending', :totalAmount, :notes
                )
            ";
            
            $bindings = [
                'id' => $bookingId,
                'userId' => $userId,
                'propertyId' => $propertyId,
                'agentId' => $agentId,
                'bookingType' => $bookingType,
                'guests' => $request->input('guests'),
                'totalAmount' => $totalAmount,
                'notes' => $request->input('notes')
            ];
            
            if ($visitDate) $bindings['visitDate'] = $visitDate;
            if ($startDate) $bindings['startDate'] = $startDate;
            if ($endDate) $bindings['endDate'] = $endDate;
            
            DB::insert($sql, $bindings);

            // For reservations, create a credit transaction log
            if ($bookingType === 'reservation') {
                $nextTxnIdResult = DB::select("SELECT NVL(MAX(id), 0) + 1 AS next_id FROM transactions");
                $txnId = $nextTxnIdResult[0]->next_id;
                $refNo = 'TXN-' . date('YmdHis') . '-' . rand(100, 999);

                DB::insert("
                    INSERT INTO transactions (
                        id, bookingId, transactionType, amount, paymentMethod, referenceNo, status
                    ) VALUES (
                        :id, :bookingId, 'booking_fee', :amount, 'credit_card', :refNo, 'completed'
                    )
                ", [
                    'id' => $txnId,
                    'bookingId' => $bookingId,
                    'amount' => $totalAmount,
                    'refNo' => $refNo
                ]);
                
                // Automatically approve booking when reservation payment is completed
                DB::statement("UPDATE bookings SET status = 'completed' WHERE id = :id", ['id' => $bookingId]);
            }

            DB::commit();
            
            $msg = $bookingType === 'reservation' 
                ? 'Property reservation completed and payment processed successfully.' 
                : 'Site visit booking request submitted successfully.';
                
            return redirect()->route('buyer.bookings')->with('success', $msg);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error creating booking: ' . $e->getMessage())->withInput();
        }
    }
}
