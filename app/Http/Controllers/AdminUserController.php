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

    /**
     * Delete a user account.
     */
    public function destroy($id)
    {
        $adminId = session('admin_user_id');

        if ($id == $adminId) {
            return back()->with('error', 'You cannot delete your own admin account.');
        }

        // Fetch user info to log details before deleting
        $userInfo = DB::select("SELECT email, fullname FROM users WHERE id = :id", ['id' => $id]);
        if (empty($userInfo)) {
            return back()->with('error', 'User not found.');
        }

        try {
            DB::beginTransaction();

            // Log the delete action in admin_audit_logs
            DB::insert("
                INSERT INTO admin_audit_logs (adminUserId, actionName, tableName, recordId, oldValues, newValues) 
                VALUES (:adminId, 'USER_DELETE', 'USERS', :recordId, :oldValues, 'DELETED')
            ", [
                'adminId' => $adminId,
                'recordId' => $id,
                'oldValues' => 'Name: ' . $userInfo[0]->fullname . ', Email: ' . $userInfo[0]->email
            ]);

            // Execute raw SQL delete
            DB::delete("DELETE FROM users WHERE id = :id", ['id' => $id]);

            DB::commit();
            return back()->with('success', 'User account and all associated profiles/records have been deleted.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error deleting user: ' . $e->getMessage());
        }
    }

    /**
     * Display a listing of agents for moderation.
     */
    public function agentsIndex(Request $request)
    {
        $search = $request->input('search');

        if ($search) {
            $agents = DB::select("
                SELECT a.*, u.fullname, u.email, u.phone, u.status,
                       (SELECT COUNT(*) FROM properties WHERE agentId = a.id) AS listings_count
                FROM agents a
                JOIN users u ON a.userId = u.id
                WHERE (LOWER(u.fullname) LIKE :search OR LOWER(a.agencyName) LIKE :search)
                ORDER BY a.id ASC
            ", ['search' => '%' . strtolower($search) . '%']);
        } else {
            $agents = DB::select("
                SELECT a.*, u.fullname, u.email, u.phone, u.status,
                       (SELECT COUNT(*) FROM properties WHERE agentId = a.id) AS listings_count
                FROM agents a
                JOIN users u ON a.userId = u.id
                ORDER BY a.id ASC
            ");
        }

        return view('admin.agents', compact('agents', 'search'));
    }

    /**
     * Update agent details or rating.
     */
    public function agentUpdate(Request $request, $id)
    {
        $request->validate([
            'agency_name' => 'nullable|string|max:255',
            'license_no' => 'required|string|max:100',
            'experience_years' => 'required|integer|min:0',
            'rating' => 'required|numeric|min:0|max:5',
        ]);

        $adminId = session('admin_user_id');

        // Fetch old values for audit logging
        $oldAgent = DB::select("SELECT * FROM agents WHERE id = :id", ['id' => $id]);
        if (empty($oldAgent)) {
            return back()->with('error', 'Agent not found.');
        }

        try {
            DB::beginTransaction();

            DB::update("
                UPDATE agents 
                SET agencyName = :agencyName, licenseNo = :licenseNo, experienceYears = :experienceYears, rating = :rating, updatedAt = CURRENT_TIMESTAMP 
                WHERE id = :id
            ", [
                'agencyName' => $request->input('agency_name'),
                'licenseNo' => $request->input('license_no'),
                'experienceYears' => $request->input('experience_years'),
                'rating' => $request->input('rating'),
                'id' => $id
            ]);

            // Log action
            DB::insert("
                INSERT INTO admin_audit_logs (adminUserId, actionName, tableName, recordId, oldValues, newValues) 
                VALUES (:adminId, 'AGENT_MODERATION', 'AGENTS', :recordId, :oldValues, :newValues)
            ", [
                'adminId' => $adminId,
                'recordId' => $id,
                'oldValues' => 'Agency: ' . $oldAgent[0]->agencyname . ', Rating: ' . $oldAgent[0]->rating,
                'newValues' => 'Agency: ' . $request->input('agency_name') . ', Rating: ' . $request->input('rating')
            ]);

            DB::commit();
            return back()->with('success', 'Agent profile details updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update agent profile: ' . $e->getMessage());
        }
    }
}
