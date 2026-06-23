<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Show the admin login view.
     */
    public function showLogin()
    {
        if (session()->has('admin_user_id')) {
            return redirect()->route('admin.dashboard');
        }
        return view('auth.login');
    }

    /**
     * Process admin authentication.
     */
    public function processLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Retrieve user via raw query to enforce database constraints
        $users = DB::select("SELECT * FROM users WHERE email = :email AND roleId = 1", [
            'email' => $request->input('email')
        ]);

        if (empty($users)) {
            return back()->withErrors(['email' => 'Invalid administrative credentials.'])->withInput();
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
            'admin_user_id' => $user->id,
            'admin_user_name' => $user->fullname,
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Welcome back, ' . $user->fullname . '!');
    }

    /**
     * Handle admin logout.
     */
    public function logout()
    {
        session()->forget(['admin_user_id', 'admin_user_name']);
        return redirect()->route('admin.login')->with('success', 'Logged out successfully.');
    }
}
