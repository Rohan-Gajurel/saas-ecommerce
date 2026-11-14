<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Business;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super Admin
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('password'),
        ]);
        $superAdmin->assignRole('Super Admin');

        // Create Businesses
        $businesses = Business::all();
        $adminCounter = 1;
        $staffCounter = 1;

        foreach ($businesses as $business) {
            // Create Admin for each Business
            $admin = User::create([
                'name' => 'Admin ' . $adminCounter,
                'email' => 'admin_' . $business->id . '@example.com',
                'password' => Hash::make('password'),
                'business_id' => $business->id,
            ]);
            $admin->assignRole('Admin');
            $adminCounter++;

            // Create Staff for each Business
            $staff = User::create([
                'name' => 'Staff ' . $staffCounter,
                'email' => 'staff_' . $business->id . '@example.com',
                'password' => Hash::make('password'),
                'business_id' => $business->id,
            ]);
            $staff->assignRole('Staff');
            $staffCounter++;
        }

    }
}