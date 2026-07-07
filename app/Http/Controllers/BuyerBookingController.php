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
            'visit_date' => 'nullable|date', 
            'visit_slot' => 'nullable|string',
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

        // Verify representative agent availability
        if ($agentId) {
            if ($bookingType === 'visit') {
                $visitDateInput = $request->input('visit_date');
                if ($visitDateInput) {
                    $agentBlock = DB::select("
                        SELECT reason FROM agent_availability 
                        WHERE agentId = :agentId 
                          AND TRUNC(unavailableDate) = TO_DATE(:visitDate, 'YYYY-MM-DD')
                    ", [
                        'agentId' => $agentId,
                        'visitDate' => date('Y-m-d', strtotime($visitDateInput))
                    ]);
                    if (!empty($agentBlock)) {
                        $reason = $agentBlock[0]->reason ? " (" . $agentBlock[0]->reason . ")" : "";
                        return back()->with('error', 'The representing agent is unavailable on ' . date('Y-m-d', strtotime($visitDateInput)) . $reason . '. Please choose another date.')->withInput();
                    }
                }
            } else {
                $startDateInput = $request->input('start_date');
                $endDateInput = $request->input('end_date');
                if ($startDateInput && $endDateInput) {
                    $startDate = date('Y-m-d', strtotime($startDateInput));
                    $endDate = date('Y-m-d', strtotime($endDateInput));
                    $agentBlock = DB::select("
                        SELECT TRUNC(unavailableDate) as block_date, reason FROM agent_availability 
                        WHERE agentId = :agentId 
                          AND TRUNC(unavailableDate) BETWEEN TO_DATE(:startDate, 'YYYY-MM-DD') AND TO_DATE(:endDate, 'YYYY-MM-DD')
                        ORDER BY unavailableDate ASC
                    ", [
                        'agentId' => $agentId,
                        'startDate' => $startDate,
                        'endDate' => $endDate
                    ]);
                    if (!empty($agentBlock)) {
                        $reason = $agentBlock[0]->reason ? " (" . $agentBlock[0]->reason . ")" : "";
                        $blockDateFormatted = date('Y-m-d', strtotime($agentBlock[0]->block_date));
                        return back()->with('error', 'The representing agent is unavailable on ' . $blockDateFormatted . $reason . ' during your reservation range. Please choose other dates.')->withInput();
                    }
                }
            }
        }
        
        // Calculate total amount (1% booking fee for unit reservations)
        $totalAmount = 0.00;
        if ($bookingType === 'reservation') {
            $totalAmount = round($property->price * 0.01, 2);
        }

        try {
            DB::beginTransaction();

            // Perform availability checking / date conflict validation inside transaction
            if ($bookingType === 'visit') {
                $visitDateInput = $request->input('visit_date');
                $visitSlotInput = $request->input('visit_slot');
                
                if (!$visitDateInput || !$visitSlotInput) {
                    return back()->withErrors(['visit_date' => 'Both visit date and time slot are required for scheduling a visit.'])->withInput();
                }

                if (strtotime($visitDateInput) < strtotime(date('Y-m-d'))) {
                    return back()->withErrors(['visit_date' => 'The visit date cannot be in the past.'])->withInput();
                }

                $visitDate = date('Y-m-d H:i:s', strtotime("$visitDateInput $visitSlotInput"));
                $startDate = null;
                $endDate = null;

                // Check slot conflict for this property
                $conflict = DB::select("
                    SELECT id FROM bookings 
                    WHERE propertyId = :propertyId 
                      AND bookingType = 'visit' 
                      AND status IN ('pending', 'approved', 'completed') 
                      AND visitDate = TO_TIMESTAMP(:visitDate, 'YYYY-MM-DD HH24:MI:SS')
                ", [
                    'propertyId' => $propertyId,
                    'visitDate' => $visitDate
                ]);

                if (!empty($conflict)) {
                    return back()->with('error', 'The selected time slot is already booked for a visit. Please choose another date or time slot.')->withInput();
                }
            } else {
                $startDateInput = $request->input('start_date');
                $endDateInput = $request->input('end_date');

                if (!$startDateInput || !$endDateInput) {
                    return back()->withErrors(['start_date' => 'Both start date and end date are required for reserving a property.'])->withInput();
                }

                if (strtotime($startDateInput) < strtotime(date('Y-m-d'))) {
                    return back()->withErrors(['start_date' => 'The reservation start date cannot be in the past.'])->withInput();
                }

                if (strtotime($endDateInput) < strtotime($startDateInput)) {
                    return back()->withErrors(['end_date' => 'The reservation end date must be after the start date.'])->withInput();
                }

                $startDate = date('Y-m-d', strtotime($startDateInput));
                $endDate = date('Y-m-d', strtotime($endDateInput));
                $visitDate = null;

                // Check reservation overlap conflicts
                $conflict = DB::select("
                    SELECT id FROM bookings 
                    WHERE propertyId = :propertyId 
                      AND bookingType = 'reservation' 
                      AND status IN ('pending', 'approved', 'completed') 
                      AND startDate <= TO_DATE(:endDate, 'YYYY-MM-DD') 
                      AND endDate >= TO_DATE(:startDate, 'YYYY-MM-DD')
                ", [
                    'propertyId' => $propertyId,
                    'startDate' => $startDate,
                    'endDate' => $endDate
                ]);

                if (!empty($conflict)) {
                    return back()->with('error', 'This property is already reserved or has a pending reservation during the selected date range. Please select other dates.')->withInput();
                }
            }

            // Get next ID manually (since sequence isn't auto-triggered)
            $nextBookingIdResult = DB::select("SELECT NVL(MAX(id), 0) + 1 AS next_id FROM bookings");
            $bookingId = $nextBookingIdResult[0]->next_id;

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
