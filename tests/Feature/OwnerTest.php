<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;

class OwnerTest extends TestCase
{
    /**
     * Test owner login page renders successfully.
     */
    public function test_owner_login_page_renders(): void
    {
        $response = $this->get('/owner/login');
        $response->assertStatus(200);
        $response->assertSee('Owner Sign In');
    }

    /**
     * Test login failure with invalid credentials.
     */
    public function test_owner_login_fails_with_invalid_credentials(): void
    {
        $response = $this->post('/owner/login', [
            'email' => 'wrongowner@estatex.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['email']);
    }

    /**
     * Test login success with valid credentials.
     */
    public function test_owner_login_success_with_valid_credentials(): void
    {
        $response = $this->post('/owner/login', [
            'email' => 'karim@estatex.com', // Seeded landlord user
            'password' => 'user123',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/owner/dashboard');
        $response->assertSessionHas('owner_user_id', 5);
    }

    /**
     * Test guest is blocked from accessing owner dashboard.
     */
    public function test_guest_cannot_access_owner_dashboard(): void
    {
        $response = $this->get('/owner/dashboard');
        $response->assertStatus(302);
        $response->assertRedirect('/owner/login');
    }

    /**
     * Test owner can view their dashboard when authenticated.
     */
    public function test_authenticated_owner_can_access_dashboard(): void
    {
        $response = $this->withSession([
            'owner_user_id' => 5,
            'owner_user_name' => 'Karim Uddin'
        ])->get('/owner/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Owner Portfolio Dashboard');
        $response->assertSee('Listed Properties');
    }

    /**
     * Test owner registration flow.
     */
    public function test_owner_registration_success(): void
    {
        // Delete test owner if exists
        DB::delete("DELETE FROM users WHERE email = :email", ['email' => 'ownertest@estatex.com']);

        $response = $this->post('/owner/register', [
            'fullname' => 'Test Landlord',
            'email' => 'ownertest@estatex.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'phone' => '01899999999',
            'profile_image' => \Illuminate\Http\UploadedFile::fake()->create('avatar.jpg', 100, 'image/jpeg')
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/owner/login');
        $response->assertSessionMissing('owner_user_id');

        // Check exists in DB
        $user = DB::select("SELECT id, profileImage FROM users WHERE email = 'ownertest@estatex.com'");
        $this->assertNotEmpty($user);
        $this->assertNotEmpty($user[0]->profileimage);

        // Clean up
        if (!empty($user[0]->profileimage) && file_exists(public_path($user[0]->profileimage))) {
            unlink(public_path($user[0]->profileimage));
        }
        DB::delete("DELETE FROM users WHERE email = :email", ['email' => 'ownertest@estatex.com']);
    }

    /**
     * Test creating a property listing.
     */
    public function test_owner_can_create_property_listing(): void
    {
        // Delete test property if exists
        DB::delete("DELETE FROM properties WHERE title = :title", ['title' => 'Test Automated Listing']);

        $response = $this->withSession([
            'owner_user_id' => 5,
            'owner_user_name' => 'Karim Uddin'
        ])->post('/owner/properties/store', [
            'title' => 'Test Automated Listing',
            'prop_description' => 'Beautiful automated test property listing description.',
            'price' => 12500000,
            'area_size' => 1500,
            'bedrooms' => 3,
            'bathrooms' => 3,
            'location_id' => 1,
            'type_id' => 1,
            'furnished_status' => 'semi-furnished',
            'parking' => 1,
            'balcony' => 2,
            'lift' => 1,
            'swimming_pool' => 0,
            'pet_friendly' => 1,
            'agent_id' => 1,
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/owner/dashboard');

        // Verify inserted into DB
        $property = DB::select("SELECT id FROM properties WHERE title = 'Test Automated Listing'");
        $this->assertNotEmpty($property);

        // Clean up
        DB::delete("DELETE FROM properties WHERE title = 'Test Automated Listing'");
    }

    /**
     * Test updating a property listing.
     */
    public function test_owner_can_update_property_listing(): void
    {
        // Target property 1 specifically
        $propId = 1;

        $response = $this->withSession([
            'owner_user_id' => 5,
            'owner_user_name' => 'Karim Uddin'
        ])->post("/owner/properties/{$propId}/update", [
            'title' => 'Updated Test Listing Title',
            'prop_description' => 'Updated automated description text.',
            'price' => 22000000,
            'area_size' => 2000,
            'bedrooms' => 4,
            'bathrooms' => 4,
            'location_id' => 1,
            'type_id' => 1,
            'furnished_status' => 'furnished',
            'parking' => 2,
            'balcony' => 3,
            'lift' => 1,
            'swimming_pool' => 1,
            'pet_friendly' => 1,
            'agent_id' => 1,
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/owner/dashboard');

        // Verify updated in DB
        $updatedProp = DB::select("SELECT title, price FROM properties WHERE id = :id", ['id' => $propId]);
        $this->assertEquals('Updated Test Listing Title', $updatedProp[0]->title);
        $this->assertEquals(22000000, $updatedProp[0]->price);

        // Revert to original seeded state to prevent interference with other tests
        DB::update("
            UPDATE properties SET 
                title = 'Luxury 3BHK Apartment near KUET',
                propDescription = 'Beautiful and spacious 3BHK flat located inside the serene campus region of KUET. Highly suitable for families, students, or lecturers.',
                price = 12000000,
                areaSize = 1600,
                bedrooms = 3,
                bathrooms = 3,
                furnishedStatus = 'furnished',
                parking = 1,
                balcony = 2,
                lift = 1,
                swimmingPool = 0,
                petFriendly = 1
            WHERE id = :id
        ", ['id' => $propId]);
    }

    /**
     * Test deleting a property listing.
     */
    public function test_owner_can_delete_property_listing(): void
    {
        // Create temporary property to delete
        $nextIdResult = DB::select("SELECT NVL(MAX(id), 0) + 1 AS next_id FROM properties");
        $tempId = $nextIdResult[0]->next_id;

        DB::insert("
            INSERT INTO properties (id, ownerId, typeId, locationId, title, price, areaSize, status) 
            VALUES (:id, 5, 1, 1, 'Temporary Listing to Delete', 5000000, 1000, 'available')
        ", ['id' => $tempId]);

        $response = $this->withSession([
            'owner_user_id' => 5,
            'owner_user_name' => 'Karim Uddin'
        ])->delete("/owner/properties/{$tempId}/delete");

        $response->assertStatus(302);
        $response->assertRedirect('/owner/dashboard');

        // Verify deleted from DB
        $deleted = DB::select("SELECT id FROM properties WHERE id = :id", ['id' => $tempId]);
        $this->assertEmpty($deleted);
    }

    /**
     * Test accessing bookings page.
     */
    public function test_owner_can_access_bookings_page(): void
    {
        $response = $this->withSession([
            'owner_user_id' => 5,
            'owner_user_name' => 'Karim Uddin'
        ])->get('/owner/bookings');

        $response->assertStatus(200);
        $response->assertSee('Scheduled Tours');
    }

    /**
     * Test changing booking status (Approve booking request).
     */
    public function test_owner_can_manage_booking_status(): void
    {
        // Ensure at least one booking exists on owner's property
        $bookings = DB::select("
            SELECT b.id 
            FROM bookings b 
            JOIN properties p ON b.propertyId = p.id 
            WHERE p.ownerId = 5 AND ROWNUM = 1
        ");
        
        if (empty($bookings)) {
            // Seed a mock booking
            $nextBookingIdResult = DB::select("SELECT NVL(MAX(id), 0) + 1 AS next_id FROM bookings");
            $bookingId = $nextBookingIdResult[0]->next_id;
            DB::insert("
                INSERT INTO bookings (id, userId, propertyId, bookingType, status, totalAmount) 
                VALUES (:id, 4, 1, 'visit', 'pending', 0)
            ", ['id' => $bookingId]);
            $bId = $bookingId;
        } else {
            $bId = $bookings[0]->id;
        }

        // Set status to pending first for deterministic testing
        DB::update("UPDATE bookings SET status = 'pending' WHERE id = :id", ['id' => $bId]);

        // Post approve
        $response = $this->withSession([
            'owner_user_id' => 5,
            'owner_user_name' => 'Karim Uddin'
        ])->post("/owner/bookings/{$bId}/approve");

        $response->assertStatus(302);
        
        $updated = DB::select("SELECT status FROM bookings WHERE id = :id", ['id' => $bId]);
        $this->assertEquals('approved', $updated[0]->status);

        // Revert booking status to completed
        DB::update("UPDATE bookings SET status = 'completed' WHERE id = :id", ['id' => $bId]);
    }

    /**
     * Test assigning an agent to represent property.
     */
    public function test_owner_can_assign_agent_to_property(): void
    {
        $prop = DB::select("SELECT id FROM properties WHERE ownerId = 5 AND ROWNUM = 1");
        $this->assertNotEmpty($prop);
        $propId = $prop[0]->id;

        $response = $this->withSession([
            'owner_user_id' => 5,
            'owner_user_name' => 'Karim Uddin'
        ])->post("/owner/properties/{$propId}/assign-agent", [
            'agent_id' => 1
        ]);

        $response->assertStatus(302);
        
        $updated = DB::select("SELECT agentId FROM properties WHERE id = :id", ['id' => $propId]);
        $this->assertEquals(1, $updated[0]->agentid);
    }
}
