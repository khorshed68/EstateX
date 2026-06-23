<?php

namespace Tests\Feature;

use Tests\TestCase;

class AdminTest extends TestCase
{
    /**
     * Test that guest is redirected to admin login page.
     */
    public function test_guest_redirected_to_login(): void
    {
        $response = $this->get('/');
        $response->assertStatus(302);
        $response->assertRedirect('/admin/login');
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
}
