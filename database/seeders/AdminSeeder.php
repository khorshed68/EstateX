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
        DB::insert("INSERT INTO roles (id, roleName, roleDescription) VALUES (3, 'buyer', 'Standard buyers, renters, and tenants')");
        DB::insert("INSERT INTO roles (id, roleName, roleDescription) VALUES (4, 'owner', 'Property owners listing real estate')");

        // 3. Seed Users
        $adminPassword = Hash::make('admin123');
        $agentPassword = Hash::make('agent123');
        $userPassword = Hash::make('user123');

        DB::insert("INSERT INTO users (id, roleId, fullname, email, password, phone, status) VALUES (1, 1, 'System Admin', 'admin@estatex.com', ?, '01700000001', 'active')", [$adminPassword]);
        DB::insert("INSERT INTO users (id, roleId, fullname, email, password, phone, status) VALUES (2, 2, 'Sheikh Sadi', 'sadi@estatex.com', ?, '01700000002', 'active')", [$agentPassword]);
        DB::insert("INSERT INTO users (id, roleId, fullname, email, password, phone, status) VALUES (3, 2, 'Tariq Anam', 'tariq@estatex.com', ?, '01700000003', 'active')", [$agentPassword]);
        DB::insert("INSERT INTO users (id, roleId, fullname, email, password, phone, status) VALUES (4, 3, 'Rahim Ahmed', 'rahim@estatex.com', ?, '01700000004', 'active')", [$userPassword]);
        DB::insert("INSERT INTO users (id, roleId, fullname, email, password, phone, status) VALUES (5, 4, 'Karim Uddin', 'karim@estatex.com', ?, '01700000005', 'active')", [$userPassword]);

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
        DB::insert("INSERT INTO properties (id, ownerId, agentId, typeId, locationId, title, propDescription, price, areaSize, bedrooms, bathrooms, furnishedStatus, parking, balcony, lift, status) VALUES (1, 5, 1, 1, 1, 'Luxury 3BHK Apartment near KUET', 'Beautiful and spacious 3BHK flat located inside the serene campus region of KUET. Highly suitable for families, students, or lecturers.', 12000000, 1600, 3, 3, 'furnished', 1, 2, 1, 'available')");
        DB::insert("INSERT INTO properties (id, ownerId, agentId, typeId, locationId, title, propDescription, price, areaSize, bedrooms, bathrooms, furnishedStatus, parking, balcony, swimmingPool, status) VALUES (2, 5, 2, 2, 3, 'Modern Duplex Villa in Sonadanga', 'High-end smart duplex villa featuring private pool, automated security, pet-friendly layout, and wide garden yard.', 35000000, 3200, 4, 5, 'semi-furnished', 2, 3, 1, 'available')");
        DB::insert("INSERT INTO properties (id, ownerId, agentId, typeId, locationId, title, propDescription, price, areaSize, status) VALUES (3, 5, 1, 3, 2, 'Commercial Office Space KDA Avenue', 'Premium quality commercial office space, fully air-conditioned, high-speed elevator access, 24/7 power backup.', 45000000, 2500, 'available')");
        DB::insert("INSERT INTO properties (id, ownerId, agentId, typeId, locationId, title, propDescription, price, areaSize, bedrooms, bathrooms, furnishedStatus, parking, balcony, lift, status) VALUES (4, 5, 1, 1, 3, 'Premium 3BHK Flat in Sonadanga', 'Spacious flat in Sonadanga, ideal for middle-class families. Close to hospitals and schools.', 15000000, 1800, 3, 3, 'semi-furnished', 1, 2, 1, 'available')");
        DB::insert("INSERT INTO properties (id, ownerId, agentId, typeId, locationId, title, propDescription, price, areaSize, bedrooms, bathrooms, furnishedStatus, parking, balcony, lift, status) VALUES (5, 5, 2, 1, 4, 'Cozy 2BHK Apartment near Boyra', 'Affordable residential flat located near Boyra. Safe neighborhood and well-ventilated rooms.', 8500000, 1200, 2, 2, 'unfurnished', 1, 1, 0, 'available')");
        DB::insert("INSERT INTO properties (id, ownerId, agentId, typeId, locationId, title, propDescription, price, areaSize, bedrooms, bathrooms, furnishedStatus, parking, balcony, lift, status) VALUES (6, 5, 1, 1, 2, 'Luxury Penthouse in KDA Avenue', 'Elegant smart penthouse on the top floor of KDA complex, offering premium lifestyle.', 28000000, 2800, 4, 4, 'furnished', 2, 3, 1, 'available')");
        DB::insert("INSERT INTO properties (id, ownerId, agentId, typeId, locationId, title, propDescription, price, areaSize, bedrooms, bathrooms, furnishedStatus, parking, balcony, swimmingPool, status) VALUES (7, 5, 2, 2, 4, 'Spacious Duplex Villa in Boyra', 'Luxurious duplex villa in Boyra. Beautiful architecture, private parking, security guard.', 32000000, 3000, 4, 4, 'furnished', 2, 2, 1, 'available')");
        DB::insert("INSERT INTO properties (id, ownerId, agentId, typeId, locationId, title, propDescription, price, areaSize, status) VALUES (8, 5, 1, 3, 3, 'Retail Shop Space Sonadanga', 'Premium commercial retail shop, excellent footfall potential.', 18000000, 900, 'available')");
        DB::insert("INSERT INTO properties (id, ownerId, agentId, typeId, locationId, title, propDescription, price, areaSize, status) VALUES (9, 5, 2, 3, 2, 'Corporate Office Building KDA Avenue', 'Multi-floor commercial building perfect for corporate headquarters.', 75000000, 5000, 'available')");
        DB::insert("INSERT INTO properties (id, ownerId, agentId, typeId, locationId, title, propDescription, price, areaSize, bedrooms, bathrooms, furnishedStatus, parking, balcony, lift, status) VALUES (10, 5, 1, 1, 1, 'Budget Student Hostel near KUET', 'Great investment property, fully set up for student rentals near KUET.', 6000000, 1400, 5, 3, 'unfurnished', 0, 1, 0, 'available')");
        DB::insert("INSERT INTO properties (id, ownerId, agentId, typeId, locationId, title, propDescription, price, areaSize, bedrooms, bathrooms, furnishedStatus, parking, balcony, swimmingPool, status) VALUES (11, 5, 2, 2, 3, 'Elegant 4BHK Villa Sonadanga', 'Outstanding standalone smart villa in Sonadanga, prime residential sector.', 42000000, 3500, 5, 5, 'furnished', 2, 3, 1, 'available')");
        DB::insert("INSERT INTO properties (id, ownerId, agentId, typeId, locationId, title, propDescription, price, areaSize, status) VALUES (12, 5, 1, 3, 4, 'Modern Office Floor in Boyra', 'Well-designed corporate floor with partition walls and server room.', 25000000, 2200, 'available')");

        // 7.5 Seed Property Images
        DB::insert("INSERT INTO property_images (id, propertyId, imagePath, isMain, displayOrder) VALUES (1, 1, '/images/properties/luxury_apartment.png', 1, 1)");
        DB::insert("INSERT INTO property_images (id, propertyId, imagePath, isMain, displayOrder) VALUES (2, 2, '/images/properties/luxury_villa.png', 1, 1)");
        DB::insert("INSERT INTO property_images (id, propertyId, imagePath, isMain, displayOrder) VALUES (3, 3, '/images/properties/commercial_office.png', 1, 1)");
        DB::insert("INSERT INTO property_images (id, propertyId, imagePath, isMain, displayOrder) VALUES (4, 4, '/images/properties/luxury_apartment.png', 1, 1)");
        DB::insert("INSERT INTO property_images (id, propertyId, imagePath, isMain, displayOrder) VALUES (5, 5, '/images/properties/luxury_apartment.png', 1, 1)");
        DB::insert("INSERT INTO property_images (id, propertyId, imagePath, isMain, displayOrder) VALUES (6, 6, '/images/properties/luxury_apartment.png', 1, 1)");
        DB::insert("INSERT INTO property_images (id, propertyId, imagePath, isMain, displayOrder) VALUES (7, 7, '/images/properties/luxury_villa.png', 1, 1)");
        DB::insert("INSERT INTO property_images (id, propertyId, imagePath, isMain, displayOrder) VALUES (8, 8, '/images/properties/commercial_office.png', 1, 1)");
        DB::insert("INSERT INTO property_images (id, propertyId, imagePath, isMain, displayOrder) VALUES (9, 9, '/images/properties/commercial_office.png', 1, 1)");
        DB::insert("INSERT INTO property_images (id, propertyId, imagePath, isMain, displayOrder) VALUES (10, 10, '/images/properties/luxury_apartment.png', 1, 1)");
        DB::insert("INSERT INTO property_images (id, propertyId, imagePath, isMain, displayOrder) VALUES (11, 11, '/images/properties/luxury_villa.png', 1, 1)");
        DB::insert("INSERT INTO property_images (id, propertyId, imagePath, isMain, displayOrder) VALUES (12, 12, '/images/properties/commercial_office.png', 1, 1)");

        // 8. Seed Bookings
        DB::insert("INSERT INTO bookings (id, userId, propertyId, agentId, bookingType, status, totalAmount) VALUES (1, 4, 1, 1, 'visit', 'completed', 0)");
        DB::insert("INSERT INTO bookings (id, userId, propertyId, agentId, bookingType, status, totalAmount) VALUES (2, 4, 3, 1, 'reservation', 'completed', 500000)");

        // 9. Seed Transactions
        DB::insert("INSERT INTO transactions (id, bookingId, transactionType, amount, paymentMethod, referenceNo, status) VALUES (1, 2, 'booking_fee', 500000, 'bank_transfer', 'TXN-20260622-001', 'completed')");

        // 10. Seed Reviews & Agent Reviews
        DB::insert("INSERT INTO reviews (id, userId, propertyId, rating, comments) VALUES (1, 4, 1, 5, 'Perfect location, very quiet, and the layout is outstanding!')");
        DB::insert("INSERT INTO agent_reviews (id, userId, agentId, rating, comments) VALUES (1, 4, 1, 5, 'Sheikh Sadi was extremely helpful throughout the entire site tour and negotiation process.')");

        // 11. Seed Wishlists
        DB::insert("INSERT INTO wishlist (id, userId, propertyId) VALUES (1, 4, 2)");

        // Commit all inserts
        DB::statement("COMMIT");
    }
}
