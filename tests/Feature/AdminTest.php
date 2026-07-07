<?php

namespace Tests\Feature;

use Tests\TestCase;

class AdminTest extends TestCase
{
    /**
     * Test that guest is redirected to admin login page.
     */
    public function test_root_landing_page_renders(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('EstateX');
        $response->assertSee('Admin Console');
    }

    /**
     * Test that the login page renders successfully.
     */
    public function test_login_page_renders(): void
    {
        $response = $this->get('/admin/login');
        $response->assertStatus(200);
        $response->assertSee('Administrator Portal');
    }

    /**
     * Test login failure with invalid credentials.
     */
    public function test_login_fails_with_invalid_credentials(): void
    {
        $response = $this->post('/admin/login', [
            'email' => 'wrong@estatex.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['email']);
    }

    /**
     * Test login success with valid credentials.
     */
    public function test_login_success_with_valid_credentials(): void
    {
        $response = $this->post('/admin/login', [
            'email' => 'admin@estatex.com',
            'password' => 'admin123',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/admin/dashboard');
        $response->assertSessionHas('admin_user_id', 1);
    }

    /**
     * Test dashboard access is blocked for guest.
     */
    public function test_guest_cannot_access_dashboard(): void
    {
        $response = $this->get('/admin/dashboard');
        $response->assertStatus(302);
        $response->assertRedirect('/admin/login');
    }

    /**
     * Test dashboard loads successfully when authenticated.
     */
    public function test_authenticated_admin_can_access_dashboard(): void
    {
        $response = $this->withSession([
            'admin_user_id' => 1,
            'admin_user_name' => 'System Admin'
        ])->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Dashboard');
        $response->assertSee('System Admin');
    }

    /**
     * Test users page loads successfully.
     */
    public function test_authenticated_admin_can_access_users_page(): void
    {
        $response = $this->withSession([
            'admin_user_id' => 1,
            'admin_user_name' => 'System Admin'
        ])->get('/admin/users');

        $response->assertStatus(200);
        $response->assertSee('User Directory');
        $response->assertSee('admin@estatex.com');
    }

    /**
     * Test properties page loads successfully.
     */
    public function test_authenticated_admin_can_access_properties_page(): void
    {
        $response = $this->withSession([
            'admin_user_id' => 1,
            'admin_user_name' => 'System Admin'
        ])->get('/admin/properties');

        $response->assertStatus(200);
        $response->assertSee('Property Listings');
        $response->assertSee('Luxury 3BHK Apartment');
    }

    /**
     * Test admin can access agent management page.
     */
    public function test_admin_can_access_agents_page(): void
    {
        $response = $this->withSession([
            'admin_user_id' => 1,
            'admin_user_name' => 'System Admin'
        ])->get('/admin/agents');

        $response->assertStatus(200);
        $response->assertSee('Agent Accounts');
        $response->assertSee('Sheikh Sadi');
    }

    /**
     * Test admin can update agent details.
     */
    public function test_admin_can_update_agent(): void
    {
        $response = $this->withSession([
            'admin_user_id' => 1,
            'admin_user_name' => 'System Admin'
        ])->post('/admin/agents/1/update', [
            'agency_name' => 'Khulna Realty Admin Mod',
            'license_no' => 'LIC-9982',
            'experience_years' => 6,
            'rating' => 4.90
        ]);

        $response->assertStatus(302);
        
        // Verify database update
        $agent = \Illuminate\Support\Facades\DB::select("SELECT agencyName, rating FROM agents WHERE id = 1");
        $this->assertEquals('Khulna Realty Admin Mod', $agent[0]->agencyname);
        $this->assertEquals(4.90, $agent[0]->rating);

        // Restore values
        \Illuminate\Support\Facades\DB::update("UPDATE agents SET agencyName = 'Khulna Realty', rating = 4.80 WHERE id = 1");
    }

    /**
     * Test admin can access bookings list.
     */
    public function test_admin_can_access_bookings_page(): void
    {
        $response = $this->withSession([
            'admin_user_id' => 1,
            'admin_user_name' => 'System Admin'
        ])->get('/admin/bookings');

        $response->assertStatus(200);
        $response->assertSee('Global Bookings Directory');
    }

    /**
     * Test admin can manage booking status and delete bookings.
     */
    public function test_admin_can_manage_bookings(): void
    {
        // 1. Insert temporary booking
        $nextBIdResult = \Illuminate\Support\Facades\DB::select("SELECT NVL(MAX(id), 0) + 1 AS next_id FROM bookings");
        $tempBookingId = $nextBIdResult[0]->next_id;

        \Illuminate\Support\Facades\DB::insert("
            INSERT INTO bookings (id, userId, propertyId, agentId, bookingType, status, totalAmount) 
            VALUES (:id, 4, 1, 1, 'visit', 'pending', 0)
        ", ['id' => $tempBookingId]);

        // 2. Action booking (Approve)
        $response = $this->withSession([
            'admin_user_id' => 1,
            'admin_user_name' => 'System Admin'
        ])->post("/admin/bookings/{$tempBookingId}/action", [
            'status' => 'approved'
        ]);

        $response->assertStatus(302);
        $booking = \Illuminate\Support\Facades\DB::select("SELECT status FROM bookings WHERE id = :id", ['id' => $tempBookingId]);
        $this->assertEquals('approved', $booking[0]->status);

        // 3. Delete booking
        $response = $this->withSession([
            'admin_user_id' => 1,
            'admin_user_name' => 'System Admin'
        ])->delete("/admin/bookings/{$tempBookingId}/delete");

        $response->assertStatus(302);
        $deleted = \Illuminate\Support\Facades\DB::select("SELECT id FROM bookings WHERE id = :id", ['id' => $tempBookingId]);
        $this->assertEmpty($deleted);
    }

    /**
     * Test admin can view transactions.
     */
    public function test_admin_can_view_transactions(): void
    {
        $response = $this->withSession([
            'admin_user_id' => 1,
            'admin_user_name' => 'System Admin'
        ])->get('/admin/transactions');

        $response->assertStatus(200);
        $response->assertSee('Financial Ledger');
    }

    /**
     * Test admin can view audit logs.
     */
    public function test_admin_can_view_audit_logs(): void
    {
        $response = $this->withSession([
            'admin_user_id' => 1,
            'admin_user_name' => 'System Admin'
        ])->get('/admin/audit-logs');

        $response->assertStatus(200);
        $response->assertSee('Action Audit Logs');
    }

    /**
     * Test admin can delete a user.
     */
    public function test_admin_can_delete_user(): void
    {
        // 1. Create a temporary user to delete
        $nextUIdResult = \Illuminate\Support\Facades\DB::select("SELECT NVL(MAX(id), 0) + 1 AS next_id FROM users");
        $tempUserId = $nextUIdResult[0]->next_id;

        \Illuminate\Support\Facades\DB::insert("
            INSERT INTO users (id, roleId, fullname, email, password, phone, status) 
            VALUES (:id, 3, 'Temp To Delete', 'temptodelete@estatex.com', 'secret', '01900000009', 'active')
        ", ['id' => $tempUserId]);

        // 2. Perform delete as admin
        $response = $this->withSession([
            'admin_user_id' => 1,
            'admin_user_name' => 'System Admin'
        ])->delete("/admin/users/delete/{$tempUserId}");

        $response->assertStatus(302);

        // 3. Verify user is deleted
        $deleted = \Illuminate\Support\Facades\DB::select("SELECT id FROM users WHERE id = :id", ['id' => $tempUserId]);
        $this->assertEmpty($deleted);
    }

    /**
     * Test admin can create user accounts.
     */
    public function test_admin_can_create_user_account(): void
    {
        $testEmail = 'newtestuser@estatex.com';
        \Illuminate\Support\Facades\DB::delete("DELETE FROM agents WHERE userId IN (SELECT id FROM users WHERE email = :email)", ['email' => $testEmail]);
        \Illuminate\Support\Facades\DB::delete("DELETE FROM users WHERE email = :email", ['email' => $testEmail]);

        $response = $this->withSession([
            'admin_user_id' => 1,
            'admin_user_name' => 'System Admin'
        ])->post('/admin/users/store', [
            'fullname' => 'Admin Created User',
            'email' => $testEmail,
            'password' => 'secret123',
            'phone' => '01711122233',
            'role_id' => 2 // Create an Agent
        ]);

        $response->assertStatus(302);
        
        $user = \Illuminate\Support\Facades\DB::select("SELECT id, fullname, roleId FROM users WHERE email = :email", ['email' => $testEmail]);
        $this->assertNotEmpty($user);
        $this->assertEquals(2, $user[0]->roleid);

        $agent = \Illuminate\Support\Facades\DB::select("SELECT id FROM agents WHERE userId = :userId", ['userId' => $user[0]->id]);
        $this->assertNotEmpty($agent);

        // Cleanup
        \Illuminate\Support\Facades\DB::delete("DELETE FROM agents WHERE userId = :userId", ['userId' => $user[0]->id]);
        \Illuminate\Support\Facades\DB::delete("DELETE FROM users WHERE id = :userId", ['userId' => $user[0]->id]);
    }

    /**
     * Test admin can update property status.
     */
    public function test_admin_can_update_property_status(): void
    {
        // Fetch first property
        $props = \Illuminate\Support\Facades\DB::select("SELECT id, status FROM properties WHERE ROWNUM = 1");
        if (empty($props)) {
            $this->markTestSkipped('No properties found to update.');
        }

        $propertyId = $props[0]->id;
        $originalStatus = $props[0]->status;

        $response = $this->withSession([
            'admin_user_id' => 1,
            'admin_user_name' => 'System Admin'
        ])->post("/admin/properties/{$propertyId}/status", [
            'status' => 'pending'
        ]);

        $response->assertStatus(302);

        $updated = \Illuminate\Support\Facades\DB::select("SELECT status FROM properties WHERE id = :id", ['id' => $propertyId]);
        $this->assertEquals('pending', $updated[0]->status);

        // Revert
        \Illuminate\Support\Facades\DB::update("UPDATE properties SET status = :status WHERE id = :id", [
            'status' => $originalStatus,
            'id' => $propertyId
        ]);
    }
}
