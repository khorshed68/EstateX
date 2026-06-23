<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminUserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        // Build raw query with optional parameters
        if ($search) {
            $users = DB::select("
                SELECT u.*, r.roleName 
                FROM users u
                JOIN roles r ON u.roleId = r.id
                WHERE (LOWER(u.fullname) LIKE :search OR LOWER(u.email) LIKE :search)
                ORDER BY u.id ASC
            ", ['search' => '%' . strtolower($search) . '%']);
        } else {
            $users = DB::select("
                SELECT u.*, r.roleName 
                FROM users u
                JOIN roles r ON u.roleId = r.id
                ORDER BY u.id ASC
            ");
        }

        return view('admin.users', compact('users', 'search'));
    }

    /**
     * Suspend a user account.
     */
    public function suspend(Request $request, $id)
    {
        $adminId = session('admin_user_id');
        $reason = $request->input('reason', 'Suspended by administrator');

        try {
            // Call administrative procedure
            DB::statement("
                BEGIN 
                    PKG_ESTATEX_ADMIN.suspend_user(:userId, :adminId, :reason); 
                END;
            ", [
                'userId'  => $id,
                'adminId' => $adminId,
                'reason'  => $reason
            ]);

            return back()->with('success', 'User account has been suspended successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error executing suspend procedure: ' . $e->getMessage());
        }
    }

    /**
     * Activate a user account.
     */
    public function activate($id)
    {
        $adminId = session('admin_user_id');

        try {
            // Call administrative procedure
            DB::statement("
                BEGIN 
                    PKG_ESTATEX_ADMIN.activate_user(:userId, :adminId); 
                END;
            ", [
                'userId'  => $id,
                'adminId' => $adminId
            ]);

            return back()->with('success', 'User account has been reactivated.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error executing activation procedure: ' . $e->getMessage());
        }
    }
}
