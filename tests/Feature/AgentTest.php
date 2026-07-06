<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AgentTest extends TestCase
{
    /**
     * Test agent login page renders successfully.
     */
    public function test_agent_login_page_renders(): void
    {
        $response = $this->get('/agent/login');
        $response->assertStatus(200);
        $response->assertSee('Agent Sign In');
    }

    /**
     * Test login failure with invalid credentials.
     */
    public function test_agent_login_fails_with_invalid_credentials(): void
    {
        $response = $this->post('/agent/login', [
            'email' => 'wrongagent@estatex.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['email']);
    }

    /**
     * Test login success with valid credentials.
     */
    public function test_agent_login_success_with_valid_credentials(): void
    {
        $response = $this->post('/agent/login', [
            'email' => 'sadi@estatex.com', // Seeded agent user
            'password' => 'agent123',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/agent/dashboard');
        $response->assertSessionHas('agent_user_id', 2);
        $response->assertSessionHas('agent_id', 1);
    }

    /**
     * Test guest is blocked from accessing agent dashboard.
     */
    public function test_guest_cannot_access_agent_dashboard(): void
    {
        $response = $this->get('/agent/dashboard');
        $response->assertStatus(302);
        $response->assertRedirect('/agent/login');
    }

    /**
     * Test agent can view their dashboard when authenticated.
     */
    public function test_authenticated_agent_can_access_dashboard(): void
    {
        $response = $this->withSession([
            'agent_user_id' => 2,
            'agent_user_name' => 'Sheikh Sadi',
            'agent_id' => 1
        ])->get('/agent/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Dashboard Overview');
        $response->assertSee('Assigned Properties');
        $response->assertSee('Total Bookings');
    }

    /**
     * Test agent registration flow.
     */
    public function test_agent_registration_success(): void
    {
        // Cleanup test agent if exists in safe dependency order
        $existing = DB::select("SELECT id FROM users WHERE email = 'testagent@estatex.com'");
        if (!empty($existing)) {
            $uId = $existing[0]->id;
            DB::delete("DELETE FROM agents WHERE userId = :userid", ['userid' => $uId]);
            DB::delete("DELETE FROM users WHERE id = :userid", ['userid' => $uId]);
        }

        $response = $this->post('/agent/register', [
            'fullname' => 'Test Agent Register',
            'email' => 'testagent@estatex.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'phone' => '01855555555',
            'agency_name' => 'Test Agency Co.',
            'license_no' => 'LIC-7728',
            'experience_years' => 4,
            'about' => 'Bio of test agent.',
            'profile_image' => \Illuminate\Http\UploadedFile::fake()->create('avatar.jpg', 100, 'image/jpeg')
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/agent/dashboard');
        $response->assertSessionHas('agent_user_id');
        $response->assertSessionHas('agent_id');

        // Check exists in DB
        $user = DB::select("SELECT id, profileImage FROM users WHERE email = 'testagent@estatex.com'");
        $this->assertNotEmpty($user);
        $this->assertNotEmpty($user[0]->profileimage);

        $agent = DB::select("SELECT id FROM agents WHERE userId = :userId", ['userId' => $user[0]->id]);
        $this->assertNotEmpty($agent);

        // Cleanup after test case
        $uId = $user[0]->id;
        if (!empty($user[0]->profileimage) && file_exists(public_path($user[0]->profileimage))) {
            unlink(public_path($user[0]->profileimage));
        }
        DB::delete("DELETE FROM agents WHERE userId = :userid", ['userid' => $uId]);
        DB::delete("DELETE FROM users WHERE id = :userid", ['userid' => $uId]);
    }

    /**
     * Test agent can view assigned properties.
     */
    public function test_agent_can_view_assigned_properties(): void
    {
        $response = $this->withSession([
            'agent_user_id' => 2,
            'agent_user_name' => 'Sheikh Sadi',
            'agent_id' => 1
        ])->get('/agent/properties');

        $response->assertStatus(200);
        $response->assertSee('Represented Properties');
    }

    /**
     * Test agent can view bookings.
     */
    public function test_agent_can_view_bookings(): void
    {
        $response = $this->withSession([
            'agent_user_id' => 2,
            'agent_user_name' => 'Sheikh Sadi',
            'agent_id' => 1
        ])->get('/agent/bookings');

        $response->assertStatus(200);
        $response->assertSee('Customer Tour Requests');
    }

    /**
     * Test booking status action (approve, complete, reject).
     */
    public function test_agent_can_action_bookings(): void
    {
        // 1. Insert a temporary booking to represent/action
        $nextBIdResult = DB::select("SELECT NVL(MAX(id), 0) + 1 AS next_id FROM bookings");
        $tempBookingId = $nextBIdResult[0]->next_id;

        DB::insert("
            INSERT INTO bookings (id, userId, propertyId, agentId, bookingType, status, totalAmount) 
            VALUES (:id, 4, 1, 1, 'visit', 'pending', 0)
        ", ['id' => $tempBookingId]);

        // 2. Test Approve Booking
        $response = $this->withSession([
            'agent_user_id' => 2,
            'agent_user_name' => 'Sheikh Sadi',
            'agent_id' => 1
        ])->post("/agent/bookings/{$tempBookingId}/approve");

        $response->assertStatus(302);
        $bookingStatus = DB::select("SELECT status FROM bookings WHERE id = :id", ['id' => $tempBookingId]);
        $this->assertEquals('approved', $bookingStatus[0]->status);

        // 3. Test Complete Booking
        $response = $this->withSession([
            'agent_user_id' => 2,
            'agent_user_name' => 'Sheikh Sadi',
            'agent_id' => 1
        ])->post("/agent/bookings/{$tempBookingId}/complete");

        $response->assertStatus(302);
        $bookingStatus = DB::select("SELECT status FROM bookings WHERE id = :id", ['id' => $tempBookingId]);
        $this->assertEquals('completed', $bookingStatus[0]->status);

        // 4. Cleanup
        DB::delete("DELETE FROM bookings WHERE id = :id", ['id' => $tempBookingId]);
    }

    /**
     * Test agent can update profile.
     */
    public function test_agent_can_update_profile(): void
    {
        $response = $this->withSession([
            'agent_user_id' => 2,
            'agent_user_name' => 'Sheikh Sadi',
            'agent_id' => 1
        ])->post('/agent/profile/update', [
            'fullname' => 'Sheikh Sadi Updated',
            'email' => 'sadi@estatex.com',
            'phone' => '01700000002',
            'agency_name' => 'Khulna Realty Team',
            'license_no' => 'LIC-9982-UPD',
            'experience_years' => 6,
            'about' => 'KUET Campus expert updated bio.'
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/agent/profile');

        // Check if database updated
        $user = DB::select("SELECT fullname FROM users WHERE id = 2");
        $this->assertEquals('Sheikh Sadi Updated', $user[0]->fullname);

        $agent = DB::select("SELECT licenseNo, experienceYears FROM agents WHERE id = 1");
        $this->assertEquals('LIC-9982-UPD', $agent[0]->licenseno);
        $this->assertEquals(6, $agent[0]->experienceyears);

        // Restore original values to prevent dirty DB state
        DB::update("UPDATE users SET fullname = 'Sheikh Sadi' WHERE id = 2");
        DB::update("UPDATE agents SET licenseNo = 'LIC-9982', experienceYears = 5, agencyName = 'Khulna Realty', about = 'Expert in KUET campus and KDA properties.' WHERE id = 1");
    }
}
