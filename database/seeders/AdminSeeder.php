<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Clean existing records in dependency-safe order
        DB::statement("BEGIN EXECUTE IMMEDIATE 'DELETE FROM wishlist'; EXCEPTION WHEN OTHERS THEN NULL; END;");
        DB::statement("BEGIN EXECUTE IMMEDIATE 'DELETE FROM agent_reviews'; EXCEPTION WHEN OTHERS THEN NULL; END;");
        DB::statement("BEGIN EXECUTE IMMEDIATE 'DELETE FROM reviews'; EXCEPTION WHEN OTHERS THEN NULL; END;");
        DB::statement("BEGIN EXECUTE IMMEDIATE 'DELETE FROM transactions'; EXCEPTION WHEN OTHERS THEN NULL; END;");
        DB::statement("BEGIN EXECUTE IMMEDIATE 'DELETE FROM bookings'; EXCEPTION WHEN OTHERS THEN NULL; END;");
        DB::statement("BEGIN EXECUTE IMMEDIATE 'DELETE FROM properties'; EXCEPTION WHEN OTHERS THEN NULL; END;");
        DB::statement("BEGIN EXECUTE IMMEDIATE 'DELETE FROM locations'; EXCEPTION WHEN OTHERS THEN NULL; END;");
        DB::statement("BEGIN EXECUTE IMMEDIATE 'DELETE FROM property_types'; EXCEPTION WHEN OTHERS THEN NULL; END;");
        DB::statement("BEGIN EXECUTE IMMEDIATE 'DELETE FROM agents'; EXCEPTION WHEN OTHERS THEN NULL; END;");
        DB::statement("BEGIN EXECUTE IMMEDIATE 'DELETE FROM users'; EXCEPTION WHEN OTHERS THEN NULL; END;");
        DB::statement("BEGIN EXECUTE IMMEDIATE 'DELETE FROM roles'; EXCEPTION WHEN OTHERS THEN NULL; END;");
        
        // Commit cleaning
        DB::statement("COMMIT");

        // 2. Seed Roles
        DB::insert("INSERT INTO roles (id, roleName, roleDescription) VALUES (1, 'admin', 'System Administrator with full dashboard access')");
        DB::insert("INSERT INTO roles (id, roleName, roleDescription) VALUES (2, 'agent', 'Real estate agents representing owners')");
        DB::insert("INSERT INTO roles (id, roleName, roleDescription) VALUES (3, 'user', 'Standard buyers, renters, and tenants')");

        // 3. Seed Users
        $adminPassword = Hash::make('admin123');
        $agentPassword = Hash::make('agent123');
        $userPassword = Hash::make('user123');

        DB::insert("INSERT INTO users (id, roleId, fullname, email, password, phone, status) VALUES (1, 1, 'System Admin', 'admin@estatex.com', ?, '01700000001', 'active')", [$adminPassword]);
        DB::insert("INSERT INTO users (id, roleId, fullname, email, password, phone, status) VALUES (2, 2, 'Sheikh Sadi', 'sadi@estatex.com', ?, '01700000002', 'active')", [$agentPassword]);
        DB::insert("INSERT INTO users (id, roleId, fullname, email, password, phone, status) VALUES (3, 2, 'Tariq Anam', 'tariq@estatex.com', ?, '01700000003', 'active')", [$agentPassword]);
        DB::insert("INSERT INTO users (id, roleId, fullname, email, password, phone, status) VALUES (4, 3, 'Rahim Ahmed', 'rahim@estatex.com', ?, '01700000004', 'active')", [$userPassword]);
        DB::insert("INSERT INTO users (id, roleId, fullname, email, password, phone, status) VALUES (5, 3, 'Karim Uddin', 'karim@estatex.com', ?, '01700000005', 'active')", [$userPassword]);

        // 4. Seed Agents
        DB::insert("INSERT INTO agents (id, userId, agencyName, licenseNo, experienceYears, about, rating) VALUES (1, 2, 'Khulna Realty', 'LIC-9982', 5, 'Expert in KUET campus and KDA properties.', 4.80)");
        DB::insert("INSERT INTO agents (id, userId, agencyName, licenseNo, experienceYears, about, rating) VALUES (2, 3, 'Boyra Properties', 'LIC-3341', 8, 'Serving Khulna city with transparency.', 4.20)");

        // 5. Seed Property Types
        DB::insert("INSERT INTO property_types (id, typeName, typeDescription) VALUES (1, 'Apartment', 'Residential flat or apartment block units')");
        DB::insert("INSERT INTO property_types (id, typeName, typeDescription) VALUES (2, 'Villa', 'Standalone luxury bungalow or villa')");
        DB::insert("INSERT INTO property_types (id, typeName, typeDescription) VALUES (3, 'Commercial', 'Offices, shops, or warehouses')");

        // 6. Seed Locations
        DB::insert("INSERT INTO locations (id, areaName, city, country) VALUES (1, 'KUET Campus Area', 'Khulna', 'Bangladesh')");
        DB::insert("INSERT INTO locations (id, areaName, city, country) VALUES (2, 'KDA Avenue', 'Khulna', 'Bangladesh')");
        DB::insert("INSERT INTO locations (id, areaName, city, country) VALUES (3, 'Sonadanga', 'Khulna', 'Bangladesh')");
        DB::insert("INSERT INTO locations (id, areaName, city, country) VALUES (4, 'Boyra', 'Khulna', 'Bangladesh')");

        // 7. Seed Properties
        DB::insert("INSERT INTO properties (id, ownerId, agentId, typeId, locationId, title, propDescription, price, areaSize, bedrooms, bathrooms, furnishedStatus, parking, balcony, lift, status) VALUES (1, 4, 1, 1, 1, 'Luxury 3BHK Apartment near KUET', 'Beautiful and spacious 3BHK flat located inside the serene campus region of KUET. Highly suitable for families, students, or lecturers.', 12000000, 1600, 3, 3, 'furnished', 1, 2, 1, 'available')");
        DB::insert("INSERT INTO properties (id, ownerId, agentId, typeId, locationId, title, propDescription, price, areaSize, bedrooms, bathrooms, furnishedStatus, parking, balcony, swimmingPool, status) VALUES (2, 4, 2, 2, 3, 'Modern Duplex Villa in Sonadanga', 'High-end smart duplex villa featuring private pool, automated security, pet-friendly layout, and wide garden yard.', 35000000, 3200, 4, 5, 'semi-furnished', 2, 3, 1, 'available')");
        DB::insert("INSERT INTO properties (id, ownerId, agentId, typeId, locationId, title, propDescription, price, areaSize, status) VALUES (3, 5, 1, 3, 2, 'Commercial Office Space KDA Avenue', 'Premium quality commercial office space, fully air-conditioned, high-speed elevator access, 24/7 power backup.', 45000000, 2500, 'available')");

        // 8. Seed Bookings
        DB::insert("INSERT INTO bookings (id, userId, propertyId, agentId, bookingType, status, totalAmount) VALUES (1, 5, 1, 1, 'visit', 'completed', 0)");
        DB::insert("INSERT INTO bookings (id, userId, propertyId, agentId, bookingType, status, totalAmount) VALUES (2, 4, 3, 1, 'reservation', 'completed', 500000)");

        // 9. Seed Transactions
        DB::insert("INSERT INTO transactions (id, bookingId, transactionType, amount, paymentMethod, referenceNo, status) VALUES (1, 2, 'booking_fee', 500000, 'bank_transfer', 'TXN-20260622-001', 'completed')");

        // 10. Seed Reviews & Agent Reviews
        DB::insert("INSERT INTO reviews (id, userId, propertyId, rating, comments) VALUES (1, 5, 1, 5, 'Perfect location, very quiet, and the layout is outstanding!')");
        DB::insert("INSERT INTO agent_reviews (id, userId, agentId, rating, comments) VALUES (1, 5, 1, 5, 'Sheikh Sadi was extremely helpful throughout the entire site tour and negotiation process.')");

        // 11. Seed Wishlists
        DB::insert("INSERT INTO wishlist (id, userId, propertyId) VALUES (1, 5, 2)");

        // Commit all inserts
        DB::statement("COMMIT");
    }
}
