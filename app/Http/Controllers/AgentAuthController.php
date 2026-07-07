<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AgentAuthController extends Controller
{
    /**
     * Show the agent login view.
     */
    public function showLogin()
    {
        if (session()->has('agent_user_id')) {
            return redirect()->route('agent.dashboard');
        }
        return view('auth.agent_login');
    }

    /**
     * Process agent authentication.
     */
    public function processLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Retrieve user via raw query to check role constraints (roleId = 2 for agents)
        $users = DB::select("
            SELECT u.*, a.id AS agent_table_id 
            FROM users u
            JOIN agents a ON u.id = a.userId
            WHERE u.email = :email AND u.roleId = 2
        ", [
            'email' => $request->input('email')
        ]);

        if (empty($users)) {
            // Self-healing database check if sandbox resets tables
            $countResult = DB::select("SELECT COUNT(*) AS cnt FROM users");
            if (!empty($countResult) && $countResult[0]->cnt == 0) {
                \Illuminate\Support\Facades\Artisan::call('db:seed');
                $users = DB::select("
                    SELECT u.*, a.id AS agent_table_id 
                    FROM users u
                    JOIN agents a ON u.id = a.userId
                    WHERE u.email = :email AND u.roleId = 2
                ", [
                    'email' => $request->input('email')
                ]);
            }
        }

        if (empty($users)) {
            return back()->withErrors(['email' => 'Invalid credentials. User must have an active agent account.'])->withInput();
        }

        $user = $users[0];

        // Verify status and password
        if ($user->status !== 'active') {
            return back()->withErrors(['email' => 'This agent account has been suspended or deactivated.']);
        }

        if (!Hash::check($request->input('password'), $user->password)) {
            return back()->withErrors(['password' => 'Incorrect password.'])->withInput();
        }

        // Set session variables (session-based custom auth)
        session([
            'agent_user_id' => $user->id,
            'agent_user_name' => $user->fullname,
            'agent_id' => $user->agent_table_id,
            'agent_user_image' => $user->profileimage,
        ]);

        return redirect()->route('agent.dashboard')->with('success', 'Welcome back, Agent ' . $user->fullname . '!');
    }

    /**
     * Show the agent registration view.
     */
    public function showRegister()
    {
        if (session()->has('agent_user_id')) {
            return redirect()->route('agent.dashboard');
        }
        return view('auth.agent_register');
    }

    /**
     * Process agent registration.
     */
    public function processRegister(Request $request)
    {
        $request->validate([
            'fullname' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:6|confirmed',
            'phone' => 'nullable|string|max:50',
            'agency_name' => 'nullable|string|max:255',
            'license_no' => 'required|string|max:100',
            'experience_years' => 'required|integer|min:0',
            'about' => 'nullable|string|max:1000',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $email = $request->input('email');

        // Check if email already exists using raw SQL
        $existing = DB::select("SELECT id FROM users WHERE email = :email", ['email' => $email]);
        if (!empty($existing)) {
            return back()->withErrors(['email' => 'This email address is already registered.'])->withInput();
        }

        try {
            DB::beginTransaction();

            // Upload profile picture
            $profileImagePath = null;
            if ($request->hasFile('profile_image')) {
                $image = $request->file('profile_image');
                $name = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $destinationPath = public_path('/uploads/profiles');
                $image->move($destinationPath, $name);
                $profileImagePath = '/uploads/profiles/' . $name;
            }

            // Generate next user ID manually
            $nextIdResult = DB::select("SELECT NVL(MAX(id), 0) + 1 AS next_id FROM users");
            $nextUserId = $nextIdResult[0]->next_id;

            // Insert new user (roleId = 2 for agent, status = active) using raw SQL
            DB::insert("
                INSERT INTO users (id, roleId, fullname, email, password, phone, profileImage, status) 
                VALUES (:id, 2, :fullname, :email, :password, :phone, :profileImage, 'active')
            ", [
                'id' => $nextUserId,
                'fullname' => $request->input('fullname'),
                'email' => $email,
                'password' => Hash::make($request->input('password')),
                'phone' => $request->input('phone'),
                'profileImage' => $profileImagePath
            ]);

            // Generate next agent profile ID manually
            $nextAgentIdResult = DB::select("SELECT NVL(MAX(id), 0) + 1 AS next_id FROM agents");
            $nextAgentId = $nextAgentIdResult[0]->next_id;

            // Insert new agent profile using raw SQL
            DB::insert("
                INSERT INTO agents (id, userId, agencyName, licenseNo, experienceYears, about, rating) 
                VALUES (:id, :userId, :agencyName, :licenseNo, :experienceYears, :about, 0.00)
            ", [
                'id' => $nextAgentId,
                'userId' => $nextUserId,
                'agencyName' => $request->input('agency_name'),
                'licenseNo' => $request->input('license_no'),
                'experienceYears' => $request->input('experience_years'),
                'about' => $request->input('about')
            ]);

            DB::commit();

            return redirect()->route('agent.login')->with('success', 'Your agent account has been created successfully! Please sign in.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Registration failed: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Handle agent logout.
     */
    public function logout()
    {
        session()->forget(['agent_user_id', 'agent_user_name', 'agent_id', 'agent_user_image']);
        return redirect()->route('agent.login')->with('success', 'Logged out successfully.');
    }
}
