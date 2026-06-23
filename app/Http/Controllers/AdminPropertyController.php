<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminPropertyController extends Controller
{
    /**
     * Display a listing of properties.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        if ($search) {
            $properties = DB::select("
                SELECT p.*, u.fullname AS owner_name, ag_u.fullname AS agent_name, 
                       l.areaName, l.city, pt.typeName
                FROM properties p
                JOIN users u ON p.ownerId = u.id
                LEFT JOIN agents ag ON p.agentId = ag.id
                LEFT JOIN users ag_u ON ag.userId = ag_u.id
                JOIN locations l ON p.locationId = l.id
                JOIN property_types pt ON p.typeId = pt.id
                WHERE (LOWER(p.title) LIKE :search OR LOWER(l.city) LIKE :search OR LOWER(l.areaName) LIKE :search)
                ORDER BY p.id ASC
            ", ['search' => '%' . strtolower($search) . '%']);
        } else {
            $properties = DB::select("
                SELECT p.*, u.fullname AS owner_name, ag_u.fullname AS agent_name, 
                       l.areaName, l.city, pt.typeName
                FROM properties p
                JOIN users u ON p.ownerId = u.id
                LEFT JOIN agents ag ON p.agentId = ag.id
                LEFT JOIN users ag_u ON ag.userId = ag_u.id
                JOIN locations l ON p.locationId = l.id
                JOIN property_types pt ON p.typeId = pt.id
                ORDER BY p.id ASC
            ");
        }

        return view('admin.properties', compact('properties', 'search'));
    }

    /**
     * Delete a property listing.
     */
    public function destroy($id)
    {
        $adminId = session('admin_user_id');

        try {
            // Call administrative procedure
            DB::statement("
                BEGIN 
                    PKG_ESTATEX_ADMIN.delete_property_listing(:propertyId, :adminId); 
                END;
            ", [
                'propertyId' => $id,
                'adminId'    => $adminId
            ]);

            return back()->with('success', 'Property listing and its historical associations deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error executing delete procedure: ' . $e->getMessage());
        }
    }
}
