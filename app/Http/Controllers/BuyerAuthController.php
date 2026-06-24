<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class BuyerAuthController extends Controller
{
    /**
     * Show the buyer login view.
     */
    public function showLogin()
    {
        if (session()->has('buyer_user_id')) {
            return redirect()->route('buyer.dashboard');
        }
        return view('auth.buyer_login');
    }

    /**
     * Process buyer authentication.
     */
    public function processLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Retrieve user via raw query to check role constraints (roleId = 3 for standard buyers)
        $users = DB::select("SELECT * FROM users WHERE email = :email AND roleId = 3", [
            'email' => $request->input('email')
        ]);

        if (empty($users)) {
            // Self-healing database check if sandbox resets tables
            $countResult = DB::select("SELECT COUNT(*) AS cnt FROM users");
            if (!empty($countResult) && $countResult[0]->cnt == 0) {
                \Illuminate\Support\Facades\Artisan::call('db:seed');
                $users = DB::select("SELECT * FROM users WHERE email = :email AND roleId = 3", [
                    'email' => $request->input('email')
                ]);
            }
        }

        if (empty($users)) {
            return back()->withErrors(['email' => 'Invalid credentials. User must have a standard buyer account.'])->withInput();
        }

        $user = $users[0];

        // Verify status and password
        if ($user->status !== 'active') {
            return back()->withErrors(['email' => 'This account has been suspended or deactivated.']);
        }

        if (!Hash::check($request->input('password'), $user->password)) {
            return back()->withErrors(['password' => 'Incorrect password.'])->withInput();
        }

        // Set session variables (session-based custom auth)
        session([
            'buyer_user_id' => $user->id,
            'buyer_user_name' => $user->fullname,
        ]);

        return redirect()->route('buyer.dashboard')->with('success', 'Welcome, ' . $user->fullname . '!');
    }

    /**
     * Show the buyer registration view.
     */
    public function showRegister()
    {
        if (session()->has('buyer_user_id')) {
            return redirect()->route('buyer.dashboard');
        }
        return view('auth.buyer_register');
    }

    /**
     * Process buyer registration.
     */
    public function processRegister(Request $request)
    {
        $request->validate([
            'fullname' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:6|confirmed',
            'phone' => 'nullable|string|max:50'
        ]);

        $email = $request->input('email');

        // Check if email already exists using raw SQL
        $existing = DB::select("SELECT id FROM users WHERE email = :email", ['email' => $email]);
        if (!empty($existing)) {
            return back()->withErrors(['email' => 'This email address is already registered.'])->withInput();
        }

        try {
            // Generate next ID manually
            $nextIdResult = DB::select("SELECT NVL(MAX(id), 0) + 1 AS next_id FROM users");
            $nextId = $nextIdResult[0]->next_id;

            // Insert new buyer user (roleId = 3, status = active) using raw SQL
            DB::insert("
                INSERT INTO users (id, roleId, fullname, email, password, phone, status) 
                VALUES (:id, 3, :fullname, :email, :password, :phone, 'active')
            ", [
                'id' => $nextId,
                'fullname' => $request->input('fullname'),
                'email' => $email,
                'password' => Hash::make($request->input('password')),
                'phone' => $request->input('phone')
            ]);

            // Set session variables to log them in automatically
            session([
                'buyer_user_id' => $nextId,
                'buyer_user_name' => $request->input('fullname'),
            ]);

            return redirect()->route('buyer.dashboard')->with('success', 'Your buyer account has been created successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Registration failed: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Handle buyer logout.
     */
    public function logout()
    {
        session()->forget(['buyer_user_id', 'buyer_user_name']);
        return redirect()->route('buyer.login')->with('success', 'Logged out successfully.');
    }
}
