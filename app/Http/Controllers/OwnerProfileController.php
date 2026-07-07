<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class OwnerProfileController extends Controller
{
    /**
     * Show the owner profile edit page.
     */
    public function index()
    {
        $userId = session('owner_user_id');

        $profiles = DB::select("
            SELECT id, fullname, email, phone, profileImage, password
            FROM users
            WHERE id = :userId AND roleId = 3
        ", ['userId' => $userId]);

        if (empty($profiles)) {
            return redirect()->route('owner.dashboard')->with('error', 'Profile not found.');
        }

        $profile = $profiles[0];

        return view('owner.profile', compact('profile'));
    }

    /**
     * Update the owner profile details.
     */
    public function update(Request $request)
    {
        $userId = session('owner_user_id');

        $request->validate([
            'fullname' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'required|email|max:255',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'current_password' => 'nullable|required_with:new_password|string',
            'new_password' => 'nullable|string|min:6|confirmed',
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

            // Handle Password Change
            if ($request->filled('new_password')) {
                // Fetch existing password hash
                $userResult = DB::select("SELECT password FROM users WHERE id = :userId", ['userId' => $userId]);
                $dbPassword = $userResult[0]->password;

                if (!Hash::check($request->input('current_password'), $dbPassword)) {
                    return back()->withErrors(['current_password' => 'Your current password is incorrect.'])->withInput();
                }

                DB::update("
                    UPDATE users 
                    SET password = :password 
                    WHERE id = :userId
                ", [
                    'password' => Hash::make($request->input('new_password')),
                    'userId' => $userId
                ]);
            }

            // Handle Profile Image Upload
            if ($request->hasFile('profile_image')) {
                $file = $request->file('profile_image');
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                
                $destinationPath = public_path('uploads/profiles');
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }
                
                $file->move($destinationPath, $filename);
                $profileImagePath = 'uploads/profiles/' . $filename;
                
                // Get old image to delete
                $oldImg = DB::select("SELECT profileImage FROM users WHERE id = :userId", ['userId' => $userId]);
                if (!empty($oldImg) && !empty($oldImg[0]->profileimage)) {
                    $oldFilePath = public_path($oldImg[0]->profileimage);
                    if (file_exists($oldFilePath) && is_file($oldFilePath)) {
                        unlink($oldFilePath);
                    }
                }
                
                // Update profileImage in database
                DB::update("
                    UPDATE users 
                    SET profileImage = :profileImage 
                    WHERE id = :userId
                ", ['profileImage' => $profileImagePath, 'userId' => $userId]);
                
                // Update session
                session(['owner_user_image' => '/' . $profileImagePath]);
            }

            DB::commit();

            // Update session values
            session(['owner_user_name' => $request->input('fullname')]);

            return redirect()->route('owner.profile')->with('success', 'Profile updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Profile update failed: ' . $e->getMessage())->withInput();
        }
    }
}
