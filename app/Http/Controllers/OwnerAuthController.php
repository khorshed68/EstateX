<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class OwnerAuthController extends Controller
{
    /**
     * Show the owner login view.
     */
    public function showLogin()
    {
        if (session()->has('owner_user_id')) {
            return redirect()->route('owner.dashboard');
        }
        return view('auth.owner_login');
    }

    /**
     * Process owner authentication.
     */
    public function processLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Retrieve user via raw query to check role constraints (roleId = 4 for owners)
        $users = DB::select("SELECT * FROM users WHERE email = :email AND roleId = 4", [
            'email' => $request->input('email')
        ]);

        if (empty($users)) {
            // Self-healing database check if sandbox resets tables
            $countResult = DB::select("SELECT COUNT(*) AS cnt FROM users");
            if (!empty($countResult) && $countResult[0]->cnt == 0) {
                \Illuminate\Support\Facades\Artisan::call('db:seed');
                $users = DB::select("SELECT * FROM users WHERE email = :email AND roleId = 4", [
                    'email' => $request->input('email')
                ]);
            }
        }

        if (empty($users)) {
            return back()->withErrors(['email' => 'Invalid credentials. User must have a standard account.'])->withInput();
        }

        $user = $users[0];

        // Verify status and password
        if ($user->status === 'pending') {
            return back()->withErrors(['email' => 'Your account is pending administrator approval. Please wait until approved.']);
        }
        if ($user->status !== 'active') {
            return back()->withErrors(['email' => 'This account has been suspended or deactivated.']);
        }

        if (!Hash::check($request->input('password'), $user->password)) {
            return back()->withErrors(['password' => 'Incorrect password.'])->withInput();
        }

        // Set session variables (session-based custom auth)
        session([
            'owner_user_id' => $user->id,
            'owner_user_name' => $user->fullname,
            'owner_user_image' => $user->profileimage,
        ]);

        return redirect()->route('owner.dashboard')->with('success', 'Welcome, ' . $user->fullname . '!');
    }

    /**
     * Show the owner registration view.
     */
    public function showRegister()
    {
        if (session()->has('owner_user_id')) {
            return redirect()->route('owner.dashboard');
        }
        return view('auth.owner_register');
    }

    /**
     * Process owner registration.
     */
    public function processRegister(Request $request)
    {
        $request->validate([
            'fullname' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:6|confirmed',
            'phone' => 'nullable|string|max:50',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $email = $request->input('email');

        // Check if email already exists using raw SQL
        $existing = DB::select("SELECT id FROM users WHERE email = :email", ['email' => $email]);
        if (!empty($existing)) {
            return back()->withErrors(['email' => 'This email address is already registered.'])->withInput();
        }

        try {
            // Upload profile picture
            $profileImagePath = null;
            if ($request->hasFile('profile_image')) {
                $image = $request->file('profile_image');
                $name = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $destinationPath = public_path('/uploads/profiles');
                $image->move($destinationPath, $name);
                $profileImagePath = '/uploads/profiles/' . $name;
            }

            // Generate next ID manually
            $nextIdResult = DB::select("SELECT NVL(MAX(id), 0) + 1 AS next_id FROM users");
            $nextId = $nextIdResult[0]->next_id;

            // Insert new owner user (roleId = 4, status = pending) using raw SQL
            DB::insert("
                INSERT INTO users (id, roleId, fullname, email, password, phone, profileImage, status) 
                VALUES (:id, 4, :fullname, :email, :password, :phone, :profileImage, 'pending')
            ", [
                'id' => $nextId,
                'fullname' => $request->input('fullname'),
                'email' => $email,
                'password' => Hash::make($request->input('password')),
                'phone' => $request->input('phone'),
                'profileImage' => $profileImagePath
            ]);

            return redirect()->route('owner.login')->with('success', 'Your owner account has been created successfully and is pending administrator approval.');
        } catch (\Exception $e) {
            return back()->with('error', 'Registration failed: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Handle owner logout.
     */
    public function logout()
    {
        session()->forget(['owner_user_id', 'owner_user_name', 'owner_user_image']);
        return redirect()->route('owner.login')->with('success', 'Logged out successfully.');
    }
}
