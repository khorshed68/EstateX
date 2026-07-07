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
        $response->assertRedirect('/agent/login');
        $response->assertSessionMissing('agent_user_id');

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

    /**
     * Test agent can view sales analytics and clients list.
     */
    public function test_agent_can_view_analytics_and_clients(): void
    {
        // 1. Test Analytics view
        $response = $this->withSession([
            'agent_user_id' => 2,
            'agent_user_name' => 'Sheikh Sadi',
            'agent_id' => 1
        ])->get('/agent/analytics');

        $response->assertStatus(200);
        $response->assertSee('Sales & Commission Analytics');
        $response->assertSee('Earned Commission');

        // 2. Test Clients view
        $response = $this->withSession([
            'agent_user_id' => 2,
            'agent_user_name' => 'Sheikh Sadi',
            'agent_id' => 1
        ])->get('/agent/clients');

        $response->assertStatus(200);
        $response->assertSee('My Active Clients CRM');
    }

    /**
     * Test agent calendar and availability management.
     */
    public function test_agent_can_manage_availability(): void
    {
        $sessionData = [
            'agent_user_id' => 2,
            'agent_user_name' => 'Sheikh Sadi',
            'agent_id' => 1
        ];

        // 1. Clean up existing test availability if any
        $testDate = date('Y-m-d', strtotime('+3 days'));
        DB::delete("DELETE FROM agent_availability WHERE agentId = 1 AND TRUNC(unavailableDate) = TO_DATE(:testDate, 'YYYY-MM-DD')", ['testDate' => $testDate]);

        // 2. View Calendar
        $response = $this->withSession($sessionData)->get('/agent/calendar');
        $response->assertStatus(200);
        $response->assertSee('Manage Availability Calendar');

        // 3. Store unavailable date
        $response = $this->withSession($sessionData)->post('/agent/calendar/store', [
            'unavailable_date' => $testDate,
            'reason' => 'Test Out of Office'
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        // Verify database entry exists
        $block = DB::select("SELECT id FROM agent_availability WHERE agentId = 1 AND TRUNC(unavailableDate) = TO_DATE(:testDate, 'YYYY-MM-DD')", ['testDate' => $testDate]);
        $this->assertNotEmpty($block);
        $blockId = $block[0]->id;

        // 4. Try storing duplicate date (should fail)
        $responseDuplicate = $this->withSession($sessionData)->post('/agent/calendar/store', [
            'unavailable_date' => $testDate,
            'reason' => 'Test Out of Office Duplicate'
        ]);
        $responseDuplicate->assertStatus(302);
        $responseDuplicate->assertSessionHas('error');

        // 5. Delete unavailable date
        $responseDelete = $this->withSession($sessionData)->delete("/agent/calendar/{$blockId}/delete");
        $responseDelete->assertStatus(302);
        $responseDelete->assertSessionHas('success');

        // Verify database entry deleted
        $blockCheck = DB::select("SELECT id FROM agent_availability WHERE id = :id", ['id' => $blockId]);
        $this->assertEmpty($blockCheck);
    }

    /**
     * Test buyer booking fails when agent is marked unavailable.
     */
    public function test_booking_fails_when_agent_is_unavailable(): void
    {
        // 1. Seed agent unavailability for tomorrow
        $blockDate = date('Y-m-d', strtotime('+1 day'));
        DB::delete("DELETE FROM agent_availability WHERE agentId = 1 AND TRUNC(unavailableDate) = TO_DATE(:blockDate, 'YYYY-MM-DD')", ['blockDate' => $blockDate]);
        
        DB::insert("
            INSERT INTO agent_availability (id, agentId, unavailableDate, reason)
            VALUES ((SELECT NVL(MAX(id), 0) + 1 FROM agent_availability), 1, TO_DATE(:blockDate, 'YYYY-MM-DD'), 'Test Vacation')
        ", ['blockDate' => $blockDate]);

        // 2. Try to book a site visit on the blocked date as a buyer
        $response = $this->withSession([
            'buyer_user_id' => 4,
            'buyer_user_name' => 'Abdur Rahim',
        ])->post('/buyer/bookings/store', [
            'property_id' => 1, // Seeded property represented by agent ID 1 (Sheikh Sadi)
            'booking_type' => 'visit',
            'visit_date' => $blockDate,
            'visit_slot' => '10:00 AM - 11:00 AM',
            'guests' => 2,
            'notes' => 'Testing agent block'
        ]);

        // Assert it is blocked and redirects back with error
        $response->assertStatus(302);
        $response->assertSessionHas('error');
        $this->assertStringContainsString('The representing agent is unavailable', session('error'));

        // 3. Cleanup availability block
        DB::delete("DELETE FROM agent_availability WHERE agentId = 1 AND TRUNC(unavailableDate) = TO_DATE(:blockDate, 'YYYY-MM-DD')", ['blockDate' => $blockDate]);
    }
}
