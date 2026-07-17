<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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

    /**
     * Store a newly created user account.
     */
    public function store(Request $request)
    {
        $request->validate([
            'fullname' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:6',
            'phone' => 'nullable|string|max:50',
            'role_id' => 'required|integer|in:1,2,3,4',
        ]);

        $email = $request->input('email');
        $adminId = session('admin_user_id');

        // Check if email already exists
        $existing = DB::select("SELECT id FROM users WHERE email = :email", ['email' => $email]);
        if (!empty($existing)) {
            return back()->withErrors(['email' => 'This email address is already registered.'])->withInput();
        }

        try {
            DB::beginTransaction();

            // Generate user ID
            $nextUserIdResult = DB::select("SELECT NVL(MAX(id), 0) + 1 AS next_id FROM users");
            $newUserId = $nextUserIdResult[0]->next_id;

            // Insert into users
            DB::insert("
                INSERT INTO users (id, roleId, fullname, email, password, phone, profileImage, status)
                VALUES (:id, :roleId, :fullname, :email, :password, :phone, NULL, 'active')
            ", [
                'id' => $newUserId,
                'roleId' => $request->input('role_id'),
                'fullname' => $request->input('fullname'),
                'email' => $email,
                'password' => Hash::make($request->input('password')),
                'phone' => $request->input('phone'),
            ]);

            // If Agent role, initialize agent record
            if ($request->input('role_id') == 2) {
                $nextAgentIdResult = DB::select("SELECT NVL(MAX(id), 0) + 1 AS next_id FROM agents");
                $newAgentId = $nextAgentIdResult[0]->next_id;

                DB::insert("
                    INSERT INTO agents (id, userId, agencyName, licenseNo, experienceYears, about, rating)
                    VALUES (:id, :userId, 'Independent Agency', :licenseNo, 0, 'Platform agent account.', 5.00)
                ", [
                    'id' => $newAgentId,
                    'userId' => $newUserId,
                    'licenseNo' => 'LIC-' . rand(1000, 9999),
                ]);
            }

            // Log administrative action
            DB::insert("
                INSERT INTO admin_audit_logs (adminUserId, actionName, tableName, recordId, oldValues, newValues)
                VALUES (:adminId, 'USER_CREATE', 'USERS', :recordId, NULL, :newValues)
            ", [
                'adminId' => $adminId,
                'recordId' => $newUserId,
                'newValues' => 'Name: ' . $request->input('fullname') . ', Email: ' . $email . ', RoleId: ' . $request->input('role_id')
            ]);

            DB::commit();
            return back()->with('success', 'User account created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create user account: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show form to edit an existing user.
     */
    public function edit($id)
    {
        $users = DB::select("SELECT * FROM users WHERE id = :id", ['id' => $id]);
        if (empty($users)) {
            abort(404, 'User not found.');
        }
        $user = $users[0];
        
        $roles = DB::select("SELECT * FROM roles ORDER BY id ASC");
        
        return view('admin.users_edit', compact('user', 'roles'));
    }

    /**
     * Update an existing user's details.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'fullname' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'role_id' => 'required|integer|in:1,2,3,4',
            'status' => 'required|string|in:active,suspended',
            'password' => 'nullable|string|min:6',
        ]);

        $adminId = session('admin_user_id');

        $oldUser = DB::select("SELECT * FROM users WHERE id = :id", ['id' => $id]);
        if (empty($oldUser)) {
            abort(404, 'User not found.');
        }

        // Check if email already registered to someone else
        $email = $request->input('email');
        $existing = DB::select("SELECT id FROM users WHERE email = :email AND id != :id", ['email' => $email, 'id' => $id]);
        if (!empty($existing)) {
            return back()->withErrors(['email' => 'This email address is already registered to another user.'])->withInput();
        }

        try {
            DB::beginTransaction();

            $passwordSql = "";
            $bindings = [
                'id' => $id,
                'roleId' => $request->input('role_id'),
                'fullname' => $request->input('fullname'),
                'email' => $email,
                'phone' => $request->input('phone'),
                'status' => $request->input('status'),
            ];

            if ($request->filled('password')) {
                $passwordSql = ", password = :password";
                $bindings['password'] = Hash::make($request->input('password'));
            }

            DB::update("
                UPDATE users SET 
                    roleId = :roleId,
                    fullname = :fullname,
                    email = :email,
                    phone = :phone,
                    status = :status
                    {$passwordSql}
                WHERE id = :id
            ", $bindings);

            // If new role is Agent and no agent record exists, initialize agent record
            if ($request->input('role_id') == 2) {
                $agentCheck = DB::select("SELECT id FROM agents WHERE userId = :userId", ['userId' => $id]);
                if (empty($agentCheck)) {
                    $nextAgentIdResult = DB::select("SELECT NVL(MAX(id), 0) + 1 AS next_id FROM agents");
                    $newAgentId = $nextAgentIdResult[0]->next_id;

                    DB::insert("
                        INSERT INTO agents (id, userId, agencyName, licenseNo, experienceYears, about, rating)
                        VALUES (:id, :userId, 'Independent Agency', :licenseNo, 0, 'Platform agent account.', 5.00)
                    ", [
                        'id' => $newAgentId,
                        'userId' => $id,
                        'licenseNo' => 'LIC-' . rand(1000, 9999),
                    ]);
                }
            }

            // Log administrative action
            DB::insert("
                INSERT INTO admin_audit_logs (adminUserId, actionName, tableName, recordId, oldValues, newValues)
                VALUES (:adminId, 'USER_UPDATE', 'USERS', :recordId, :oldValues, :newValues)
            ", [
                'adminId' => $adminId,
                'recordId' => $id,
                'oldValues' => 'Name: ' . $oldUser[0]->fullname . ', Email: ' . $oldUser[0]->email . ', Role: ' . $oldUser[0]->roleid . ', Status: ' . $oldUser[0]->status,
                'newValues' => 'Name: ' . $request->input('fullname') . ', Email: ' . $email . ', Role: ' . $request->input('role_id') . ', Status: ' . $request->input('status')
            ]);

            DB::commit();
            return redirect()->route('admin.users')->with('success', 'User details updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update user account: ' . $e->getMessage())->withInput();
        }
    }
}
