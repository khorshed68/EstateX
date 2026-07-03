<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AgentProfileController extends Controller
{
    /**
     * Show the agent profile edit page.
     */
    public function index()
    {
        $userId = session('agent_user_id');

        $profiles = DB::select("
            SELECT u.fullname, u.email, u.phone, a.agencyName, a.licenseNo, a.experienceYears, a.about
            FROM users u
            JOIN agents a ON u.id = a.userId
            WHERE u.id = :userId
        ", ['userId' => $userId]);

        if (empty($profiles)) {
            return redirect()->route('agent.dashboard')->with('error', 'Profile not found.');
        }

        $profile = $profiles[0];

        return view('agent.profile', compact('profile'));
    }

    /**
     * Update the agent profile details.
     */
    public function update(Request $request)
    {
        $userId = session('agent_user_id');

        $request->validate([
            'fullname' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'required|email|max:255',
            'agency_name' => 'nullable|string|max:255',
            'license_no' => 'required|string|max:100',
            'experience_years' => 'required|integer|min:0',
            'about' => 'nullable|string|max:1000',
        ]);

        $email = $request->input('email');

        // Check if email already exists for another user
        $existing = DB::select("
            SELECT id FROM users 
            WHERE email = :email AND id != :userId
        ", ['email' => $email, 'userId' => $userId]);

        if (!empty($existing)) {
            return back()->withErrors(['email' => 'This email address is already in use by another user.'])->withInput();
        }

        try {
            DB::beginTransaction();

            // Update user details
            DB::update("
                UPDATE users 
                SET fullname = :fullname, phone = :phone, email = :email, updatedAt = CURRENT_TIMESTAMP 
                WHERE id = :userId
            ", [
                'fullname' => $request->input('fullname'),
                'phone' => $request->input('phone'),
                'email' => $email,
                'userId' => $userId
            ]);

            // Update agent details
            DB::update("
                UPDATE agents 
                SET agencyName = :agencyName, licenseNo = :licenseNo, experienceYears = :experienceYears, about = :about, updatedAt = CURRENT_TIMESTAMP 
                WHERE userId = :userId
            ", [
                'agencyName' => $request->input('agency_name'),
                'licenseNo' => $request->input('license_no'),
                'experienceYears' => $request->input('experience_years'),
                'about' => $request->input('about'),
                'userId' => $userId
            ]);

            DB::commit();

            // Update session values
            session(['agent_user_name' => $request->input('fullname')]);

            return redirect()->route('agent.profile')->with('success', 'Profile updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Profile update failed: ' . $e->getMessage())->withInput();
        }
    }
}
