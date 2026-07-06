<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;

class BuyerTest extends TestCase
{
    /**
     * Test guest is redirected to login from root if not logged in.
     */
    public function test_buyer_login_page_renders(): void
    {
        $response = $this->get('/buyer/login');
        $response->assertStatus(200);
        $response->assertSee('Buyer Sign In');
    }

    /**
     * Test login failure with wrong credentials.
     */
    public function test_buyer_login_fails_with_invalid_credentials(): void
    {
        $response = $this->post('/buyer/login', [
            'email' => 'wrongbuyer@estatex.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['email']);
    }

    /**
     * Test login success with valid credentials.
     */
    public function test_buyer_login_success_with_valid_credentials(): void
    {
        $response = $this->post('/buyer/login', [
            'email' => 'rahim@estatex.com',
            'password' => 'user123',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/buyer/dashboard');
        $response->assertSessionHas('buyer_user_id', 4);
    }

    /**
     * Test dashboard access is blocked for guests.
     */
    public function test_guest_cannot_access_buyer_dashboard(): void
    {
        $response = $this->get('/buyer/dashboard');
        $response->assertStatus(302);
        $response->assertRedirect('/buyer/login');
    }

    /**
     * Test dashboard loads successfully when authenticated.
     */
    public function test_authenticated_buyer_can_access_dashboard(): void
    {
        $response = $this->withSession([
            'buyer_user_id' => 4,
            'buyer_user_name' => 'Rahim Ahmed'
        ])->get('/buyer/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Marketplace');
        $response->assertSee('Rahim Ahmed');
    }

    /**
     * Test property details show page loads successfully.
     */
    public function test_authenticated_buyer_can_view_property_details(): void
    {
        $response = $this->withSession([
            'buyer_user_id' => 4,
            'buyer_user_name' => 'Rahim Ahmed'
        ])->get('/buyer/properties/1');

        $response->assertStatus(200);
        $response->assertSee('Luxury 3BHK Apartment');
        $response->assertSee('Schedule Visit');
    }

    /**
     * Test wishlist page loads.
     */
    public function test_authenticated_buyer_can_access_wishlist_page(): void
    {
        $response = $this->withSession([
            'buyer_user_id' => 4,
            'buyer_user_name' => 'Rahim Ahmed'
        ])->get('/buyer/wishlist');

        $response->assertStatus(200);
        $response->assertSee('My Wishlist');
    }

    /**
     * Test adding to wishlist.
     */
    public function test_buyer_can_add_and_remove_wishlist(): void
    {
        // Delete any existing wishlist to make the test deterministic
        DB::delete("DELETE FROM wishlist WHERE userId = 4 AND propertyId = 1");

        // Add
        $response = $this->withSession([
            'buyer_user_id' => 4,
            'buyer_user_name' => 'Rahim Ahmed'
        ])->post('/buyer/wishlist/add/1');

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        // Check exists
        $wish = DB::select("SELECT id FROM wishlist WHERE userId = 4 AND propertyId = 1");
        $this->assertNotEmpty($wish);

        // Remove
        $response = $this->withSession([
            'buyer_user_id' => 4,
            'buyer_user_name' => 'Rahim Ahmed'
        ])->post('/buyer/wishlist/remove/1');

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        // Check deleted
        $wish = DB::select("SELECT id FROM wishlist WHERE userId = 4 AND propertyId = 1");
        $this->assertEmpty($wish);
    }

    /**
     * Test bookings page loads.
     */
    public function test_authenticated_buyer_can_access_bookings_page(): void
    {
        $response = $this->withSession([
            'buyer_user_id' => 4,
            'buyer_user_name' => 'Rahim Ahmed'
        ])->get('/buyer/bookings');

        $response->assertStatus(200);
        $response->assertSee('My Bookings');
    }

    /**
     * Test booking creation.
     */
    public function test_buyer_can_create_site_visit_booking(): void
    {
        $response = $this->withSession([
            'buyer_user_id' => 4,
            'buyer_user_name' => 'Rahim Ahmed'
        ])->post('/buyer/bookings/store', [
            'property_id' => 1,
            'booking_type' => 'visit',
            'visit_date' => date('Y-m-d H:i:s', strtotime('+2 days')),
            'guests' => 2,
            'notes' => 'Looking forward to the visit'
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/buyer/bookings');
        $response->assertSessionHas('success');
    }

    /**
     * Test registration page renders.
     */
    public function test_buyer_register_page_renders(): void
    {
        $response = $this->get('/buyer/register');
        $response->assertStatus(200);
        $response->assertSee('Create Account');
    }

    /**
     * Test registration fails with validation errors (e.g. password mismatch).
     */
    public function test_buyer_register_fails_with_validation_errors(): void
    {
        $response = $this->post('/buyer/register', [
            'fullname' => 'New User',
            'email' => 'newuser@estatex.com',
            'password' => 'secret123',
            'password_confirmation' => 'mismatch',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['password']);
    }

    /**
     * Test registration fails with duplicate email.
     */
    public function test_buyer_register_fails_with_duplicate_email(): void
    {
        $response = $this->post('/buyer/register', [
            'fullname' => 'Duplicate User',
            'email' => 'rahim@estatex.com', // Already seeded
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['email']);
    }

    /**
     * Test registration success.
     */
    public function test_buyer_register_success(): void
    {
        // Delete test user if exists
        DB::delete("DELETE FROM users WHERE email = :email", ['email' => 'registertest@estatex.com']);

        $response = $this->post('/buyer/register', [
            'fullname' => 'Register Test User',
            'email' => 'registertest@estatex.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'phone' => '01799999999',
            'profile_image' => \Illuminate\Http\UploadedFile::fake()->create('avatar.jpg', 100, 'image/jpeg')
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/buyer/dashboard');
        $response->assertSessionHas('buyer_user_id');

        // Verify exists in database
        $user = DB::select("SELECT id, fullname, roleId, profileImage FROM users WHERE email = :email", ['email' => 'registertest@estatex.com']);
        $this->assertNotEmpty($user);
        $this->assertEquals(3, $user[0]->roleid); // Should have buyer roleId = 3
        $this->assertNotEmpty($user[0]->profileimage);

        // Clean up
        if (!empty($user[0]->profileimage) && file_exists(public_path($user[0]->profileimage))) {
            unlink(public_path($user[0]->profileimage));
        }
        DB::delete("DELETE FROM users WHERE email = :email", ['email' => 'registertest@estatex.com']);
    }

    /**
     * Test buyer dashboard sorting by price.
     */
    public function test_buyer_dashboard_sorting(): void
    {
        $response = $this->withSession([
            'buyer_user_id' => 4,
            'buyer_user_name' => 'Rahim Ahmed'
        ])->get('/buyer/dashboard?sort=price_asc');

        $response->assertStatus(200);
        $response->assertSee('Real Estate Marketplace');
        
        $properties = $response->viewData('properties');
        $this->assertNotEmpty($properties);
        
        // Assert that prices are in ascending order
        for ($i = 0; $i < count($properties) - 1; $i++) {
            $this->assertTrue($properties[$i]->price <= $properties[$i+1]->price);
        }
    }

    /**
     * Test property comparison flow.
     */
    public function test_buyer_comparison_system(): void
    {
        // Clean database first
        DB::delete("DELETE FROM comparisons WHERE userId = 4");

        // 1. Add comparison
        $response = $this->withSession([
            'buyer_user_id' => 4,
            'buyer_user_name' => 'Rahim Ahmed'
        ])->post('/buyer/comparisons/add', [
            'property_id_1' => 1,
            'property_id_2' => 2
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/buyer/comparisons');

        // Verify comparison pair in database
        $comp = DB::select("SELECT id FROM comparisons WHERE userId = 4 AND propertyId1 = 1 AND propertyId2 = 2");
        $this->assertNotEmpty($comp);
        $compId = $comp[0]->id;

        // 2. View comparisons page
        $response = $this->withSession([
            'buyer_user_id' => 4,
            'buyer_user_name' => 'Rahim Ahmed'
        ])->get('/buyer/comparisons');

        $response->assertStatus(200);
        $response->assertSee('Listing Comparisons');
        $response->assertSee('Luxury 3BHK Apartment');
        $response->assertSee('Modern Duplex Villa in Sonadanga');

        // 3. Remove comparison
        $response = $this->withSession([
            'buyer_user_id' => 4,
            'buyer_user_name' => 'Rahim Ahmed'
        ])->delete("/buyer/comparisons/remove/{$compId}");

        $response->assertStatus(302);
        
        // Verify deleted from database
        $compDeleted = DB::select("SELECT id FROM comparisons WHERE id = :id", ['id' => $compId]);
        $this->assertEmpty($compDeleted);
    }
}
