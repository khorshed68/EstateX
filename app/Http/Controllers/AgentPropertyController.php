<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AgentPropertyController extends Controller
{
    /**
     * Display a listing of properties assigned to the agent.
     */
    public function index()
    {
        $agentId = session('agent_id');

        $properties = DB::select("
            SELECT p.*, l.areaName, l.city, pt.typeName, u.fullname AS owner_name, u.email AS owner_email,
                   (SELECT imagePath FROM property_images WHERE propertyId = p.id AND isMain = 1 AND ROWNUM = 1) AS main_image
            FROM properties p
            JOIN locations l ON p.locationId = l.id
            JOIN property_types pt ON p.typeId = pt.id
            JOIN users u ON p.ownerId = u.id
            WHERE p.agentId = :agentId
            ORDER BY p.id DESC
        ", ['agentId' => $agentId]);

        return view('agent.properties', compact('properties'));
    }
}
