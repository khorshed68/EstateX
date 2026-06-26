<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OwnerAgentController extends Controller
{
    /**
     * Display listing of agents and properties.
     */
    public function index()
    {
        $ownerId = session('owner_user_id');

        // Fetch owner properties to map agent assignments
        $properties = DB::select("
            SELECT p.id, p.title, p.agentId, u.fullname AS agent_name
            FROM properties p
            LEFT JOIN agents a ON p.agentId = a.id
            LEFT JOIN users u ON a.userId = u.id
            WHERE p.ownerId = :ownerId
            ORDER BY p.title ASC
        ", ['ownerId' => $ownerId]);

        // Fetch agents list
        $agents = DB::select("
            SELECT a.id AS agent_id, u.fullname, u.email, u.phone, a.agencyName, a.experienceYears, a.rating
            FROM agents a
            JOIN users u ON a.userId = u.id
            WHERE u.status = 'active'
            ORDER BY a.rating DESC, u.fullname ASC
        ");

        return view('owner.agents', compact('properties', 'agents'));
    }

    /**
     * Assign agent to property.
     */
    public function assign(Request $request, $propertyId)
    {
        $ownerId = session('owner_user_id');

        $request->validate([
            'agent_id' => 'nullable|integer'
        ]);

        $agentId = $request->input('agent_id');

        // Verify property ownership
        $property = DB::select("SELECT id FROM properties WHERE id = :id AND ownerId = :ownerId", [
            'id' => $propertyId,
            'ownerId' => $ownerId
        ]);

        if (empty($property)) {
            return back()->with('error', 'Property listing not found.');
        }

        // If assigning, verify agent exists
        if ($agentId) {
            $agent = DB::select("SELECT id FROM agents WHERE id = :id", ['id' => $agentId]);
            if (empty($agent)) {
                return back()->with('error', 'Selected agent does not exist.');
            }
        }

        // Update the agentId field using raw SQL
        DB::update("
            UPDATE properties 
            SET agentId = :agentId, updatedAt = CURRENT_TIMESTAMP 
            WHERE id = :id AND ownerId = :ownerId
        ", [
            'agentId' => $agentId,
            'id' => $propertyId,
            'ownerId' => $ownerId
        ]);

        return back()->with('success', 'Agent assignment updated successfully.');
    }
}
